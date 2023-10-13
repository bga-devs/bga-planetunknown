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
use PU\Models\Planet;

class CollectMeeple extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_COLLECT_MEEPLE;
  }

  public function isDoable($player)
  {
    return $this->getCollectableMeeples($player);
  }

  public function getN()
  {
    return $this->getCtxArg('n') ?? 1;
  }

  public function getAction()
  {
    return $this->getCtxArg('action') ?? 'collect';
  }

  public function getType()
  {
    return $this->getCtxArg('type');
  }

  public function getLocation()
  {
    return $this->getCtxArg('location') ?? 'planet';
  }

  public function getForcedMeeple()
  {
    $forcedMeeples = $this->getCtxArg('forcedMeeples');
    return $forcedMeeples ? Tiles::getMany($forcedMeeples) : null;
  }

  public function getCollectableMeeples($player)
  {
    $meeples = $player
      ->getMeeples($this->getType())
      ->where('location', $this->getLocation())
      ->toArray();
    return array_map(fn ($meeple) => $meeple->getX() . '_' . $meeple->getY(), $meeples);
  }

  public function argsCollectMeeple()
  {
    $player = $this->getPlayer();
    $collectableMeeples = $this->getCollectableMeeples($player);

    return [
      'meeples' => $collectableMeeples,
      'action' => $this->getAction() == 'destroy' ? clienttranslate('destroy') : clienttranslate('collect'),
      'type' => $this->getType() == LIFEPOD ? clienttranslate('lifepod(s)') : clienttranslate('rover(s)'),
      'n' => min($this->getN(), count($collectableMeeples)),
      'i18n' => ['action', 'type'],
    ];
  }

  public function actCollectMeeple($spaceIds)
  {
    $player = $this->getPlayer();
    $args = $this->argsCollectMeeple();

    if (count($spaceIds) != $args['n']) {
      throw new \BgaVisibleSystemException('You must collect exactly ' . $args['n'] . ' meeples. Should not happen.');
    }

    // take meeples
    $meeples = [];
    $type = $this->getType();

    foreach ($spaceIds as $spaceId) {
      if (!in_array($spaceId, $args['meeples'])) {
        throw new \BgaVisibleSystemException('You cannot collect here ' . $spaceId . '. Should not happen.');
      }

      $cell = Planet::getCellFromId($spaceId);
      $meeples[] = $player->getMeepleOnCell($cell, $type);
    }

    //move them
    if ($args['action'] == 'destroy') {
      foreach ($meeples as $meeple) {
        $meeple->destroy();
      }
    } else {
      foreach ($meeples as $meeple) {
        $player->corporation()->collect($meeple);

        if ($player->corporation()->getId() == COSMOS_INC) {
          $this->pushParallelChild([
            'action' => POSITION_LIFEPOD_ON_TRACK,
            'args' => ['lifepodId' => $meeple->getId()],
            'optional' => true,
          ]);
        }
      }
    }

    Notifications::collectMeeple($player, $meeples, $args['action']);
  }
}
