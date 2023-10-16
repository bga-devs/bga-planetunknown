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

  public function getTerrainTypes()
  {
    $type = $this->getType();
    return $type == BIOMASS_PATCH ? [BIOMASS] : Tiles::getTypeFamily($type);
  }

  public function isBiomassPatch()
  {
    return $this->getType() == BIOMASS_PATCH;
  }

  public function getSymbolsForDiscardedTile()
  {
    $datas = $this->getData();
    $result = [];
    foreach ($datas as $cell) {
      if ($cell['symbol']) {
        $result[] = [
          'type' => $cell['type'],
        ];
      }
    }

    //remove ENERGY and replace it by the other symbol
    if ($result[0]['type'] == ENERGY) {
      $result[0]['type'] = $result[1]['type'];
    } elseif ($result[1]['type'] == ENERGY) {
      $result[1]['type'] = $result[0]['type'];
    }

    return $result;
  }
}
