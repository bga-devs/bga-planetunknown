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
    $card->setState($this->id);
    if ($card->effectType == IMMEDIATE) {
      $card->setLocation('table');
      $card->effect();
    } else {
      $card->setLocation('hand');
    }
  }

  public function getMeeples($type)
  {
    return Meeples::getOfPlayer($this, $type);
  }

  public function getMeteorOnCell($cell)
  {
    return static::getMeeples(METEOR)
      ->where('x', $cell['x'])
      ->where('y', $cell['y'])
      ->first();
  }

  public function getRoverOnCell($cell)
  {
    return static::getMeeples(ROVER_MEEPLE)
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
    return $this->getMeeples(ROVER_MEEPLE)
      ->where('location', 'planet');
  }

  public function getPossibleMovesByRover()
  {
    $rovers = $this->getRoversOnPlanet();

    $spaceIds = [];

    foreach ($rovers as $roverId => $rover) {
      $neighbours = $this->planet()->getPossibleMovesFrom(['x' => $rover->getX(), 'y' => $rover->getY()]);
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
    [$detail, $score] = $this->score($current);
    $data['scores'] = [
      $this->id => [
        'detail' => $detail,
        'total' => $score
      ]
    ];
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
  public function score($isCurrent = true, $save = true)
  {
    $detail = [];
    //count every full row and column
    $detail = array_merge($detail, $this->planet()->score());

    //TODO highest value for each tracker in tracks
    $detail = array_merge($detail, $this->corporation()->score());

    //TODO lifepods = 1, METEOR = 1/3

    //TODO CIV Cards

    //TODO POCards

    //TODO NOCards
    $score = array_reduce($detail, fn ($sum, $item) => $sum + $item, 0);

    if ($save) {
      $this->setScore($score);
      $this->setScoreAux(10000 - $this->planet()->countEmptySpaces() * 100 - $this->planet()->countMeteors());
    }

    return [$detail, $score];
  }

  public function addEndOfTurnAction($flow)
  {
    $actions = PGlobals::getPendingActionsEndOfTurn($this->id);
    $actions[] = $flow;
    PGlobals::setPendingActionsEndOfTurn($this->id, $actions);
  }

  public function addEndOfGameAction($flow)
  {
    $actions = PGlobals::getPendingActionsEndOfGame($this->id);
    $actions[] = $flow;
    PGlobals::setPendingActionsEndOfGame($this->id, $actions);
  }
}
