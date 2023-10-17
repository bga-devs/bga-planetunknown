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

class ReachNextMilestone extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_REACH_NEXT_MILESTONE;
  }

  public function getDescription()
  {
    $player = $this->getPlayer();
    $flux = $player->corporation()->getFluxTrack();
    return [
      'log' => $this->isDoable($player)
        ? clienttranslate('Move to the next ${type} milestone')
        : clienttranslate('There is no next milestone on ${type} track'),
      'args' => [
        'type' => $flux,
        'i18n' => ['type']
      ],
    ];
  }

  public function isAutomatic($player = null)
  {
    return true;
  }

  public function isDoable($player)
  {
    return $player->corporation()->getFluxToNextMilestone() !== false;
  }

  public function argsReachNextMilestone()
  {
    $flux = $this->getPlayer()->corporation()->getFluxTrack();
    return ['flux' => $flux];
  }

  public function stReachNextMilestone()
  {
    return [];
  }

  public function actReachNextMilestone()
  {
    $player = $this->getPlayer();
    $action = $player->corporation()->getFluxToNextMilestone();

    if ($action) {
      $this->insertAsChild($action);
    }
  }
}
