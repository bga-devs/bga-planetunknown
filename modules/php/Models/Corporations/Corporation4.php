<?php

namespace PU\Models\Corporations;

class Corporation4 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Jump Drive');
    $this->desc = clienttranslate('Choose to place a collected lifepod onto your tech or your scoring area. A lifepod placed onto tech unlocks the tech in any order.'); //TODO

    $this->techBonuses = [
      1 => [ //TODOCautionNOSynergy
        'text' => clienttranslate('Gain synergy boost instead of a biomass patch. Once per round.')
      ],
      2 => [ //TODO
        'text' => clienttranslate('Teleport a rover to a tile ofthe same terrain. One movement cost. Once per round.')
      ],
      3 => [ //TODO
        'text' => clienttranslate('Advance the tracker twice when using a synergy boost. Once per round.')
      ],
      4 => [ //TODO
        'text' => clienttranslate('You may treat a tech resource as energy during tile placement')
      ],
      5 => [ //TODO
        'text' => clienttranslate('Choose a tracker and claim all benefits in its row. Once per game.')
      ],
      6 => [ //TODO
        'text' => clienttranslate('Draw 3 objectives and keep on that all players compete for. Once per game.')
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '4';
  protected $tracks = [
    CIV => [null, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [null, null, 1, SYNERGY, 2, null, 3, SYNERGY, 4, null, 5, null, SYNERGY, 7, null, 10],
    BIOMASS => [null, null, SYNERGY, BIOMASS, null, 1, BIOMASS, null, 2, BIOMASS, SYNERGY, BIOMASS, 3, BIOMASS, null, 5],
    ROVER => [null, ROVER, 'move_1', 'move_1', 'move_2', 'move_2', 'move_2', 'move_2', ['move_2', 1], 'move_2', ['move_2', SYNERGY], 'move_2', ['move_2', 2], 'move_3', 'move_3', ['move_3', 5]],
    TECH => [null, 5, SYNERGY, null, null, null, 3, null, 2, null, null, 1, SYNERGY, null, null, null]
  ];
  protected $level = 3;
}
