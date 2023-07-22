<?php

namespace PU\Models;

use PU\Managers\Meeples;
use PU\Managers\Buildings;
use PU\Managers\Players;
use PU\Helpers\UserException;
use PU\Helpers\Utils;
use PU\Helpers\Collection;
use PU\Core\Stats;

/*
 * Planet: all utility functions concerning a Planet
 */

const DIRECTIONS = [
  ['x' => -1, 'y' => -1],
  ['x' => 0, 'y' => -2],
  ['x' => 1, 'y' => -1],
  ['x' => 1, 'y' => 1],
  ['x' => 0, 'y' => 2],
  ['x' => -1, 'y' => 1],
];

class Planet
{
  // STATIC DATA
  protected $id = '';
  protected $name = '';
  protected $desc = '';
  protected $terrains = [];

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

  public function canUseEffect()
  {
    return true;
  }

  public function getIncome()
  {
    return [];
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
  protected $buildings = [];
  protected function fetchDatas()
  {
    if ($this->player == null) {
      return;
    }

    $this->grid = self::createGrid();
    foreach ($this->grid as $x => $col) {
      foreach ($col as $y => $cell) {
        $this->grid[$x][$y] = [
          'building' => null,
        ];
      }
    }

    $this->buildings = Buildings::getOfPlayer($this->pId);
    foreach ($this->buildings as $building) {
      foreach ($this->getBuildingCoveredHexes($building, false) as $hex) {
        $this->grid[$hex['x']][$hex['y']]['building'] = $building;
      }
    }
  }

  ///////////////////////////////////////////////
  //  ____        _ _     _ _
  // | __ ) _   _(_) | __| (_)_ __   __ _ ___
  // |  _ \| | | | | |/ _` | | '_ \ / _` / __|
  // | |_) | |_| | | | (_| | | | | | (_| \__ \
  // |____/ \__,_|_|_|\__,_|_|_| |_|\__, |___/
  //                                |___/
  ///////////////////////////////////////////////
  public function addBuilding($buildingType, $pos, $rotation)
  {
    if (in_array($buildingType, \ENCLOSURES)) {
      Stats::incBuiltEnclosures($this->pId);
    } elseif ($buildingType == KIOSK) {
      Stats::incBuiltKiosks($this->pId);
    } elseif ($buildingType == PAVILION) {
      Stats::incBuiltPavilions($this->pId);
    } else {
      Stats::incBuiltUniqueStructures($this->pId);
    }
    Stats::incCoveredHexes($this->pId, count(BUILDINGS[$buildingType]));

    $building = Buildings::add($this->pId, $buildingType, $pos, $rotation);
    $this->buildings[$building['id']] = $building;
    $bonuses = [];
    $bonusHydrologist = 0;
    $bonusGeologist = 0;
    $isAlreadyFull = $this->countEmptySpaces() == 0 ? true : false;
    $this->invalidateCachedDatas();

    // useful for archeologist
    $border = $this->getBorderCells();
    $nBonusesOnBorder = 0;

    foreach ($this->getBuildingCoveredHexes($building, false) as $hex) {
      $uid = self::getCellId($hex);
      // Hydrologist
      if ($this->player->hasPlayedCard('S241_Hydrologist')) {
        $neighbours = $this->getNeighbours($hex);
        list($water, $rock) = $this->countWaterAndRock($neighbours);
        $bonusHydrologist += $water > 0 ? 1 : 0;
      }
      // Geologist
      if ($this->player->hasPlayedCard('S242_Geologist')) {
        $neighbours = $this->getNeighbours($hex);
        list($water, $rock) = $this->countWaterAndRock($neighbours);
        $bonusGeologist += $rock > 0 ? 1 : 0;
      }
    }

    foreach ($this->getBuildingCoveredHexes($building, false) as $hex) {
      $this->grid[$hex['x']][$hex['y']]['building'] = $building;
      $uid = self::getCellId($hex);
      foreach ($this->bonuses[$uid] ?? [] as $bonus => $n) {
        $bonuses[] = [$bonus => $n];
        if (in_array($hex, $border)) {
          $nBonusesOnBorder++;
        }
      }
    }

    if ($bonusHydrologist > 0) {
      $bonuses[] = [
        MONEY => $bonusHydrologist,
        'sourceId' => 'S241_Hydrologist',
      ];
    }
    if ($bonusGeologist > 0) {
      $bonuses[] = [
        MONEY => $bonusGeologist,
        'sourceId' => 'S242_Geologist',
      ];
    }

    if (!$isAlreadyFull && $this->countEmptySpaces() == 0) {
      $bonuses[] = [
        APPEAL => 7,
        'source' => clienttranslate('filling the map'),
      ];
    }

    // ARCHEOLOGIST
    if ($nBonusesOnBorder > 0 && $this->player->hasPlayedCard('S221_Archeologist')) {
      $bonusesLeft = [];
      foreach ($this->bonuses as $uid => $b) {
        if ($this->hasBuildingAtPos($this->getHexFromId($uid))) {
          continue;
        }

        foreach ($b as $type => $n) {
          $bonusesLeft[] = [
            'action' => TAKE_BONUS,
            'args' => [
              'type' => $type,
              'n' => $n,
              'sourceId' => 'S221_Archeologist',
            ],
          ];
        }
      }

      if (!empty($bonusesLeft)) {
        for ($i = 0; $i < $nBonusesOnBorder; $i++) {
          $bonuses[] = [
            'type' => \NODE_XOR,
            'optional' => true,
            'childs' => $bonusesLeft,
            'customDescription' => \clienttranslate('Gain an (uncovered) placement bonus'),
            'sourceId' => 'S221_Archeologist',
          ];
        }
      }
    }

    if (!$isAlreadyFull) {
      Stats::setEmptyHexes($this->pId, $this->countEmptySpaces());
    }

    return [$building, $bonuses];
  }

  public function getBuildingAtPos($hex)
  {
    return $this->grid[$hex['x']][$hex['y']]['building'] ?? null;
  }

  public function hasBuildingAtPos($hex)
  {
    return !is_null($this->getBuildingAtPos($hex));
  }

  public function getBuildingsOfType($buildingType)
  {
    return $this->buildings->filter(function ($b) use ($buildingType) {
      return $b['type'] == $buildingType;
    });
  }

  public function getBuildingOfType($buildingType)
  {
    return $this->getBuildingsOfType($buildingType)->first();
  }

  public function hasBuilding($buildingType)
  {
    return $this->getBuildingOfType($buildingType) !== null;
  }

  protected function getBuildingsNeighbourCells()
  {
    $cells = [];
    foreach (self::getListOfCells() as $cell) {
      if (!is_null($this->getBuildingAtPos($cell))) {
        $cells = array_merge($cells, $this->getNeighbours($cell));
      }
    }
    return Utils::uniqueZones($cells);
  }

  public function isBuildingAdjacentTo($building, $cell)
  {
    $neighbours = [];
    foreach ($this->getBuildingCoveredHexes($building, false) as $hex) {
      $neighbours = array_merge($neighbours, $this->getNeighbours($hex));
    }
    return !empty(Utils::intersectZones([$cell], $neighbours));
  }

  protected $checkingCells = null;
  protected $freeCells = null;
  public function getPlacementOptionsCachedDatas()
  {
    if (is_null($this->checkingCells)) {
      $this->checkingCells = $this->buildings->empty() ? $this->getBorderCells() : $this->getConnectedCells();
    }
    if (is_null($this->freeCells)) {
      $cells = self::getListOfCells();
      Utils::filter($cells, function ($cell) {
        return !$this->hasBuildingAtPos($cell);
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

  public function getPlacementOptions($buildingType, $checkIsDoable = false)
  {
    list($checkingCells, $freeCells) = $this->getPlacementOptionsCachedDatas();
    $byPassCheck = $this->player->hasPlayedCard('S219_DiversityResearcher');
    $size1 = count(BUILDINGS[$buildingType]) == 1;

    $result = [];
    // For each possible cell to place the reference hex of the building
    foreach ($freeCells as $pos) {
      if ($buildingType == 'kiosk' && !$this->isFarEnoughFromOtherKiosk($pos)) {
        continue;
      }

      $rotations = [];
      // Compute which rotations are valid
      for ($rotation = 0; $rotation < ($size1 ? 1 : 6); $rotation++) {
        $hexes = self::getCoveredHexes($buildingType, $pos, $rotation);
        // Are all the hexes valid to build upon ?
        if ($hexes === false) {
          continue;
        }

        // Constraints for water/rock adjacency
        if (!$byPassCheck && !empty(BUILDINGS_CONSTRAINTS[$buildingType] ?? [])) {
          $enclosure = [
            'type' => $buildingType,
            'rotation' => $rotation,
            'x' => $pos['x'],
            'y' => $pos['y'],
          ];
          $this->addSurroundingsToEnclosure($enclosure);
          $satisfied = true;
          foreach (BUILDINGS_CONSTRAINTS[$buildingType] as $constraint => $n) {
            if ($n > $enclosure[$constraint]) {
              $satisfied = false;
              break;
            }
          }

          if (!$satisfied) {
            continue;
          }
        }

        // Adjacency check: either adjacent to existing buildings, or on the border otherwise
        if ($buildingType == SIDE_ENTRANCE) {
          $rotations[] = $rotation;
        } elseif ($this->isIntersectionNonEmpty($hexes, $checkingCells)) {
          $rotations[] = $rotation;
        }
      }
      if (!empty($rotations)) {
        $result[] = [
          'pos' => $pos,
          'rotations' => $rotations,
        ];
        if ($checkIsDoable) {
          return $result;
        }
      }
    }
    return $result;
  }

  /**
   * getCoveredHexes: given a building type, a position and rotation, return the list of hexes that would be covered by the building placed that way
   */
  public function getCoveredHexes($buildingType, $pos, $rotation, $checkAvailableToBuild = true)
  {
    $hexes = [];
    if ($this->player->hasPlayedCard('S219_DiversityResearcher')) {
      $ignore = [WATER => true, ROCK => true];
    }

    foreach (BUILDINGS[$buildingType] as $delta) {
      $hexOffset = self::getRotatedHex(['x' => $delta[0], 'y' => $delta[1]], $rotation);
      $hex = [
        'x' => $pos['x'] + $hexOffset['x'],
        'y' => $pos['y'] + $hexOffset['y'],
      ];

      if (!$this->isCellAvailableToBuild($hex, $ignore ?? []) && $checkAvailableToBuild) {
        return false;
      } else {
        $hexes[] = $hex;
      }
    }

    // Check constraints, if any
    $constraints = [];
    if (in_array($buildingType, ['zoo-school', \SIDE_ENTRANCE])) {
      $constraints = ['border' => 2];
    }

    foreach ($constraints as $type => $value) {
      if ($type == 'border') {
        $borders = $this->getBorderCells();
        $check = 0;
        foreach ($hexes as $hex) {
          if (in_array($hex, $borders)) {
            $check++;
          }
        }
        if ($check < $value) {
          return false;
        }
      }
    }

    return $hexes;
  }

  // Same thing for a given DB result representing a building
  public function getBuildingCoveredHexes($building, $checkAvailableToBuild = true)
  {
    return $this->getCoveredHexes($building['type'], self::extractPos($building), $building['rotation'], $checkAvailableToBuild);
  }

  /**
   * isCellAvailableToBuild: given an hex, can we build here ?
   */
  public function isCellAvailableToBuild($hex, $ignore = [])
  {
    $uid = self::getCellId($hex);
    // Can't build on an invalid cell or already built cell
    if (!$this->isCellValid($hex) || !is_null($this->getBuildingAtPos($hex))) {
      return false;
    }
    // Can't build on water
    if (!($ignore[WATER] ?? false) && in_array($uid, $this->terrains[WATER])) {
      return false;
    }
    // Can't build on rock
    if (!($ignore[ROCK] ?? false) && in_array($uid, $this->terrains[ROCK])) {
      return false;
    }
    // Can't build on upgraded spaces
    if (!($ignore[UPGRADED_BUILD_CARD] ?? $this->player->isCardUpgraded(BUILD)) && in_array($uid, $this->upgradeNeeded)) {
      return false;
    }

    return true;
  }

  /////////////////////////////
  //  _  ___           _
  // | |/ (_) ___  ___| | __
  // | ' /| |/ _ \/ __| |/ /
  // | . \| | (_) \__ \   <
  // |_|\_\_|\___/|___/_|\_\
  /////////////////////////////

  /**
   * isFarEnoughFromOtherKiosk : check whether we can build a kiosk on a given cell
   */
  protected function isFarEnoughFromOtherKiosk($hex)
  {
    foreach ($this->buildings as $building) {
      if ($building['type'] == 'kiosk' && self::getDistance($hex, self::extractPos($building)) < 3) {
        return false;
      }
    }
    return true;
  }

  /**
   * getKioskIncome : compute the income yield by the kiosks on the map
   */
  public function getKioskIncome()
  {
    $money = 0;
    foreach ($this->getBuildingsOfType(KIOSK) as $building) {
      // 1 money per neighbours
      $nbNeighbours = $this->countBuildingNeighbours($building);
      $money += $nbNeighbours;
    }

    return $money;
  }

  /**
   * countBuildingNeighbours : count the number of neighbours around a building
   *  (auxiliary function to compute kiosk income + side entrance)
   */
  public function countBuildingNeighbours($building)
  {
    $neighbours = [];
    foreach ($this->getCoveredHexes($building['type'], $building, $building['rotation'], false) as $hex) {
      foreach ($this->getNeighbours($hex) as $cell) {
        $building2 = $this->getBuildingAtPos($cell);
        // Only count each building once as a neighbourd of current building
        if (is_null($building2) || in_array($building2['id'], $neighbours) || $building2['id'] == $building['id']) {
          continue;
        }
        // Empty regular enclosure dont count
        if (in_array($building2['type'], \REGULAR_ENCLOSURES) && $building2['state'] == 0) {
          continue;
        }

        $neighbours[] = $building2['id'];
      }
    }

    return count($neighbours);
  }

  //////////////////////////////////////////////////////
  //  _____            _
  // | ____|_ __   ___| | ___  ___ _   _ _ __ ___  ___
  // |  _| | '_ \ / __| |/ _ \/ __| | | | '__/ _ \/ __|
  // | |___| | | | (__| | (_) \__ \ |_| | | |  __/\__ \
  // |_____|_| |_|\___|_|\___/|___/\__,_|_|  \___||___/
  //////////////////////////////////////////////////////

  // Given an enclosure (building), return the list of hexes around that enclosure
  protected function getEnclosureNeighbourHexes($enclosure)
  {
    $cells = [];
    foreach ($this->getBuildingCoveredHexes($enclosure, false) as $cell) {
      $cells = array_merge($cells, $this->getNeighbours($cell));
    }
    return Utils::uniqueZones($cells);
  }

  // Add the number of water/rock to an enclosure
  public function addSurroundingsToEnclosure(&$enclosure)
  {
    $neighbours = $this->getEnclosureNeighbourHexes($enclosure);
    list($water, $rock) = $this->countWaterAndRock($neighbours);
    $enclosure[WATER] = $water;
    $enclosure[ROCK] = $rock;
  }

  // Return the list of enclosures with number of water/rock surrounding them
  public function getEnclosuresWithSurroundings()
  {
    $enclosures = $this->buildings->filter(function ($building) {
      return in_array($building['type'], ENCLOSURES);
    });

    foreach ($enclosures as &$enclosure) {
      $this->addSurroundingsToEnclosure($enclosure);
    }

    return $enclosures;
  }

  /**
   * isAnimalFittingEnclosure:
   *  - $animal : object
   *  - enclosure : array
   *  - isAnimalAdded : allow to distinguish whether we try to find an empty enclosure to add an animal, or a filled enclosure to free an animal
   *  - ignoreRequirements : allow to bypass requirements check in case of release and no other option
   */
  public function isAnimalFittingEnclosure($animal, $enclosure, $isAnimalAdded = true, $ignoreRequirements = false)
  {
    // Check enclosure requirements
    $requirements = $animal->getEnclosureRequirements();
    if ($this->player->hasPlayedCard('S219_DiversityResearcher')) {
      $requirements[WATER] = 0;
      $requirements[ROCK] = 0;
    }

    if (
      !$ignoreRequirements &&
      ($enclosure[WATER] < ($requirements[WATER] ?? 0) || $enclosure[ROCK] < ($requirements[ROCK] ?? 0))
    ) {
      return false;
    }

    $type = $enclosure['type'];
    $enclosureSize = $enclosure['size'] ?? count(BUILDINGS[$type]);

    // Regular enclosure
    if (in_array($type, \REGULAR_ENCLOSURES)) {
      $size = $animal->getEnclosureSize();
      // Animal must be ok with regular enclosure (all except domestic animals) + size big enough + enclosure is free
      if ($size == 0 || $enclosureSize < $size || $enclosure['state'] == ($isAnimalAdded ? 1 : 0)) {
        return false;
      }
    }
    // Special enclosure
    else {
      $special = $animal->getSpecialEnclosure();
      // Check that this kind of special enclosure is allowed and enough place for all the cubes
      if (
        ($special['type'] ?? null) != $type ||
        ($isAnimalAdded && $enclosure['state'] + $special['cubes'] > $enclosureSize) ||
        (!$isAnimalAdded && $special['cubes'] > $enclosure['state'])
      ) {
        return false;
      }
    }

    return true;
  }

  public function getAvailableEnclosures(
    $animal,
    $isAnimalAdded = true,
    $ignoreRequirements = false,
    $checkIsDoable = false,
    $constraint = null
  ) {
    $enclosures = $this->getEnclosuresWithSurroundings();
    $fittingEnclosures = new Collection([]);
    foreach ($enclosures as $enclosure) {
      if (!is_null($constraint) && !in_array($enclosure['type'], $constraint)) {
        continue;
      }

      if ($this->isAnimalFittingEnclosure($animal, $enclosure, $isAnimalAdded, $ignoreRequirements)) {
        $fittingEnclosures[$enclosure['id']] = $enclosure;
        if ($checkIsDoable) {
          return $fittingEnclosures;
        }
      }
    }

    return $fittingEnclosures;
  }

  public function getReleasableEnclosures($animal, $removeSpecialEnclosure = false)
  {
    // 1. Do you have a matching special enclosure? (animal card has the icon, enough player tokens, water/rock if needed) If so, remove the tokens.
    // 2. Otherwise, do you have a matching standard enclosure? (occupied, large enough, water/rock if needed) If so, unflip the smallest such tile.
    // 3. Otherwise, do you have a matching special enclosure, ignoring water/rock? (animal card has the icon, enough player tokens) If so, remove the tokens.
    // 4. Otherwise, do you have a matching standard enclosure, ignoring water/rock? (occupied, large enough) If so, unflip the smallest such tile.

    // First get all the available enclosure that match water/rock requirements
    $enclosures = $this->getAvailableEnclosures($animal, false)->filter(function ($enclosure) use ($removeSpecialEnclosure) {
      return !$removeSpecialEnclosure || !in_array($enclosure['type'], \SPECIAL_ENCLOSURES);
    });
    // If none of them, just ignore the water/rock requirements
    if ($enclosures->empty()) {
      $enclosures = $this->getAvailableEnclosures($animal, false, true);
    }

    // Now check the special enclosure, if any
    $type = $animal->getSpecialEnclosure()['type'] ?? null;
    $filteredEnclosures = $enclosures->filter(function ($enclosure) use ($type, $removeSpecialEnclosure) {
      // Keep only special enclosure if $removeSpecialEnclosure if false
      //  or keep all but special enclosure if $removeSpecialEnclosure is true
      return $enclosure['type'] == $type xor $removeSpecialEnclosure;
    });

    if ($removeSpecialEnclosure || !$filteredEnclosures->empty()) {
      $enclosures = $filteredEnclosures;
    }

    // Keep the smallest ones
    $sizes = [];
    foreach ($enclosures as $enclosure) {
      $size = $enclosure['size'] ?? count(BUILDINGS[$enclosure['type']]);
      $sizes[$size][$enclosure['id']] = $enclosure;
    }

    return new Collection(empty($sizes) ? [] : $sizes[min(array_keys($sizes))]);
  }

  /**
   * Fill enclosure with a new animal
   */
  public function fillEnclosure($enclosureId, $animal)
  {
    $enclosure = &$this->buildings[$enclosureId];
    $newState = 1;
    if (in_array($enclosure['type'], \SPECIAL_ENCLOSURES)) {
      $newState = $enclosure['state'] + $animal->getSpecialEnclosure()['cubes'];
    }
    Buildings::setState($enclosureId, $newState);
    $enclosure['state'] = $newState;
    return $enclosure;
  }

  /**
   * Free an enclosure of an animal
   */
  public function emptyEnclosure($enclosureId, $animal)
  {
    $enclosure = &$this->buildings[$enclosureId];
    $newState = 0;
    if (in_array($enclosure['type'], \SPECIAL_ENCLOSURES)) {
      $newState = $enclosure['state'] - $animal->getSpecialEnclosure()['cubes'];
    }
    Buildings::setState($enclosureId, $newState);
    $enclosure['state'] = $newState;
    return $enclosure;
  }

  //////////////////////////////////////
  //    ____      _   _
  //   / ___| ___| |_| |_ ___ _ __ ___
  //  | |  _ / _ \ __| __/ _ \ '__/ __|
  //  | |_| |  __/ |_| ||  __/ |  \__ \
  //   \____|\___|\__|\__\___|_|  |___/
  //////////////////////////////////////

  public function getPlacementBonusHexes()
  {
    $cells = [];
    foreach ($this->bonuses as $uid => $bonus) {
      $cells[] = $this->getHexFromId($uid);
    }
    return $cells;
  }

  public function getRockHexes()
  {
    $cells = [];
    foreach ($this->terrains[ROCK] as $uid) {
      $cells[] = $this->getHexFromId($uid);
    }
    return $cells;
  }

  public function getWaterHexes()
  {
    $cells = [];
    foreach ($this->terrains[WATER] as $uid) {
      $cells[] = $this->getHexFromId($uid);
    }
    return $cells;
  }

  // Count the number of empty spaces (excluding water/rock)
  public function countEmptySpaces()
  {
    $hexes = [];
    foreach ($this->getListOfCells() as $cell) {
      if (!$this->hasBuildingAtPos($cell)) {
        $hexes[] = $cell;
      }
    }
    list($water, $rock) = $this->countWaterAndRock($hexes);

    return count($hexes) - $water - $rock;
  }

  // Count the number of water/rock space on a given list of hexes
  protected function countWaterAndRock($hexes)
  {
    $water = 0;
    $rock = 0;
    foreach ($hexes as $hex) {
      // If a building is over a water/rock space (due to special card), the space is no longer water/rock
      if (!is_null($this->getBuildingAtPos($hex))) {
        continue;
      }

      $uid = self::getCellId($hex);
      if (in_array($uid, $this->terrains[WATER])) {
        $water++;
      }
      if (in_array($uid, $this->terrains[ROCK])) {
        $rock++;
      }
    }
    return [$water, $rock];
  }

  /* check if water or rock hex are connected */
  public function areAllTerrainHexConnected($type)
  {
    foreach ($this->terrains[$type] as $uId) {
      $hex = self::getHexFromId($uId);
      if (!is_null($this->getBuildingAtPos($hex))) {
        continue;
      }

      $found = false;
      foreach ($this->getNeighbours($hex) as $cell) {
        if ($this->hasBuildingAtPos($cell)) {
          $found = true;
        }
      }
      if ($found === false) {
        return false;
      }
    }
    return true;
  }

  public function areBorderCellsCovered()
  {
    foreach ($this->getBorderCells() as $hex) {
      $uid = self::getCellId($hex);
      if (!is_null($this->getBuildingAtPos($hex))) {
        continue;
      }

      if (in_array($uid, $this->terrains[WATER])) {
        continue;
      }

      if (in_array($uid, $this->terrains[ROCK])) {
        continue;
      }

      return false;
    }
    return true;
  }

  /**
   * getNonBuildingCells: return the list of cells that are not considered as buildings cells
   */
  public function getNonBuildingCells()
  {
    $cells = [];
    foreach (array_merge($this->terrains[WATER], $this->terrains[ROCK]) as $uid) {
      $cells[] = self::getHexFromId($uid);
    }
    return $cells;
  }

  /**
   * isBuildingCell: return true if the cell is considered as building cells
   */
  public function isBuildingCell($cell)
  {
    $uid = self::getCellId($cell);
    return !in_array($uid, $this->terrains[WATER]) && !in_array($uid, $this->terrains[ROCK]);
  }

  /**
   * getConnectedCells: return list of cells adjacent to at least one building
   *  => useful for some sponsors
   */
  public function getConnectedCells($withoutBuildings = true)
  {
    $cells = $this->getBuildingsNeighbourCells();
    if ($withoutBuildings) {
      Utils::filter($cells, function ($cell) {
        return !$this->hasBuildingAtPos($cell);
      });
    }
    return $cells;
  }

  /**
   * getIsolatedCells: return list of cells not adjacent to any building
   *  => useful for some sponsors
   */
  public function getIsolatedCells()
  {
    return Utils::diffZones(self::getListOfCells(), $this->getBuildingsNeighbourCells());
  }

  /////////////////////////////////////////////
  //   ____      _     _   _   _ _   _ _
  //  / ___|_ __(_) __| | | | | | |_(_) |___
  // | |  _| '__| |/ _` | | | | | __| | / __|
  // | |_| | |  | | (_| | | |_| | |_| | \__ \
  //  \____|_|  |_|\__,_|  \___/ \__|_|_|___/
  ////////////////////////////////////////////

  public static function getCellId($hex)
  {
    return $hex['x'] . '_' . $hex['y'];
  }

  public static function getHexFromId($uid)
  {
    $coord = explode('_', $uid);
    return ['x' => $coord[0], 'y' => $coord[1]];
  }

  public static function extractPos($building)
  {
    return [
      'x' => $building['x'],
      'y' => $building['y'],
    ];
  }

  public static function createGrid($defaultValue = null)
  {
    $dim = ['x' => 9, 'y' => 7];
    $g = [];
    for ($x = 0; $x < $dim['x']; $x++) {
      $size = $dim['y'] - ($x % 2 == 0 ? 1 : 0);
      for ($y = 0; $y < $size; $y++) {
        $row = 2 * $y + ($x % 2 == 0 ? 1 : 0);
        $g[$x][$row] = $defaultValue;
      }
    }
    return $g;
  }

  public static function getListOfCells()
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

  protected function isCellValid($cell)
  {
    return isset($this->grid[$cell['x']][$cell['y']]);
  }

  protected function areSameCell($cell1, $cell2)
  {
    return $cell1['x'] == $cell2['x'] && $cell1['y'] == $cell2['y'];
  }

  public function getNeighbours($cell)
  {
    $cells = [];
    foreach (DIRECTIONS as $dir) {
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

  protected function getRotatedHex($hex, $rotation)
  {
    if ($rotation == 0 || ($hex['x'] == 0 && $hex['y'] == 0)) {
      return $hex;
    }

    $q = $hex['x'];
    $r = ($hex['y'] - $hex['x']) / 2;
    $cube = [$q, $r, -$q - $r];
    for ($i = 0; $i < $rotation; $i++) {
      $cube = [-$cube[1], -$cube[2], -$cube[0]];
    }
    return [
      'x' => $cube[0],
      'y' => 2 * $cube[1] + $cube[0],
    ];
  }

  protected function getDistance($hex1, $hex2)
  {
    $deltaX = abs($hex1['x'] - $hex2['x']);
    $deltaY = abs($hex1['y'] - $hex2['y']);
    return $deltaX + max(0, ($deltaY - $deltaX) / 2);
  }
}
