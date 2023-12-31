<?php

namespace PU\Models\Corporations;

class Corporation6 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Oasis Ultd.');
    $this->desc = clienttranslate('Advance your water tracker once for each water terrain square that covers planet ice. Gain the benefits from advancing your water tracker even when it advances onto another track.');

    $this->flagsToReset = [TECH_GET_1_MOVE_STARTING_ON_WATER];

    $this->techBonuses = [
      1 => [
        'text' => clienttranslate('Skip over another tracker blocking your advancement.')
      ],
      2 => [
        'text' => clienttranslate('Gain one movement for each rover starting on water terrain.')
      ],
      3 => [
        'text' => clienttranslate('Advance your water tracker once if you place your tile covering no ice.')
      ],
      4 => [
        'text' => clienttranslate('Gain Biomass patch when you place a water resource tile.')
      ],
      5 => [
        'text' => clienttranslate('Gain a synergy boost when you place a water resource tile.')
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '6';
  protected $tracks = [
    CIV => [null, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [null, null, SKIP, SYNERGY, SKIP, SYNERGY, 3, SKIP, SKIP, 6, SKIP, 8, SKIP, 12, null, 15],
    BIOMASS => [null, null, SYNERGY, BIOMASS, null, 1, BIOMASS, null, 2, BIOMASS, SYNERGY, BIOMASS, 3, BIOMASS, null, 5],
    ROVER => [null, ROVER, 'move_1', 'move_1', 'move_2', 'move_2', ['move_2', ROVER], 'move_2', ['move_2', 1], 'move_2', ['move_2', SYNERGY], 'move_2', ['move_2', 2], 'move_3', 'move_3', ['move_3', 5], ['move_3']],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5]
  ];

  protected $waterTrack = [
    ['x' => WATER, 'y' => 0],
    ['x' => WATER, 'y' => 1],
    ['x' => BIOMASS, 'y' => 2],
    ['x' => ROVER, 'y' => 3],
    ['x' => TECH, 'y' => 4],
    ['x' => ROVER, 'y' => 5],
    ['x' => BIOMASS, 'y' => 4],
    ['x' => WATER, 'y' => 3],
    ['x' => CIV, 'y' => 4],
    ['x' => WATER, 'y' => 5],
    ['x' => WATER, 'y' => 6],
    ['x' => CIV, 'y' => 7],
    ['x' => CIV, 'y' => 8],
    ['x' => WATER, 'y' => 9],
    ['x' => BIOMASS, 'y' => 8],
    ['x' => ROVER, 'y' => 7],
    ['x' => TECH, 'y' => 8],
    ['x' => ROVER, 'y' => 9],
    ['x' => BIOMASS, 'y' => 10],
    ['x' => WATER, 'y' => 11],
    ['x' => CIV, 'y' => 12],
    ['x' => WATER, 'y' => 13],
    ['x' => WATER, 'y' => 14],
    ['x' => WATER, 'y' => 15]
  ];
  protected $level = 3;

  /*
   * return the tracker level (how many step it's from start)
   * could be different than Y on corporations where progression is not linear
   */
  public function getLevelOnTrack($type)
  {
    $tracker = $this->player->getTracker($type);
    if (is_null($tracker)) return 0;

    //if $type is not water, or is water and on water track do as usual
    if ($type != WATER) {
      return $tracker->getY();
    } else {
      return $this->convertWaterPositionToY($tracker);
    }
  }

  //overide due to this very special water track
  public function getBestMedal($type)
  {
    //for other types, nothing changes
    if ($type != WATER) {
      return parent::getBestMedal($type);
    } else {
      //for water, find the last medal crossed specificaly on water track 
      //take the water track
      $waterTracker = $this->player->getTracker($type);
      if (is_null($waterTracker)) return 0;
      for ($y = $this->convertWaterPositionToY($waterTracker); $y > 0; $y--) {
        $previousPositionOnWaterTrack = $this->convertYToWaterPosition($y);
        if ($previousPositionOnWaterTrack['x'] == WATER) {
          $medal = $this->extractMedal($this->tracks[WATER][$previousPositionOnWaterTrack['y']]);
          if ($medal) {
            return $medal;
          }
        }
      }
      return 0;
    };
  }

  public function isTrackerOnTop($type)
  {
    if ($type == WATER) {
      return count($this->waterTrack) == $this->getLevelOnTrack(WATER) + 1;
    } else {
      return parent::isTrackerOnTop($type);
    }
  }


  /**
   * Return cell corresponding to a level in water track, 
   * by default : the level of the water tracker
   */
  public function getWaterCoords($y = null)
  {
    $y = $y ?? $this->player->getTracker(WATER)->getY();
    return $this->convertYToWaterPosition($y);
  }

  public function convertWaterPositionToY($tracker)
  {
    $x = $tracker->getX();
    $y = $tracker->getY();

    foreach ($this->waterTrack as $id => $cell) {
      if ($cell['x'] == $x && $cell['y'] == $y) {
        return $id;
      }
    }
  }

  public function convertYToWaterPosition($y)
  {
    if ($y < 0 || $y >= count($this->waterTrack)) return false;
    return $this->waterTrack[$y];
  }

  /**
   * Return an array of all cells this TYPE tracker can reach with a N move.
   * @return Array of CellIDs ('x_y')
   */
  public function getNextSpaceIds($type, $n = 1)
  {
    $trackPawn = $this->player->getTracker($type);

    // skip spaceId with tracker
    $dy = $n > 0 ? 1 : -1;

    //determine coord for next space
    if ($type == WATER) {
      $nextY = $this->convertWaterPositionToY($this->player->getTracker($type)) + $n;
      if ($nextY >= count($this->waterTrack) || $nextY < 0) {
        return [];
      }
      $nextCell = $this->getWaterCoords($nextY);
      $x = $nextCell['x'];
      $y = $nextCell['y'];
    } else {
      $nextY = $this->getLevelOnTrack($type) + $n;
      $x = $trackPawn->getX();
      $y = $nextY;
    }

    //check if the nextSpace is busy
    while ($this->player->hasMeepleOnTrack($x, $y)) {
      if ($this->canUse(TECH_SKIP_OVER_TRACKER)) {
        $nextY += $dy;
        //determine coord for next space
        if ($type == WATER) {
          if ($nextY >= count($this->waterTrack) || $nextY < 0) {
            return [];
          }
          $nextCell = $this->getWaterCoords($nextY);
          $x = $nextCell['x'];
          $y = $nextCell['y'];
        } else {
          $x = $trackPawn->getX();
          $y = $nextY;
        }
      } else {
        return [];
      }
    }

    // Blocked at the top or the bottom => why would anyone do that anyway ??
    if ($y >= count($this->tracks[$type]) || $y < 0) {
      return [];
    }

    return [$x . '_' . $y];
  }

  public function getAnytimeActions()
  {
    $actions = [];

    if ($this->canUse(TECH_GET_1_MOVE_STARTING_ON_WATER)) {
      $action = $this->get1MoveStartingOnWater();
      if (is_array($action)) {
        $action['source'] = $this->name;
        $action['flag'] = TECH_GET_1_MOVE_STARTING_ON_WATER;
        $actions[] = $action;
      }
    }

    return $actions;
  }

  public function get1MoveStartingOnWater()
  {
    $rovers = $this->player->getRoversOnPlanet();
    $move = 0;
    foreach ($rovers as $id => $rover) {
      if ($this->player->planet()->getVisibleAtPos($rover->getCell()) ==  WATER) {
        $move += 1;
      }
    }

    if ($move > 0) {
      return [
        'action' => MOVE_ROVER,
        'args' => [
          'remaining' => $move,
        ],
      ];
    }
  }
}
