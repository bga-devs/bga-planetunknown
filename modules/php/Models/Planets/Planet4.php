<?php

namespace PU\Models\Planets;

class Planet4 extends \PU\Models\Planet
{
  protected $id = '4';
  protected $columnMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $rowMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $level = 2;
  protected $terrains = [
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, CITY, LIFEPOD, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, ICE, LAND, LAND, LAND, LAND, ICE, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, CITY, LIFEPOD, ICE, LAND, LAND, ICE, LAND, CITY, NOTHING, NOTHING],
    [NOTHING, LAND, LAND, ICE, ICE, ICE, ICE, ICE, ICE, LAND, LAND, NOTHING],
    [NOTHING, LAND, LAND, ICE, LIFEPOD, LAND, LAND, LAND, ICE, LAND, LAND, NOTHING],
    [LAND, LAND, LAND, ICE, LAND, LAND, LAND, LAND, ICE, LAND, LAND, CITY],
    [CITY, LAND, LAND, ICE, LAND, LAND, LAND, LAND, ICE, LAND, LAND, LAND],
    [NOTHING, LAND, LAND, ICE, LAND, LAND, LAND, LIFEPOD, ICE, LAND, LAND, NOTHING],
    [NOTHING, LAND, LAND, ICE, ICE, ICE, ICE, ICE, ICE, LIFEPOD, LAND, NOTHING],
    [NOTHING, NOTHING, CITY, LAND, LAND, LAND, LAND, LAND, LAND, CITY, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, LAND, LAND, LAND, LAND, LAND, LAND, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LIFEPOD, CITY, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
  ];

  public function __construct($player)
  {
    $this->name = clienttranslate('Gaia');
    $this->desc = clienttranslate('Every city not covered by tiles scores two medals. Score complete rows and columns as normal.');
    parent::__construct($player);
  }
}
