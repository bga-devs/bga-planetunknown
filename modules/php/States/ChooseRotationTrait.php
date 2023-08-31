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
use PU\Managers\Susan;
use PU\Managers\ZooCards;

trait ChooseRotationTrait
{
  public function stChooseRotation()
  {
    $playerCount = Players::count();
    if ($playerCount < 3) {

      $rotation = Globals::getSusanRotation();
      $rotation = ($rotation + 5) % 6;

      Susan::rotate($rotation);
      $this->gamestate->nextState(Globals::getEventCardsGame());
    }
  }

  public function actChooseRotation($rotation)
  {
    $player = Players::getActive();

    $this->checkAction('actChooseRotation');

    if ($rotation < 0 || $rotation > 5) {
      throw new \BgaVisibleSystemException('You cannot rotate Susan in position ' . $rotation . '. Should not happen');
    }
    Susan::rotate($rotation, $player);
    $this->gamestate->nextState(Globals::getEventCardsGame());
  }
}
