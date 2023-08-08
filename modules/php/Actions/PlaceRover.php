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

class PlaceRover extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_PLACE_ROVER;
  }

  public function isDoable($player)
  {
    return $player->getAvailableRover() != null && $this->getPossibleSpaceIds($player);
  }

  public function getPossibleSpaceIds($player)
  {
    $lastTile = Tiles::getAll()
      ->where('id', $player->getLastTileId())
      ->first();

    //exclude spaceIds where there is already a Rover
    $possibleCells = array_filter(
      $player->planet()->getTileCoveredCells($lastTile, false),
      fn ($cell) => !$player
        ->planet()
        ->getMeepleOnCell($cell, ROVER)
        ->count()
    );

    return array_map(fn ($cell) => Planet::getCellId($cell), $possibleCells);
  }

  public function argsPlaceRover()
  {
    $player = $this->getPlayer();

    return [
      'spaceIds' => $this->getPossibleSpaceIds($player),
    ];
  }

  public function actPlaceRover($spaceId)
  {
    $player = $this->getPlayer();
    $args = $this->argsPlaceRover();
    if (!in_array($spaceId, $args['spaceIds'])) {
      throw new \BgaVisibleSystemException('You cannot place your Rover here. Should not happen');
    }

    $cell = Planet::getCellFromId($spaceId);

    $rover = $player->getAvailableRover();

    // Place it on the board
    $rover->placeOnPlanet($cell);

    Notifications::placeRover($player, $rover);

    //collect meteor
    $meteor = $player->getMeteorOnCell($cell);
    if (!is_null($meteor)) {
      $meteor->setLocation('board');
      //TODO caution, some planet/corporation are different
      Notifications::collectMeteor($player, $cell);
    }
  }
}
