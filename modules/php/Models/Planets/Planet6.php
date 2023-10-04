<?php

namespace PU\Models\Planets;
use PU\Helpers\Utils;

class Planet6 extends \PU\Models\Planet
{
  protected $id = '6';
  protected $columnMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $rowMedals = [1, 1, 1, 2, 2, 3, 3, 2, 2, 1, 1, 1];
  protected $level = 2;
  protected $terrains = [
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, TOXIC, LAND, LAND, LAND, ICE, ICE, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, TOXIC, TOXIC, TOXIC, LAND, LAND, LAND, ICE, LIFEPOD, NOTHING, NOTHING],
    [NOTHING, ICE, ICE, TOXIC, LIFEPOD, TOXIC, LAND, TOXIC, TOXIC, LAND, LAND, NOTHING],
    [NOTHING, ICE, LAND, LAND, TOXIC, LAND, TOXIC, TOXIC, LAND, LAND, LAND, NOTHING],
    [LIFEPOD, TOXIC, LAND, LAND, LAND, LAND, LAND, LAND, LAND, LAND, LAND, LIFEPOD],
    [LAND, TOXIC, LAND, ICE, ICE, ICE, LAND, LAND, ICE, ICE, ICE, LAND],
    [NOTHING, LAND, ICE, ICE, TOXIC, ICE, LAND, LAND, ICE, TOXIC, TOXIC, NOTHING],
    [NOTHING, LAND, ICE, TOXIC, ICE, ICE, LAND, LAND, ICE, TOXIC, LIFEPOD, NOTHING],
    [NOTHING, NOTHING, TOXIC, TOXIC, ICE, LAND, LAND, ICE, ICE, TOXIC, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, LAND, ICE, LIFEPOD, LAND, ICE, LAND, NOTHING, NOTHING, NOTHING],
    [NOTHING, NOTHING, NOTHING, NOTHING, NOTHING, LAND, LAND, NOTHING, NOTHING, NOTHING, NOTHING, NOTHING],
  ];

  public function __construct($player)
  {
    $this->name = clienttranslate('K\'Aax');
    $this->desc = clienttranslate(
      'Your rover may not move onto toxic terrain. You may place tiles over toxic terrain to neutralize it.'
    );
    parent::__construct($player);
  }

  //to be overriden
  public function getPossibleMovesFrom($cell)
  {
    $cells = parent::getPossibleMovesFrom($cell);
    //can't move on a toxic terrain
    Utils::filter($cells, function ($c) {
      return $this->getVisible($c['x'], $c['y']) != TOXIC;
    });

    return $cells;
  }
}
