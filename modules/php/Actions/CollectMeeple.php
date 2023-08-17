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
    return $this->getCtxArg('N');
  }

  public function getToDestroy()
  {
    return $this->getCtxArg('destroy') == 'destroy';
  }

  public function getType()
  {
    return $this->getCtxArg('type');
  }

  public function getForcedMeeple()
  {
    $forcedMeeples = $this->getCtxArg('forcedMeeples');
    return $forcedMeeples ? Tiles::getMany($forcedMeeples) : null;
  }

  public function getCollectableMeeples($player)
  {
    $meeples = $player->getMeeples($this->getType())
      ->where('location', 'planet')
      ->toArray();
    return array_map(fn ($meeple) => $meeple->getX() . '_' . $meeple->getY(), $meeples);
  }

  public function argsCollectMeeple()
  {
    $player = $this->getPlayer();
    $collectableMeeples = $this->getCollectableMeeples($player);

    return [
      'meeples' => $collectableMeeples,
      'action' => $this->getToDestroy() ? clienttranslate('destroy') : clienttranslate('collect'),
      'type' => $this->getType(),
      'n' => min($this->getN(), count($collectableMeeples)),
      'i18n' => ['action', 'type']
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

    foreach ($spaceIds as $spaceId) {
      if (!in_array($spaceId, $args['meeples'])) {
        throw new \BgaVisibleSystemException('You cannot collect here ' . $spaceId . '. Should not happen.');
      }

      $cell = Planet::getCellFromId($spaceId);

      $meeples[] = $player->getMeepleOnCell($cell, $args['type']);
    }

    //move them
    if ($args['action'] == "destroy") {
      foreach ($meeples as $meeple) {
        $meeple->destroy();
      }
    } else {
      foreach ($meeples as $meeple) {
        $player->corporation()->collect($meeple);
      }
    }

    Notifications::collectMeeple($player, $meeples, $args['action']);
  }
}
