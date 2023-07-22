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
    'location' => 'meeple_location',
    'state' => 'meeple_state',
    'type' => 'type',
    'pId' => 'player_id',
    'x' => 'x',
    'y' => 'y',
  ];
}
