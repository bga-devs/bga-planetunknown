<?php

namespace PU\States;

use PU\Core\Globals;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\Stats;
use PU\Core\Preferences;
use PU\Managers\Players;
use PU\Managers\Meeples;
use PU\Managers\Tiles;
use PU\Managers\Actions;
use PU\Helpers\Utils;
use PU\Helpers\Log;
use PU\Managers\Cards;
use PU\Managers\Susan;
use PU\Models\Meeple;

trait SetupTrait
{
  /*
   * setupNewGame:
   */
  protected function setupNewGame($players, $options = [])
  {
    Globals::setMode(MODE_APPLY);
    Globals::setupNewGame($players, $options);
    Players::setupNewGame($players, $options);
    Preferences::setupNewGame($players, $this->player_preferences);
    Meeples::setupNewGame($players, $options);
    Tiles::setupNewGame($players, $options);
    Cards::setupNewGame($players, $options);
    Susan::setupNewGame($players, $options);
    // Stats::checkExistence();

    // Globals::setFirstPlayer($this->getNextPlayerTable()[0]);

    Globals::setSetupChoices([]);
  }

  // SETUP BRANCH : finish setup for first game or go to advanced setup to choose corpo/planet/private objectives
  public function stSetupBranch()
  {
    if (
      Globals::getPlanetOption() == OPTION_PLANET_A &&
      Globals::getCorporationOption() == OPTION_CORPORATION_UNIVERSAL &&
      !Globals::isPrivateObjectiveCardsGame()
    ) {
      $this->gamestate->jumpToState(ST_FINISH_SETUP);
    } else {
      $this->gamestate->setAllPlayersMultiactive();
      $this->gamestate->jumpToState(ST_CHOOSE_SETUP);
    }
  }

  // FINISH SETUP : create meeples
  public function stFinishSetup()
  {
    $players = Players::getAll();
    foreach ($players as $pId => $player) {
      $meeples = Meeples::setupPlayer($pId);
      Notifications::setupPlayer($player, $meeples);
    }

    Notifications::finishSetup();
    $this->activeNextPlayer();
    $this->gamestate->nextState('');
  }
}
