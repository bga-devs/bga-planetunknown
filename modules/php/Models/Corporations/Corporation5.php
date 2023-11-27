<?php

namespace PU\Models\Corporations;

use PU\Managers\Meeples;

class Corporation5 extends Corporation
{
  public function __construct($player)
  {
    $this->name = clienttranslate('Makeshift');
    $this->desc = clienttranslate(
      'You may advance any tracker diagonally onto an adjacent track. Any tracker may claim any benefit. Tech Levels are locked if no tracker is currently unlocking the tech.'
    );

    $this->flagsToReset = [TECH_REGRESS_TRACKER];

    $this->techBonuses = [
      1 => [
        'text' => clienttranslate('Treat civ and tech tracks as adjacent.'),
      ],
      2 => [
        'text' => clienttranslate('+1 rover movement if more than one tracker occupies the rover track.'),
      ],
      3 => [
        'text' => clienttranslate('Shift a tracker laterally to an ajacent track instead of advancing.'),
      ],
      4 => [
        'text' => clienttranslate('Regress any one tracker. Once per round.'),
      ],
      5 => [
        'text' => clienttranslate('Score each tracker as the highest scoring tracker on the track it occupies.'),
      ],
    ];
    parent::__construct($player);
  }

  protected $id = '5';
  protected $tracks = [
    CIV => [null, null, 1, CIV, null, 2, CIV, null, SYNERGY, 3, CIV, SYNERGY, null, null, CIV, 5],
    WATER => [null, null, 1, SYNERGY, 2, null, SYNERGY, 3, SYNERGY, null, null, 4, SYNERGY, null, null, 5],
    BIOMASS => [null, null, SYNERGY, BIOMASS, null, 1, BIOMASS, null, 2, BIOMASS, SYNERGY, BIOMASS, 3, BIOMASS, null, 5],
    ROVER => [
      null,
      ROVER,
      'move_1',
      'move_1',
      'move_2',
      'move_2',
      ['move_2', ROVER],
      'move_2',
      ['move_2', SYNERGY],
      'move_2',
      ['move_2', 1],
      'move_2',
      ['move_2', 2],
      'move_3',
      'move_3',
      ['move_3', 5],
      ['move_3'],
    ],
    TECH => [null, null, SYNERGY, TECH, null, TECH, 1, TECH, null, null, TECH, null, SYNERGY, 2, TECH, 5],
  ];
  protected $level = 4;

  public function moveRoverBy($n)
  {
    if (!$this->canUse(TECH_PLUS_1_ROVER_IF_MULTIPLE_TRACKERS)) {
      return $n;
    }
    $nTrackersOnRover = 0;
    foreach (ALL_TYPES as $type) {
      $trackPawn = $this->player->getTracker($type);
      if ($trackPawn->getX() == ROVER) {
        $nTrackersOnRover++;
      }
    }
    if ($nTrackersOnRover > 1) {
      $n++;
    }

    return $n;
  }

  public function getAnytimeActions()
  {
    $actions = [];
    if ($this->canUse(TECH_REGRESS_TRACKER)) {
      $actions[] = [
        'action' => CHOOSE_TRACKS,
        'args' => [
          'types' => ALL_TYPES,
          'n' => 1,
          'move' => -1,
          'from' => clienttranslate('corporation tech'),
        ],
        'source' => $this->name,
        'flag' => TECH_REGRESS_TRACKER,
      ];
    }

    return $actions;
  }

  public function getNextSpaceIds($type, $n = 1)
  {
    $trackPawn = $this->player->getTracker($type);
    $directions = [[0, $n]];
    if ($n > 0) {
      $directions[] = [-1, $n];
      $directions[] = [1, $n];
      if ($this->player->hasTech(TECH_SHIFT_TRACKER)) {
        $directions[] = [-1, 0];
        $directions[] = [1, 0];
      }
    }

    $spaces = [];
    $baseX = array_search($trackPawn->getX(), array_keys($this->tracks));
    foreach ($directions as $dir) {
      $x = $baseX + $dir[0];
      $y = $trackPawn->getY() + $dir[1];

      // Must stay inside the range (unless tech is enabled)
      if ($x < 0 || $x > 4) {
        if ($this->player->hasTech(TECH_CIV_TECH_ADJACENT)) {
          $x = ($x + 5) % 5;
        } else {
          continue;
        }
      }
      $x = array_keys($this->tracks)[$x];

      // Must stay within the column
      if ($y < 0 || $y > count($this->tracks[$x]) - 1) {
        continue;
      }
      // Must be free
      $meeple = $this->player->getMeepleOnCell(['x' => $x, 'y' => $y], null, false);
      if (!is_null($meeple)) {
        continue;
      }
      //if spaceId is rover_16, rover_15 must be free too
      if ($y == 16 && $x == ROVER) {
        $meeple = $this->player->getMeepleOnCell(['x' => $x, 'y' => $y - 1], null, false);
        if (!is_null($meeple)) {
          continue;
        }
      }

      $spaces[] = $x . '_' . $y;
    }

    return $spaces;
  }

  public function canMoveTrack($type, $n)
  {
    //for negative move be sure it's fully possible
    if ($n < 0) {
      $tracker = $this->player->getTracker($type);
      if (is_null($tracker)) return false;
      //if it can regress
      if ($tracker->getY() + $n >= 0) {
        //check if there is nothing under it
        return Meeples::getOfPlayer($this->player)
          ->where('location', 'corporation')
          ->where('x', $tracker->getX())
          ->where('y', range($tracker->getY() - 1, $tracker->getY() + $n))
          ->count() == 0;
      } else {
        return false;
      }
    } else {
      //for positive progression
      return !empty($this->getNextSpaceIds($type, $n)) || $type == ROVER;
    }
  }

  public function getLevelOnTrack($type)
  {
    $tracker = $this->player->getTracker($type);
    if (is_null($tracker)) {
      return 0;
    }

    $y = 0;
    foreach (ALL_TYPES as $type2) {
      $trackPawn = $this->player->getTracker($type2);
      if ($trackPawn->getX() == $type) {
        $y = max($y, $trackPawn->getY());
      }
    }
    return $y;
  }

  public function getBestMedal($trackerType)
  {
    $trackPawn = $this->player->getTracker($trackerType);
    if (is_null($trackPawn)) {
      return 0;
    }

    $type = $trackPawn->getX();
    $lvl = $this->player->hasTech(TECH_SCORE_HIGHEST_TRACKER) ? $this->getLevelOnTrack($type) : $trackPawn->getY();
    for ($i = $lvl; $i > 0; $i--) {
      $medal = $this->extractMedal($this->tracks[$type][$i]);
      if ($medal !== false) {
        return $medal;
      }
    }
    return 0;
  }
}
