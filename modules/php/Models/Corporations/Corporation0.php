<?php

namespace PU\Models\Corporations;

use PU\Core\Notifications;
use PU\Managers\Actions;
use PU\Managers\Tiles;

class Corporation0 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Universal Coalition');
    $this->desc = clienttranslate('Advance your trackers to gain benefits, unlock milestones, and score the highest medal your tracker covers or surpasses');

    $this->techBonuses = [
      1 => [
        'text' => clienttranslate('Tile adjacency placement restriction may be ignored.')
      ],
      2 => [
        'text' => clienttranslate('You may store biomass patches to be placed at the end of game.')
      ],
      3 => [
        'text' => clienttranslate('+1 each time you gain rover movement.')
      ],
      4 => [
        'text' => clienttranslate('Double water advancement from water tile placement.')
      ],
      5 => [
        'text' => clienttranslate('Unaffected by meteor strikes. Do not place meteorites.')
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '0';
  protected $tracks = [
    CIV => [null, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [null, null, 1, SYNERGY, 2, null, 3, SYNERGY, 4, null, 5, null, SYNERGY, 7, null, 10],
    BIOMASS => [null, null, SYNERGY, BIOMASS, null, 1, BIOMASS, null, 2, BIOMASS, SYNERGY, BIOMASS, 3, BIOMASS, null, 5],
    ROVER => [null, ROVER, 'move_1', 'move_1', 'move_2', 'move_2', ['move_2', ROVER], 'move_2', ['move_2', 1], 'move_3', ['move_3', SYNERGY], 'move_3', ['move_3', 2], 'move_4', 'move_4', ['move_4', 5]],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5]
  ];

  public function moveRoverby($n)
  {
    return $this->player->hasTech(TECH_ROVER_MOVE_PLUS_ONE) ? $n + 1 : $n;
  }

  // add an end of game action or return patch to be placed immediately
  public function receiveBiomassPatch()
  {
    $patch = Tiles::createBiomassPatch($this->player);
    if ($this->player->hasTech(TECH_CAN_STORE_BIOMASS_PATCH) && $this->gamestate->state_id() != ST_END_GAME_TURN) {
      $this->player->addEndOfGameAction(Actions::getBiomassPatchFlow($patch->getId()));

      Notifications::receiveBiomassPatch($this->player, $patch, true);
    } else {
      Notifications::receiveBiomassPatch($this->player, $patch);
      return $patch;
    }
  }
}
