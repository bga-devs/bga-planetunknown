<?php

namespace PU\Models\Planets;

class Planet5 extends \PU\Models\Planet
{
  protected $id = '5';
  protected $columnMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $rowMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $level = 3;
  protected $terrains = [
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, ICE, ICE, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, ICE, ICE, LIFEPOD, ICE, ICE, ICE, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, ICE, ICE, ICE, ICE, ICE, ICE, ICE, ICE, NOTHING, NOTHING],
    [NOTHING, ICE, ICE, ICE, ICE, ICE, ICE, LAND, LIFEPOD, ICE, ICE, NOTHING],
    [NOTHING, ICE, ICE, LIFEPOD, ICE, ICE, ICE, ICE, LAND, ICE, ICE, NOTHING],
    [ICE, ICE, LAND, LAND, ICE, ICE, ICE, ICE, ICE, ICE, ICE, ICE],
    [ICE, ICE, LIFEPOD, ICE, ICE, ICE, ICE, ICE, ICE, ICE, LAND, LIFEPOD],
    [NOTHING, ICE, ICE, ICE, ICE, ICE, ICE, ICE, ICE, ICE, LAND, NOTHING],
    [NOTHING, ICE, ICE, ICE, ICE, LAND, LAND, ICE, ICE, ICE, LAND, NOTHING],
    [NOTHING, NOTHING, ICE, ICE, ICE, LIFEPOD, LAND, ICE, ICE, ICE, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, ICE, ICE, ICE, ICE, ICE, ICE, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, ICE, ICE, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
  ];

  public function __construct($player)
  {
    $this->name = clienttranslate('K-273');
    $this->desc = clienttranslate('You may not place a tile that overlaps both planet terrain and planet ice.');
    parent::__construct($player);
  }

  public function isValidPlacementOption($tile, $cells)
  {
    $touchingIce = false;
    $touchingLand = false;
    foreach ($cells as $cell) {
      $terrain = $this->terrains[$cell['y']][$cell['x']];
      if ($terrain === ICE) {
        $touchingIce = true;
      } elseif ($terrain !== NOTHING) {
        $touchingLand = true;
      }
    }

    return $touchingLand xor $touchingIce;
  }
}
