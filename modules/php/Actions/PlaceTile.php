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

class PlaceTile extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_PLACE_TILE;
  }

  public function isDoable($player)
  {
    return $this->getPlayableTiles($player, true);
  }

  public function getPossibleTiles($player)
  {
    $tiles = [];
    $tiles[] = Tiles::getTopOf('interior-0')->first();
    $tiles[] = Tiles::getTopOf('exterior-0')->first();
    return $tiles;
  }

  public function getPlayableTiles($player, $checkIsDoable = false, $forcedTiles = null)
  {
    $tiles = [];
    foreach ($forcedTiles ?? $this->getPossibleTiles($player) as $tile) {
      $placementOptions = $player->planet()->getPlacementOptions($tile->getType(), $checkIsDoable);
      if (!empty($placementOptions)) {
        if ($checkIsDoable) {
          return true;
        }
        $tiles[$tile->getId()] = $placementOptions;
      }
    }

    return $checkIsDoable ? false : $tiles;
  }

  public function argsPlaceTile()
  {
    $player = $this->getPlayer();

    return [
      'tiles' => $this->getPlayableTiles($player, false),
    ];
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
