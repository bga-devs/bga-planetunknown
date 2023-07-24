<?php

namespace PU\Managers;

use PU\Core\Stats;
use PU\Core\Globals;
use PU\Helpers\UserException;
use PU\Helpers\Collection;

/* Class to manage all the tiles for PlanetUnknown */

class Tiles extends \PU\Helpers\CachedPieces
{
  protected static $table = 'tiles';
  protected static $prefix = 'tile_';
  protected static $customFields = ['type', 'player_id', 'x', 'y', 'rotation', 'flipped'];
  protected static $datas = null;
  protected static $autoremovePrefix = false;

  protected static function cast($meeple)
  {
    return new \PU\Models\Tile($meeple);
  }
  public static function getUiData()
  {
    $tiles = self::getInLocation('board');
    for ($j = 0; $j < 6; $j++) {
      $tile = self::getTopOf("interior-$j")->first();
      if (!is_null($tile)) {
        $tiles[] = $tile;
      }

      $tile = self::getTopOf("exterior-$j")->first();
      if (!is_null($tile)) {
        $tiles[] = $tile;
      }
    }

    return $tiles->toArray();
  }

  public static function getOfPlayer($pId)
  {
    return self::where('pId', $pId);
  }

  ////////////////////////////////////
  //  ____       _
  // / ___|  ___| |_ _   _ _ __
  // \___ \ / _ \ __| | | | '_ \
  //  ___) |  __/ |_| |_| | |_) |
  // |____/ \___|\__|\__,_| .__/
  //                      |_|
  ////////////////////////////////////

  /* Creation of various meeples */
  public static function setupNewGame($players, $options)
  {
    // $interior = [8, 3, 10, 4, 2, 6]; //TO ASK : CHECK IF IS NORMAL ORDER, DOESN'T SEEM
    // $exterior = [9, 5, 7, 1, 0, 11];
    $tiles = [];
    foreach (SMALL_RING as $j => $shapeId) {
      for ($i = 0; $i < 12; $i++) {
        $tiles[] = [
          'type' => $i * 12 + $shapeId,
          'location' => "interior-$j",
        ];
      }
    }
    foreach (LARGE_RING as $j => $shapeId) {
      for ($i = 0; $i < 12; $i++) {
        $tiles[] = [
          'type' => $i * 12 + $shapeId,
          'location' => "exterior-$j",
        ];
      }
    }

    self::create($tiles);
    for ($j = 0; $j < 6; $j++) {
      self::shuffle("interior-$j");
      self::shuffle("exterior-$j");
    }
  }

  /*
              █████               █████     ███                  █████            █████             
             ░░███               ░░███     ░░░                  ░░███            ░░███              
      █████  ███████    ██████   ███████   ████   ██████      ███████   ██████   ███████    ██████  
     ███░░  ░░░███░    ░░░░░███ ░░░███░   ░░███  ███░░███    ███░░███  ░░░░░███ ░░░███░    ░░░░░███ 
    ░░█████   ░███      ███████   ░███     ░███ ░███ ░░░    ░███ ░███   ███████   ░███      ███████ 
     ░░░░███  ░███ ███ ███░░███   ░███ ███ ░███ ░███  ███   ░███ ░███  ███░░███   ░███ ███ ███░░███ 
     ██████   ░░█████ ░░████████  ░░█████  █████░░██████    ░░████████░░████████  ░░█████ ░░████████
    ░░░░░░     ░░░░░   ░░░░░░░░    ░░░░░  ░░░░░  ░░░░░░      ░░░░░░░░  ░░░░░░░░    ░░░░░   ░░░░░░░░ 

  */

  public static $shapes = [
    0 => [
      'pattern' => ['2_1', '0_1', '1_1', '2_0', '3_0'],
      'meteorPlace' => '3_0',
      'symbolPlaces' => ['1_1', '2_0'],
      'types' => [0, 0, 0, 1, 1],
    ],
    1 => [
      'pattern' => ['1_1', '0_1', '2_1', '2_0', '1_2'],
      'meteorPlace' => '0_1',
      'symbolPlaces' => ['2_1', '1_2'],
      'types' => [0, 0, 1, 1, 0],
    ],
    2 => [
      'pattern' => ['0_1', '0_0', '0_2'],
      'meteorPlace' => '0_2',
      'symbolPlaces' => ['0_1', '0_0'],
      'types' => [0, 1, 0],
    ],
    3 => [
      'pattern' => ['1_0', '0_0', '1_1'],
      'meteorPlace' => '0_0',
      'symbolPlaces' => ['1_0', '1_1'],
      'types' => [0, 0, 1],
    ],
    4 => [
      'pattern' => ['1_0', '0_0', '1_1', '2_0'],
      'meteorPlace' => '1_1',
      'symbolPlaces' => ['0_0', '2_0'],
      'types' => [0, 1, 0, 0],
    ],
    5 => [
      'pattern' => ['1_0', '0_1', '1_1', '2_0'],
      'meteorPlace' => '1_1',
      'symbolPlaces' => ['1_0', '0_1'],
      'types' => [1, 0, 0, 1],
    ],
    6 => [
      'pattern' => ['1_1', '0_2', '1_0', '1_2'],
      'meteorPlace' => '0_2',
      'symbolPlaces' => ['1_1', '1_2'],
      'types' => [0, 1, 0, 1],
    ],
    7 => [
      'pattern' => ['1_1', '0_0', '0_1', '2_0', '2_1'],
      'meteorPlace' => '2_1',
      'symbolPlaces' => ['0_0', '2_0'],
      'types' => [0, 1, 1, 0, 0],
    ],
    8 => [
      'pattern' => ['0_0', '0_1'],
      'meteorPlace' => '0_1',
      'symbolPlaces' => ['0_0', '0_1'],
      'types' => [0, 1],
    ],
    9 => [
      'pattern' => ['1_0', '0_0', '2_0', '3_0'],
      'meteorPlace' => '3_0',
      'symbolPlaces' => ['0_0', '2_0'],
      'types' => [0, 0, 1, 1],
    ],
    10 => [
      'pattern' => ['0_0', '0_1', '1_0', '1_1'],
      'meteorPlace' => '1_0',
      'symbolPlaces' => ['0_0', '1_1'],
      'types' => [1, 0, 1, 0],
    ],
    11 => [
      'pattern' => ['1_1', '0_0', '0_1', '2_1', '2_2'],
      'meteorPlace' => '0_1',
      'symbolPlaces' => ['1_1', '0_0'],
      'types' => [0, 1, 1, 0, 0],
    ],
  ];

  public static $typesNames = [[CIV, ENERGY], [ENERGY, WATER], [ROVER, TECH], [TECH, BIOMASS], [WATER, ROVER], [BIOMASS, CIV]];
}
