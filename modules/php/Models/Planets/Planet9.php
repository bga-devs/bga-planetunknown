<?php

namespace PU\Models\Planets;

class Planet9 extends \PU\Models\Planet
{
  protected $id = '9';
  protected $columnMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $rowMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $level = 2;
  protected $terrains = [
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, ICE, ICE, LAND, LAND, LAND, LAND, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, LAND, ICE, LAND, LAND, LAND, ICE, ICE, LIFEPOD, NOTHING, NOTHING],
    [NOTHING, LAND, LIFEPOD, ICE, LAND, LAND, LAND, LAND, ICE, ICE, LAND, NOTHING],
    [NOTHING, ICE, LAND, LAND, ICE, ICE, LAND, LAND, LIFEPOD, LAND, LAND, NOTHING],
    [LAND, ICE, ICE, LAND, ICE, ICE, LAND, ICE, LAND, LAND, LAND, LAND],
    [LAND, LAND, ICE, LAND, LAND, LAND, LAND, ICE, LAND, LAND, LAND, LAND],
    [NOTHING, LAND, LIFEPOD, ICE, LAND, LAND, LAND, ICE, ICE, LAND, LAND, NOTHING],
    [NOTHING, LAND, LAND, ICE, LAND, LAND, LAND, LAND, LIFEPOD, LAND, LAND, NOTHING],
    [NOTHING, NOTHING, LAND, ICE, LAND, LAND, LAND, ICE, ICE, ICE, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, ICE, LAND, LAND, LIFEPOD, LAND, ICE, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
  ];

  public function __construct($player)
  {
    $this->name = clienttranslate('Pajitnov');
    $this->desc = clienttranslate('You must be able to slide your tiles into place.');
    parent::__construct($player);
  }
}
