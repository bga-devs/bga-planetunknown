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
use PU\Managers\Cards;
use PU\Managers\Susan;
use PU\Models\Corporations\Corporation;
use PU\Models\Planet;

class EmptySlot extends \PU\Models\Action
{
  public function getState()
  {
    return ST_EMPTY_SLOT;
  }

  public function isDoable($player)
  {
    return true;
  }

  public function getDescription()
  {
    return [
      'log' => \clienttranslate('Remove all tiles from one slot of the current depot'),
      'args' => [],
    ];
  }

  public function argsEmptySlot()
  {
    $player = $this->getPlayer();
    $slots = [];

    foreach (Susan::getDepotOfPlayer($player) as $key => $value) {
      $slots[] = $key . '-' . $value;
    }

    return [
      'slots' => $slots,
    ];
  }

  public function actEmptySlot($slotId)
  {
    $player = $this->getPlayer();

    Tiles::moveAllInLocation($slotId, 'trash');
    Tiles::moveAllInLocation('top-' . $slotId, 'trash');

    Notifications::emptySlot($player, $slotId);
  }
}
