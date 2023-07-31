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
    // TODO : adapt to backward
    // TODO : do we really want to increase by two or should it be one by one ??
    return [
      'log' => \clienttranslate('Move ${type} track ${n} space(s) forward'),
      'args' => [
        'n' => $this->getN(),
        'type' => $this->getStrType(),
        'i18n' => ['type'],
      ],
    ];
  }

  public function argsMoveTrack()
  {
    $player = $this->getPlayer();

    // TODO : compute next space

    return [
      'type' => $this->getStrType(),
    ];
  }

  public function stMoveTrack()
  {
  }

  public function actMoveTrack($spaceId)
  {
    self::checkAction('actMoveTrack');
    $player = $this->getPlayer();

    $this->resolveAction([$spaceId]);
  }
}
