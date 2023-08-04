<?php

namespace PU\Models\Planets;

class Planet10 extends \PU\Models\Planet
{
  protected $id = '10';
  protected $columnMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $rowMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $level = 3;
  protected $terrains = [
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, ICE, LAND, LAND, LAND, LAND, LAND, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, ICE, ICE, ICE, ICE, LAND, LAND, LAND, LAND, NOTHING, NOTHING],
    [NOTHING, LAND, ICE, LAND, LIFEPOD, ICE, LAND, LAND, LAND, LAND, LAND, NOTHING],
    [NOTHING, LAND, ICE, LAND, LAND, ICE, LAND, LAND, LAND, LAND, LIFEPOD, NOTHING],
    [LAND, LIFEPOD, ICE, LAND, LAND, ICE, ICE, ICE, ICE, ICE, ICE, ICE],
    [LAND, LAND, ICE, LAND, LAND, ICE, LAND, LAND, LAND, LAND, LAND, LAND],
    [NOTHING, LAND, ICE, LIFEPOD, ICE, ICE, LAND, LAND, LAND, LAND, LAND, NOTHING],
    [NOTHING, LAND, ICE, ICE, ICE, LAND, LAND, LAND, LAND, LAND, LAND, NOTHING],
    [NOTHING, NOTHING, LAND, ICE, LAND, LAND, LAND, LAND, LAND, LIFEPOD, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, ICE, LIFEPOD, LAND, LAND, LAND, LAND, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
  ];

  public function __construct($player)
  {
    $this->name = clienttranslate('Persephone');
    $this->desc = clienttranslate('Each quadrant has one restricted resource. Do not place a restricted resource in the quadrant.');
    parent::__construct($player);
  }
}
