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

  public function isDoable($player)
  {
    return $player->corporation()->canMoveTrack($this->getType(), $this->getN());
  }

  public function getDescription()
  {
    //adapt to backward
    $n = $this->getN();
    $direction = $n > 0 ? clienttranslate('forward') : clienttranslate('backward');

    return [
      'log' => \clienttranslate('Move ${type}${type_name} track ${n} space(s) ${direction}'),
      'args' => [
        'n' => abs($n),
        'direction' => $direction,
        'type' => '',
        'type_name' => $this->getType(),
        'i18n' => ['type_name', 'direction'],
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
      'type_name' => $type,
      'type' => $type,
      'x' => $x,
      'y' => $y,
    ];
  }

  public function stMoveTrack()
  {
    //TODO add flag $isAutomatic if needed
    $args = $this->argsMoveTrack();
    $this->actMoveTrack($args['type'], Corporation::getSpaceId($args), true);
    return true; // Ensure the UI is not entering the state !!!
  }

  public function actMoveTrack($type, $spaceId, $auto = false)
  {
    self::checkAction('actMoveTrack', $auto);
    $player = $this->getPlayer();

    [$pawn, $bonuses] = $player->corporation()->moveTrack($type, $spaceId, $this->getWithBonus());

    Notifications::moveTrack($player, $type, $this->getN(), $pawn);

    $this->createActionFromBonus($bonuses, $player);

    $this->resolveAction([$type, $spaceId]);
  }
}
