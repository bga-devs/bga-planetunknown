<?php

namespace PU\Actions;

use PU\Managers\Meeples;
use PU\Managers\Players;
use PU\Managers\Tiles;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\Stats;
use PU\Helpers\Utils;
use PU\Helpers\FlowConvertor;
use PU\Managers\Susan;
use PU\Models\Meeple;
use PU\Models\Planet;

class MoveRover extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_MOVE_ROVER;
  }

  public function isDoable($player)
  {
    return $player->hasRoverOnPlanet() && $this->getPossibleSpaceIds($player);
  }

  public function isOptional()
  {
    return true;
  }

  public function getTeleport()
  {
    return $this->getCtxArg('teleport');
  }

  public function getDescription()
  {
    return [
      'log' => clienttranslate('Move your rover (${remaining})'),
      'args' => [
        'remaining' => $this->getCtxArg('remaining')
      ],
    ];
  }

  public function getPossibleSpaceIds($player)
  {
    return $player->getPossibleMovesByRover($this->getTeleport());
  }

  public function argsMoveRover()
  {
    $player = $this->getPlayer();

    return [
      'player' => $player,
      'spaceIds' => $this->getPossibleSpaceIds($player),
      'remaining' => $this->getCtxArg('remaining'),
      'currentRoverId' => $this->getCtxArg('currentRoverId') ?? ""
    ];
  }

  public function actMoveRover($roverId, $spaceId)
  {
    $player = $this->getPlayer();
    $args = $this->argsMoveRover();
    if (!in_array($spaceId, $args['spaceIds'][$roverId])) {
      throw new \BgaVisibleSystemException('You cannot move your Rover here. Should not happen');
    }

    $cell = Planet::getCellFromId($spaceId);

    $rover = Meeples::get($roverId);
    $meteor = null;

    //horizon group move meteor with rover
    if ($player->corporation()->getId() == HORIZON_GROUP) {
      $meteor = $player->planet()->getMeteorOnCell($rover->getCell());
      //can move meteor only if the destination has no meteor yet
      if (!is_null($meteor) && !$player->planet()->getMeteorOnCell($cell)) {
        $meteor->placeOnPlanet($cell);
      } else {
        $meteor = null; //(meteor has not been moved)
      }
    }

    // Move it on the board
    $rover->placeOnPlanet($cell);

    Notifications::moveRover($player, $rover, $meteor);

    //if a $meteor has been convoyed on water terrain, it's destroyed
    if ($meteor && $player->hasTech(TECH_DESTROY_METEORITE_ON_WATER) && $player->planet()->getVisible() == WATER) {
      $player->corporation->destroy($meteor);
    }

    //collect lifepod or meteor
    $action = $player->collectOnCell($cell);
    if ($action) {
      $this->pushParallelChild($action);
    }

    //if move is not ended add a new movement option
    if ($this->getCtxArg('remaining') > 1) {
      $cost = 1;
      if (
        $player->hasTech(TECH_FREE_MOVE_ON_ENERGY) &&
        ($player->planet->getVisible($cell) == ENERGY || $player->planet()->getVisible($cell) == ELECTRIC)
      ) {
        $cost = 0;
      }

      $this->pushParallelChild([
        'action' => MOVE_ROVER,
        'args' => [
          'remaining' => $this->getCtxArg('remaining') - $cost,
          'currentRoverId' => $roverId
        ]
      ]);
    }
  }
}
