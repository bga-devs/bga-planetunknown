<?php

namespace PU\Models\Planets;

class Planet2 extends \PU\Models\Planet
{
  protected $id = '2';
  protected $columnMedals = [1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 1];
  protected $rowMedals = [1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 1];
  protected $level = 3;
  protected $terrains = [
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, LAND, LAND, NOTHING],
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, LAND, ICE, NOTHING],
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LIFEPOD, ICE, ICE, ICE, LAND],
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, ICE, ICE, LAND, LAND, LAND],
    [NOTHING, NOTHING, LAND, LAND, LAND, NOTHING, NOTHING, ICE, LAND, LAND, LIFEPOD, NOTHING],
    [NOTHING, LAND, LIFEPOD, LAND, LAND, LAND, LAND, ICE, ICE, ICE, LAND, NOTHING],
    [LAND, LAND, LAND, LAND, LAND, LAND, ICE, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
    [LAND, LAND, ICE, ICE, ICE, ICE, ICE, NOTHING, LAND, LAND, ICE, NOTHING],
    [LAND, ICE, ICE, LAND, LAND, ICE, LIFEPOD, LAND, LAND, LAND, ICE, LAND],
    [LAND, ICE, LAND, LIFEPOD, LAND, ICE, ICE, ICE, ICE, ICE, ICE, LAND],
    [NOTHING, LAND, LAND, LAND, LAND, LAND, LAND, LAND, LIFEPOD, LAND, ICE, LAND],
    [NOTHING, NOTHING, LAND, LAND, LAND, LAND, NOTHING, NOTHING, LAND, LAND, LAND, NOTHING],
  ];

  public function __construct($player)
  {
    $this->name = clienttranslate('Cerberus');
    $this->desc = clienttranslate('Gain a bonus 2 medals for each planet your tiles cover completely.');
    parent::__construct($player);
  }
}
