<?php

namespace PU\Actions;

use PU\Managers\Meeples;
use PU\Managers\Players;
use PU\Managers\Tiles;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\Stats;
use PU\Helpers\Collection;
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
      'log' => $this->getCtxArg('description') ?? clienttranslate('Move your rover (${remaining})'),
      'args' => [
        'remaining' => $this->getCtxArg('remaining'),
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
      'currentRoverId' => $this->getCtxArg('currentRoverId') ?? '',
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

    $fromCell = $rover->getCell();

    $carried_meteor = null;

    //horizon group move meteor with rover
    if ($player->corporation()->getId() == HORIZON_GROUP) {
      $carried_meteor = $player->getMeteorOnCell($rover->getCell());
      //can move meteor only if the destination has no meteor yet
      if (!is_null($carried_meteor) && !$player->getMeteorOnCell($cell)) {
        $carried_meteor->placeOnPlanet($cell);
      } else {
        $carried_meteor = null; //(meteor has not been moved)
      }
    }

    // Move it on the board
    $rover->placeOnPlanet($cell);

    //if player has used TECH_TELEPORT_ROVER_SAME_TERRAIN_ONCE_PER_ROUND, flag it
    if (
      !in_array($cell, $player->planet()->getNeighbours($fromCell, $player->hasTech(TECH_ROVER_MOVE_DIAG)))
      && $this->getTeleport() != "anywhere" && $player->corporation()->canUse(TECH_TELEPORT_ROVER_SAME_TERRAIN_ONCE_PER_ROUND)
    ) {
      $player->corporation()->addFlag(TECH_TELEPORT_ROVER_SAME_TERRAIN_ONCE_PER_ROUND);
    }


    Notifications::moveRover($player, $rover, $carried_meteor);

    $additionalAction = null;
    //if a $carried_meteor has been convoyed on water terrain, it's destroyed
    if ($carried_meteor && $player->hasTech(TECH_DESTROY_METEORITE_ON_WATER) && $player->planet()->getVisibleAtPos($cell) == WATER) {
      // $player->corporation()->destroy($carried_meteor);
      // Notifications::collectMeeple($player, [$carried_meteor], 'destroy');
      $additionalAction = [
        'action' => COLLECT_MEEPLE,
        'args' => [
          'type' => METEOR,
          'action' => 'destroy',
          'n' => 1,
          'forcedMeeples' => $carried_meteor->getId()
        ],
        'optional' => true
      ];
    }

    //collect lifepod or meteor
    $action = $player->collectOnCell($cell);
    if ($action) {
      $this->pushParallelChild($action);
    }

    //if move is not ended add a new movement option
    $cost = 1;
    if ($player->hasTech(TECH_FREE_MOVE_ON_ENERGY)) {
      $visible = $player->planet()->getVisible($cell['x'], $cell['y']);
      if (in_array($visible, [ENERGY, ELECTRIC])) {
        $cost = 0;
      }
    }

    $left = $this->getCtxArg('remaining') - $cost;

    if ($left > 0) {
      if ($additionalAction) {
        $this->pushParallelChild([
          'type' => NODE_SEQ,
          'childs' => [
            $additionalAction,
            [
              'action' => MOVE_ROVER,
              'args' => [
                'remaining' => $left,
                'currentRoverId' => $roverId,
              ],
            ]
          ]
        ]);
      } else {
        $this->pushParallelChild([
          'action' => MOVE_ROVER,
          'args' => [
            'remaining' => $left,
            'currentRoverId' => $roverId,
          ],
        ]);
      }
    } else if ($additionalAction) {
      $this->pushParallelChild($additionalAction);
    }
  }
}
