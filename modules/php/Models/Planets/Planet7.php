<?php

namespace PU\Models\Planets;

class Planet7 extends \PU\Models\Planet
{
  protected $id = '7';
  protected $columnMedals = [1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 1];
  protected $rowMedals = [1, 2, 2, 2, 2, 2, 2, 2, 2, 1, 1, 1];
  protected $level = 4;
  protected $terrains = [
    [NOTHING, NOTHING, NOTHING, NOTHING, LAND, ICE, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, LAND, ICE, ICE, ICE, LAND, LAND, LAND, LAND, NOTHING, NOTHING],
    [NOTHING, LAND, ICE, ICE, LAND, NOTHING, NOTHING, LAND, LAND, LAND, LAND, NOTHING],
    [NOTHING, LAND, ICE, LIFEPOD, NOTHING, LAND, LAND, NOTHING, LAND, LAND, LAND, NOTHING],
    [LAND, LAND, ICE, ICE, NOTHING, NOTHING, LAND, LIFEPOD, ICE, ICE, ICE, ICE],
    [LAND, LIFEPOD, LAND, ICE, ICE, LAND, LAND, LAND, ICE, NOTHING, NOTHING, LAND],
    [LAND, LAND, NOTHING, LAND, ICE, ICE, NOTHING, LAND, ICE, LAND, LIFEPOD, LAND],
    [LAND, LAND, LAND, NOTHING, LAND, ICE, ICE, ICE, ICE, LAND, NOTHING, LAND],
    [NOTHING, LAND, LAND, NOTHING, LAND, LAND, ICE, NOTHING, LIFEPOD, LAND, LAND, NOTHING],
    [NOTHING, NOTHING, NOTHING, LAND, LIFEPOD, LAND, ICE, LAND, LAND, NOTHING, LAND, NOTHING],
    [NOTHING, NOTHING, NOTHING, LAND, LAND, ICE, ICE, LAND, LAND, LAND, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, NOTHING, LAND, ICE, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING],
  ];

  public function __construct($player)
  {
    $this->name = clienttranslate('Lacuna');
    $this->desc = clienttranslate('You may not place tiles covering the holes of the planet.');
    parent::__construct($player);
  }
}
