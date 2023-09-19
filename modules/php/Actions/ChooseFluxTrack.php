<?php

namespace PU\Actions;

use PU\Managers\Meeples;
use PU\Managers\Players;
use PU\Managers\Tiles;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\PGlobals;
use PU\Core\Stats;
use PU\Helpers\Utils;
use PU\Helpers\FlowConvertor;
use PU\Managers\Susan;
use PU\Models\Meeple;
use PU\Models\Planet;

class ChooseFluxTrack extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_CHOOSE_FLUX_TRACK;
  }

  public function argsChooseFluxTrack()
  {
    $player = $this->getPlayer();

    return [
      'tracks' => ALL_TYPES
    ];
  }

  public function actChooseFluxTrack($track)
  {
    $player = $this->getPlayer();
    if (!in_array($track, ALL_TYPES)) {
      throw new \BgaVisibleSystemException('Track value unknown. Should not happen');
    }

    PGlobals::setFluxTrack($player->getId(), $track);
  }
}
