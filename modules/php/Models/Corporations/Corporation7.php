<?php

namespace PU\Models\Corporations;

use PU\Managers\Susan;
use PU\Managers\Tiles;

class Corporation7 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Republic');
    $this->desc = clienttranslate('You must regress another tracker each time you claim a milestone from the civ track. Do not claim benefits from regressing.');

    $this->flagsToReset = [TECH_REPUBLIC_MOVE_ROVER_WITH_CIV_TILE, REPUBLIC_TILE_PLACED];

    $this->techBonuses = [
      1 => [
        'text' => clienttranslate('Gain movement based on your rover tracker when you select a civ tile.')
      ],
      2 => [
        'text' => clienttranslate('Add a card from the next rank to your choices when you claim a civ milestone.')
      ],
      3 => [
        'text' => clienttranslate('Teleport your rover between any two civ resources. One movement cost.')
      ],
      4 => [
        'text' => clienttranslate('Gain a synergy boost when you claim a civ milestone.')
      ],
      5 => [
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
    ROVER => [null, ROVER, 'move_1', 'move_1', 'move_2', 'move_2', 'move_2', 'move_2', ['move_2', 1], 'move_2', ['move_2', SYNERGY], 'move_2', ['move_2', 2], 'move_3', 'move_3', ['move_3', 5], ['move_3']],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5]
  ];
  protected $level = 2;

  public function getCivLevel()
  {
    $civLevels = [0, 1, 2, 2, 3, 3, 4, 4];
    return $civLevels[$this->countLevel(CIV)];
  }

  public function regressOnCivMilestone()
  {
    return [
      'action' => CHOOSE_TRACKS,
      'args' => [
        'types' => [WATER, BIOMASS, ROVER, TECH],
        'n' => 1,
        'move' => -1,
        'from' => $this->name,
        'withBonus' => false
      ],
    ];
  }

  public function getAnytimeActions()
  {
    $actions = [];

    //tile must not have been played
    if ($this->canUse(TECH_REPUBLIC_MOVE_ROVER_WITH_CIV_TILE) && !$this->isFlagged(REPUBLIC_TILE_PLACED)) {

      $tiles = Susan::getPlayableTilesForPlayer($this->player);

      $hasCiv = false;
      foreach ($tiles as $tile) {
        if (in_array(CIV, $tile->getTerrainTypes())) {
          $hasCiv = true;
          break;
        }
      }

      if ($hasCiv) {
        $action = $this->getMoveFromRoverTrack();
        if ($action) {
          $actions[] = $action;
        }
      }
    }


    return $actions;
  }

  public function getMoveFromRoverTrack()
  {
    $roverLevel = $this->getLevelOnTrack(ROVER);

    $bonuses = (is_array($this->tracks[ROVER][$roverLevel])) ? $this->tracks[ROVER][$roverLevel] : [$this->tracks[ROVER][$roverLevel]];
    foreach ($bonuses as $bonus) {
      if (is_string($bonus) && str_starts_with($bonus, 'move')) {
        $levelMove = $this->moveRoverBy(explode('_', $bonus)[1]);
        return [
          'action' => MOVE_ROVER,
          'args' => [
            'remaining' => $levelMove,
            'description' => clienttranslate('Move your rover (${remaining}) if you select a civ tile')
          ],
          'flag' => TECH_REPUBLIC_MOVE_ROVER_WITH_CIV_TILE,
          'source' => $this->name
        ];
      }
    }
    return false;
  }
}
