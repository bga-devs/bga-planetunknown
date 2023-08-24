<?php

namespace PU\Models\Planets;

class Planet8 extends \PU\Models\Planet
{
  protected $id = '8';
  protected $columnMedals = [3, 2, 2, 1, 1, 1, 1, 1, 1, 2, 2, 3];
  protected $rowMedals = [3, 3, 1, 1, 1, 0, 0, 1, 1, 1, 3, 3];
  protected $level = 3;
  protected $terrains = [
    //TODO probably wrong to determine edge cells
    [ICE, ICE, ICE, LAND, LAND, LAND, LAND, LAND, LAND, ICE, ICE, ICE],
    [ICE, ICE, ICE, LAND, LAND, LAND, LIFEPOD, LAND, LAND, ICE, ICE, ICE],
    [ICE, ICE, ICE, LAND, NOTHING, NOTHING, NOTHING, NOTHING, LAND, ICE, ICE, ICE],
    [LIFEPOD, LAND, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LIFEPOD, LAND, LAND],
    [LAND, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, LAND],
    [LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND],
    [LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND],
    [LAND, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, LAND],
    [LAND, LAND, LIFEPOD, LAND, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, LAND, LIFEPOD],
    [ICE, ICE, ICE, LAND, NOTHING, NOTHING, NOTHING, NOTHING, LAND, ICE, ICE, ICE],
    [ICE, ICE, ICE, LAND, LIFEPOD, LAND, LAND, LAND, LAND, ICE, ICE, ICE],
    [ICE, ICE, ICE, LAND, LAND, LAND, LAND, LAND, LAND, ICE, ICE, ICE],
  ];

  public function __construct($player)
  {
    $this->name = clienttranslate('Oblivion');
    $this->desc = clienttranslate('Ice has amassed in the corners');
    parent::__construct($player);
  }
}
