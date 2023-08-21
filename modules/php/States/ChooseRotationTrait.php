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
      Susan::rotate(-1);
      $this->gamestate->nextState(Globals::getEventCardsGame());
    }
  }
}
