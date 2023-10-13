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

class PositionLifepodOnTech extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_POSITION_LIFEPOD_ON_TRACK;
  }

  public function getDescription()
  {
    $lifepodId = $this->getCtxArg('lifepodId');
    $log = is_null($lifepodId) ? clienttranslate('Position ${n} lifepod(s)') : clienttranslate('Position the lifepod you collected');
    return [
      'log' => $log,
      'args' => ['n' => $this->getRemaining()],
    ];
  }

  public function isDoable($player)
  {
    return $player->getCollectedLifepods()->count() > 0;
  }

  public function isOptional()
  {
    return true;
  }

  public function getRemaining()
  {
    return $this->getCtxArg('remaining') ?? 1;
  }

  public function getPossibleLifepodIds()
  {
    $lifepodId = $this->getCtxArg('lifepodId') ?? null;
    return is_null($lifepodId)
      ? $this->getPlayer()
      ->getCollectedLifepods()
      ->getIds()
      : [$lifepodId];
  }

  public function getPossibleSpaceIds($player)
  {
    $spaceIds = [];

    for ($index = 1; $index <= 6; $index++) {
      if (!$player->corporation()->hasTechLevel($index)) {
        $spaceIds[] = 'tech_nb_' . $index;
      }
    }

    if (is_null($this->getCtxArg('lifepodId'))) {
      $spaceIds[] = 'reserve';
    }

    return $spaceIds;
  }

  public function argsPositionLifepodOnTrack()
  {
    $player = $this->getPlayer();
    $lifepodId = $this->getCtxArg('lifepodId');

    return [
      'spaceIds' => $this->getPossibleSpaceIds($player),
      'lifepodIds' => $this->getPossibleLifepodIds(),
      'remaining' => min($this->getRemaining(), $player->getCollectedLifepods()->count()),
      'descSuffix' => is_null($lifepodId) ? '' : 'aftercollect',
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

    $lifepod = Meeples::get($lifepodId);
    if ($spaceId == 'reserve') {
      $lifepod->setX('');
      $lifepod->setY('');
    } else {
      $lifepod->setX($spaceId);
      $lifepod->setY('');
    }
    Notifications::repositionLifepod($player, $lifepod);

    if ($this->getRemaining() > 1) {
      $this->pushParallelChild([
        'action' => POSITION_LIFEPOD_ON_TECH,
        'args' => [
          'remaining' => $this->getRemaining() - 1,
        ],
      ]);
    }
  }
}
