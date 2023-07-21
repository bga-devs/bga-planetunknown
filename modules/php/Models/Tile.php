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
    'location' => ['tile_location', 'int'],
    'state' => ['tile_state', 'int'],
    'pId' => 'player_id',
    'x' => 'x',
    'y' => 'y',
    'rotation' => ['rotation', 'int'],
    'flipped' => ['flipped', 'int'],
  ];
}
