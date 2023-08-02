<?php

namespace PU\Models\Corporations;

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

  public function moveTrack($type, $spaceId, $withBonus = true)
  {
    $pawn = $this->player->getTracker($type);
    $oldCoord = [
      'x' => $pawn->getX(),
      'y' => $pawn->getY(),
    ];
    $newCoord = static::getCoordFromSpaceId($spaceId);
    $pawn->setX($newCoord['x']);
    $pawn->setY($newCoord['y']);
    if ($withBonus) {
      $this->activateBonus($type, $oldCoord['y'], $newCoord['y']);
    }

    return $pawn;
  }

  public function activateBonus($type, $from, $to = null)
  {
    $to = $to ?? $from;

    $bonuses = [];
    for ($i = 1; $i <= abs($to - $from); $i++) {
      $index = $from < $to ? $from + $i : $from - $i;
      $bonus[] = $this->tracks[$type][$index];
    }

    foreach ($bonuses as $bonus) {
      switch ($bonus) {
        case CIV:
          $levelCiv = $this->getCivLevel();
          // TODO create action civ
          break;
        case BIOMASS:
          // TODO create action biomass
          break;
        case TECH:
          $levelTech = $this->getTechLevel();
          // TODO create action tech
          break;
        case SYNERGY:
          // TODO create move track
          break;
        case ROVER:
          // TODO create action rover
          break;
        default:
          //handle 'move_x' bonuses
          if (is_string($bonus) && str_starts_with($bonus, 'move')) {
            //TODO create action move with arg
          }
          break;
      }
    }
  }

  public function canMoveTrack($type, $n)
  {
    //for negative move be sure it's fully possible
    if ($n < 0) {
      return $this->getLevelOnTrack($type) + $n >= 0;
    } else {
      //for positive progression
      //rover can always been activated even on last level
      if ($type == ROVER) return true;
      else return !$this->isTrackerOnTop($type);
    }
  }

  /*
  * return the tracker level (how many step it's from start)
  * could be different than Y on corporation where progression is not linear
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
      if (is_int($this->tracks[$i])) {
        return $this->tracks[$i];
      }
    }
    return 0;
  }

  public function getCivLevel()
  {
    return $this->countLevel(CIV);
  }

  public function getTechLevel()
  {
    return $this->countLevel(TECH);
  }

  public function countLevel($track)
  {
    $result = 0;
    for ($i = $this->getLevelOnTrack($track); $i > 0; $i--) {
      if ($this->tracks[$track][$i] == $track) {
        $result++;
      }
    }
    return $result;
  }

  public function getNextSpace($type, $n = 1)
  {
    $trackPawn = $this->player->getTracker($type);

    //Y can't be lower than 0
    $nextSpaceY = max(0, $trackPawn->getY() + $n);

    //Y can't be higher than top of the track type
    $nextSpaceY = min(count($this->tracks[$type]) - 1, $nextSpaceY);

    return [$trackPawn->getX(), $nextSpaceY];
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
}
