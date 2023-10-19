<?php

namespace PU\Actions;

use PU\Managers\Meeples;
use PU\Managers\Players;
use PU\Managers\Tiles;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\Stats;
use PU\Core\Globals;
use PU\Core\PGlobals;
use PU\Helpers\Utils;
use PU\Helpers\FlowConvertor;
use PU\Managers\Susan;
use PU\Models\Corporations\Corporation;
use PU\Models\Planet;
use PU\Models\Tile;

class GainBiomassPatch extends \PU\Models\Action
{
  public function getState()
  {
    return ST_GAIN_BIOMASS_PATCH;
  }

  public function getDescription()
  {
    return clienttranslate('Gain one <BIOMASS-PATCH>');
  }

  public function isDoable($player)
  {
    $tile = new Tile(['type' => BIOMASS_PATCH]);
    $specialRule = Globals::getTurnSpecialRule();
    return !empty($player->planet()->getPlacementOptions($tile, true, $specialRule));
  }

  public function argsGainBiomassPatch()
  {
    $n = $this->getPlayer()
      ->corporation()
      ->canUse(TECH_WORMHOLE_GAIN_TWO_BIOMASS_PATCHES)
      ? 2
      : 1;
    return [
      'n' => $n,
      'descSuffix' => $n > 1 ? 'choice' : '',
    ];
  }

  public function stGainBiomassPatch()
  {
    $args = $this->argsGainBiomassPatch();
    if ($args['n'] == 1) {
      return [1];
    } // Ensure the UI is not entering the state !!!
  }

  public function actGainBiomassPatch($n)
  {
    $args = $this->argsGainBiomassPatch();
    if ($n > $args['n']) {
      throw new \BgaVisibleSystemException('You cannot gain that much biomass patch. Should not happen.');
    }
    $player = $this->getPlayer();

    // WORMHOLE => flag TECH
    if ($n > 1) {
      $pId = $player->getId();
      $flags = PGlobals::getFlags($pId);
      $flags[TECH_WORMHOLE_GAIN_TWO_BIOMASS_PATCHES] = true;
      PGlobals::setFlags($pId, $flags);
    }

    // Create patch and either store them or place them
    for ($j = 0; $j < $n; $j++) {
      $patch = Tiles::createBiomassPatch($player);
      $action = [
        'action' => PLACE_TILE,
        'args' => [
          'forcedTiles' => [$patch->getId()],
          'biomassPatch' => true,
        ],
      ];

      Notifications::receiveBiomassPatch($player, $patch);
      $this->insertAsChild($action);
    }
  }
}
