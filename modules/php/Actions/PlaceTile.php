<?php
namespace PU\Actions;
use PU\Managers\Meeples;
use PU\Managers\Players;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\Stats;
use PU\Helpers\Utils;
use PU\Helpers\FlowConvertor;

class PlaceTile extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_PLACE_TILE;
  }

  public function argsPlaceTile()
  {
    $type = $this->getCtxArg('type');
    return [];
  }

  public function actPlaceTile($n)
  {
    $t = [
      0 => FOO_A,
      1 => FOO_B,
      2 => FOO_C,
    ];
    $action = $t[$n];

    $this->insertAsChild([
      'action' => $action,
    ]);

    Notifications::message('${player_name} places a tile', [
      'player' => Players::getCurrent(),
    ]);

    $this->resolveAction([$n]);
  }
}
