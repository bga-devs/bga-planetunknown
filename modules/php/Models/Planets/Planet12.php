<?php

namespace PU\Models\Planets;

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
    [NOTHING, LAND, LAND, LAND, LAND, LAND, LAND, ICE, LAND, LAND, LAND, LIFEPOD],
    [NOTHING, NOTHING, LAND, LAND, LAND, LAND, LIFEPOD, LAND, LAND, LAND, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, LAND, LAND, LAND, LAND, LAND, LAND, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
  ];
  //TODO code Chasms

  public function __construct($player)
  {
    $this->name = clienttranslate('Tartarus');
    $this->desc = clienttranslate('Tiles may not be placed covering the chasm. Rovers may not move across the chasm with orthogonal movement.');
    parent::__construct($player);
  }
}
