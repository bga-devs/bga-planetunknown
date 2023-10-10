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

  public function getPlayableTrackers()
  {
    $player = $this->getPlayer();
    $playableTypes = [];
    foreach (ALL_TYPES as $type) {
      if ($player->corporation()->getLevelOnTrack($type) < 4) {
        $playableTypes[] = $type;
      }
    }
    return $playableTypes;
  }

  public function isDoable($player)
  {
    return count($this->getPlayableTrackers());
  }

  public function getDescription()
  {
    return [
      'log' => \clienttranslate('Advance all your trackers to 5th position, if possible'),
      'args' => [],
    ];
  }

  public function argsMoveTrackersToFive()
  {
    return [
      'playableTracks' => $this->getPlayableTrackers()
    ];
  }

  public function actMoveTrackersToFive($type)
  {
    $playableTypes = $this->getPlayableTrackers();

    if (!in_array($type, $playableTypes)) {
      throw new BgaVisibleSystemException("You can\'t choose the $type tracker. Should not happen");
    }

    $this->insertAsChild([
      'type' => NODE_SEQ,
      'childs' => [
        [
          'action' => MOVE_TRACKER_BY_ONE,
          'args' => [
            'type' => $type,
            'moveId' => 1,
            'n' => 1,
            'withBonus' => NO_SYNERGY,
          ],
        ],
        [
          'action' => MOVE_TRACKERS_TO_FIVE
        ]
      ]
    ]);
  }
}
