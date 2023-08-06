<?php

namespace PU\Models\Corporations;

class Corporation8 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Wormhole Corp');
    $this->desc = clienttranslate('Advance your biomass tracker from tile placement only when you expand a biomass terrain area.'); //TODO

    $this->techBonuses = [
      1 => [ //TODO
        'text' => clienttranslate('Biomass tracker may be reset to zero if at maximum.')
      ],
      2 => [ //TODO
        'text' => clienttranslate('Biomass patches may be placed on top of a tile.')
      ],
      3 => [ //TODO
        'text' => clienttranslate('Gain two biomass patches instead of one. Once per round.')
      ],
      4 => [ //TODO
        'text' => clienttranslate('Biomass patches may be stored and placed at the end of the game.')
      ],
      5 => [ //TODO
        'text' => clienttranslate('Biomass patches may be placed to destroy a meteorite.')
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '8';
  protected $tracks = [
    CIV => [null, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [null, null, 1, SYNERGY, 2, null, 3, SYNERGY, 4, null, 5, null, SYNERGY, 7, null, 10],
    BIOMASS => [null, SKIP, SYNERGY, BIOMASS, [SKIP, 1], SYNERGY_CIV, BIOMASS, [SKIP, 2], SYNERGY_WATER, BIOMASS, [SKIP, 3], SYNERGY_TECH, BIOMASS, [SKIP, 5], SYNERGY_ROVER, BIOMASS],
    ROVER => [null, ROVER, 'move_1', 'move_1', 'move_2', 'move_2', ['move_2', ROVER], 'move_2', ['move_2', 1], 'move_3', ['move_3', SYNERGY], 'move_3', ['move_3', 2], 'move_4', 'move_4', ['move_4', 5]],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5]
  ];
  protected $level = 2;
}
