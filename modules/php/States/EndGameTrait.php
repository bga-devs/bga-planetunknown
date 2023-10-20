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

trait EndGameTrait
{
  // Utility function that reveal CIV or objective cards
  public function revealCardsInHand($type)
  {
    // Reveal cards and update scores
    $fromLocation = $type == CIV ? 'hand_civ' : 'hand_obj';
    $cardIds = Cards::getInLocation($fromLocation)->getIds();
    if (empty($cardIds)) {
      return;
    }

    $toLocation = $type == CIV ? 'playedCivCards' : 'playedObjCards';
    Cards::move($cardIds, $toLocation);
    Notifications::revealCards($type);
    Notifications::scores();
  }

  public function stPreEndGameTurn()
  {
    $this->revealCardsInHand(CIV);
    $this->gamestate->nextState('');
  }

  public function stEndGameTurn()
  {
    $players = Players::getAll();
    Globals::setPhase(END_OF_GAME_PHASE);
    $flows = [];
    foreach ($players as $pId => $player) {
      $actions = $player->getEndOfGameActions();

      if ($player->hasTech(TECH_REPUBLIC_GET_2_CIV_CARDS_END_OF_GAME)) {
        $actions[] = [
          'action' => TAKE_CIV_CARD,
          'args' => [
            'level' => 'all',
            'n' => 2,
          ],
        ];
      }

      if ($actions) {
        $flows[$pId] = [
          'type' => NODE_PARALLEL,
          'childs' => $actions,
        ];
      }
    }

    Engine::multipleSetup($flows, ['method' => 'stPostEndGameTurn']);
  }

  public function stPostEndGameTurn()
  {
    Susan::refill();
    Globals::setPhase(NORMAL_PHASE);
    Notifications::endOfTurn();

    $players = Players::getAll();
    $newTurn = false;
    foreach ($players as $pId => $player) {
      $player->emptyEndOfGameActions();
      $newTurn = $newTurn || $player->getEndOfTurnActions();
    }

    // Game end if no player has gain an extra end of turn action
    if ($newTurn) {
      $this->initCustomTurnOrder('civCardTurn', $order, 'stChooseCivCard', ST_END_TURN);
    } else {
      $this->revealCardsInHand('Obj');
      $this->gamestate->jumpToState(ST_PRE_END_OF_GAME);
    }
  }

  function stPreEndOfGame()
  {
    Notifications::endOfGame();
    if (Globals::isSolo()) {
      $player = Players::getAll()->first();
      Notifications::soloReveal($player);
    }
    $this->gamestate->nextState('');
  }
}
