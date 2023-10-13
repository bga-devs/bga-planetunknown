<?php

namespace PU\Models\Corporations;

class Corporation6 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Oasis Ultd.');
    $this->desc = clienttranslate('Advance your water tracker once for each water terrain square that covers planet ice. Gain the benefits from advancing your water tracker even when it advances onto another track.');

    $this->techBonuses = [
      1 => [ //TODO
        'text' => clienttranslate('Skip over another tracker blocking your advancement.')
      ],
      2 => [ //TODO
        'text' => clienttranslate('Gain one movement for each rover starting on water terrain.')
      ],
      3 => [ //TODO
        'text' => clienttranslate('Advance your water tracker once if you place your tile covering no ice.')
      ],
      4 => [ //TODO
        'text' => clienttranslate('Gain Biomass patch when you place a water resource tile.')
      ],
      5 => [ //TODOCautionNOSynergy
        'text' => clienttranslate('Gain a synergy boost when you place a water resource tile.')
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '6';
  protected $tracks = [
    CIV => [null, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [null, null, SKIP, SYNERGY, SKIP, SYNERGY, 3, SKIP, SKIP, 5, SKIP, 8, SKIP, 12, null, 15],
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
    return $type != WATER ?
      $this->player->getTracker($type)->getY() :
      $this->getWaterCoords()['y'];
  }

  public function getWaterCoords($y = null)
  {
    $y = $y ?? $this->player->getTracker(WATER)->getY();
    return $this->waterTrack[$y];
  }

  /**
   * Return an array of all cells this TYPE tracker can reach with a N move.
   * @return Array of CellIDs ('x_y')
   */
  public function getNextSpaceIds($type, $n = 1)
  {
    $trackPawn = $this->player->getTracker($type);
    //Y can't be lower than 0
    $nextSpaceY = max(0, $trackPawn->getY() + $n);

    //Y can't be higher than top of the track type
    $nextSpaceY = min(count($this->tracks[$type]) - 1, $nextSpaceY);

    if ($type == WATER) {
      $nextCell = $this->getWaterCoords($nextSpaceY);
      return [$this->getSpaceId($nextCell)];
    } else {
      return [$trackPawn->getX() . '_' . $nextSpaceY];
    }
  }
}
