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
  public function stPreEndOfTurn()
  {
    $civCardIds = Cards::getAll()
      ->where('location', 'hand_civ')
      ->getIds();

    Cards::move($civCardIds, 'playedCivCards');

    Notifications::revealCards();

    Notifications::scores();
  }

  public function stEndGameTurn()
  {
    $players = Players::getAll();

    $flows = [];

    foreach ($players as $pId => $player) {
      $actions = [];

      $actions = $player->getEndOfGameActions();

      if ($actions) {
        $flows[$pId] = [
          'type' => NODE_PARALLEL,
          'childs' => $actions
        ];
      }
    }

    Engine::multipleSetup(
      $flows,
      ['method' => 'stPostEndGameTurn']
    );
  }

  public function stPostEndGameTurn()
  {
    Susan::refill();

    Notifications::endOfTurn();

    $players = Players::getAll();

    $newTurn = false;

    foreach ($players as $pId => $player) {
      $player->emptyEndOfGameActions();
      $newTurn =  $newTurn || $player->getEndOfTurnActions();
    }

    //Game end if no player has gain an extra end of turn action
    if ($newTurn) {
      $this->gamestate->nextState('newTurn');
    }

    $this->gamestate->nextState('endGame');
  }
}
