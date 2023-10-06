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

  public function score()
  {
    $score = parent::score();

    //cerberus [fromX, fromY, toX, toY]
    $c1 = [6, 0, 11, 5, 'Cerberus1'];
    $c2 = [0, 4, 6, 11, 'Cerberus2'];
    $c3 = [7, 7, 11, 11, 'Cerberus3'];
    $i = 0;
    $cerberus = [$c1, $c2, $c3];
    foreach ($cerberus as $i => $c) {
      $s = 2;
      for ($x = $c[0]; $x < $c[2]; $x++) {
        for ($y = $c[1]; $y < $c[3]; $y++) {
          if ($x == 6 && $y == 5 && $c[4] == 'Cerberus1') {
            continue;
          } // (only exception)
          if ($this->isPlanet($x, $y) && !$this->hasTileAtCoord($x, $y)) {
            $s = 0;
            break;
          }
        }
        if ($s == 0) {
          break;
        }
      }
      $score[$c[4]] = $s;
    }
    return $score;
  }
}
