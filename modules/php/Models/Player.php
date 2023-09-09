<?php

namespace PU\Models;

use PU\Core\Stats;
use PU\Core\Notifications;
use PU\Core\Preferences;
use PU\Managers\Actions;
use PU\Managers\Meeples;
use PU\Managers\Buildings;
use PU\Core\Globals;
use PU\Core\Engine;
use PU\Core\PGlobals;
use PU\Helpers\FlowConvertor;
use PU\Helpers\Utils;
use PU\Managers\Cards;

/*
 * Player: all utility functions concerning a player
 */

class Player extends \PU\Helpers\DB_Model
{
  private $planet = null;
  private $corporation = null;
  protected $table = 'player';
  protected $primary = 'player_id';
  protected $attributes = [
    'id' => ['player_id', 'int'],
    'no' => ['player_no', 'int'],
    'name' => 'player_name',
    'color' => 'player_color',
    'eliminated' => 'player_eliminated',
    'score' => ['player_score', 'int'],
    'scoreAux' => ['player_score_aux', 'int'],
    'zombie' => 'player_zombie',
    'planetId' => 'planet_id',
    'corporationId' => 'corporation_id',
    'position' => ['position', 'int'],
    'lastTileId' => ['last_tile_id', 'int'],
    'extraData' => ['extra_datas', 'obj'],
  ];

  // Cached attribute
  public function planet()
  {
    if ($this->planet == null) {
      $planetId = $this->getPlanetId();

      if (is_null($planetId) || $planetId == '') {
        return null;
      }
      $className = '\PU\Models\Planets\Planet' . $planetId;
      $this->planet = new $className($this);
    }
    return $this->planet;
  }

  // Cached attribute
  public function corporation()
  {
    if ($this->corporation == null) {
      $corporationId = $this->getCorporationId();

      if (is_null($corporationId) || $corporationId == '') {
        return null;
      }
      $className = '\PU\Models\Corporations\Corporation' . $corporationId;
      $this->corporation = new $className($this);
    }
    return $this->corporation;
  }

  public function takeCivCard($card)
  {
    $card->setPId($this->id);
    $flow = $card->effect();
    if ($card->getEffectType() == IMMEDIATE) {
      $card->setLocation('playedCivCards');
      return $flow;
    } else {
      $card->setLocation('hand');
      $this->addEndOfGameAction($flow);
    }
  }

  public function getMeeples($type)
  {
    return Meeples::getOfPlayer($this, $type);
  }

  public function getMeteorOnCell($cell)
  {
    return static::getMeepleOnCell($cell, METEOR);
  }

  public function getLifepodOnCell($cell)
  {
    return static::getMeepleOnCell($cell, LIFEPOD);
  }

  public function getRoverOnCell($cell)
  {
    return static::getMeepleOnCell($cell, ROVER_MEEPLE);
  }

  public function getMeepleOnCell($cell, $type)
  {
    return static::getMeeples($type)
      ->where('x', $cell['x'])
      ->where('y', $cell['y'])
      ->first();
  }

  public function getAvailableRover()
  {
    return $this->getMeeples(ROVER_MEEPLE)
      ->where('location', 'corporation')
      ->first();
  }

  public function hasRoverOnPlanet()
  {
    return count($this->getRoversOnPlanet()) > 0;
  }

  public function getRoversOnPlanet()
  {
    return $this->getMeeples(ROVER_MEEPLE)->where('location', 'planet');
  }

  public function hasMeteorOnPlanet()
  {
    return count($this->getMeteorsOnPlanet()) > 0;
  }

  public function getMeteorsOnPlanet()
  {
    return $this->getMeeples(METEOR)->where('location', 'planet');
  }

  public function getPossibleMovesByRover($teleport = null)
  {
    $rovers = $this->getRoversOnPlanet();

    $spaceIds = [];

    foreach ($rovers as $roverId => $rover) {
      if ($teleport == 'anywhere') {
        $neighbours = $this->planet()->getListOfCells();
      } else {
        $neighbours = $this->planet()->getPossibleMovesFrom(['x' => $rover->getX(), 'y' => $rover->getY()]);
      }

      //filter cells depending on turn special rule :
      $contraint = Globals::getTurnSpecialRule();
      if (in_array($contraint, FORBIDDEN_TERRAINS)) {
        $neighbours = array_filter(
          $neighbours,
          fn ($cell) => $this->planet->getTypeAtPos($cell) != FORBIDDEN_TERRAINS[$contraint]
        );
      }

      $spaceIds[$roverId] = array_map(fn ($cell) => Planet::getCellId($cell), $neighbours);
    }

    return $spaceIds;
  }

