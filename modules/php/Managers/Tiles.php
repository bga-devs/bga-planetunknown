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
    return self::getAll()->toArray();
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

  public static $initialPattern = [
    0 => ['2_1', '0_1', '1_1', '2_0', '3_0'],
    1 => ['1_1', '0_1', '2_1', '2_0', '1_2'],
    2 => ['0_1', '0_0', '0_2'],
    3 => ['1_0', '0_0', '1_1'],
    4 => ['1_0', '0_0', '1_1', '2_0'],
    5 => ['1_0', '0_1', '1_1', '2_0'],
    6 => ['1_1', '0_2', '1_0', '1_2'],
    7 => ['1_1', '0_0', '0_1', '2_0', '2_1'],
    8 => ['0_0', '0_1'],
    9 => ['1_0', '0_0', '2_0', '3_0'],
    10 => ['0_0', '0_1', '1_0', '1_1'],
    11 => ['1_1', '0_0', '0_1', '2_1', '2_2'],
  ];

  public static $meteorPlace = [
    0 => ['3_0'],
    1 => ['0_1'],
    2 => ['0_2'],
    3 => ['0_0'],
    4 => ['1_1'],
    5 => ['1_1'],
    6 => ['0_2'],
    7 => ['2_1'],
    8 => ['0_1'],
    9 => ['3_0'],
    10 => ['1_0'],
    11 => ['0_1'],
  ];

  public static $symbolPlaces = [
    0 => ['1_1', '2_0'],
    1 => ['2_1', '1_2'],
    2 => ['0_1', '0_0'],
    3 => ['1_0', '1_1'],
    4 => ['0_0', '2_0'],
    5 => ['1_0', '0_1'],
    6 => ['1_1', '1_2'],
    7 => ['0_0', '2_0'],
    8 => ['0_0', '0_1'],
    9 => ['0_0', '2_0'],
    10 => ['0_0', '1_1'],
    11 => ['1_1', '0_0'],
  ];

  public static $types = [
    0 => [0, 0, 0, 1, 1],
    1 => [0, 0, 1, 1, 0],
    2 => [0, 1, 0],
    3 => [0, 0, 1],
    4 => [0, 1, 0, 0],
    5 => [1, 0, 0, 1],
    6 => [0, 1, 0, 1],
    7 => [0, 1, 1, 0, 0],
    8 => [0, 1],
    9 => [0, 0, 1, 1],
    10 => [1, 0, 1, 0],
    11 => [0, 1, 1, 0, 0],
  ];

  public static $typesNames = [
    [CIV, ENERGY],
    [ENERGY, WATER],
    [ROVER, TECH],
    [TECH, BIOMASS],
    [WATER, ROVER],
    [BIOMASS, CIV]
  ];
}
