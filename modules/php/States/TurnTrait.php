<?php

namespace PU\States;

use PU\Core\Globals;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\PGlobals;
use PU\Core\Stats;
use PU\Helpers\Log;
use PU\Managers\Players;
use PU\Managers\ActionCards;
use PU\Managers\Meeples;
use PU\Managers\Scores;
use PU\Managers\Actions;
use PU\Managers\Cards;
use PU\Managers\Susan;
use PU\Managers\Tiles;
use PU\Managers\ZooCards;

trait TurnTrait
{
  public function stStartTurn()
  {
    $this->gamestate->setAllPlayersMultiactive();
    $this->gamestate->jumpToState(ST_CHOOSE_ROTATION);
  }

  ///////////////////////////////////////////////
  //  ____       _        _   _
  // |  _ \ ___ | |_ __ _| |_(_) ___  _ __
  // | |_) / _ \| __/ _` | __| |/ _ \| '_ \
  // |  _ < (_) | || (_| | |_| | (_) | | | |
  // |_| \_\___/ \__\__,_|\__|_|\___/|_| |_|
  ///////////////////////////////////////////////

  public function stChooseRotation()
  {
    $playerCount = Players::count();
    if ($playerCount >= 3) {
      return;
    }

    $rotation = Globals::getSusanRotation();
    $rotation = ($rotation + 5) % 6;

    Susan::rotate($rotation);

    // Go to tile selection OR resolve event card
    $transition = Globals::getEventCardsGame();
    $this->gamestate->nextState($transition);
  }

  public function actChooseRotation($rotation)
  {
    $this->checkAction('actChooseRotation');
    $player = Players::getActive();
    Susan::rotate((($rotation % 6) + 6) % 6, $player);

    // Go to tile selection OR resolve event card
    $transition = Globals::getEventCardsGame();
    $this->gamestate->nextState($transition);
  }

  //////////////////////////////////
  //  _____                 _
  // | ____|_   _____ _ __ | |_
  // |  _| \ \ / / _ \ '_ \| __|
  // | |___ \ V /  __/ | | | |_
  // |_____| \_/ \___|_| |_|\__|
  //////////////////////////////////

  public function stRevealEventCard()
  {
    $card = Cards::pickOneForLocation('deck_event', 'discard_event', Cards::countInLocation('discard_event'));

    //if deck_event is empty -> last round
    if (!Cards::countInLocation('deck_event') && !Globals::isGameEndTriggered()) {
      Globals::setGameEndTriggered(true);
      Notifications::endOfGameTriggeredEventCard();
    }

    Notifications::newEventCard($card);

    $this->gamestate->setAllPlayersMultiactive();
    $this->gamestate->nextState('');
  }

  function argPlayAfterEventCard()
  {
    return [
      'eventCardId' => Cards::getTopOf('discard_event')
        ->first()
        ->getId(),
    ];
  }

  function stPlayAfterEventCard()
  {
    $pIds = Players::getAll()->getIds();
    $args = $this->argPlayAfterEventCard();
    $card = Cards::get($args['eventCardId']);

    $effect = $card->effect();
    if (is_array($effect)) {
      //if each player have special flow
      if (isset($effect['nestedFlows'])) {
        Engine::multipleSetup($effect['nestedFlows'], ['method' => 'stEndOfEventTurn'], 'endOfTurn', $pIds);
      } else {
        Engine::setup($effect, ['method' => 'stEndOfEventTurn'], 'endOfTurn', $pIds);
      }
    } else {
      $this->gamestate->jumpToState(ST_SETUP_BRANCH);
    }
  }

  /**
   * End of eventTurn : start engine
   */
  function stEndOfEventTurn()
  {
    $this->gamestate->setAllPlayersMultiactive();
    $this->gamestate->jumpToState(ST_START_TURN_ENGINE);
  }

