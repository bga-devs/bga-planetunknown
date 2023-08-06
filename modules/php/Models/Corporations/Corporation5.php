<?php

namespace PU\Models\Corporations;

class Corporation5 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Make Shift');
    $this->desc = clienttranslate('You may advance any tracker diagonally onto an adjacent track. Any tracker may claim any benefit. Tech Levels are locked if no tracker is currently unlocking the tech.'); //TODO

    $this->techBonuses = [
      1 => [ //TODO
        'text' => clienttranslate('Treat civ and tech tracks as adjacent.')
      ],
      2 => [ //TODO
        'text' => clienttranslate('+1 rover movement if more than one tracker occupies the rover track.')
      ],
      3 => [ //TODO
        'text' => clienttranslate('Shift a tracker laterally to an ajacent track instead of advancing.')
      ],
      4 => [ //TODO
        'text' => clienttranslate('Regress any one tracker. Once per round.')
      ],
      5 => [ //TODO
        'text' => clienttranslate('Scoring each tracker as the highest scoring tracker on the track it occupies.')
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '5';
  protected $tracks = [
    CIV => [null, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [null, null, 1, SYNERGY, 2, null, SYNERGY, 3, SYNERGY, null, null, 4, SYNERGY, null, null, 5],
    BIOMASS => [null, null, SYNERGY, BIOMASS, null, 1, BIOMASS, null, 2, BIOMASS, SYNERGY, BIOMASS, 3, BIOMASS, null, 5],
    ROVER => [null, ROVER, 'move_1', 'move_1', 'move_2', 'move_2', ['move_2', ROVER], 'move_2', ['move_2', SYNERGY], 'move_2', ['move_2', 1], 'move_2', ['move_2', 2], 'move_3', 'move_3', ['move_3', 5]],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5]
  ];
  protected $level = 4;
}
