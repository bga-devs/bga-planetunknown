<?php
namespace PU\Managers;
use PU\Core\Stats;
use PU\Core\Globals;
use PU\Helpers\UserException;
use PU\Helpers\Collection;

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

  /* Creation of various meeples */
  public static function setupNewGame($players, $options)
  {
  }
}
