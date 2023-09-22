<?php

namespace PU\Models;

use PU\Managers\Meeples;
use PU\Managers\Tiles;
use PU\Managers\Players;
use PU\Helpers\UserException;
use PU\Helpers\Utils;
use PU\Helpers\Collection;
use PU\Core\Stats;

/*
 * Planet: all utility functions concerning a Planet
 */

const DIRECTIONS = [['x' => -1, 'y' => 0], ['x' => 0, 'y' => -1], ['x' => 1, 'y' => 0], ['x' => 0, 'y' => 1]];
const DIRECTIONS_DIAG = [['x' => -1, 'y' => 0], ['x' => -1, 'y' => -1], ['x' => -1, 'y' => 1], ['x' => 0, 'y' => -1], ['x' => 1, 'y' => 0], ['x' => 1, 'y' => 1], ['x' => 1, 'y' => -1], ['x' => 0, 'y' => 1]];
class Planet
{
  // STATIC DATA
  protected $id = '';
  protected $name = '';
  protected $desc = '';
  protected $terrains = [];
  protected $columnMedals = [];
  protected $rowMedals = [];

  // CONSTRUCT
  protected $player = null;
  protected $pId = null;
  public function __construct($player = null)
  {
    if (!is_null($player)) {
      $this->player = $player;
      $this->pId = $player->getId();
      $this->fetchDatas();
    }
  }

