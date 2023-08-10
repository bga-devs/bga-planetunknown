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
    'type' => 'type',
    'pId' => 'player_id',
    'x' => ['x', 'int'],
    'y' => ['y', 'int'],
    'rotation' => ['rotation', 'int'],
    'flipped' => ['flipped', 'int'],
  ];

  public function isFlipped()
  {
    return $this->flipped == 1;
  }

  public function getData()
  {
    return Tiles::getStaticDataFromType($this->getType());
  }
}
