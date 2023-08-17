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

  public function placeOnPlanet($cell)
  {
    $this->setLocation('planet');
    $this->setX($cell['x']);
    $this->setY($cell['y']);
  }

  public function destroy()
  {
    $this->setX('');
    $this->setY('');
    $this->setLocation('trash');
  }
}
