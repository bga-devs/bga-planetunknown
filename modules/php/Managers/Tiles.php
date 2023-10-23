<?php

namespace PU\Managers;

use PU\Core\Stats;
use PU\Core\Globals;
use PU\Helpers\UserException;
use PU\Helpers\Collection;
use PU\Models\Tile;

/* Class to manage all the tiles for PlanetUnknown */

class Tiles extends \PU\Helpers\CachedPieces
{
  protected static $table = 'tiles';
  protected static $prefix = 'tile_';
  protected static $customFields = ['type', 'player_id', 'x', 'y', 'rotation', 'flipped'];
  protected static $datas = null;
  protected static $autoremovePrefix = false;
  protected static $autoIncrement = true;
  protected static $maxIndex = 0;

  protected static function cast($meeple)
  {
    return new \PU\Models\Tile($meeple);
  }
  public static function getUiData()
  {
    return self::getInLocation('planet')
      ->merge(self::getInLocation('corporation'))
      ->merge(self::getSusan())
      ->toArray();
  }

  public static function getSusan()
  {
    $tiles = new Collection();
    for ($j = 0; $j < 6; $j++) {
      $tile = self::getTopOf("top-interior-$j")->first();
      if (!is_null($tile)) {
        $tiles[$tile->getId()] = $tile;
      }

      $tile = self::getTopOf("top-exterior-$j")->first();
      if (!is_null($tile)) {
        $tiles[$tile->getId()] = $tile;
      }
    }

    return $tiles;
  }

  public static function getOfPlayer($pId)
  {
    return self::where('pId', $pId)->filter(fn($tile) => $tile->getLocation() != 'box');
  }

  public static function getBiomassPatch($player)
  {
    $patch = self::getFiltered($player->getId(), 'box', BIOMASS_PATCH)->first();

    // TODO : REMOVE => LEGACY CODE
    if (is_null($patch)) {
      return static::singleCreate([
        'type' => BIOMASS_PATCH,
        'location' => 'corporation',
        'player_id' => $player->getId(),
        'x' => -1,
        'y' => -1,
        'rotation' => 0,
        'flipped' => 0,
      ]);
    }

    $patch->setLocation('corporation');
    $patch->setX(-1);
    $patch->setY(-1);
    return $patch;
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

    // Create biomass patches for each player
    foreach ($players as $pId => $player) {
      $tiles[] = [
        'type' => \BIOMASS_PATCH,
        'location' => 'box',
        'player_id' => $pId,
        'nbr' => 50,
      ];
    }

    self::create($tiles);
    for ($j = 0; $j < 6; $j++) {
      self::shuffle("interior-$j");
      self::shuffle("exterior-$j");
    }

    Susan::refill();
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

  static function getTypeFamily($type)
  {
    return static::$typesNames[intdiv($type, 24)];
  }

  static function getStaticDataFromType($type)
  {
    if ($type == BIOMASS_PATCH) {
      $shape = Tiles::$shapes[BIOMASS_PATCH];
      $tileFamily = 5; //hack to indicate BIOMASS
      $hasMeteor = false;
    } else {
      $shape = Tiles::$shapes[$type % 12];
      $tileFamily = intdiv($type, 24);
      $hasMeteor = intdiv($type, 12) % 2 == 1;
    }

    $data = [];

    [$baseX, $baseY] = explode('_', $shape['pattern'][0]);

    foreach ($shape['pattern'] as $index => $coord) {
      [$x, $y] = explode('_', $coord);
      $data[] = [
        'x' => $x - $baseX,
        'y' => $y - $baseY,
        'type' => self::$typesNames[$tileFamily][$shape['types'][$index]],
        'meteor' => $hasMeteor && $coord == $shape['meteorPlace'],
        'symbol' => in_array($coord, $shape['symbolPlaces']),
      ];
    }

    return $data;
  }

  public static $shapes = [
    TILE_N => [
      'pattern' => ['2_1', '0_1', '1_1', '2_0', '3_0'],
      'meteorPlace' => '3_0',
      'symbolPlaces' => ['1_1', '2_0'],
      'types' => [0, 0, 0, 1, 1],
    ],
    TILE_F => [
      'pattern' => ['1_1', '0_1', '2_1', '2_0', '1_2'],
      'meteorPlace' => '0_1',
      'symbolPlaces' => ['2_1', '1_2'],
      'types' => [0, 0, 1, 1, 0],
    ],
    TILE_I => [
      'pattern' => ['0_1', '0_0', '0_2'],
      'meteorPlace' => '0_2',
      'symbolPlaces' => ['0_1', '0_0'],
      'types' => [0, 1, 0],
    ],
    TILE_v => [
      'pattern' => ['1_0', '0_0', '1_1'],
      'meteorPlace' => '0_0',
      'symbolPlaces' => ['1_0', '1_1'],
      'types' => [0, 0, 1],
    ],
    TILE_t => [
      'pattern' => ['1_0', '0_0', '1_1', '2_0'],
      'meteorPlace' => '1_1',
      'symbolPlaces' => ['0_0', '2_0'],
      'types' => [0, 1, 0, 0],
    ],
    TILE_s => [
      'pattern' => ['1_0', '0_1', '1_1', '2_0'],
      'meteorPlace' => '1_1',
      'symbolPlaces' => ['1_0', '0_1'],
      'types' => [1, 0, 0, 1],
    ],
    TILE_L => [
      'pattern' => ['1_1', '0_2', '1_0', '1_2'],
      'meteorPlace' => '0_2',
      'symbolPlaces' => ['1_1', '1_2'],
      'types' => [0, 1, 0, 1],
    ],
    TILE_U => [
      'pattern' => ['1_1', '0_0', '0_1', '2_0', '2_1'],
      'meteorPlace' => '2_1',
      'symbolPlaces' => ['0_0', '2_0'],
      'types' => [0, 1, 1, 0, 0],
    ],
    TILE_i => [
      'pattern' => ['0_0', '0_1'],
      'meteorPlace' => '0_1',
      'symbolPlaces' => ['0_0', '0_1'],
      'types' => [0, 1],
    ],
    TILE_BIG_I => [
      'pattern' => ['1_0', '0_0', '2_0', '3_0'],
      'meteorPlace' => '3_0',
      'symbolPlaces' => ['0_0', '2_0'],
      'types' => [0, 0, 1, 1],
    ],
    TILE_O => [
      'pattern' => ['0_0', '0_1', '1_0', '1_1'],
      'meteorPlace' => '1_0',
      'symbolPlaces' => ['0_0', '1_1'],
      'types' => [1, 0, 1, 0],
    ],
    TILE_S => [
      'pattern' => ['1_1', '0_0', '0_1', '2_1', '2_2'],
      'meteorPlace' => '0_1',
      'symbolPlaces' => ['1_1', '0_0'],
      'types' => [0, 1, 1, 0, 0],
    ],
    BIOMASS_PATCH => [
      'pattern' => ['0_0'],
      'meteorPlace' => '',
      'symbolPlaces' => [],
      'types' => [0],
    ],
  ];

  public static $typesNames = [[CIV, ENERGY], [ENERGY, WATER], [ROVER, TECH], [TECH, BIOMASS], [WATER, ROVER], [BIOMASS, CIV]];
}
