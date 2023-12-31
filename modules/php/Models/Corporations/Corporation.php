<?php

namespace PU\Models\Corporations;

use PU\Core\Notifications;
use PU\Core\PGlobals;
use PU\Managers\Meeples;
use PU\Managers\Tiles;

use function PHPSTORM_META\type;

class Corporation
{
  protected $id;
  protected $tracks;

  public $name;
  public $desc;

  public $techBonuses;
  protected $flagsToReset = [];

  // CONSTRUCT
  protected $player = null;
  protected $pId = null;
  public function __construct($player = null)
  {
    if (!is_null($player)) {
      $this->player = $player;
      $this->pId = $player->getId();
      // $this->fetchDatas();
    }
  }

  public function getUiData()
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'desc' => $this->desc,
      'techBonuses' => $this->techBonuses,
      'tracks' => $this->tracks,
    ];
  }

  public function getName()
  {
    return $this->name;
  }

  //to be overidden in case of special bonus increasing move
  public function moveTrackBy($type, $n)
  {
    return $n;
  }

  /**
   * register the new coord of a tracker
   * and returns an Array of subsequent actions
   */
  public function moveTrack($type, $spaceId, $withBonus = true)
  {
    $pawn = $this->player->getTracker($type);

    $coord = $this->getCoordFromSpaceId($spaceId);

    $oldCoord = [
      'x' => $pawn->getX(),
      'y' => $pawn->getY(),
    ];

    $pawn->setX($coord['x']);

    //if reach the top of rover track, regress by 1
    if ($type == ROVER && $coord['y'] == count($this->tracks[ROVER]) - 1) {
      $coord['y']--;
    }
    $pawn->setY($coord['y']);

    Notifications::moveTrack($this->player, $oldCoord, $pawn);

    if ($withBonus) {
      return $this->getBonuses($coord, $withBonus);
    } else {
      return [];
    }
  }

  public function getBonuses($cell, $withBonus = true)
  {
    $bonuses = [];

    if (is_array($this->tracks[$cell['x']][$cell['y']])) {
      foreach ($this->tracks[$cell['x']][$cell['y']] as $bonus) {
        if ($withBonus !== NO_SYNERGY || $bonus != SYNERGY) {
          $bonuses[] = $bonus;
        }
      }
    } elseif ($this->tracks[$cell['x']][$cell['y']]) {
      $bonus = $this->tracks[$cell['x']][$cell['y']];
      if ($withBonus !== NO_SYNERGY || $bonus != SYNERGY) {
        $bonuses[] = $bonus;
      }
    }

    return $bonuses;
  }

  public function canMoveTrack($type, $n)
  {
    //for negative move be sure it's fully possible
    if ($n < 0) {
      return $this->getLevelOnTrack($type) + $n >= 0;
    } else {
      //for positive progression
      return !$this->isTrackerOnTop($type) || $type == ROVER;
    }
  }

  /*
   * return the tracker level (how many step it's from start)
   * could be different than Y on corporations where progression is not linear
   */
  public function getLevelOnTrack($type)
  {
    $tracker = $this->player->getTracker($type);
    if (is_null($tracker)) {
      return 0;
    }
    // TODO : not working for corporation where trackers can move around
    return $tracker->getY();
  }

  // TODO to override for some corporations USELESS
  // public function setLevelOnTrack($type, $n, $onlyForward = true)
  // {
  //   if ($this->getLevelOnTrack($type) < $n && $onlyForward) {
  //     $pawn = $this->player->getTracker($type);
  //     $from = ['x' => $pawn->getX(), 'y' => $pawn->getY()];
  //     $pawn->setY($n);

  //     Notifications::moveTrack($this->player, $from, $pawn);
  //   }
  // }

  public function isTrackerOnTop($type)
  {
    $n = $type != ROVER ? 1 : 2;
    //if $type == ROVER top is 2 under the steps number (because the very last step is 'virtual')
    return count($this->tracks[$type]) == $this->getLevelOnTrack($type) + $n;
  }

  public function getMaxIndexOnTrack($type)
  {
    return count($this->tracks[$type]) - 1;
  }

  //return score on this track
  public function getBestMedal($type)
  {
    for ($i = $this->getLevelOnTrack($type); $i > 0; $i--) {
      $medal = $this->extractMedal($this->tracks[$type][$i]);
      if ($medal !== false) {
        return $medal;
      }
    }
    return 0;
  }

  public function extractMedal($trackCell)
  {
    if (is_int($trackCell)) {
      return $trackCell;
    } elseif (is_array($trackCell)) {
      foreach ($trackCell as $value) {
        if (is_int($value)) {
          return $value;
        }
      }
    }
    return false;
  }

  public function scoreByTracks()
  {
    $result = [];
    foreach (ALL_TYPES as $type) {
      $result['tracker_' . $type] = $this->getBestMedal($type);
    }
    return $result;
  }

  public function scoreByMeteors($current)
  {
    return floor($this->getNCollected(METEOR) / 3) * (1 + $this->player->countMatchingCard('meteorRepo', $current));
  }

  public function scoreByLifepods()
  {
    return $this->getNCollected(LIFEPOD);
  }

  public function getCivLevel($y = null)
  {
    return $this->countLevel(CIV, $y);
  }

  //receive $action when reach the mileston to add an action if needed
  public function getTechLevel($action = null)
  {
    return $this->countLevel(TECH);
  }

  public function hasTechLevel($techLvl)
  {
    return $this->getTechLevel() >= $techLvl;
  }

  /**
   * count the milestones reached by a tracker
   * to be overriden for some corporations
   */
  public function countLevel($track, $y = null)
  {
    $result = 0;
    for ($i = $y ?? $this->getLevelOnTrack($track); $i > 0; $i--) {
      if ($this->isOrIn($track, $this->tracks[$track][$i])) {
        $result++;
      }
    }
    return $result;
  }

  //to be overidden
  public function moveRoverBy($n)
  {
    return $n;
  }

  /**
   * Return an array of all cells this TYPE tracker can reach with a N move.
   * @return Array of CellIDs ('x_y')
   */
  public function getNextSpaceIds($type, $n = 1)
  {
    $trackPawn = $this->player->getTracker($type);

    //Y can't be lower than 0
    $nextSpaceY = max(0, $trackPawn->getY() + $n);

    //Y can't be higher than top of the track type
    $nextSpaceY = min(count($this->tracks[$type]) - 1, $nextSpaceY);

    return [$trackPawn->getX() . '_' . $nextSpaceY];
  }

  public function canPlaceBiomassPatchLater()
  {
    return false;
  }

  public function collect($meeple)
  {
    $meeple->setX('');
    $meeple->setY('');
    // $meeple->setPId($this->pId); useless, is already owned by player
    $meeple->setLocation('corporation');
  }

  public function destroy($meeple)
  {
    $meeple->setX('');
    $meeple->setY('');
    $meeple->setLocation('trash');
  }

  public function getNCollected($type)
  {
    return Meeples::getOfPlayer($this->player, $type)
      ->where('location', 'corporation')
      ->count();
  }

  public function getAnytimeActions()
  {
    return [];
  }

  public function addFlag($flag)
  {
    $pId = $this->player->getId();
    $flags = PGlobals::getFlags($pId);
    $flags[$flag] = true;
    PGlobals::setFlags($pId, $flags);
  }

  //unused for now
  public function addFlagValue($flag, $value)
  {
    $pId = $this->player->getId();
    $flags = PGlobals::getFlags($pId);
    $flags[$flag] = $value;
    PGlobals::setFlags($pId, $flags);
  }

  public function getFlagValue($flag)
  {
    $pId = $this->player->getId();
    $flags = PGlobals::getFlags($pId);
    return $flags[$flag] ?? 0;
  }

  public function resetFlags()
  {
    $flags = PGlobals::getFlags($this->pId);
    foreach ($this->flagsToReset as $flag) {
      unset($flags[$flag]);
    }
    PGlobals::setFlags($this->pId, $flags);
  }

  public function isFlagged($flag)
  {
    return PGlobals::getFlags($this->pId)[$flag] ?? false;
  }

  public function canUse($tech)
  {
    return $this->player->hasTech($tech) && !$this->isFlagged($tech);
  }

  /*
   *  █████  █████ ███████████ █████ █████        █████████
   * ░░███  ░░███ ░█░░░███░░░█░░███ ░░███        ███░░░░░███
   *  ░███   ░███ ░   ░███  ░  ░███  ░███       ░███    ░░░
   *  ░███   ░███     ░███     ░███  ░███       ░░█████████
   *  ░███   ░███     ░███     ░███  ░███        ░░░░░░░░███
   *  ░███   ░███     ░███     ░███  ░███      █ ███    ░███
   *  ░░████████      █████    █████ ███████████░░█████████
   *   ░░░░░░░░      ░░░░░    ░░░░░ ░░░░░░░░░░░  ░░░░░░░░░
   *
   *
   *
   */

  public static function getSpaceId($coord)
  {
    return $coord['x'] . '_' . $coord['y'];
  }

  public static function getCoordFromSpaceId($spaceId)
  {
    $coord = explode('_', $spaceId);
    return ['x' => $coord[0], 'y' => $coord[1]];
  }

  /**
   * as a tracker level can be an array or not, need to check both possibilities
   */
  protected function isOrIn($trackValue, $neededValue)
  {
    return $trackValue == $neededValue || (is_array($trackValue) && in_array($neededValue, $trackValue));
  }

  /*
         █████████  ██████████ ███████████ ███████████ ██████████ ███████████          
        ███░░░░░███░░███░░░░░█░█░░░███░░░█░█░░░███░░░█░░███░░░░░█░░███░░░░░███         
       ███     ░░░  ░███  █ ░ ░   ░███  ░ ░   ░███  ░  ░███  █ ░  ░███    ░███   █████ 
      ░███          ░██████       ░███        ░███     ░██████    ░██████████   ███░░  
      ░███    █████ ░███░░█       ░███        ░███     ░███░░█    ░███░░░░░███ ░░█████ 
      ░░███  ░░███  ░███ ░   █    ░███        ░███     ░███ ░   █ ░███    ░███  ░░░░███
       ░░█████████  ██████████    █████       █████    ██████████ █████   █████ ██████ 
        ░░░░░░░░░  ░░░░░░░░░░    ░░░░░       ░░░░░    ░░░░░░░░░░ ░░░░░   ░░░░░ ░░░░░░  
                                                                                       
                                                                                       
                                                                                       
  */

  public function getId()
  {
    return $this->id;
  }

  /**
   * Return the number of rover a player need for a game (from static data)
   */
  public function getRoverNb()
  {
    return count(
      array_filter($this->tracks[ROVER], function ($value) {
        return $this->isOrIn($value, ROVER);
      })
    );
  }
}
