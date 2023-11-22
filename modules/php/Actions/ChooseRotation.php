<?php

namespace PU\Actions;

use PU\Managers\Meeples;
use PU\Managers\Players;
use PU\Managers\Tiles;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\Stats;
use PU\Helpers\Utils;
use PU\Helpers\FlowConvertor;
use PU\Managers\Susan;
use PU\Models\Meeple;
use PU\Models\Planet;

class ChooseRotation extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_CHOOSE_ROTATION_ENGINE;
  }

  public function actChooseRotation($rotation)
  {
    $player = $this->getPlayer();
    $rotation = (($rotation % 6) + 6) % 6;
    Susan::rotate($rotation, $player);
  }
}
