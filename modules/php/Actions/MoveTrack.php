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
use PU\Models\Corporations\Corporation;
use PU\Models\Planet;

class MoveTrack extends \PU\Models\Action
{
  public function getState()
  {
    return ST_MOVE_TRACK;
  }

  public function getN()
  {
    return $this->getCtxArg('n');
  }

  public function getType()
  {
    return $this->getCtxArg('type');
  }

  public function getWithBonus()
  {
    return $this->getCtxArg('withBonus');
  }

  public function getStrType()
  {
    $names = [
      BIOMASS => \clienttranslate('Biomass'),
      TECH => \clienttranslate('Tech'),
      CIV => \clienttranslate('Civ'),
      WATER => \clienttranslate('Water'),
      ROVER => \clienttranslate('Rover'),
    ];
    return $names[$this->getType()];
  }

  public function getDescription()
  {
    //adapt to backward
    $n = $this->getN();
    $direction = ($n > 0) ? clienttranslate('forward') : clienttranslate('backward');

    return [
      'log' => \clienttranslate('Move ${type} track ${n} space(s) ${direction}'),
      'args' => [
        'n' => abs($n),
        'direction' => $direction,
        'type' => $this->getStrType(),
        'i18n' => ['type', 'direction'],
      ],
    ];
  }

  public function argsMoveTrack()
  {
    $player = $this->getPlayer();

    $type = $this->getType();
    $n = $this->getN();

    [$x, $y] = $player->corporation()->getNextSpace($type, $n);

    return [
      'strType' => $this->getStrType(),
      'type' => $type,
      'x' => $x,
      'y' => $y
    ];
  }

  public function stMoveTrack()
  {
    //TODO add flag $isAutomatic if needed
    $args = $this->argsMoveTrack();
    $this->actMoveTrack($args['type'], Corporation::getSpaceId($args));
  }

  public function actMoveTrack($type, $spaceId)
  {
    self::checkAction('actMoveTrack');
    $player = $this->getPlayer();

    $player->corporation->moveTrack($type, $spaceId, $this->getWithBonus());

    $this->resolveAction([$spaceId]);
  }
}
