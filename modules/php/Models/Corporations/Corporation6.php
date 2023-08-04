<?php

namespace PU\Models\Corporations;

class Corporation6 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Oasis Ultd.');
    $this->desc = clienttranslate('Advance your water tracker once for each water terrain square that covers planet ice. Gain the benefits from advancing your water tracker even when it advances onto another track.'); //TODO

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
      5 => [ //TODO
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
    ROVER => [null, ROVER, 'move_1', 'move_1', 'move_2', 'move_2', ['move_2', ROVER], 'move_2', ['move_2', 1], 'move_2', ['move_2', SYNERGY], 'move_2', ['move_2', 2], 'move_3', 'move_3', ['move_3', 5]],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5]
  ];

  protected $waterTrack = [
    [WATER, 0],
    [WATER, 1],
    [BIOMASS, 2],
    [ROVER, 3],
    [TECH, 4],
    [ROVER, 5],
    [BIOMASS, 4],
    [WATER, 3],
    [CIV, 4],
    [WATER, 5],
    [WATER, 6],
    [CIV, 7],
    [CIV, 8],
    [WATER, 9],
    [BIOMASS, 8],
    [ROVER, 7],
    [TECH, 8],
    [ROVER, 9],
    [BIOMASS, 10],
    [WATER, 11],
    [CIV, 12],
    [WATER, 13],
    [WATER, 14],
    [WATER, 15]
  ];
  protected $level = 3;
}
