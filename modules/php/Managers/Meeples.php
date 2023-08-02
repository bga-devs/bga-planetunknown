<?php

namespace PU\Managers;

use PU\Core\Stats;
use PU\Core\Globals;
use PU\Helpers\UserException;
use PU\Helpers\Collection;
use PU\Helpers\Utils;
use PU\Models\Planet;

/* Class to manage all the meeples for PlanetUnknown */

class Meeples extends \PU\Helpers\CachedPieces
{
  protected static $table = 'meeples';
  protected static $prefix = 'meeple_';
  protected static $customFields = ['type', 'player_id', 'x', 'y'];
  protected static $datas = null;
  protected static $autoremovePrefix = false;

  protected static function cast($meeple)
  {
    return new \PU\Models\Meeple($meeple);
  }
  public static function getUiData()
  {
    return self::getAll()->toArray();
  }

  public static function getOfPlayer($player, $type = null)
  {
    return ($type)
      ? static::getSelectQuery()
      ->where('player_id', $player->getId())
      ->where('type', $type)
      ->get()
      : static::getSelectQuery()
      ->where('player_id', $player->getId())
      ->get();
  }

  public static function destroyCoveredMeeples($player, $tile)
  {
    $planet = $player->planet();
    $toDestroy = new Collection();
    foreach ($planet->getTileCoveredCells($tile, false) as $i => $cell) {
      // $planet->grid[$cell['x']][$cell['y']]['tile'] = $tile;
      $toDestroy = $toDestroy->merge(static::getSelectQuery()
        ->whereIn('type', [LIFEPOD, ROVER])
        ->where('player_id', $player->getId())
        ->where('x', $cell['x'])
        ->where('y', $cell['y'])
        ->get());
    }
    static::move($toDestroy->getIds(), 'trash');

    $destroyedRover = $toDestroy->where('type', ROVER);
    $destroyedLifepod = $toDestroy->where('type', LIFEPOD);

    return [$destroyedRover, $destroyedLifepod];
  }

  public static function addMeteor($player, $meteor)
  {
    return static::create([[
      'type' => METEOR,
      'location' => 'planet',
      'player_id' => $player->getId(),
      'x' => $meteor['x'],
      'y' => $meteor['y']
    ]]);
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
    $data = [];
    foreach ($players as $pId => $player) {
      //create trackers
      foreach ([CIV, WATER, BIOMASS, ROVER, TECH] as $value) {
        $data[] = [
          'type' => $value,
          'location' => 'corporation',
          'player_id' => $pId,
          'x' => $value,
          'y' => 0
        ];
      }
    }
    static::create($data);
  }

  /* Creation of lifepods after player has choosen his planet */
  public static function setupPlayer($pId)
  {
    $player = Players::get($pId);
    $data = [];
    //create lifepods
    $planet = $player->planet();

    $lifepodsCoords = $planet->getStartingLifePodsCoord();

    foreach ($lifepodsCoords as $lifepodCoord) {
      $data[] = [
        'type' => LIFEPOD,
        'location' => 'planet',
        'player_id' => $pId,
        'x' => $lifepodCoord['x'],
        'y' => $lifepodCoord['y']
      ];
    }

    //create rovers
    $corporation = $player->corporation();

    for ($i = 0; $i < $corporation->getRoverNb(); $i++) {
      $data[] = [
        'type' => ROVER,
        'location' => 'board',
        'player_id' => $pId,
        'x' => '',
        'y' => ''
      ];
    }

    static::create($data);
  }
}
