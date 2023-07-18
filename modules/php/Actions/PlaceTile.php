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
    return [];
  }

  public function actPlaceTile()
  {
  }
}
