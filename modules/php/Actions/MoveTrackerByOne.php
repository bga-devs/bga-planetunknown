<?php

namespace PU\Actions;

use PU\Managers\Meeples;
use PU\Managers\Players;
use PU\Managers\Tiles;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\Globals;
use PU\Core\Stats;
use PU\Helpers\Utils;
use PU\Helpers\FlowConvertor;
use PU\Managers\Susan;
use PU\Models\Corporations\Corporation;
use PU\Models\Planet;

class MoveTrackerByOne extends \PU\Models\Action
{
  public function getState()
  {
    return ST_MOVE_TRACKER_BY_ONE;
  }

  public function getN()
  {
    return $this->getCtxArg('n');
  }

  public function getMove()
  {
    return $this->getN() > 0 ? 1 : -1;
  }

  public function getType()
  {
    return $this->getCtxArg('type');
  }

  public function getWithBonus()
  {
    $withBonus = $this->getCtxArg('withBonus');
    return $withBonus;
  }

  public function getMoveId()
  {
    return $this->getCtxArg('moveId');
  }

  public function isDoable($player)
  {
    return count($player->corporation()->getNextSpaceIds($this->getType(), $this->getMove()));
  }

  public function isOptional()
  {
    return !$this->isDoable($this->getPlayer());
  }

  public function getDescription()
  {
    //adapt to backward
    $n = $this->getN();
    $direction = $n > 0 ? clienttranslate('forward') : clienttranslate('backward');

    return [
      'log' => \clienttranslate('Move ${type}${type_name} tracker ${direction}'),
      'args' => [
        'direction' => $direction,
        'type' => '',
        'type_name' => $this->getType(),
        'i18n' => ['type_name', 'direction'],
      ],
    ];
  }

  public function argsMoveTrackerByOne()
  {
    $player = $this->getPlayer();

    $type = $this->getType();

    $spaceIds = $player->corporation()->getNextSpaceIds($type, $this->getMove());

    return [
      'type_name' => $type,
      'type' => $type,
      'spaceIds' => $spaceIds,
      'withBonus' => $this->getWithBonus(),
    ];
  }

  public function stMoveTrackerByOne()
  {
    $args = $this->argsMoveTrackerByOne();
    if (count($args['spaceIds']) == 1) {
      return [$args['type'], $args['spaceIds'][0]];
    }
  }

  public function actMoveTrackerByOne($type, $spaceId)
  {
    $player = $this->getPlayer();

    $args = $this->argsMoveTrackerByOne();

    //TODO hack to avoid bug on virtual space id in rover track
    // if (in_array('rover_16', $args['spaceIds'])) {
    //   $args['spaceIds'] = 'rover_15';
    // }

    if ($type != $args['type']) {
      throw new \BgaVisibleSystemException('You can not move this tracker now. Should not happen ', $type);
    }

    if (!in_array($spaceId, $args['spaceIds'])) {
      throw new \BgaVisibleSystemException('You can not move this tracker here. Should not happen ' . $spaceId);
    }

    $bonuses = $player->corporation()->moveTrack($type, $spaceId, $this->getWithBonus());

    $this->createActionFromBonus($bonuses, $player);
  }
}
