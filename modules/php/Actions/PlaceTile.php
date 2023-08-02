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
    self::checkAction('actPlaceTile');
    $player = $this->getPlayer();
    $args = $this->argsPlaceTile();
    $tiles = $args['tiles'];
    $tileOptions = $tiles[$tileId] ?? null;
    if (is_null($tileOptions)) {
      throw new \BgaVisibleSystemException('You cannot place that tile. Should not happen');
    }
    $option = Utils::search($tileOptions, function ($option) use ($pos) {
      return $option['pos']['x'] == $pos['x'] && $option['pos']['y'] == $pos['y'];
    });
    if ($option === false) {
      throw new \BgaVisibleSystemException('You cannot place the tile here. Should not happen');
    }
    if (!in_array([$rotation, $flipped], $tileOptions[$option]['r'])) {
      throw new \BgaVisibleSystemException('You cannot place the tile here with that rotation/flip. Should not happen');
    }

    // Place it on the board
    list($tile, $symbols, $coveringWater, $meteor) = $player->planet()->addTile($tileId, $pos, $rotation, $flipped);

    //record it
    $player->setLastTileId($tileId);

    // Add asteroid meeples
    if (!is_null($meteor)) {
      $meteor = Meeples::addMeteor($player, $meteor);
    }

    // Destroy pod/rover if any are covered
    [$destroyedRover, $destroyedLifepod] = Meeples::destroyCoveredMeeples($player, $tile);

    // Move tracks
    $tileTypes = [];
    foreach ($symbols as $symbol) {
      $type = $symbol['type'];
      $tileTypes[] = $type;

      // Energy => compute the possible tracks
      if ($type == ENERGY) {
        $types = $player->planet()->getTypesAdjacentToEnergy($symbol['cell']);
        $this->pushParallelChild([
          'action' => CHOOSE_TRACKS,
          'args' => [
            'types' => $types,
            'n' => 1,
            'energy' => true,
          ],
        ]);
        continue;
      }
      // Water => stop if the tile is not covering water
      elseif ($type == WATER && !$coveringWater) {
        continue;
      }

      // Normal case: add parallel child
      $this->pushParallelChild([
        'action' => MOVE_TRACK,
        'args' => ['type' => $type, 'n' => 1, 'withBonus' => true],
      ]);
    }

    Notifications::placeTile($player, $tile, $meteor, $tileTypes);

    if ($destroyedLifepod->count()) {
      Notifications::destroyedMeeples($player, $destroyedLifepod, LIFEPOD);
    }
    if ($destroyedRover->count()) {
      Notifications::destroyedMeeples($player, $destroyedRover, ROVER);
    }

    $this->resolveAction([$tileId, $pos, $rotation, $flipped]);
  }
}
