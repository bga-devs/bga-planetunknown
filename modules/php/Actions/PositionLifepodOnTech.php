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
    return \ST_POSITION_LIFEPOD_ON_TECH;
  }

  public function getDescription()
  {
    return clienttranslate('Position the lifepod you collected');
  }

  public function isDoable($player)
  {
    return $player->getCollectedLifepods()->count() > 0;
  }

  public function isOptional()
  {
    return true;
  }

  public function getPossibleLifepodIds()
  {
    $lifepodId = $this->getCtxArg('lifepodId') ?? null;
    return [$lifepodId];
  }

  public function getPossibleSpaceIds($player)
  {
    $spaceIds = [];

    for ($index = 1; $index <= 6; $index++) {
      if (!$player->corporation()->hasTechLevel($index)) {
        $spaceIds[] = 'tech-nb_' . $index;
      }
    }

    return $spaceIds;
  }

  public function argsPositionLifepodOnTech()
  {
    $player = $this->getPlayer();
    $lifepodId = $this->getCtxArg('lifepodId');

    return [
      'spaceIds' => $this->getPossibleSpaceIds($player),
      'lifepodIds' => $this->getPossibleLifepodIds(),
    ];
  }

  public function actPositionLifepodOnTech($lifepodId, $spaceId)
  {
    $player = $this->getPlayer();
    $args = $this->argsPositionLifepodOnTech();
    if (!in_array($lifepodId, $args['lifepodIds'])) {
      throw new \BgaVisibleSystemException('You cannot place this lifepod. Should not happen');
    }
    if (!in_array($spaceId, $args['spaceIds'])) {
      throw new \BgaVisibleSystemException('You cannot place your lifepod here. Should not happen');
    }

    $y = explode('_', $spaceId)[1];
    $lifepod = Meeples::get($lifepodId);
    $lifepod->setX('tech-nb');
    $lifepod->setY($y);
    Notifications::repositionLifepod($player, $lifepod);
  }
}
