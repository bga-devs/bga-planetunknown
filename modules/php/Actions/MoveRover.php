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

  public function getTeleport()
  {
    return $this->getCtxArg('teleport');
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
      'remaining' => $this->getCtxArg('remaining')
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

    // Move it on the board
    $rover->placeOnPlanet($cell);

    Notifications::placeRover($player, $rover);

    //collect meteor
    $meteor = $player->getMeteorOnCell($cell);
    if (!is_null($meteor)) {
      $player->corporation()->collect($meteor);
      Notifications::collectMeeple($player, [$meteor], 'collect');
    }
  }
}
