<?php

namespace PU\Models\Planets;

use PU\Helpers\Utils;

class Planet12 extends \PU\Models\Planet
{
  protected $id = '12';
  protected $columnMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $rowMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $level = 3;
  protected $terrains = [
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LIFEPOD, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, ICE, LAND, LAND, LAND, LAND, LAND, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, LAND, ICE, LAND, LAND, LAND, LAND, ICE, ICE, NOTHING, NOTHING],
    [NOTHING, LIFEPOD, ICE, ICE, LAND, LAND, LAND, ICE, ICE, ICE, LAND, NOTHING],
    [NOTHING, LAND, ICE, LAND, LAND, ICE, LIFEPOD, ICE, LAND, LAND, LAND, NOTHING],
    [LAND, ICE, ICE, LAND, LAND, ICE, ICE, ICE, LAND, LAND, LAND, LAND],
    [ICE, ICE, LAND, LAND, LAND, ICE, LIFEPOD, ICE, LAND, LAND, LAND, LAND],
    [NOTHING, LAND, LAND, LAND, LAND, LAND, LAND, ICE, LAND, LAND, LAND, NOTHING],
    [NOTHING, LAND, LAND, LAND, LAND, LAND, LAND, ICE, LAND, LAND, LIFEPOD, NOTHING],
    [NOTHING, NOTHING, LAND, LAND, LAND, LAND, LIFEPOD, LAND, LAND, LAND, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, LAND, LAND, LAND, LAND, LAND, LAND, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
  ];
  protected $sides = [
    [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1],
    [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1],
    [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1],
    [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1],
    [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1],
    [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1],
    [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1],
    [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1],
    [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1],
    [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1],
    [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1],
  ];

  public function getUiData()
  {
    $data = parent::getUiData();
    $data['sides'] = $this->sides;
    return $data;
  }

  public function __construct($player)
  {
    $this->name = clienttranslate('Tartarus');
    $this->desc = clienttranslate(
      'Tiles may not be placed covering the chasm. Rovers may not move across the chasm with orthogonal movement.'
    );
    parent::__construct($player);
  }

  public function isValidPlacementOption($tile, $cells, $pos, $rotation, $flipped)
  {
    $touchingLeft = false;
    $touchingRight = false;
    foreach ($cells as $cell) {
      if ($this->sides[$cell['y']][$cell['x']] == 0) {
        $touchingLeft = true;
      } else {
        $touchingRight = true;
      }
    }

    return $touchingLeft xor $touchingRight;
  }

  public function getPossibleMovesFrom($cell)
  {
    // Side of cell
    $side = $this->sides[$cell['y']][$cell['x']];
    // Check diagonal moves
    $otherCorner = null;
    if ($this->areSameCell($cell, ['x' => 5, 'y' => 3])) {
      $otherCorner = ['x' => 6, 'y' => 4];
    }
    if ($this->areSameCell($cell, ['x' => 6, 'y' => 4])) {
      $otherCorner = ['x' => 5, 'y' => 3];
    }
    if ($this->areSameCell($cell, ['x' => 5, 'y' => 9])) {
      $otherCorner = ['x' => 6, 'y' => 8];
    }
    if ($this->areSameCell($cell, ['x' => 6, 'y' => 8])) {
      $otherCorner = ['x' => 5, 'y' => 9];
    }

    $cells = parent::getPossibleMovesFrom($cell);
    // Must move on same side
    Utils::filter($cells, function ($c) use ($side, $otherCorner) {
      return $this->sides[$c['y']][$c['x']] == $side || (!is_null($otherCorner) && $this->areSameCell($c, $otherCorner));
    });

    // It shouldnt prevent teleport rover civ terrain for corpo
    if ($this->player->corporation()->canUse(TECH_REPUBLIC_TELEPORT_ROVER_CIV_TERRAIN) && $this->getTypeAtPos($cell) == CIV) {
      $cells = array_merge($this->getCellsOfType(CIV), $cells);
    }

    return $cells;
  }
}
