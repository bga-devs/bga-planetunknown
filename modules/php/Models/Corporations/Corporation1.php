<?php

namespace PU\Models\Corporations;

class Corporation1 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Cosmos inc');
    $this->desc = clienttranslate('Choose to position a collected lifepod onto your advancement track or onto your scoring area. Your trackers skip over lifepods when advancing. Benefits and medals covered by lifepods are ignored.');
    //TODO

    $this->techBonuses = [
      1 => [ //TODO
        'text' => clienttranslate('Your rover may move diagonally;')
      ],
      2 => [ //TODO
        'text' => clienttranslate('Reposition three collected lifepods immediately. Once per game.')
      ],
      3 => [ //TODO
        'text' => clienttranslate('Reposition a collected lifepod when you place an energy tile.')
      ],
      4 => [ //TODO
        'text' => clienttranslate('Rovers moving onto energy terrain do not spend movements.')
      ],
      5 => [ //TODO
        'text' => clienttranslate('Reposition one collected lifepod. Once per round.')
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '1';
  protected $tracks = [
    CIV => [null, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [null, null, 1, SYNERGY, 2, null, 3, SYNERGY, 4, null, 5, null, SYNERGY, 7, null, 10],
    BIOMASS => [null, null, SYNERGY, BIOMASS, null, 1, BIOMASS, null, 2, BIOMASS, SYNERGY, BIOMASS, 3, BIOMASS, null, 5],
    ROVER => [null, ROVER, 'move_1', 'move_1', 'move_2', 'move_2', 'move_2', 'move_2', ['move_2', 1], 'move_2', ['move_2', SYNERGY], 'move_2', ['move_2', 2], 'move_3', 'move_3', ['move_3', 5]],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5]
  ];
  protected $level = 2;
}
