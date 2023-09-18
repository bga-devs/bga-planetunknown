<?php

namespace PU\Actions;

use PU\Managers\Meeples;
use PU\Managers\Players;
use PU\Managers\Tiles;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\Globals;
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
    // TODO : check rule for biomass patch
    // Handle here
    return true; //$this->getPlayableTiles($player, true);
  }

  public function getWithBonus()
  {
    return $this->getCtxArg('withBonus') ?? true;
  }

  public function getForcedTiles()
  {
    $forcedTiles = $this->getCtxArg('forcedTiles');
    return $forcedTiles ? Tiles::getMany($forcedTiles) : null;
  }

  public function getPossibleTiles($player)
  {
    $tiles = [];
    $depot = Susan::getDepotOfPlayer($player);
    $tile = Tiles::getTopOf('top-interior-' . $depot['interior'])->first();
    if ($tile) {
      $tiles[] = $tile;
    }
    $tile2 = Tiles::getTopOf('top-exterior-' . $depot['exterior'])->first();
    if ($tile2) {
      $tiles[] = $tile2;
    }
    return $tiles;
  }

  public function getPlayableTiles($player, $checkIsDoable = false, $forcedTiles = null)
  {
    $tiles = [];
    $forcedTiles = $forcedTiles ?? $this->getForcedTiles();

    $specialRule = Globals::getTurnSpecialRule();
    foreach ($forcedTiles ?? $this->getPossibleTiles($player) as $tile) {
      $placementOptions = $player->planet()->getPlacementOptions($tile->getType(), $checkIsDoable, $specialRule);
      if (!empty($placementOptions)) {
        if ($checkIsDoable) {
          return true;
        }
        $tiles[$tile->getId()] = $placementOptions;
      }
    }

    if (!empty($tiles)) {
      return $tiles;
    }

    // Same without special rule
    foreach ($forcedTiles ?? $this->getPossibleTiles($player) as $tile) {
      $placementOptions = $player->planet()->getPlacementOptions($tile->getType(), $checkIsDoable);
      if (!empty($placementOptions)) {
        if ($checkIsDoable) {
          return true;
        }
        $tiles[$tile->getId()] = $placementOptions;
      }
    }

    if (!empty($tiles)) {
      return $tiles;
    } elseif ($checkIsDoable) {
      return false;
    }

    // Otherwise, let them pick any tile without placing it
    foreach ($forcedTiles ?? $this->getPossibleTiles($player) as $tile) {
      $tiles[$tile->getId()] = NO_PLACEMENT;
    }
    return $tiles;
  }

  public function getDescription()
  {
    $description = $this->getCtxArg('descriptionTile') ?? clienttranslate('a new tile');

    return [
      'log' => \clienttranslate('Place ${description} on your planet'),
      'args' => [
        'description' => $description,
        'i18n' => ['description'],
      ],
    ];
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
    $player = $this->getPlayer();
    $args = $this->argsPlaceTile();
    $tiles = $args['tiles'];
    $tileOptions = $tiles[$tileId] ?? null;
    $no_placement = $tileOptions == NO_PLACEMENT;

    if (is_null($tileOptions)) {
      throw new \BgaVisibleSystemException('You cannot place that tile. Should not happen ' . $tileId);
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
    list($tile, $symbols, $coveringWater, $meteor) = $player->planet()->addTile($tileId, $pos, $rotation, $flipped, $no_placement);

    if (!$no_placement) {
      //record it
      $player->setLastTileId($tileId);

      // Add asteroid meeples
      if (!is_null($meteor)) {
        $meteor = Meeples::addMeteor($player, $meteor);
      }

      // Destroy pod/rover if any are covered
      [$destroyedRover, $destroyedLifepod] = Meeples::destroyCoveredMeeples($player, $tile);

      // Place extra Rover if it's the turn special rules
      if (Globals::getTurnSpecialRule() == ADD_ROVER) {
        $this->pushParallelChild([
          'action' => PLACE_ROVER,
        ]);
      }
    }

    // Move tracks
    $tileTypes = [];

    $actions = [];

    //ADD EXTRA ACTION EACH TURN
    if ($player->hasTech(TECH_REPOSITION_ONE_LIFEPOD_EACH_TURN)) {
      $actions = [
        'action' => POSITION_LIFEPOD_ON_TRACK,
        'args' => [
          'remaining' => 1
        ]
      ];
    }

    foreach ($symbols as $symbol) {
      $type = $symbol['type'];
      $tileTypes[] = $type;

      if (!$this->getWithBonus()) {
        continue;
      }

      // Energy => compute the possible tracks
      if ($type == ENERGY) {
        //big if for corpo1
        if ($player->hasTech(TECH_REPOSITION_LIFEPOD_AFTER_ENERGY)) {
          $actions[] = [
            'action' => POSITION_LIFEPOD_ON_TRACK,
            'args' => [
              'remaining' => 1
            ]
          ];
        }

        $types = $player->planet()->getTypesAdjacentToEnergy($symbol['cell']);
        $actions[] = [
          'action' => CHOOSE_TRACKS,
          'args' => [
            'types' => $types,
            'n' => 1,
            'energy' => true,
            'from' => ENERGY,
          ],
        ];
        continue;
      }
      // Water => stop if the tile is not covering water
      elseif ($type == WATER && !$coveringWater && !$no_placement) {
        continue;
      }

      // Normal case: add parallel child
      $actions[] = [
        'action' => MOVE_TRACK,
        'args' => ['type' => $type, 'n' => 1, 'withBonus' => true],
      ];
    }
    if (Globals::getTurnSpecialRule() == ONLY_ONE_MOVE_TRACKER) {
      $this->pushParallelChild([
        'type' => NODE_XOR,
        'actions' => $actions,
      ]);
    } else {
      //normal case
      $this->pushParallelChilds($actions);
    }

    Notifications::placeTile($player, $tile, $meteor, $tileTypes);

    if ($destroyedLifepod->count()) {
      Notifications::destroyedMeeples($player, $destroyedLifepod, LIFEPOD);
    }
    if ($destroyedRover->count()) {
      Notifications::destroyedMeeples($player, $destroyedRover, ROVER);
    }
  }
}
