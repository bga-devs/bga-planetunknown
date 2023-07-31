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
use PU\Managers\ZooCards;

trait ChooseBoardsTrait
{
  public function argChooseSetup()
  {
    return [];
  }

  public function stChooseSetup()
  {
    if (
      Globals::getPlanetOption() == OPTION_PLANET_A &&
      Globals::getCorporationOption() == OPTION_CORPORATION_UNIVERSAL &&
      !Globals::isPrivateObjectiveCardsGame()
    ) {
      $this->gamestate->setAllPlayersNonMultiactive('notNeeded');
    }
  }
}
