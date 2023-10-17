<?php

namespace PU\Models\Corporations;

use PU\Core\PGlobals;
use PU\Managers\Meeples;

class Corporation2 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Flux Industries');
    $this->desc = clienttranslate(
      'Your flux token designates one of your resource tracks as the flux track. Whenever you unlock a tech, you may reposition your flux token. Only your most recently unlocked tech is available.'
    );

    $this->flagsToReset = [TECH_ADVANCE_FLUX, TECH_GET_2_MOVES_ON_FLUX];

    $this->techBonuses = [
      1 => [
        'text' => clienttranslate('Gain two movements for each rover starting on terrain of the flux track.'),
      ],
      2 => [
        //TODO
        'text' => clienttranslate('Advance one tracker to the next milestone of the flux track. Once per Game.'),
      ],
      3 => [
        'text' => clienttranslate('Improve the flux track'),
      ],
      4 => [
        'text' => clienttranslate('Collect a meteorite when you place a tile matching the flux track.'),
      ],
      5 => [
        'text' => clienttranslate('Advance the flux track. Once per turn.'),
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '2';
  protected $tracks = [
    CIV => [null, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [null, null, 1, SYNERGY, 2, null, 3, SYNERGY, 4, null, 5, null, SYNERGY, 7, null, 10],
    BIOMASS => [null, null, SYNERGY, BIOMASS, null, 1, BIOMASS, null, 2, BIOMASS, SYNERGY, BIOMASS, 3, BIOMASS, null, 5],
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
      'move_2',
      ['move_2', SYNERGY],
      'move_2',
      ['move_2', 2],
      'move_3',
      'move_3',
      ['move_3', 5],
      ['move_3'],
    ],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5],
  ];
  protected $upgradedTracks = [
    CIV => [CIV, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [
      null,
      null,
      1,
      [SYNERGY, SYNERGY],
      2,
      null,
      3,
      [SYNERGY, SYNERGY],
      4,
      null,
      5,
      null,
      [SYNERGY, SYNERGY],
      7,
      null,
      10,
    ],
    BIOMASS => [
      null,
      null,
      SYNERGY,
      [BIOMASS, BIOMASS],
      null,
      1,
      [BIOMASS, BIOMASS],
      null,
      2,
      [BIOMASS, BIOMASS],
      SYNERGY,
      [BIOMASS, BIOMASS],
      3,
      [BIOMASS, BIOMASS],
      null,
      5,
    ],
    ROVER => [
      null,
      ROVER,
      'move_2',
      'move_2',
      'move_3',
      'move_3',
      ['move_3', ROVER],
      'move_3',
      ['move_3', 1],
      'move_3',
      ['move_3', SYNERGY],
      'move_3',
      ['move_3', 2],
      'move_4',
      'move_4',
      ['move_4', 5],
    ],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5],
  ];
  protected $level = 3;

  public function getFluxTrack()
  {
    $meeple = Meeples::getOfPlayer($this->player, \FLUX_MEEPLE)->first();
    return $meeple->getX();
  }

  public function hasTechLevel($techLvl)
  {
    return $this->getTechLevel() == $techLvl;
  }

  public function getBonuses($cell, $withBonus = true)
  {
    $bonuses = [];

    $t = $this->tracks;
    if ($this->player->hasTech(TECH_UPGRADED_FLUX_TRACK) && $cell['x'] == $this->getFluxTrack()) {
      $t = $this->upgradedTracks;
    }

    if (is_array($t[$cell['x']][$cell['y']])) {
      $bonuses = $t[$cell['x']][$cell['y']];
    } elseif ($t[$cell['x']][$cell['y']]) {
      $bonuses[] = $t[$cell['x']][$cell['y']];
    }

    return $bonuses;
  }

  public function get2MovesOnFlux()
  {
    $rovers = $this->player->getRoversOnPlanet();
    $flux = $this->getFluxTrack();

    $move = 0;
    foreach ($rovers as $id => $rover) {
      if ($this->player->planet()->getVisible($rover->getX(), $rover->getY()) == $flux) {
        $move += 2;
      }
    }

    return $move;
  }
  public function getCivLevel()
  {
    if ($this->player->hasTech(TECH_UPGRADED_FLUX_TRACK) && $this->getFluxTrack() == CIV) {
      $civLevels = [0, 2, 3, 4, 4];
      return $civLevels[$this->countLevel(CIV)];
    } else {
      return $this->countLevel(CIV);
    }
  }

  public function getTechAction($techLvl)
  {
    if ($this->canUse('tech_' . $this->id . '_' . $techLvl)) {
      return [
        'action' => REACH_NEXT_MILESTONE
      ];
    }
  }

  public function getAnytimeActions()
  {
    $actions = [];
    if ($this->canUse(TECH_ADVANCE_FLUX)) {
      $actions[] = [
        'action' => MOVE_TRACK,
        'args' => [
          'type' => $this->getFluxTrack(),
          'n' => 1,
          'withBonus' => true,
        ],
        'source' => $this->name,
        'flag' => TECH_ADVANCE_FLUX
      ];
    }

    if ($this->canUse(TECH_GET_2_MOVES_ON_FLUX)) {
      $move = PGlobals::getFreeRoverMoves($this->player->getId());
      if ($move) {
        $actions[] = [
          'action' => MOVE_ROVER,
          'args' => [
            'remaining' => $move,
          ],
          'source' => $this->name,
          'flag' => TECH_GET_2_MOVES_ON_FLUX
        ];
      }
    }

    return $actions;
  }

  public function getFluxToNextMilestone()
  {
    $flux = $this->getFluxTrack();
    $fluxLevel = $this->getLevelOnTrack($flux);

    for ($i = $fluxLevel + 1; $i < count($this->tracks[$flux]); $i++) {
      if ($this->isOrIn($this->tracks[$flux][$i], $flux)) {
        return [
          'type' => NODE_SEQ,
          'childs' => [
            [
              'action' => MOVE_TRACK,
              'args' => [
                'type' => $flux,
                'n' => $i - $fluxLevel - 1,
                'withBonus' => false,
              ],
            ],
            [
              'action' => MOVE_TRACK,
              'args' => [
                'type' => $flux,
                'n' => 1,
                'withBonus' => true,
              ],
              'flag' => TECH_FLUX_TO_NEXT_MILESTONE,
              'source' => $this->name
            ],
          ]
        ];
      }
    }
    return false;
  }

  public function resetFlags()
  {
    $flags = PGlobals::getFlags($this->pId);
    foreach ($this->flagsToReset as $flag) {
      unset($flags[$flag]);
    }
    PGlobals::setFlags($this->pId, $flags);

    PGlobals::setFreeRoverMoves($this->pId, $this->get2MovesOnFlux());
  }
}