  public function getId()
  {
    return $this->id;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getUiData()
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'desc' => $this->desc,
      'terrains' => $this->terrains,
      'columnMedals' => $this->columnMedals,
      'rowMedals' => $this->rowMedals,
    ];
  }

  public function refresh()
  {
    $this->fetchDatas();
  }

  /**
   * Fetch DB for tiles and fill the grid
   */
  protected $grid = [];
  protected $tiles = [];
  protected function fetchDatas()
  {
    if ($this->player == null) {
      return;
    }

    $this->grid = self::createGrid();
    foreach ($this->grid as $x => $col) {
      foreach ($col as $y => $cell) {
        $this->grid[$x][$y] = [
          'terrain' => self::getTerrain($x, $y),
          'tile' => null,
          'type' => null,
          'symbol' => null,
        ];
      }
    }

    $this->tiles = Tiles::getOfPlayer($this->pId);
    foreach ($this->tiles as $tile) {
      $datas = $tile->getData();
      foreach ($this->getTileCoveredCells($tile, false) as $i => $cell) {
        $this->grid[$cell['x']][$cell['y']]['tile'] = $tile;
        $this->grid[$cell['x']][$cell['y']]['type'] = $datas[$i]['type'];
        $this->grid[$cell['x']][$cell['y']]['symbol'] = $datas[$i]['symbol'] ? $datas[$i]['type'] : null;
        $this->grid[$cell['x']][$cell['y']]['meteorSymbol'] = $datas[$i]['meteor'];
      }
    }
  }

  /*
  █████████    █████████     ███████    ███████████   ██████████
 ███░░░░░███  ███░░░░░███  ███░░░░░███ ░░███░░░░░███ ░░███░░░░░█
░███    ░░░  ███     ░░░  ███     ░░███ ░███    ░███  ░███  █ ░ 
░░█████████ ░███         ░███      ░███ ░██████████   ░██████   
 ░░░░░░░░███░███         ░███      ░███ ░███░░░░░███  ░███░░█   
 ███    ░███░░███     ███░░███     ███  ░███    ░███  ░███ ░   █
░░█████████  ░░█████████  ░░░███████░   █████   █████ ██████████
 ░░░░░░░░░    ░░░░░░░░░     ░░░░░░░    ░░░░░   ░░░░░ ░░░░░░░░░░ 
                                                                
                                                                
                                                                
*/

  /**
   * Return score as an array ['row_0' => 2] id => score
   */
  public function score()
  {
    $score = [];
    $meteors = Meeples::getOfPlayer($this->player, METEOR)->where('location', 'planet');
    $burntRows = [];
    $burntColumns = [];

    //BIG IF except for Corporation0 tech5
    if (!$this->player->hasTech(TECH_NO_METEOR)) {
      foreach ($meteors as $id => $meteor) {
        $burntColumns[] = $meteor->getX();
        $burntRows[] = $meteor->getY();
      }
    }

    foreach ($this->rowMedals as $rowId => $value) {
      if (in_array($rowId, $burntRows)) {
        $score['row_' . $rowId] = 0;
      } else {
        $score['row_' . $rowId] = $value;
        foreach ($this->columnMedals as $columnId => $_) {
          if ($this->isCoveredCoord($columnId, $rowId)) {
            $score['row_' . $rowId] = 0;
          }
        }
      }
    }

    foreach ($this->columnMedals as $columnId => $value) {
      if (in_array($columnId, $burntColumns)) {
        $score['column_' . $columnId] = 0;
      } else {
        $score['column_' . $columnId] = $value;
        foreach ($this->rowMedals as $rowId => $_) {
          if ($this->isCoveredCoord($columnId, $rowId)) {
            $score['column_' . $columnId] = 0;
          }
        }
      }
    }

    return $score;
  }

  public function countSymbolsOnEdge($symbol)
  {
    $cells = $this->getBorderCells();
    return array_reduce($cells, fn ($result, $cell) => $result + ($this->getSymbol($cell['x'], $cell['y']) == $symbol ? 1 : 0), 0);
  }

  /**
   * detect all connected area from one type given in args
   */
  public function detectZones($type)
  {
    $zones = [];

    $cells = $this->getListOfCells();

    $usedCells = [];

    foreach ($cells as $cell) {
      if (in_array($cell, $usedCells)) {
        continue;
      }

      $validatedCells = [];
      $adjacentCells = [$cell];

      while ($candidateCell = array_shift($adjacentCells)) {
        $usedCells[] = $candidateCell;
        if ($this->getType($candidateCell['x'], $candidateCell['y']) == $type) {
          $validatedCells[] = $candidateCell;
          $neighbours = array_udiff($this->getNeighbours($candidateCell), $usedCells, 'static::compareCells');
          $adjacentCells = array_merge($adjacentCells, $neighbours);
        }
      }

      if (count($validatedCells)) {
        $zones[] = $validatedCells;
      }
    }
    return $zones;
  }

  public function countZoneNb($type)
  {
    $zones = $this->detectZones($type);

    return count($zones);
  }

  public function countLargestAdjacent($type)
  {
    $zones = $this->detectZones($type);
    return $zones ? max(array_map(fn ($zone) => count($zone), $zones)) : 0;
  }

  public function countSymbols($type)
  {
    $cells = array_filter($this->getListOfCells(), fn ($cell) => $this->getSymbol($cell['x'], $cell['y']) == $type);
    return count($cells);
  }

  public function getEmptyMeteorSymbolCells()
  {
    return array_filter(
      $this->getListOfCells(),
      fn ($cell) => $this->hasMeteorSymbol($cell['x'], $cell['y']) && $this->player->getMeteorOnCell($cell)
    );
  }

  ///////////////////////////////////////////////
  //  _____ _ _
  // |_   _(_) | ___  ___
  //   | | | | |/ _ \/ __|
  //   | | | | |  __/\__ \
  //   |_| |_|_|\___||___/
  ///////////////////////////////////////////////
  public function addTile($tileId, $pos, $rotation, $flipped, $no_placement)
  {
    $tile = Tiles::getSingle($tileId);

    if (!$no_placement) {
      $tile->setLocation('planet');
      $tile->setX($pos['x']);
      $tile->setY($pos['y']);
      $tile->setRotation($rotation);
      $tile->setFlipped($flipped ? 1 : 0);
    }

    $tile->setPId($this->pId);
    $this->tiles[$tile->getId()] = $tile;

    $datas = $tile->getData();
    $coveringWater = false;
    $meteor = null;

    $symbols = [];
    if (!$no_placement) {

      foreach ($this->getTileCoveredCells($tile, false) as $i => $cell) {
        $this->grid[$cell['x']][$cell['y']]['tile'] = $tile;
        $type = $datas[$i]['type'];
        $this->grid[$cell['x']][$cell['y']]['type'] = $type;
        $this->grid[$cell['x']][$cell['y']]['symbol'] = $datas[$i]['symbol'] ? $datas[$i]['type'] : null;
        $this->grid[$cell['x']][$cell['y']]['meteorSymbol'] = $datas[$i]['meteor'];

        if ($datas[$i]['symbol']) {
          $symbols[] = [
            'cell' => $cell,
            'type' => $type,
          ];
        }

        if ($datas[$i]['meteor'] && !$this->player->hasTech(TECH_NO_METEOR)) {
          $meteor = $cell;
        }

        if ($type == WATER && $this->getTerrain($cell['x'], $cell['y']) == ICE) {
          $coveringWater = true;
        }
      }
    } else {
      $symbols = $tile->getSymbolsForDiscardedTile();
    }

    self::invalidateCachedDatas();
    return [$tile, $symbols, $coveringWater, $meteor];
  }

  public function getTileAtPos($cell)
  {
    return $this->grid[$cell['x']][$cell['y']]['tile'] ?? null;
  }

  public function getTileAtCoord($x, $y)
  {
    return $this->grid[$x][$y]['tile'] ?? null;
  }

  public function hasTileAtPos($cell)
  {
    return !is_null($this->getTileAtPos($cell));
  }

  public function hasTileAtCoord($x, $y)
  {
    return !is_null($this->getTileAtCoord($x, $y));
  }

  public function getTilesOfType($tileType)
  {
    return $this->tiles->where('type', $tileType);
  }

  public function getTileOfType($tileType)
  {
    return $this->getTilesOfType($tileType)->first();
  }

  public function hasTile($tileType)
  {
    return $this->getTileOfType($tileType) !== null;
  }

  protected function getTilesNeighbourCells()
  {
    $cells = [];
    foreach (self::getListOfCells() as $cell) {
      if (!is_null($this->getTileAtPos($cell))) {
        $cells = array_merge($cells, $this->getNeighbours($cell));
      }
    }
    return Utils::uniqueZones($cells);
  }

  public function isTileAdjacentTo($tile, $cell)
  {
    $neighbours = [];
    foreach ($this->getTileCoveredCells($tile, false) as $cell) {
      $neighbours = array_merge($neighbours, $this->getNeighbours($cell));
    }
    return !empty(Utils::intersectZones([$cell], $neighbours));
  }

  protected $checkingCells = null;
  protected $freeCells = null;
  public function getPlacementOptionsCachedDatas()
  {
    if (is_null($this->checkingCells)) {
      $this->checkingCells = $this->tiles->empty() ? $this->getBorderCells() : $this->getConnectedCells();
    }
    if (is_null($this->freeCells)) {
      $cells = self::getListOfCells();
      Utils::filter($cells, function ($cell) {
        return !$this->hasTileAtPos($cell);
      });
      $this->freeCells = $cells;
    }

    return [$this->checkingCells, $this->freeCells];
  }
  public function invalidateCachedDatas()
  {
    $this->checkingCells = null;
    $this->freeCells = null;
  }

  public function getPlacementOptions($tileType, $checkIsDoable = false, $specialRule = null)
  {
    list($checkingCells, $freeCells) = $this->getPlacementOptionsCachedDatas();
    $border = $this->getBorderCells();
    $byPassCheck = false; // Coorpo techs

    $result = [];
    // For each possible cell to place the reference cell of the tile
    foreach ($freeCells as $pos) {
      $rotations = [];
      // Compute which rotations are valid
      for ($rotation = 0; $rotation < 4; $rotation++) {
        foreach ([false, true] as $flipped) {
          $cells = self::getCoveredCells($tileType, $pos, $rotation, $flipped);
          // Are all the cells valid to build upon ?
          if ($cells === false) {
            continue;
          }

          // TODO: add check function that can be overwritten by some planets

          if ($this->isIntersectionNonEmpty($cells, $checkingCells) || $this->player->hasTech(TECH_BYPASS_ADJACENT_CONSTRAINT)) {
            // Check if tile is intersecting border or not
            if ($specialRule == CANNOT_PLACE_ON_EDGE && $this->isIntersectionNonEmpty($cells, $border)) {
              continue;
            }
            $rotations[] = [$rotation, $flipped];
          }
        }
      }
      if (!empty($rotations)) {
        $result[] = [
          'pos' => $pos,
          'r' => $rotations,
        ];
        if ($checkIsDoable) {
          return $result;
        }
      }
    }
    return $result;
  }

  /**
   * getCoveredCells: given a tile type, a position and rotation, return the list of cells that would be covered by the tile placed that way
   */
  public function getCoveredCells($tileType, $pos, $rotation, $flipped, $checkAvailableToBuild = true)
  {
    $cells = [];

    foreach (self::getCellsOfTileType($tileType) as $delta) {
      $cellOffset = self::getRotatedFlippedCell(['x' => $delta[0], 'y' => $delta[1]], $rotation, $flipped);
      $cell = [
        'x' => $pos['x'] + $cellOffset['x'],
        'y' => $pos['y'] + $cellOffset['y'],
      ];

      if (!$this->isCellAvailableToBuild($cell) && $checkAvailableToBuild) {
        return false;
      } else {
        $cells[] = $cell;
      }
    }
    return $cells;
  }

  public function getCellsOfTileType($tileType)
  {
    if ($tileType == BIOMASS_PATCH) {
      return [[0, 0]];
    }
    $types = [
      [[0, 0], [-2, 0], [-1, 0], [0, -1], [1, -1]],
      [[0, 0], [-1, 0], [1, 0], [1, -1], [0, 1]],
      [[0, 0], [0, -1], [0, 1]],
      [[0, 0], [-1, 0], [0, 1]],
      [[0, 0], [-1, 0], [0, 1], [1, 0]],
      [[0, 0], [-1, 1], [0, 1], [1, 0]],
      [[0, 0], [-1, 1], [0, -1], [0, 1]],
      [[0, 0], [-1, -1], [-1, 0], [1, -1], [1, 0]],
      [[0, 0], [0, 1]],
      [[0, 0], [-1, 0], [1, 0], [2, 0]],
      [[0, 0], [0, 1], [1, 0], [1, 1]],
      [[0, 0], [-1, -1], [-1, 0], [1, 0], [1, 1]],
    ];

    return $types[$tileType % 12];
  }

  // Same thing for a given DB result representing a tile
  public function getTileCoveredCells($tile, $checkAvailableToBuild = true)
  {
    return $this->getCoveredCells(
      $tile->getType(),
      self::extractPos($tile),
      $tile->getRotation(),
      $tile->isFlipped(),
      $checkAvailableToBuild
    );
  }

  /**
   * isCellAvailableToBuild: given an cell, can we build here ?
   */
  public function isCellAvailableToBuild($cell)
  {
    $uid = self::getCellId($cell);
    // Can't build on an invalid cell or already built cell
    if (!$this->isCellValid($cell) || !is_null($this->getTileAtPos($cell))) {
      return false;
    }

    return true;
  }

  public function isCoveredCoord($x, $y)
  {
    return ($this->isPlanet($x, $y) && !$this->hasTileAtCoord($x, $y));
  }

  /**
   * getTypesAdjacentToEnergy: given a energy cell, compute the types adjacent to the zone
   */
  public function getTypesAdjacentToEnergy($cell)
  {
    $zone = [];
    $types = [];

    $queue = [$cell];
    while (!empty($queue)) {
      $pos = array_shift($queue);
      $uid = $this->getCellId($pos);
      if (in_array($uid, $zone)) {
        continue;
      }

      $zone[] = $uid;
      foreach ($this->getNeighbours($pos) as $neighbour) {
        $x = $neighbour['x'];
        $y = $neighbour['y'];
        if ($this->isEnergy($x, $y)) {
          $queue[] = $neighbour;
        } else {
          $type = $this->getType($x, $y);
          if (!is_null($type) && !in_array($type, $types)) {
            $types[] = $type;
          }
        }
      }
    }

    return $types;
  }

  //////////////////////////////////////
  //    ____      _   _
  //   / ___| ___| |_| |_ ___ _ __ ___
  //  | |  _ / _ \ __| __/ _ \ '__/ __|
  //  | |_| |  __/ |_| ||  __/ |  \__ \
  //   \____|\___|\__|\__\___|_|  |___/
  //////////////////////////////////////

  public function getStartingLifePodsCoord()
  {
    $result = [];
    for ($y = 0; $y < count($this->terrains); $y++) {
      for ($x = 0; $x < count($this->terrains[$y]); $x++) {
        if ($this->terrains[$y][$x] == LIFEPOD) {
          $result[] = [
            'x' => $x,
            'y' => $y,
          ];
        }
      }
    }
    return $result;
  }

  //terrain is on the board (mainly ICE or LAND)
  public function getTerrain($x, $y)
  {
    return $this->terrains[$y][$x];
  }

  //type depends on tile
  public function getType($x, $y)
  {
    return $this->grid[$x][$y]['type'];
  }

  //return what is visible at coord (tile or terrain if there is no tile)
  //return type if not null else terrain
  public function getVisible($x, $y)
  {
    return $this->getType($x, $y) ?? $this->getTerrain($x, $y);
  }

  public function getTypeAtPos($cell)
  {
    return $this->getType($cell['x'], $cell['y']);
  }

  public function getSymbol($x, $y)
  {
    return $this->grid[$x][$y]['symbol'];
  }

  public function getSymbolAtPos($cell)
  {
    return $this->getSymbol($cell['x'], $cell['y']);
  }

  public function hasMeteorSymbol($x, $y)
  {
    return $this->grid[$x][$y]['meteorSymbol'];
  }

  // Can be overwritten by some planets
  public function isIce($x, $y)
  {
    return $this->getTerrain($x, $y) == ICE;
  }

  public function isPlanet($x, $y)
  {
    return $this->getTerrain($x, $y) != NOTHING;
  }

  // Can be overwritten by some planets
  public function isEnergy($x, $y)
  {
    return $this->getType($x, $y) == ENERGY;
  }

  // Count the number of empty spaces
  public function countEmptySpaces()
  {
    $cells = [];
    foreach ($this->getListOfCells() as $cell) {
      if (!$this->hasTileAtPos($cell)) {
        $cells[] = $cell;
      }
    }

    return count($cells);
  }

  // Count the number of Meteor on planet
  public function countMeteors()
  {
    return Meeples::getOfPlayer($this->player, METEOR)
      ->where('location', 'planet')
      ->count();
  }

  public function getMeepleOnCell($cell, $type = null)
  {
    return Meeples::getOfPlayer($this->player, $type)
      ->where('x', $cell['x'])
      ->where('y', $cell['y']);
  }

  /**
   * getConnectedCells: return list of cells adjacent to at least one tile
   */
  public function getConnectedCells($withoutTiles = true)
  {
    $cells = $this->getTilesNeighbourCells();
    if ($withoutTiles) {
      Utils::filter($cells, function ($cell) {
        return !$this->hasTileAtPos($cell);
      });
    }
    return $cells;
  }

  /////////////////////////////////////////////
  //   ____      _     _   _   _ _   _ _
  //  / ___|_ __(_) __| | | | | | |_(_) |___
  // | |  _| '__| |/ _` | | | | | __| | / __|
  // | |_| | |  | | (_| | | |_| | |_| | \__ \
  //  \____|_|  |_|\__,_|  \___/ \__|_|_|___/
  ////////////////////////////////////////////

  public static function getCellId($cell)
  {
    return $cell['x'] . '_' . $cell['y'];
  }

  public static function getCellFromId($uid)
  {
    $coord = explode('_', $uid);
    return ['x' => $coord[0], 'y' => $coord[1]];
  }

  public static function compareCells($a, $b)
  {
    return $a['x'] * 20 + $a['y'] - ($b['x'] * 20 + $b['y']);
  }

  public function extractPos($tile)
  {
    return [
      'x' => $tile->getX(),
      'y' => $tile->getY(),
    ];
  }

  public function createGrid($defaultValue = null)
  {
    $g = [];
    for ($y = 0; $y < count($this->terrains); $y++) {
      for ($x = 0; $x < count($this->terrains[$y]); $x++) {
        if ($this->getTerrain($x, $y) != NOTHING) {
          $g[$x][$y] = $defaultValue;
        }
      }
    }
    return $g;
  }

  public function getListOfCells()
  {
    $grid = self::createGrid(0);
    $cells = [];
    foreach ($grid as $x => $col) {
      foreach ($col as $y => $t) {
        $cells[] = ['x' => $x, 'y' => $y];
      }
    }
    return $cells;
  }

  public function getBorderCells()
  {
    if (!isset($this->_borderCells)) {
      $grid = self::createGrid(0);
      $cells = [];
      foreach (self::getListOfCells() as $cell) {
        if (count(self::getNeighbours($cell)) < 4) {
          $cells[] = $cell;
        }
      }
      $this->_borderCells = $cells;
    }

    return $this->_borderCells;
  }

  /*
  TODO : same as above except for Oblivion !
  public function getEdgeCells()
  {
    if (!isset($this->_borderCells)) {
      $grid = self::createGrid(0);
      $cells = [];
      foreach ($grid as $x => $col) {
        foreach ($col as $y => $t) {
          if ($y <= 1 || $x <= 0 || $y >= 11 || $x >= 8) {
            $cells[] = ['x' => $x, 'y' => $y];
          }
        }
      }
      $this->_borderCells = $cells;
    }

    return $this->_borderCells;
  }
  */

  protected function isCellValid($cell)
  {
    return isset($this->grid[$cell['x']][$cell['y']]);
  }

  protected function areSameCell($cell1, $cell2)
  {
    return $cell1['x'] == $cell2['x'] && $cell1['y'] == $cell2['y'];
  }

  public function getNeighbours($cell, $bool_diag = false)
  {
    $cells = [];

    $directions = $bool_diag ? DIRECTIONS_DIAG : DIRECTIONS;

    foreach ($directions as $dir) {
      $newCell = [
        'x' => $cell['x'] + $dir['x'],
        'y' => $cell['y'] + $dir['y'],
      ];
      if ($this->isCellValid($newCell)) {
        $cells[] = $newCell;
      }
    }
    return $cells;
  }

  //to be overriden
  public function getPossibleMovesFrom($cell)
  {
    $cells = $this->getNeighbours($cell, $this->player->hasTech(TECH_ROVER_MOVE_DIAG));
    //can't move on a rover
    return array_filter($cells, function ($c) {
      return !$this->player->getRoverOnCell($c);
    });
  }

  protected function isIntersectionNonEmpty($cells1, $cells2)
  {
    foreach ($cells1 as $cell1) {
      foreach ($cells2 as $cell2) {
        if (self::areSameCell($cell1, $cell2)) {
          return true;
        }
      }
    }
    return false;
  }

  protected function getRotatedFlippedCell($cell, $rotation, $flipped)
  {
    if (($rotation == 0 && !$flipped) || ($cell['x'] == 0 && $cell['y'] == 0)) {
      return $cell;
    }

    // Apply flip
    $x = $flipped ? -$cell['x'] : $cell['x'];
    $y = $cell['y'];

    // Apply rotation
    $c = (int) cos(($rotation * pi()) / 2);
    $s = (int) sin(($rotation * pi()) / 2);
    return [
      'x' => $c * $x - $s * $y,
      'y' => $s * $x + $c * $y,
    ];
  }
}
