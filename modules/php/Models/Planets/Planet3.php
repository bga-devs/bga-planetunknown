<?php

namespace PU\Models\Planets;

class Planet3 extends \PU\Models\Planet
{
  protected $id = '3';
  protected $columnMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $rowMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $level = 1;
  protected $terrains = [
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, LAND, LIFEPOD, LAND, LAND, LAND, LAND, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, LAND, ICE, ICE, ICE, LAND, LAND, ICE, LIFEPOD, NOTHING, NOTHING],
    [NOTHING, LAND, LAND, LAND, LIFEPOD, ICE, LAND, LAND, ICE, ICE, LAND, NOTHING],
    [NOTHING, LAND, LAND, LAND, ICE, ICE, ICE, ICE, ICE, LIFEPOD, LAND, NOTHING],
    [ICE, LAND, LAND, LAND, ICE, HOLE, HOLE, ICE, LAND, LAND, LAND, LAND],
    [ICE, LAND, LAND, LAND, ICE, HOLE, HOLE, ICE, LAND, LAND, LAND, LAND],
    [NOTHING, LAND, LAND, LAND, ICE, ICE, ICE, ICE, ICE, ICE, LAND, NOTHING],
    [NOTHING, LAND, LAND, ICE, ICE, LIFEPOD, LAND, LIFEPOD, ICE, ICE, LAND, NOTHING],
    [NOTHING, NOTHING, LAND, ICE, LAND, LAND, LAND, LAND, LAND, LAND, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, LAND, LAND, LAND, LAND, LAND, LAND, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, ICE, ICE, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
  ];

  public function __construct($player)
  {
    $this->name = clienttranslate('Charybdis');
    $this->desc = clienttranslate('First tile placement must cover one of the central four squares.');
    parent::__construct($player);
  }
}
