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

class PlaceMeeple extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_PLACE_MEEPLE;
  }

  public function isDoable($player)
  {
    return $this->getPossibleSpaceIds($player);
  }

  public function isOptional()
  {
    $player = $this->getPlayer();
    if ($player->hasTech(TECH_NO_METEOR) && $this->getType() == METEOR) return true;
    return !$this->isDoable($player);
  }

  public function getConstraint()
  {
    return $this->getCtxArg('constraint');
  }

  public function getType()
  {
    return $this->getCtxArg('type');
  }

  public function getPossibleSpaceIds($player)
  {
    switch ($this->getConstraint()) {
      case 'emptyMeteorSymbol':
        $possibleCells = $player->planet()->getEmptyMeteorSymbolCells();
        break;

      default:
        $possibleCells = $player->planet()->getListOfCells();
        break;
    }

    return array_map(fn ($cell) => Planet::getCellId($cell), $possibleCells);
  }

  public function argsPlaceMeeple()
  {
    $player = $this->getPlayer();

    return [
      'spaceIds' => $this->getPossibleSpaceIds($player),
      'type' => $this->getType(),
      'meeple_type_name' => Meeples::getTranslatableType($this->getType()),
      'i18n' => ['meeple_type_name'],
    ];
  }

  public function actPlaceMeeple($spaceId, $meepleId = null)
  {
    $player = $this->getPlayer();
    $args = $this->argsPlaceMeeple();
    if (!in_array($spaceId, $args['spaceIds'])) {
      throw new \BgaVisibleSystemException('You cannot place your Meeple here. Should not happen');
    }

    $cell = Planet::getCellFromId($spaceId);

    switch ($args['type']) {
      case METEOR:
        $meeple = Meeples::addMeteor($player, $cell);
        Notifications::placeMeeple($player, $args['type'], $meeple);

        $destroyedRover = $player->getRoverOnCell($cell);
        if (!is_null($destroyedRover)) {
          Meeples::move($destroyedRover->getId(), 'trash');
          Notifications::destroyedMeeplesByMeteor($player, $destroyedRover, ROVER);
        }
    }
  }
}
