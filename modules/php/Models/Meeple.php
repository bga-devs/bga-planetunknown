<?php
namespace PU\Models;

/*
 * Meeple
 */

class Meeple extends \PU\Helpers\DB_Model
{
  protected $table = 'meeples';
  protected $primary = 'meeple_id';
  protected $attributes = [
    'id' => ['meeple_id', 'int'],
    'location' => ['meeple_location', 'int'],
    'state' => ['meeple_state', 'int'],
    'pId' => 'player_id',
    'x' => 'x',
    'y' => 'y',
  ];
}
