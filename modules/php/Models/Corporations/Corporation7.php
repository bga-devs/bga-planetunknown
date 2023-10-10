<?php

namespace PU\Models\Corporations;

class Corporation7 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Republic');
    $this->desc = clienttranslate('You must regress another tracker each time you claim a milestone from the civ track. Do not claim benefits from regressing.'); //TODO

    $this->techBonuses = [
      1 => [ //TODO
        'text' => clienttranslate('Gain movement based on your rover tracker when you select a civ tile.')
      ],
      2 => [ //TODO
        'text' => clienttranslate('Add a card from the next rank to your choices when you claim a civ milestone.')
      ],
      3 => [ //TODO
        'text' => clienttranslate('Teleport your rover between any two civ resources. One movement cost.')
      ],
      4 => [ //TODOCautionNOSynergy
        'text' => clienttranslate('Gain a synergy boost when you claim a civ milestone.')
      ],
      5 => [ //TODO
        'text' => clienttranslate('Look at all remaining civ cards and keep two. End of game.')
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '7';
  protected $tracks = [
    CIV => [null, null, CIV, null, CIV, null, CIV, null, CIV, null, CIV, null, CIV, null, CIV, 5],
    WATER => [null, null, 1, SYNERGY, 2, null, 3, SYNERGY, 4, null, 5, null, SYNERGY, 7, null, 10],
    BIOMASS => [null, null, SYNERGY, BIOMASS, null, 1, BIOMASS, null, 2, BIOMASS, SYNERGY, BIOMASS, 3, BIOMASS, null, 5],
    ROVER => [null, ROVER, 'move_1', 'move_1', 'move_2', 'move_2', 'move_2', 'move_2', ['move_2', 1], 'move_2', ['move_2', SYNERGY], 'move_2', ['move_2', 2], 'move_3', 'move_3', ['move_3', 5]],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5]
  ];
  protected $level = 2;

  public function getCivLevel()
  {
    $civLevels = [0, 1, 2, 2, 3, 3, 4, 4];
    return $civLevels[$this->countLevel(CIV)];
  }
}