  /////////////////////////////////////////////////////////////////
  //  ____  _             _     _____             _
  // / ___|| |_ __ _ _ __| |_  | ____|_ __   __ _(_)_ __   ___
  // \___ \| __/ _` | '__| __| |  _| | '_ \ / _` | | '_ \ / _ \
  //  ___) | || (_| | |  | |_  | |___| | | | (_| | | | | |  __/
  // |____/ \__\__,_|_|   \__| |_____|_| |_|\__, |_|_| |_|\___|
  //                                        |___/
  /////////////////////////////////////////////////////////////////

  /**
   * Boot engine for all players
   */
  function stStartTurnEngine()
  {
    $players = Players::getAll();

    $flows = [];
    $endOfGameTriggered = false;

    foreach ($players as $pId => $player) {
      $actions = [];

      $actions[] = [
        'action' => \PLACE_TILE,
        'args' => ['type' => 'normal'],
      ];

      // Check if end of game is triggered or not
      if (!$endOfGameTriggered && !$player->canTakeAction(PLACE_TILE, [])) {
        $endOfGameTriggered = true;
        Notifications::endOfGameTriggered($player);
        Globals::setGameEndTriggered(true);
      }

      //ADD EXTRA ACTION EACH TURN
      $player->corporation()->addAutomaticActions($actions);

      $flows[$pId] = [
        'type' => NODE_PARALLEL,
        'childs' => $actions,
      ];
    }

    Engine::multipleSetup($flows, ['method' => 'stEndTurnEngine']);
  }

  //////////////////////////////////////////////////////////
  //  _____           _   _____             _
  // | ____|_ __   __| | | ____|_ __   __ _(_)_ __   ___
  // |  _| | '_ \ / _` | |  _| | '_ \ / _` | | '_ \ / _ \
  // | |___| | | | (_| | | |___| | | | (_| | | | | |  __/
  // |_____|_| |_|\__,_| |_____|_| |_|\__, |_|_| |_|\___|
  //                                  |___/
  ///////////////////////////////////////////////////////////

  /**
   * End of turn : replenish and check if any CIV card need to be taken
   */
  function stEndTurnEngine()
  {
    Susan::refill();
    Notifications::endOfTurn();

    // Compute the list of players with endOfTurn actions and wake themp up in turn order
    $order = [];
    $firstPlayer = Globals::getFirstPlayer();
    $pId = $firstPlayer;
    do {
      if (Players::get($pId)->getEndOfTurnActions()) {
        $order[] = $pId;
      }
      $pId = Players::getNextId($pId);
    } while ($pId != $firstPlayer);

    $this->initCustomTurnOrder('civCardTurn', $order, 'stChooseCivCard', ST_END_TURN);
  }

  // Boot the engine for that single awaken player
  public function stChooseCivCard()
  {
    $player = Players::getActive();
    Engine::setup(
      [
        'type' => NODE_PARALLEL,
        'childs' => $player->getEndOfTurnActions(),
      ],
      ['order' => 'civCardTurn'],
      'CivCard',
      [$player->getId()]
    );
  }

  // Now that everyone is done, proceed to the end of turn
  public function stEndTurn()
  {
    Susan::refill();

    // Update first player
    $nextId = Players::getNextId(Globals::getFirstPlayer());
    Globals::setFirstPlayer($nextId);
    Notifications::changeFirstPlayer($nextId);

    // Notify end of turn
    Notifications::endOfTurn();

    // Clear endOfTurn actions
    $players = Players::getAll();
    foreach ($players as $pId => $player) {
      $player->emptyEndOfTurnActions();
    }

    // Game end if one depot is empty or if gameEnded flag is true (if a player couldn't play any tile or all event cards were played)
    $nextState = Susan::hasEmptyDepot() || Globals::isGameEndTriggered() ? ST_PRE_END_GAME_TURN : ST_START_TURN;
    $this->gamestate->jumpToState($nextState);
  }
}
