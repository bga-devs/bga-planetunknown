<?php

namespace PU\Models;

use PU\Managers\Tiles;

/*
 * Tile
 */

class Tile extends \PU\Helpers\DB_Model
{
  protected $table = 'tiles';
  protected $primary = 'tile_id';
  protected $attributes = [
    'id' => ['tile_id', 'int'],
    'location' => 'tile_location',
    'state' => ['tile_state', 'int'],
    'type' => ['type', 'int'],
    'pId' => 'player_id',
    'x' => ['x', 'int'],
    'y' => ['y', 'int'],
    'rotation' => ['rotation', 'int'],
    'flipped' => ['flipped', 'int'],
  ];


  static function getStaticDataFromId($id)
  {
    $shape = Tiles::$shapes[$id % 12];
    $tileFamily = intdiv($id, 24);
    $hasMeteor = (intdiv($id, 12) % 2 == 0);
    $data = [];

    [$baseX, $baseY] = explode('_', $shape['pattern'][0]);

    foreach ($shape['pattern'] as $index => $coord) {
      [$x, $y] = explode('_', $coord);
      $data[] = [
        'x' => $x - $baseX,
        'y' => $y - $baseY,
        'type' => Tiles::$typesNames[$tileFamily][$shape['types'][$index]],
        'meteor' => ($hasMeteor && $coord == $shape['meteorPlace']),
        'symbol' => in_array($coord, $shape['symbolPlaces'])
      ];
    }

    return $data;
  }

  public function getData()
  {
    $shape = Tiles::$shapes[$this->getId() % 12];
    $tileFamily = intdiv($this->getId(), 24);
    $hasMeteor = (intdiv($this->getId(), 12) % 2 == 0);
    $data = [];

    [$baseX, $baseY] = explode('_', $shape['pattern'][0]);

    foreach ($shape['pattern'] as $index => $coord) {
      [$x, $y] = explode('_', $coord);
      $data[] = [
        'x' => $x - $baseX,
        'y' => $y - $baseY,
        'type' => Tiles::$typesNames[$tileFamily][$shape['types'][$index]],
        'meteor' => ($hasMeteor && $coord == $shape['meteorPlace']),
        'symbol' => in_array($coord, $shape['symbolPlaces'])
      ];
    }

    return $data;
  }
}
