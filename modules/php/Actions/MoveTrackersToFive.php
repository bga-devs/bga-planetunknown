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

class MoveTrackersToFive extends \PU\Models\Action
{
  public function getState()
  {
    return ST_MOVE_TRACKERS_TO_FIVE;
  }

  public function isDoable($player)
  {
    return true;
  }

  public function isAutomatic($player = null)
  {
    return true;
  }

  public function getDescription()
  {
    return [
      'log' => \clienttranslate('Advance all your trackers to 5th position, if possible'),
      'args' => [
      ],
    ];
  }

  public function argsMoveTrackersToFive()
  {

    return [
      
    ];
  }

  public function stMoveTrackersToFive()
  {
    return []; // Ensure the UI is not entering the state !!!
  }

  public function actMoveTrackersByOne()
  {
    $player = $this->getPlayer();
    
    foreach (ALL_TYPES as $type) {
      $player->corporation()->setLevelOnTrack($type, 5);
    }
  }
}
