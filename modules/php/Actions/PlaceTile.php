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

class PlaceTile extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_PLACE_TILE;
  }

  public function isDoable($player)
  {
    return $this->getPlayableTiles($player, true);
  }

  public function getPossibleTiles($player)
  {
    $tiles = [];
    $depot = Susan::getDepotOfPlayer($player);
    $tiles[] = Tiles::getTopOf('interior-' . $depot['interior'])->first();
    $tiles[] = Tiles::getTopOf('exterior-' . $depot['exterior'])->first();
    return $tiles;
  }

  public function getPlayableTiles($player, $checkIsDoable = false, $forcedTiles = null)
  {
    $tiles = [];
    foreach ($forcedTiles ?? $this->getPossibleTiles($player) as $tile) {
      $placementOptions = $player->planet()->getPlacementOptions($tile->getType(), $checkIsDoable);
      if (!empty($placementOptions)) {
        if ($checkIsDoable) {
          return true;
        }
        $tiles[$tile->getId()] = $placementOptions;
      }
    }

    return $checkIsDoable ? false : $tiles;
  }

  public function argsPlaceTile()
  {
    $player = $this->getPlayer();

    return [
      'tiles' => $this->getPlayableTiles($player, false),
    ];
  }

  public function actPlaceTile($tileId, $pos, $rotation, $flipped)
  {
    var_dump($tileId, $pos, $rotation, $flipped);
    die('test');

    Notifications::message('${player_name} places a tile', [
      'player' => Players::getCurrent(),
    ]);

    $this->resolveAction([$n]);
  }
}
