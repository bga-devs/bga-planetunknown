<?php
namespace PU\Models;

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
}
