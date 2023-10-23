<?php

namespace PU\Models\Corporations;

use PU\Core\Globals;

class Corporation8 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Wormhole Corp');
    $this->desc = clienttranslate(
      'Advance your biomass tracker from tile placement only when you expand a biomass terrain area.'
    );
    $this->flagsToReset = [TECH_WORMHOLE_GAIN_TWO_BIOMASS_PATCHES];

    $this->techBonuses = [
      1 => [
        'text' => clienttranslate('Biomass tracker may be reset to zero if at maximum.'),
      ],
      2 => [
        'text' => clienttranslate('Biomass patches may be placed on top of a tile.'),
      ],
      3 => [
        'text' => clienttranslate('Gain two biomass patches instead of one. Once per round.'),
      ],
      4 => [
        'text' => clienttranslate('Biomass patches may be stored and placed at the end of the game.'),
      ],
      5 => [
        'text' => clienttranslate('Biomass patches may be placed to destroy a meteorite.'),
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '8';
  protected $tracks = [
    CIV => [null, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [null, null, 1, SYNERGY, 2, null, 3, SYNERGY, 4, null, 5, null, SYNERGY, 7, null, 10],
    BIOMASS => [
      null,
      SKIP,
      SYNERGY,
      BIOMASS,
      [SKIP, 1],
      SYNERGY_CIV,
      BIOMASS,
      [SKIP, 2],
      SYNERGY_WATER,
      BIOMASS,
      [SKIP, 3],
      SYNERGY_TECH,
      BIOMASS,
      [SKIP, 5],
      SYNERGY_ROVER,
      BIOMASS,
    ],
    ROVER => [
      null,
      ROVER,
      'move_1',
      'move_1',
      'move_2',
      'move_2',
      ['move_2', ROVER],
      'move_2',
      ['move_2', 1],
      'move_3',
      ['move_3', SYNERGY],
      'move_3',
      ['move_3', 2],
      'move_4',
      'move_4',
      ['move_4', 5],
      ['move_4'],
    ],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5],
  ];
  protected $level = 2;

  public function canPlaceBiomassPatchLater()
  {
    return $this->player->hasTech(TECH_WORMHOLE_CAN_STORE_BIOMASS_PATCH) && Globals::getPhase() != END_OF_GAME_PHASE;
  }

  public function getAnytimeActions()
  {
    $actions = [];
    if ($this->player->hasTech(TECH_WORMHOLE_RESET_BIOMASS) && $this->isTrackerOnTop(BIOMASS)) {
      $actions[] = [
        'action' => RESET_TRACK,
        'args' => [
          'type' => BIOMASS,
        ],
        'source' => $this->name,
      ];
    }

    return $actions;
  }

  public function isTrackerOnTop($type)
  {
    $n = ($type == ROVER || $type == BIOMASS) ? 2 : 1;
    //if $type == ROVER top is 2 under the steps number (because the very last step is 'virtual')
    //if $type == BIOMASS top is 2 under the steps number (because the two last steps are the same)
    return count($this->tracks[$type]) == $this->getLevelOnTrack($type) + $n;
  }

  /**
   * Return an array of all cells this TYPE tracker can reach with a N move.
   * @return Array of CellIDs ('x_y')
   */
  public function getNextSpaceIds($type, $n = 1)
  {
    if ($type != BIOMASS) {
      return parent::getNextSpaceIds($type, $n);
    }

    $trackPawn = $this->player->getTracker($type);

    $dy = $n > 0 ? 1 : -1;
    $x = $trackPawn->getX();
    $y = $trackPawn->getY() + $n;

    // First find the next SKIP
    while (!$this->isOrIn($this->tracks[$type][$y] ?? '', SKIP)) {
      $y += $dy;
    }
    // Then move one more step
    $y += $dy;

    // Blocked at the top or the bottom
    if ($y >= count($this->tracks[$type]) || $y < 0) {
      return [];
    }

    // Otherwise, get the two next spaces
    $spaces = [];
    $spaces[] = $x . '_' . $y;
    $y += $dy;
    $spaces[] = $x . '_' . $y;
    return $spaces;
  }
}
