<?php

namespace PU\Models\Corporations;

class Corporation3 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Horizon Group');
    $this->desc = clienttranslate('To Collect meteorite, your rover must pick it up and deliver it to a rover resource on your planet.'); //TODO

    $this->techBonuses = [
      1 => [ //TODO
        'text' => clienttranslate('Rover tiles may be placed ignoring placement restrictions.')
      ],
      2 => [ //TODO
        'text' => clienttranslate('Gain one movement for each rover carrying a meteorite at round start.')
      ],
      3 => [ //TODO
        'text' => clienttranslate('Gain biomass patch when you collect a meteorite.')
      ],
      4 => [ //TODO
        'text' => clienttranslate('Destroy a meteorite by delivering it to water terrain.')
      ],
      5 => [ //TODO
        'text' => clienttranslate('Advance your rover tracker. Once per round.')
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '3';
  protected $tracks = [
    CIV => [null, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [null, null, 1, SYNERGY, 2, null, 3, SYNERGY, 4, null, 5, null, SYNERGY, 7, null, 10],
    BIOMASS => [null, null, SYNERGY, BIOMASS, null, 1, BIOMASS, null, 2, BIOMASS, SYNERGY, BIOMASS, 3, BIOMASS, null, 5],
    ROVER => [null, ROVER, 'move_2', ['move_2', ROVER], 'move_2', 'move_3', ['move_3', ROVER], 'move_3', ['move_4', 1], ['move_4', ROVER], ['move_4', SYNERGY], 'move_4', ['move_5', 2], 'move_5', 'move_5', ['move_5', 5]],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5]
  ];
  protected $level = 2;
}