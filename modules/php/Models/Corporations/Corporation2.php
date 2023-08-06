<?php

namespace PU\Models\Corporations;

class Corporation2 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Flux Industries');
    $this->desc = clienttranslate('Your flux token designates on of your resource tracks as the flux track. Whenever you unlock a tech, you may reposition your flux token. Only your most recently unlocked teck is available.'); //TODO

    $this->techBonuses = [
      1 => [ //TODO
        'text' => clienttranslate('Gain two movement for each rover starting on terrain of the flux track.')
      ],
      2 => [ //TODO
        'text' => clienttranslate('Advance one tracker to the next mileston of the flux track. Once per Game.')
      ],
      3 => [ //TODO
        'text' => clienttranslate('Improve the flux track')
      ],
      4 => [ //TODO
        'text' => clienttranslate('Collect a meteorite when you place a tile matching the flux track.')
      ],
      5 => [ //TODO
        'text' => clienttranslate('Advance the flux track. Once per turn.')
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '2';
  protected $tracks = [
    CIV => [null, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [null, null, 1, SYNERGY, 2, null, 3, SYNERGY, 4, null, 5, null, SYNERGY, 7, null, 10],
    BIOMASS => [null, null, SYNERGY, BIOMASS, null, 1, BIOMASS, null, 2, BIOMASS, SYNERGY, BIOMASS, 3, BIOMASS, null, 5],
    ROVER => [null, ROVER, 'move_1', 'move_1', 'move_2', 'move_2', ['move_2', ROVER], 'move_2', ['move_2', 1], 'move_2', ['move_2', SYNERGY], 'move_2', ['move_2', 2], 'move_3', 'move_3', ['move_3', 5]],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5]
  ];
  protected $level = 3;
}
