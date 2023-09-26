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
    if ($card->getEffectType() == IMMEDIATE) {
      $card->setLocation('playedCivCards');
    } else {
      $card->setLocation('hand_civ');
    }
  }

  public function activateCivCard($card)
  {
    $flow = $card->effect();
    if ($card->getEffectType() == IMMEDIATE) {
      return $flow;
    } else {
      $this->addEndOfGameAction($flow);
    }
  }

  public function countMatchingCard($criteria)
  {
    $cards = Cards::getAll()
      ->where('pId', $this->id)
      ->where('location', ['hand_obj', 'hand_civ']);

    $result = 0;
    foreach ($cards as $cardId => $card) {
      if ($card->$criteria) {
        $result++;
      }
    }
    return $result;
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

  public function getMeepleOnCell($cell, $type, $bool_planet = true)
  {
    return static::getMeeples($type)
      ->where('location', $bool_planet ? 'planet' : 'corporation')
      ->where('x', $cell['x'])
      ->where('y', $cell['y'])
      ->first();
  }

  public function hasLifepodOnTrack($x, $y)
  {
    return $this->getLifepodOnTrack()->count() > 0;
  }

  public function getLifepodOnTrack($x, $y)
  {
    return $this->getMeeples(LIFEPOD)
      ->where('location', 'corporation')
      ->where('x', $x)
      ->where('y', $y);
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

  public function getCollectedLifepod()
  {
    return $this->getMeeples(LIFEPOD)
      ->where('location', 'corporation')
      ->where('x', '')
      ->where('y', '');
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
          fn($cell) => $this->planet->getVisible($cell['x'], $cell['y']) != FORBIDDEN_TERRAINS[$contraint]
        );
      }

      $spaceIds[$roverId] = array_map(fn($cell) => Planet::getCellId($cell), $neighbours);
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
    $hand = $this->getHandCiv();
    $data['handCiv'] = $current ? $hand : Utils::filterPrivateDatas($hand);
    $data['handCivCount'] = $this->getHandCiv()->count();
    $data['playedCiv'] = $this->getPlayedCivCards();
    $data['playedCivCount'] = $this->getPlayedCivCards()->count();
    $data['handObj'] = $current ? $this->getHandObj() : [];
    return $data;
  }

  public function getHandCiv()
  {
    return Cards::getInLocation('hand_civ')->where('pId', $this->id);
  }

  public function getHandObj()
  {
    return Cards::getInLocation('hand_obj')->where('pId', $this->id);
  }

  public function getHand()
  {
    return $this->getHandCiv()->merge($this->getHandObj());
  }

  public function getPlayedCivCards()
  {
    return Cards::getInLocation('playedCivCards')->where('pId', $this->id);
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
    $isCurrent = $this->id == $currentPlayerId;
    $result = [];
    $total = 0;

    //count every full row and column
    $result['planet'] = [
      'entries' => $this->planet()->score(),
    ];

    //highest value for each tracker in tracks
    $result['tracks'] = [
      'entries' => $this->corporation()->scoreByTracks(),
    ];

    //Civ Cards and Private Objectives Cards
    $result['civ']['entries'] = [];
    $result['objectives']['entries'] = [];

    if ($isCurrent) {
      $privateCards = $this->getHand();
      foreach ($privateCards as $cardId => $privateCard) {
        if ($privateCard->getType() == 'civCard') {
          if ($privateCard->commerceAgreement) {
            continue;
          }
          $result['civ']['entries'][$privateCard->getType() . '_' . $cardId] = $privateCard->score();
        } elseif ($privateCard->getType() == 'POCard') {
          $result['objectives']['entries'][$privateCard->getType() . '_' . $cardId] = $privateCard->score();
        } else {
          die(var_dump($privateCard->getType()));
        }
      }
      //special for commerceAgreement
      $scoreCommerceAgreement = [0, 1, 3, 6, 10];
      $result['civ']['entries']['commerceAgreement'] = $scoreCommerceAgreement[$this->countMatchingCard('commerceAgreement')];
    }

    $NOCards = Cards::getInLocation('NOCards')
      ->where('pId', $this->id)
      ->merge(Cards::getInLocation('NOCards')->where('pId2', $this->id));

    foreach ($NOCards as $id => $NOcard) {
      $result['objectives']['entries'][$NOcard->getType() . '_' . $id] = $NOcard->score($this);
    }

    foreach ($result as $category => $entries) {
      $score = $this->reduce_entries($entries);
      $result[$category]['total'] = $score;
      $total += $score;
    }

    //lifepods = 1, METEOR = 1/3
    $scoreLifepods = $this->corporation()->scoreByLifepods();
    $result['lifepods']['total'] = $scoreLifepods;
    $total += $scoreLifepods;

    $scoreMeteors = $this->corporation()->scoreByMeteors();
    $result['meteors']['total'] = $scoreMeteors;
    $total += $scoreMeteors;

    $result['total'] = $total;

    if ($save) {
      $this->setScore($total);
      $this->setScoreAux(10000 - $this->planet()->countEmptySpaces() * 100 - $this->planet()->countMeteors());
    }

    return $result;
  }

  public static function reduce_entries($array)
  {
    return array_reduce($array['entries'], fn($sum, $item) => $sum + $item, 0);
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

  public function emptyEndOfGameActions()
  {
    PGlobals::setPendingActionsEndOfGame($this->id, []);
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

    if ($this->corporation()->getId() == FLUX) {
      //with FLUX corpo you can only use your last tech level
      return $this->corporation()->getId() == $corporation && $this->corporation()->getTechLevel() == $tech;
    } else {
      return $this->corporation()->getId() == $corporation && $this->corporation()->getTechLevel() >= $tech;
    }
  }

  public function collectOnCell($cell)
  {
    $flow = null;

    //collect meteor
    $meteor = $this->getMeteorOnCell($cell);

    //Corpo HOrizon Group need to be on Rover terrain to collect meteor
    if (
      !is_null($meteor) &&
      ($this->corporation()->getId() != HORIZON_GROUP || $this->planet()->getSymbolAtPos($cell) == ROVER)
    ) {
      $this->corporation()->collect($meteor);
      Notifications::collectMeeple($this, [$meteor], 'collect');

      if ($this->hasTech(TECH_GET_BIOMASS_COLLECTING_METEOR)) {
        $patchToPlace = $this->corporation()->receiveBiomassPatch();
        if ($patchToPlace) {
          $flow = Actions::getBiomassPatchFlow($patchToPlace->getId());
        }
      }
    }

    //collect lifepod
    $lifepod = $this->getLifepodOnCell($cell);
    if (!is_null($lifepod)) {
      $this->corporation()->collect($lifepod);
      Notifications::collectMeeple($this, [$lifepod], 'collect');
    }

    return $flow;
  }
}
