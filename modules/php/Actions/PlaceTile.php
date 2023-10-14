<?php

namespace PU\Actions;

use PU\Managers\Meeples;
use PU\Managers\Players;
use PU\Managers\Tiles;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\Globals;
use PU\Core\PGlobals;
use PU\Core\Stats;
use PU\Helpers\Utils;
use PU\Helpers\FlowConvertor;
use PU\Managers\Actions;
use PU\Managers\Susan;

class PlaceTile extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_PLACE_TILE;
  }

  public function isDoable($player)
  {
    if ($this->getCtxArg('type') == 'normal') {
      return true;
    }

    list($tiles, $canPlace) = $this->getPlayableTiles($player, true);
    return $canPlace;
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
      $placementOptions = $player->planet()->getPlacementOptions($tile, $checkIsDoable, $specialRule);
      if (!empty($placementOptions)) {
        if ($checkIsDoable) {
          return [$tiles, true];
        }
        $tiles[$tile->getId()] = $placementOptions;
      }
    }

    if (!empty($tiles)) {
      return [$tiles, true];
    }

    // Same without special rule
    foreach ($forcedTiles ?? $this->getPossibleTiles($player) as $tile) {
      $placementOptions = $player->planet()->getPlacementOptions($tile, $checkIsDoable);
      if (!empty($placementOptions)) {
        if ($checkIsDoable) {
          return true;
        }
        $tiles[$tile->getId()] = $placementOptions;
      }
    }

    if (!empty($tiles)) {
      return [$tiles, true];
    }

    // Otherwise, let them pick any tile without placing it
    foreach ($forcedTiles ?? $this->getPossibleTiles($player) as $tile) {
      $tiles[$tile->getId()] = NO_PLACEMENT;
    }
    return [$tiles, false];
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
    list($tiles, $canPlace) = $this->getPlayableTiles($player);

    return [
      'tiles' => $tiles,
      'descSuffix' => $canPlace ? '' : 'impossible',
    ];
  }

  public function actPlaceTile($tileId, $pos, $rotation, $flipped)
  {
    $player = $this->getPlayer();
    $args = $this->argsPlaceTile();
    $tiles = $args['tiles'];
    $tileOptions = $tiles[$tileId] ?? null;

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
    list($tile, $symbols, $coveringWater, $meteor) = $player->planet()->addTile($tileId, $pos, $rotation, $flipped);

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

    //if the played tile cover no ice, a corpo advance its water tracker
    if ($player->hasTech(TECH_MOVE_WATER_IF_NO_ICE)) {
      $coveredCells = $player->planet()->getTileCoveredCells($tile, false);
      $iceCells = $player->planet()->getIceCells();
      if (!$player->planet()->isIntersectionNonEmpty($coveredCells, $iceCells)) {
        $this->pushParallelChild([
          'action' => MOVE_TRACK,
          'args' => ['type' => WATER, 'n' => 1, 'withBonus' => true],
        ]);
      }
    }

    // Move tracks
    $tileTypes = [];

    $actions = [];

    foreach ($symbols as $symbol) {
      $type = $symbol['type'];
      $tileTypes[] = $type;

      if (!$this->getWithBonus()) {
        continue;
      }

      if ($player->hasTech(TECH_COLLECT_METEOR_FLUX) && $type == PGlobals::getFluxTrack($player->getId)) {
        $actions[] = [
          'action' => COLLECT_MEEPLE,
          'args' => [
            'type' => METEOR,
          ],
        ];
      }

      // Energy => compute the possible tracks
      if ($type == ENERGY) {
        //big if for corpo1
        if ($player->hasTech(TECH_REPOSITION_LIFEPOD_AFTER_ENERGY)) {
          $actions[] = [
            'action' => POSITION_LIFEPOD_ON_TRACK,
            'args' => [
              'remaining' => 1,
            ],
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
      // Water => stop if the tile is not covering water or give some action for special corpo
      elseif ($type == WATER) {
        if ($player->hasTech(TECH_GET_BIOMASS_WITH_WATER)) {
          $patchToPlace = $player->corporation()->receiveBiomassPatch();
          if ($patchToPlace) {
            $this->pushParallelChild(Actions::getBiomassPatchFlow($patchToPlace->getId()));
          }
        }
        if ($player->hasTech(TECH_GET_SYNERGY_WITH_WATER)) {
          $action = $player->getSynergy();
          if ($action) {
            $this->pushParallelChild($action);
          }
        }

        if (!$coveringWater) {
          continue;
        } elseif ($player->corporation()->getId() == OASIS) {
          $actions[] = [
            'action' => MOVE_TRACK,
            'args' => ['type' => $type, 'n' => $coveringWater, 'withBonus' => true],
          ];
          continue;
        }
      }

      // Normal case: add parallel child
      $actions[] = [
        'action' => MOVE_TRACK,
        'args' => ['type' => $type, 'n' => 'placingTile', 'withBonus' => true],
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

  public function actPlaceTileNoPlacement($tileId)
  {
    $player = $this->getPlayer();
    $args = $this->argsPlaceTile();
    $tiles = $args['tiles'];
    $tileOptions = $tiles[$tileId] ?? null;
    $noPlacement = $tileOptions == NO_PLACEMENT;
    if (is_null($tileOptions) || !$noPlacement) {
      throw new \BgaVisibleSystemException('You cannot place that tile. Should not happen ' . $tileId);
    }

    // Place it on the board
    list($tile, $symbols) = $player->planet()->addTileNoPlacement($tileId);

    // Move tracks
    $tileTypes = [];
    $actions = [];
    foreach ($symbols as $symbol) {
      if (!$this->getWithBonus()) {
        continue;
      }

      $type = $symbol['type'];
      $tileTypes[] = $type;
      $n = 1;
      if ($type == WATER && $player->hasTech(TECH_WATER_ADVANCE_TWICE)) {
        $n *= 2;
      }

      $actions[] = [
        'action' => MOVE_TRACK,
        'args' => ['type' => $type, 'n' => $n, 'withBonus' => true],
      ];
    }

    if (Globals::getTurnSpecialRule() == ONLY_ONE_MOVE_TRACKER) {
      $this->pushParallelChild([
        'type' => NODE_XOR,
        'actions' => $actions,
      ]);
    } else {
      $this->pushParallelChilds($actions);
    }

    Notifications::placeTileNoPlacement($player, $tile, $tileTypes);
  }
}
