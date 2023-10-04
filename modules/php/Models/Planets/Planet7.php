<?php

namespace PU\Models\Planets;
use PU\Helpers\Utils;

const DIRECTIONS = [['x' => -1, 'y' => 0], ['x' => 0, 'y' => -1], ['x' => 1, 'y' => 0], ['x' => 0, 'y' => 1]];

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
  protected $holes = [
    ['y' => 2, 'x' => 5],
    ['y' => 2, 'x' => 6],

    ['y' => 3, 'x' => 4],
    ['y' => 3, 'x' => 7],

    ['y' => 4, 'x' => 4],
    ['y' => 4, 'x' => 5],

    ['y' => 5, 'x' => 9],
    ['y' => 5, 'x' => 10],

    ['y' => 6, 'x' => 2],
    ['y' => 6, 'x' => 6],

    ['y' => 7, 'x' => 3],
    ['y' => 7, 'x' => 10],

    ['y' => 8, 'x' => 3],
    ['y' => 8, 'x' => 7],

    ['y' => 9, 'x' => 9],
  ];

  public function __construct($player)
  {
    $this->name = clienttranslate('Lacuna');
    $this->desc = clienttranslate('You may not place tiles covering the holes of the planet.');
    parent::__construct($player);
  }

  public function getBorderCells()
  {
    if (!isset($this->_borderCells)) {
      $grid = self::createGrid(0);
      $cells = [];

      foreach (self::getListOfCells() as $cell) {
        // Count neighbours, including holes
        $n = 0;
        foreach (DIRECTIONS as $dir) {
          $newCell = [
            'x' => $cell['x'] + $dir['x'],
            'y' => $cell['y'] + $dir['y'],
          ];
          if ($this->isCellValid($newCell) || in_array($newCell, $this->holes)) {
            $n++;
          }
        }

        if ($n < 4) {
          $cells[] = $cell;
        }
      }
      $this->_borderCells = $cells;
    }

    return $this->_borderCells;
  }
}
