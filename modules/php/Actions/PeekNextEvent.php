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

class PeekNextEvent extends \PU\Models\Action
{
  public function getState()
  {
    return ST_PEEK_NEXT_EVENT;
  }

  public function isDoable($player)
  {
    return true;
  }

  public function isIrreversible($player = null)
  {
    return true;
  }

  public function isAutomatic($player = null)
  {
    return true;
  }

  public function getDescription()
  {
    return [
      'log' => \clienttranslate('Peek at next event card'),
      'args' => [],
    ];
  }

  public function argsMoveTrackersToFive()
  {
    return [];
  }

  public function stMoveTrackersToFive()
  {
    return []; // Ensure the UI is not entering the state !!!
  }

  public function actMoveTrackersToFive()
  {
    $player = $this->getPlayer();

    $card = Cards::getTopOf('deck_event');

    Notifications::peekNextEvent($player, $card);
  }
}
