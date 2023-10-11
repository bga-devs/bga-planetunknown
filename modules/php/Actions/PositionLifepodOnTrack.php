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
    return $player->getCollectedLifepods()->count() && $this->getPossibleSpaceIds($player);
  }

  public function getRemaining()
  {
    return $this->getCtxArg('remaining') ?? 1;
  }

  public function getPossibleLifepodIds()
  {
    $lifepodId = $this->getCtxArg('lifepodId') ?? null;
    return is_null($lifepodId)? $this->getPlayer()->getCollectedLifepods()->getIds() : [$lifepodId];
  }

  public function getPossibleSpaceIds($player)
  {
    $spaceIds = [];

    foreach (ALL_TYPES as $track) {
      $level = $player->corporation()->getLevelOnTrack($track);
      $max = $player->corporation()->getMaxIndexOnTrack($track);
      for ($i = 0; $i < $max; $i++) {
        if (is_null($player->getMeepleOnCell(['x' => $track, 'y' => $i], null, false))) {
          $spaceIds[] = $track . '_' . $i;
        }
      }
    }

    return $spaceIds;
  }

  public function argsPositionLifepodOnTrack()
  {
    $player = $this->getPlayer();
    $lifepodId = $this->getCtxArg('lifepodId') ?? null;

    return [
      'spaceIds' => $this->getPossibleSpaceIds($player),
      'lifepodIds' => $this->getPossibleLifepodIds(),
      'remaining' => min($this->getRemaining(), $player->getCollectedLifepods()->count()),
      'descSuffix' => is_null($lifepodId)? '' : 'aftercollect'
    ];
  }

  public function actPositionLifepodOnTrack($lifepodId, $spaceId)
  {
    $player = $this->getPlayer();
    $args = $this->argsPositionLifepodOnTrack();
    if (!in_array($lifepodId, $args['lifepodIds'])) {
      throw new \BgaVisibleSystemException('You cannot place this lifepod. Should not happen');
    }
    if (!in_array($spaceId, $args['spaceIds'])) {
      throw new \BgaVisibleSystemException('You cannot place your lifepod here. Should not happen');
    }

    $cell = Planet::getCellFromId($spaceId);
    $lifepod = Meeples::get($lifepodId);
    $lifepod->setX($cell['x']);
    $lifepod->setY($cell['y']);
    Notifications::placeMeeple($player, LIFEPOD, $lifepod);

    if($this->getRemaining() > 1){
      $this->pushParallelChild([
        'action' => POSITION_LIFEPOD_ON_TRACK,
        'args' => [
          'remaining' => $this->getRemaining() - 1
        ]
      ]);
    }
  }
}
