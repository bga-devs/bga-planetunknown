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
use PU\Helpers\FlowConvertor;
use PU\Helpers\Utils;

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
    'position' => ['position', 'int']
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
    return $this->corporation();
  }

  public function canUseplanet($planetId) //TO ASK 
  {
    if ($this->getplanetId() != $planetId) {
      return false;
    }
    return $this->planet()->canUseEffect();
  }

  public function getUiData($currentPlayerId = null)
  {
    $data = parent::getUiData();
    $current = $this->id == $currentPlayerId;
    //TODO
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
}
