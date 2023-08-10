<?php

namespace PU\Models\Corporations;

use PU\Core\Notifications;
use PU\Managers\Tiles;

use function PHPSTORM_META\type;

class Corporation
{
  protected $id;
  protected $tracks;

  public $name;
  public $desc;

  public $techBonuses;

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
      'tracks' => $this->tracks,
    ];
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
    $pawn->setY($coord['y']);

    Notifications::moveTrack($this->player, $oldCoord, $pawn);

    if ($withBonus) {
      return $this->getBonuses($coord);
    } else {
      return [];
    }
  }

  public function getBonuses($cell)
  {
    $bonuses = [];

    if (is_array($this->tracks[$cell['x']][$cell['y']])) {
      $bonuses = $this->tracks[$cell['x']][$cell['y']];
    } elseif ($this->tracks[$cell['x']][$cell['y']]) {
      $bonuses[] = $this->tracks[$cell['x']][$cell['y']];
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
      //rover can always been activated even on last level
      if ($type == ROVER) {
        return true;
      } else {
        return !$this->isTrackerOnTop($type);
      }
    }
  }

  /*
   * return the tracker level (how many step it's from start)
   * could be different than Y on corporations where progression is not linear
   */
  public function getLevelOnTrack($type)
  {
    return $this->player->getTracker($type)->getY();
  }

  public function isTrackerOnTop($type)
  {
    return count($this->tracks[$type]) == $this->getLevelOnTrack($type) + 1;
  }

  //return score on this track
  public function getBestMedal($type)
  {
    for ($i = $this->getLevelOnTrack($type); $i > 0; $i--) {
      if (is_int($this->tracks[$type][$i])) {
        return $this->tracks[$type][$i];
      }
    }
    return 0;
  }

  public function score()
  {
    $result = [];
    foreach (ALL_TYPES as $type) {
      $result['tracker_' . $type] = $this->getBestMedal($type);
    }
    return $result;
  }

  public function getCivLevel()
  {
    return $this->countLevel(CIV);
  }

  public function getTechLevel()
  {
    return $this->countLevel(TECH);
  }

  /**
   * count the milestones reached by a tracker
   * to be overriden for some corporations
   */
  public function countLevel($track)
  {
    $result = 0;
    for ($i = $this->getLevelOnTrack($track); $i > 0; $i--) {
      if ($this->isOrIn($track, $this->tracks[$track][$i])) {
        $result++;
      }
    }
    return $result;
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

  //should be overidden for certain tech level/corporations
  public function receiveBiomassPatch()
  {
    return Tiles::createBiomassPatch($this->player);
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
}
