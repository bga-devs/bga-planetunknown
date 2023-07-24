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

trait SetupTrait
{
  /*
   * setupNewGame:
   */
  protected function setupNewGame($players, $options = [])
  {
    Globals::setupNewGame($players, $options);
    Players::setupNewGame($players, $options);
    Preferences::setupNewGame($players, $this->player_preferences);
    Meeples::setupNewGame($players, $options);
    Tiles::setupNewGame($players, $options);
    Cards::setupNewGame($players, $options);
    Susan::setupNewGame($players, $options);
    // Stats::checkExistence();

    Globals::setFirstPlayer($this->getNextPlayerTable()[0]);

    $this->activeNextPlayer();
  }

  // protected function setupPlayer($player, $notif = false)
  // {
  //   $pId = $player->getId();
  //   $meeples = Meeples::setupPlayer($pId);
  //   $cards = ActionCards::setupPlayer($pId);

  //   // Create buildings for map A
  //   $buildings = [];
  //   $mapId = $player->getMapId();
  //   Stats::setMap($player, $mapId == 'A' ? 100 : ((int) $mapId));
  //   if ($mapId == 'A') {
  //     $buildings[] = Buildings::add($pId, 'size-3', ['x' => 0, 'y' => 9], 0);
  //     $buildings[] = Buildings::add($pId, 'kiosk', ['x' => 0, 'y' => 7], 0);
  //     Stats::incBuiltEnclosures($pId);
  //     Stats::incBuiltKiosks($pId);
  //     Stats::incCoveredHexes($pId, 4);
  //   }

  //   if ($notif) {
  //     Notifications::setupPlayer($player, $mapId, $cards, $meeples, $buildings);
  //   }
  // }

  public function stSetupBranch()
  {
    $this->gamestate->setAllPlayersMultiactive();
    $this->gamestate->nextState('selection');
  }
}
