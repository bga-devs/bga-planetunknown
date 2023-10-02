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

class DestroyAllInRow extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_DESTROY_ALL_IN_ROW;
  }

  public function isDoable($player)
  {
    return $player->hasMeteorOnPlanet();
  }

  public function getCollectableMeteors($player)
  {
    $rows = [];
    $meteors = $player->getMeteorsOnPlanet();
    foreach ($meteors as $id => $meteor) {
      $row = $meteor->getY();
      $column = $meteor->getX();

      $rows['ROW_' . $row][] = $meteor->getId();
      $rows['COLUMN_' . $column][] = $meteor->getId();
    }
    return $rows;
  }

  public function argsDestroyAllInRow()
  {
    $player = $this->getPlayer();
    $collectableMeeples = $this->getCollectableMeteors($player);

    return [
      'rows' => $collectableMeeples,
    ];
  }

  public function actDestroyAllInRow($rowId)
  {
    $player = $this->getPlayer();
    $args = $this->argsDestroyAllInRow();

    $meepleIds = $args['rows'][$rowId] ?? null;
    if (is_null($meepleIds)) {
      throw new \BgaVisibleSystemException('You cannot collect this row/column ' . $rowId . '. Should not happen.');
    }

    // take meeples
    $meeples = Meeples::getMany($meepleIds);
    Notifications::collectMeeple($player, $meeples->toArray(), 'destroy');
    foreach ($meeples as $meeple) {
      $meeple->destroy();
    }
  }
}
