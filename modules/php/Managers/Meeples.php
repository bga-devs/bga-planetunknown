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
  protected static $maxIndex = 0;

  protected static function cast($meeple)
  {
    return new \PU\Models\Meeple($meeple);
  }
  public static function getUiData()
  {
    return self::getAll()->where('location', ['corporation', 'planet'])->toArray();
  }

  public static function getOfPlayer($player, $type = null)
  {
    return $type
      ? static::getAll()
      ->where('pId', $player->getId())
      ->where('type', $type)
      : static::getAll()->where('pId', $player->getId());
  }

  public static function destroyCoveredMeeples($player, $tile)
  {
    $planet = $player->planet();
    $toDestroy = new Collection();
    foreach ($planet->getTileCoveredCells($tile, false) as $i => $cell) {
      // $planet->grid[$cell['x']][$cell['y']]['tile'] = $tile;
      $toDestroy = $toDestroy->merge(
        static::getAll()
          ->where('type', [LIFEPOD, ROVER_MEEPLE])
          ->where('pId', $player->getId())
          ->where('x', $cell['x'])
          ->where('y', $cell['y'])
      );
    }
    static::move($toDestroy->getIds(), 'trash');

    $destroyedRover = $toDestroy->where('type', ROVER_MEEPLE);
    $destroyedLifepod = $toDestroy->where('type', LIFEPOD);

    return [$destroyedRover, $destroyedLifepod];
  }

  public static function addMeteor($player, $pos)
  {
    $meteor = self::getFiltered($player->getId(), 'box', METEOR)->first();

    // TODO : REMOVE => LEGACY CODE
    if (is_null($meteor)) {
      return static::singleCreate([
        'type' => METEOR,
        'location' => 'planet',
        'player_id' => $player->getId(),
        'x' => $pos['x'],
        'y' => $pos['y'],
      ]);
    }

    $meteor->setLocation('planet');
    $meteor->setX($pos['x']);
    $meteor->setY($pos['y']);
    return $meteor;
  }

  public static function add($type, $players)
  {
    $toCreate = [];
    foreach ($players as $playerId => $player) {
      $toCreate[] = [
        'type' => $type,
        'location' => 'corporation',
        'player_id' => $playerId,
      ];
    }
    return static::create($toCreate);
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
      foreach (ALL_TYPES as $value) {
        $data[] = [
          'type' => $value,
          'location' => 'corporation',
          'player_id' => $pId,
          'x' => $value,
          'y' => 0,
        ];
      }

      $data[] = [
        'type' => METEOR,
        'location' => 'box',
        'player_id' => $pId,
        'nbr' => 50,
      ];
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
        'y' => $lifepodCoord['y'],
      ];
    }

    //create rovers
    $corporation = $player->corporation();

    for ($i = 0; $i < $corporation->getRoverNb(); $i++) {
      $data[] = [
        'type' => ROVER_MEEPLE,
        'location' => 'corporation',
        'player_id' => $pId,
        'x' => '',
        'y' => '',
      ];
    }

    // create flux if needed
    if ($corporation->getId() == FLUX) {
      $choices = Globals::getSetupChoices();
      $track = $choices[$pId]['flux'];
      $data[] = [
        'type' => FLUX_MEEPLE,
        'location' => 'corporation',
        'player_id' => $pId,
        'x' => $track,
        'y' => 'flux',
      ];
    }

    return static::create($data);
  }
}
