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

trait CivTrait
{
  public function stPreChooseCivCard()
  {
    $order = [];
    $firstPlayer = Globals::getFirstPlayer();
    $pId = $firstPlayer;
    do {
      if (Players::get($pId)->getEndOfTurnActions()) {
        $order[] = $pId;
      }
      $pId = Players::getNextId($pId);
    } while ($pId != $firstPlayer);

    $this->initCustomTurnOrder('civCardTurn', $order, 'stChooseCivCard', ST_POST_CHOOSE_CIV_CARD);
  }

  public function stChooseCivCard()
  {
    $player = Players::getActive();
    Engine::setup(
      [
        'type' => NODE_PARALLEL,
        'childs' => $player->getEndOfTurnActions()
      ],
      ['order' => 'civCardTurn'],
      'CivCard',
      [$player->getId()]
    );
  }

  public function stPostChooseCivCard()
  {
    Susan::refill();

    $nextId = Players::getNextId(Globals::getFirstPlayer());
    Globals::setFirstPlayer($nextId);
    Notifications::changeFirstPlayer($nextId);

    Notifications::endOfTurn();

    $players = Players::getAll();
    foreach ($players as $pId => $player) {
      $player->emptyEndOfTurnActions();
    }

    //Game end if one depot is empty or if gameEnded flag is true (if a player couldn't play any tile)
    if (Susan::hasEmptyDepot() || Globals::isGameEnded()) {
      $this->gamestate->nextState('gameEnd');
    }

    $this->gamestate->nextState('nextTurn');
  }
}
