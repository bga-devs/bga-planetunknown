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
use PU\Models\Corporations\Corporation;
use PU\Models\Planet;

class ResetTrack extends \PU\Models\Action
{
  public function getState()
  {
    return ST_RESET_TRACK;
  }

  public function getType()
  {
    return $this->getCtxArg('type');
  }

  public function getDescription()
  {
    return [
      'log' => \clienttranslate('Reset ${type}${type_name} track'),
      'args' => [
        'type' => '',
        'type_name' => $this->getType(),
        'i18n' => ['type_name'],
      ],
    ];
  }

  public function stResetTrack()
  {
    return [$this->getType()]; // Ensure the UI is not entering the state !!!
  }

  public function actResetTrack($type)
  {
    $player = $this->getPlayer();
    $spaceId = $type . '_' . 0;
    $player->corporation()->moveTrack($type, $spaceId, false);
  }
}
