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
    $interior = [8, 3, 10, 4, 2, 6];
    $exterior = [9, 5, 7, 1, 0, 11];
    $tiles = [];
    foreach ($interior as $j => $shapeId) {
      for ($i = 0; $i < 12; $i++) {
        $tiles[] = [
          'type' => $i * 12 + $shapeId,
          'location' => "interior-$j",
        ];
      }
    }
    foreach ($exterior as $j => $shapeId) {
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
}
