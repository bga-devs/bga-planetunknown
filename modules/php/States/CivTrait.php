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
        'childs' => $player->getEndOfTurnActions(),
      ],
      null, //which callback ? As a call back is defined in initCustomTurnOrder ?
      [$player->getId()]
    );
  }

  public function stPostChooseCivCard()
  {
    $players = Players::getAll();
    foreach ($players as $pId => $player) {
      $player->emptyEndOfTurnActions();
    }
    $this->gamestate->nextState('');
  }
}
