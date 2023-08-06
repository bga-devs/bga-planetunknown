<?php

namespace PU\Models\Planets;

class Planet1 extends \PU\Models\Planet
{
  protected $id = '1';
  protected $columnMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $rowMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $level = 2;
  protected $terrains = [
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, LAND, ICE, ICE, ICE, LAND, LAND, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, LIFEPOD, LAND, LAND, LAND, LAND, LAND, LAND, LIFEPOD, NOTHING, NOTHING],
    [NOTHING, LAND, LAND, ICE, ICE, ICE, LAND, ICE, ICE, LAND, LAND, NOTHING],
    [NOTHING, LAND, LAND, LAND, LAND, LIFEPOD, LAND, ICE, LAND, LAND, LAND, NOTHING],
    [RING, RING, RING, RING, RING, RING, RING, RING, RING, RING, RING, RING],
    [RING, RING, RING, RING, RING, RING, RING, RING, RING, RING, RING, RING],
    [NOTHING, ICE, ICE, ICE, LAND, LAND, LIFEPOD, ICE, ICE, LAND, LAND, NOTHING],
    [NOTHING, LAND, LAND, LAND, ICE, LAND, LAND, ICE, ICE, LAND, LAND, NOTHING],
    [NOTHING, NOTHING, LIFEPOD, LAND, LAND, LAND, LAND, LAND, LAND, LIFEPOD, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, LAND, ICE, ICE, ICE, ICE, LAND, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
  ];

  public function __construct($player)
  {
    $this->name = clienttranslate('Arashi');
    $this->desc = clienttranslate('Every Tile placed onto the planet\'s ring must also be anchored outside the ring.');
    parent::__construct($player);
  }
}
