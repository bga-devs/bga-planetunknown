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
    'location' => ['tile_location', 'int'],
    'state' => ['tile_state', 'int'],
    'pId' => 'player_id',
    'x' => 'x',
    'y' => 'y',
    'rotation' => ['rotation', 'int'],
    'flipped' => ['flipped', 'int'],
  ];


  static function getStaticDataFromId($id)
  {
    $tileType = $id % 12;
    $tileFamily = intdiv($id, 24);
    $hasMeteor = (intdiv($id, 12) % 2 == 0);
    $data = [];

    [$baseX, $baseY] = explode('_', Tiles::$initialPattern[$tileType][0]);

    foreach (Tiles::$initialPattern[$tileType] as $index => $coord) {
      [$x, $y] = explode('_', $coord);
      $data[] = [
        'x' => $x - $baseX,
        'y' => $y - $baseY,
        'type' => Tiles::$typesNames[$tileFamily][Tiles::$types[$tileType][$index]],
        'meteor' => ($hasMeteor && in_array($coord, Tiles::$meteorPlace[$tileType])),
        'symbol' => in_array($coord, Tiles::$symbolPlaces[$tileType])
      ];
    }

    return $data;
  }

  public function getData()
  {
    $tileType = $this->getId() % 12;
    $tileFamily = intdiv($this->getId(), 24);
    $hasMeteor = (intdiv($this->getId(), 12) % 2 == 0);
    $data = [];

    [$baseX, $baseY] = explode('_', Tiles::$initialPattern[$tileType][0]);

    foreach (Tiles::$initialPattern[$tileType] as $index => $coord) {
      [$x, $y] = explode('_', $coord);
      $data[] = [
        'x' => $x - $baseX, //TODO modify to handle rotation and flipping
        'y' => $y - $baseY,
        'type' => Tiles::$typesNames[$tileFamily][Tiles::$types[$tileType][$index]],
        'meteor' => ($hasMeteor && in_array($coord, Tiles::$meteorPlace[$tileType])),
        'symbol' => in_array($coord, Tiles::$symbolPlaces[$tileType])
      ];
    }

    return $data;
  }
}
