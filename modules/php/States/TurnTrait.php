<?php

namespace PU\States;

use PU\Core\Globals;
use PU\Core\Notifications;
use PU\Core\Engine;
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
  /**
   * Boot engine for all players
   */
  function stStartParallel()
  {
    $pIds = Players::getAll()->getIds();
    Engine::setup(
      [
        'action' => \PLACE_TILE
      ],
      ['method' => 'stEndOfTurn'],
      $pIds
    );
  }

  public function stEventCard()
  {
    $card = Cards::pickOneForLocation('deck_event', 'discard_event', Cards::countInLocation('discard_event'));

    //if deck_event is empty -> last round
    if (!Cards::countInLocation('deck_event')) {
      Globals::setGameEnded(true);
      Notifications::lastTurn('eventCard');
    }

    Notifications::newEventCard($card);

    $this->gamestate->setAllPlayersMultiactive();
    $this->gamestate->nextState('');
  }

  function argPlayAfterEventCard()
  {
    return [
      'eventCardId' => Cards::getTopOf('discard_event')->first()->getId()
    ];
  }

  function stPlayAfterEventCard()
  {
    $pIds = Players::getAll()->getIds();
    $args = $this->argPlayAfterEventCard();
    $card = Cards::get($args['eventCardId']);

    $effect = $card->effect();
    if (is_array($effect)) { //if each player have special flow
      if (isset($effect['nestedFlows'])) {
        foreach ($effect['nestedFlows'] as $pId => $flow) {
          Engine::setup(
            $flow,
            ['method' => 'stEndOfEventTurn'],
            $pId
          );
        }
      } else {
        Engine::setup(
          $effect,
          ['method' => 'stEndOfEventTurn'],
          $pIds
        );
      }
    } else {
      $this->gamestate->jumpToState(ST_SETUP_BRANCH);
    }
  }

  /*******************************
   ********************************
   ********** END OF TURN *********
   ********************************
   *******************************/

  /**
   * End of eventTurn : ST_SETUP_BRANCH
   */
  function stEndOfEventTurn()
  {

    $this->gamestate->jumpToState(ST_SETUP_BRANCH);
  }

  /**
   * End of turn : replenish and check break 
   */
  function stEndOfTurn()
  {
    Susan::refill();

    Notifications::endOfTurn();

    $this->gamestate->jumpToState(ST_PRE_CHOOSE_CIV_CARD);
  }
}
