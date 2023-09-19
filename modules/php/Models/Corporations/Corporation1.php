<?php

namespace PU\Models\Corporations;

use PU\Core\Notifications;
use PU\Core\PGlobals;

class Corporation1 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Cosmos inc');
    $this->desc = clienttranslate('Choose to position a collected lifepod onto your advancement track or onto your scoring area. Your trackers skip over lifepods when advancing. Benefits and medals covered by lifepods are ignored.');


    $this->techBonuses = [
      1 => [
        'text' => clienttranslate('Your rover may move diagonally;')
      ],
      2 => [
        'text' => clienttranslate('Reposition three collected lifepods immediately. Once per game.')
      ],
      3 => [
        'text' => clienttranslate('Reposition a collected lifepod when you place an energy tile.')
      ],
      4 => [
        'text' => clienttranslate('Rovers moving onto energy terrain do not spend movements.')
      ],
      5 => [
        'text' => clienttranslate('Reposition one collected lifepod. Once per round.')
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '1';
  protected $tracks = [
    CIV => [null, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [null, null, 1, SYNERGY, 2, null, 3, SYNERGY, 4, null, 5, null, SYNERGY, 7, null, 10],
    BIOMASS => [null, null, SYNERGY, BIOMASS, null, 1, BIOMASS, null, 2, BIOMASS, SYNERGY, BIOMASS, 3, BIOMASS, null, 5],
    ROVER => [null, ROVER, 'move_1', 'move_1', 'move_2', 'move_2', 'move_2', 'move_2', ['move_2', 1], 'move_2', ['move_2', SYNERGY], 'move_2', ['move_2', 2], 'move_3', 'move_3', ['move_3', 5]],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5]
  ];
  protected $level = 2;

  /**
   * Return an array of all cells this TYPE tracker can reach with a N move.
   * @return Array of CellIDs ('x_y')
   */
  public function getNextSpaceIds($type, $n = 1)
  {
    $trackPawn = $this->player->getTracker($type);

    //Y can't be lower than 0
    $nextSpaceY = max(0, $trackPawn->getY() + $n);

    //if forward move, skip spaceId with lifepod
    if ($n > 0) {
      for ($i = $trackPawn->getY() + 1; $i <= $nextSpaceY; $i++) {
        if ($this->player->hasLifepodOnTrack($trackPawn->getX(), $i)) {
          $nextSpaceY++;
        }
      }
    }

    //Y can't be higher than top of the track type
    $nextSpaceY = min(count($this->tracks[$type]) - 1, $nextSpaceY);

    return [$trackPawn->getX() . '_' . $nextSpaceY];
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

    for ($i = $pawn->getY() + 1; $i < $coord['y']; $i++) {
      if ($lifepod = $this->player->getLifepodOnTrack($pawn->getX(), $i)) {
        $this->collect($lifepod);
        Notifications::collectMeeple($this->player, [$lifepod]);
      }
    }

    $pawn->setX($coord['x']);
    $pawn->setY($coord['y']);

    Notifications::moveTrack($this->player, $oldCoord, $pawn);

    if ($withBonus) {
      return $this->getBonuses($coord);
    } else {
      return [];
    }
  }

  /**
   * count the milestones reached by a tracker
   * specific for this corpo :
   * do not count milestone hidden under lifepod
   */
  public function countLevel($track)
  {
    $result = 0;
    for ($i = $this->getLevelOnTrack($track); $i > 0; $i--) {
      if ($this->isOrIn($track, $this->tracks[$track][$i]) && !$this->player->hasLifepodOnTrack($track, $i)) {
        $result++;
      }
    }
    return $result;
  }

  public function getTechLevel($action = null)
  {
    $result = $this->countLevel(TECH);

    if ($action && $result == 2 && !PGlobals::isTech2Used($this->player->getId())) {
      $action->pushParallelChild([
        'action' => POSITION_LIFEPOD_ON_TRACK,
        'args' => [
          'remaining' => 3
        ]
      ]);
      PGlobals::setTech2used($this->player->getId(), true);
    }

    return $result;
  }

  //return score on this track
  public function getBestMedal($type)
  {
    for ($i = $this->getLevelOnTrack($type); $i > 0; $i--) {
      if (is_int($this->tracks[$type][$i]) && !$this->player->hasLifepodOnTrack($type, $i)) {
        return $this->tracks[$type][$i];
      }
    }
    return 0;
  }

  public function addAutomaticActions(&$actions)
  {
    if ($this->player->hasTech(TECH_REPOSITION_ONE_LIFEPOD_EACH_TURN)) {
      $actions[] = [
        'action' => POSITION_LIFEPOD_ON_TRACK,
        'args' => [
          'remaining' => 1
        ]
      ];
    }
  }
}
