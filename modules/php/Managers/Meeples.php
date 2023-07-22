<?php

namespace PU\Managers;

use PU\Core\Stats;
use PU\Core\Globals;
use PU\Helpers\UserException;
use PU\Helpers\Collection;
use PU\Helpers\Utils;

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

  ////////////////////////////////////
  //  ____       _
  // / ___|  ___| |_ _   _ _ __
  // \___ \ / _ \ __| | | | '_ \
  //  ___) |  __/ |_| |_| | |_) |
  // |____/ \___|\__|\__,_| .__/
  //                      |_|
  ////////////////////////////////////

  // `meeple_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  // `meeple_location` varchar(32) NOT NULL,
  // `meeple_state` int(10),
  // `type` varchar(32),
  // `player_id` int(10) NULL,
  // `x` varchar(100) NULL,
  // `y` varchar(100) NULL,

  /* Creation of various meeples */
  public static function setupNewGame($players, $options)
  {
    $data = [];
    foreach ($players as $pId => $player) {
      //create lifepods
      $planet = Players::get($pId)->planet();

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

      //create trackers
      foreach ([CIV, WATER, BIOMASS, ROVER, TECH] as $value) {
        $data[] = [
          'type' => 'tracker_' . $value,
          'location' => 'corporation',
          'player_id' => $pId,
          'x' => $value,
          'y' => 0
        ];
      }
    }
    static::create($data);
  }
}
