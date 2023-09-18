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

class PositionLifepodOnTrack extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_POSITION_LIFEPOD_ON_TRACK;
  }

  public function isDoable($player)
  {
    return $player->getCollectedLifepod()->count() && $this->getPossibleSpaceIds($player);
  }

  public function getRemaining()
  {
    return $this->getCtxArg('remaining');
  }

  public function getPossibleSpaceIds($player)
  {
    $spaceIds = [];

    foreach (ALL_TYPES as $track) {
      $level = $player->corporation()->getLevelOnTrack($track);
      $max = $player->corporation()->getMaxIndexOnTrack($track);
      for ($i = $level + 1; $i < $max; $i++) {
        if (!$player->hasLifepodOnTrack($track, $i)) {
          $spaceIds[] = $track . '_' . $i;
        }
      }
    }

    return $spaceIds;
  }

  public function argsPositionLifepodOnTrack()
  {
    $player = $this->getPlayer();

    return [
      'spaceIds' => $this->getPossibleSpaceIds($player),
      'remaining' => min($this->getRemaining(), $player->getCollectedLifepod()->count())
    ];
  }

  public function actPositionLifepodOnTrack($spaceId)
  {
    $player = $this->getPlayer();
    $args = $this->argsPlaceMeeple();
    if (!in_array($spaceId, $args['spaceIds'])) {
      throw new \BgaVisibleSystemException('You cannot place your lifepod here. Should not happen');
    }

    $cell = Planet::getCellFromId($spaceId);

    $lifepod = $player->getCollectedLifepod()->first();

    $lifepod->setX($cell['x']);
    $lifepod->setY($cell['y']);
    Notifications::placeMeeple($player, LIFEPOD, $lifepod);

    $this->pushParallelChild([
      'action' => POSITION_LIFEPOD_ON_TRACK,
      'args' => [
        'remaining' => $this->getRemaining() - 1
      ]
    ]);
  }
}
