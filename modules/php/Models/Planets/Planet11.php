<?php

namespace PU\Models\Planets;

class Planet11 extends \PU\Models\Planet
{

  protected $id = '11';
  protected $columnMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $rowMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $level = 2;
  protected $terrains = [
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, LAND, ELECTRIC, LAND, LAND, LAND, LAND, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, LAND, LIFEPOD, ELECTRIC, ELECTRIC, LAND, LAND, LIFEPOD, ELECTRIC, NOTHING, NOTHING],
    [NOTHING, LAND, LAND, LAND, LAND, ELECTRIC, LAND, ELECTRIC, ELECTRIC, ELECTRIC, LAND, NOTHING],
    [NOTHING, LAND, LAND, LAND, LAND, ELECTRIC, LAND, ELECTRIC, LAND, LAND, LAND, NOTHING],
    [LAND, LAND, ELECTRIC, ELECTRIC, ELECTRIC, ELECTRIC, LAND, ELECTRIC, ELECTRIC, ELECTRIC, LAND, LAND],
    [LAND, ELECTRIC, ELECTRIC, LAND, LIFEPOD, ELECTRIC, LAND, ELECTRIC, LIFEPOD, ELECTRIC, LAND, LAND],
    [NOTHING, ELECTRIC, LAND, LAND, ELECTRIC, ELECTRIC, ELECTRIC, ELECTRIC, ELECTRIC, ELECTRIC, LAND, NOTHING],
    [NOTHING, ELECTRIC, ELECTRIC, ELECTRIC, ELECTRIC, LAND, LAND, ELECTRIC, LAND, LAND, LAND, NOTHING],
    [NOTHING, NOTHING, LAND, LIFEPOD, LAND, LAND, LAND, ELECTRIC, ELECTRIC, LAND, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, LAND, LAND, LAND, LAND, LAND, ELECTRIC, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
  ];

  public function __construct($player)
  {
    $this->name = clienttranslate('Petra');
    $this->desc = clienttranslate('You may treat the electrical currents as energy terrain. There is no Ice.');
    parent::__construct($player);
  }

  public function getStartingLifePodsCoord()
  {
    $result = parent::getStartingLifePodsCoord();
    //add an extra lifepod (on ELECTRIC cell)
    $result[] = [
      'x' => 8,
      'y' => 9
    ];
    return $result;
  }
}