  public function getTracker($type)
  {
    return $this->getMeeples($type)->first();
  }

  public function getUiData($currentPlayerId = null)
  {
    $data = parent::getUiData();
    $current = $this->id == $currentPlayerId;
    $data['POCards'] = $current ? Cards::getInLocation('hand', $this->id) : Cards::countInLocation('hand', $this->id);
    $data['civCard'] = Cards::getInLocation('table', $this->id);
    return $data;
  }

  public function getPref($prefId)
  {
    return Preferences::get($this->id, $prefId);
  }

  public function getStat($name)
  {
    $name = 'get' . \ucfirst($name);
    return Stats::$name($this->id);
  }

  public function canTakeAction($action, $ctx)
  {
    return Actions::isDoable($action, $ctx, $this);
  }

  //calculate player score
  public function score($currentPlayerId = null, $save = true)
  {
    $current = $this->id == $currentPlayerId;
    $result = [];
    //count every full row and column
    $result['planet'] = [
      'entries' => $this->planet()->score(),
    ];
    $scorePlanet = $this->reduce_entries($result['planet']);
    $result['planet']['total'] = $scorePlanet;
    $total = $scorePlanet;

    //highest value for each tracker in tracks
    $result['tracks'] = [
      'entries' => $this->corporation()->scoreByTracks(),
    ];
    $scoreTracks = $this->reduce_entries($result['tracks']);
    $result['tracks']['total'] = $scoreTracks;
    $total += $scoreTracks;

    //lifepods = 1, METEOR = 1/3
    $scoreLifepods = $this->corporation()->scoreByLifepods();
    $result['lifepods']['total'] = $scoreLifepods;
    $total += $scoreLifepods;

    $scoreMeteors = $this->corporation()->scoreByMeteors();
    $result['meteors']['total'] = $scoreMeteors;
    $total += $scoreMeteors;

    //TODO CIV Cards
    $result['civ']['entries'] = [];
    $scoreCivs = $this->reduce_entries($result['civ']);
    $result['civ']['total'] = $scoreCivs;
    $total += $scoreCivs;

    //TODO POCards
    //TODO NOCards
    $result['objectives'] = [
      'entries' => [],
    ];
    $NOCards = Cards::getInLocation('NOCards')
      ->where('pId', $this->id)
      ->merge(Cards::getInLocation('NOCards')->where('pId2', $this->id));

    foreach ($NOCards as $id => $NOcard) {
      $newEntry = $NOcard->score($this);
      $result['objectives']['entries'] = array_merge($newEntry, $result['objectives']['entries']);
    }

    $scoreObjectives = $this->reduce_entries($result['objectives']);
    $result['objectives']['total'] = $scoreObjectives;
    $total += $scoreObjectives;

    $result['total'] = $total;

    if ($save) {
      $this->setScore($total);
      $this->setScoreAux(10000 - $this->planet()->countEmptySpaces() * 100 - $this->planet()->countMeteors());
    }

    return $result;
  }

  public static function reduce_entries($array)
  {
    return array_reduce($array['entries'], fn ($sum, $item) => $sum + $item, 0);
  }

  public function addEndOfTurnAction($flow)
  {
    $actions = PGlobals::getPendingActionsEndOfTurn($this->id);
    $actions[] = $flow;
    PGlobals::setPendingActionsEndOfTurn($this->id, $actions);
  }

  public function getEndOfTurnActions()
  {
    return PGlobals::getPendingActionsEndOfTurn($this->id);
  }

  public function emptyEndOfTurnActions()
  {
    PGlobals::setPendingActionsEndOfTurn($this->id, []);
  }

  public function addEndOfGameAction($flow)
  {
    if (!$flow) {
      return;
    } //useless to register a null flow

    $actions = PGlobals::getPendingActionsEndOfGame($this->id);
    $actions[] = $flow;
    PGlobals::setPendingActionsEndOfGame($this->id, $actions);
  }

  public function getEndOfGameActions()
  {
    return PGlobals::getPendingActionsEndOfGame($this->id);
  }

  /**
   * from a techId given returns if player has this tech
   * @param $techId String as 'tech_0_5' which means corporation0 tech 5
   *
   * @return bool
   */
  public function hasTech($techId)
  {
    [$_, $corporation, $tech] = explode('_', $techId);

    return $this->corporation()->getId() == $corporation && $this->corporation()->getTechLevel() >= $tech;
  }
}
