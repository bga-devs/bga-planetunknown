<?php

namespace PU\Models\Corporations;

class Corporation{ID} extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('{TITLE}');
    $this->desc = clienttranslate('{TEXT}');//TODO

    $this->techBonuses = [
      1 => [//TODO
        'text' => clienttranslate('{TB1}')
      ],
      2 => [//TODO
        'text' => clienttranslate('{TB2}')
      ],
      3 => [//TODO
        'text' => clienttranslate('{TB3}')
      ],
      4 => [//TODO
        'text' => clienttranslate('{TB4}')
      ],
      5 => [//TODO
        'text' => clienttranslate('{TB5}')
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '{ID}';
  protected $tracks = [
    CIV => [null, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [null, null, 1, SYNERGY, 2, null, 3, SYNERGY, 4, null, 5, null, SYNERGY, 7, null, 10],
    BIOMASS => [null, null, SYNERGY, BIOMASS, null, 1, BIOMASS, null, 2, BIOMASS, SYNERGY, BIOMASS, 3, BIOMASS, null, 5],
    ROVER => [null, ROVER, 'move_1', 'move_1', 'move_2', 'move_2', ['move_2', ROVER], 'move_2', ['move_2', 1], 'move_3', ['move_3', SYNERGY], 'move_3', ['move_3', 2], 'move_4', 'move_4', ['move_4', 5]],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5]
  ];
  protected $level = {LEVEL};
}
