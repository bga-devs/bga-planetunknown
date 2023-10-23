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

class ChooseObjectiveForAll extends \PU\Models\Action
{
  public function getState()
  {
    return ST_CHOOSE_OBJECTIVE_FOR_ALL;
  }

  public function isOptional()
  {
    return false;
  }

  public function isIrreversible($player = null)
  {
    return true;
  }

  public function getDescription()
  {
    return clienttranslate('Draw 3 objectives and keep one for all players');
  }

  public function stPreChooseObjectiveForAll()
  {
    // Ensure checkpoint
  }

  public function argsChooseObjectiveForAll()
  {
    $NOCards = Cards::getTopOf('deck_objectives', 3);

    return [
      'NOCards' => $NOCards,
    ];
  }

  public function actChooseObjectiveForAll($cardId)
  {
    $player = $this->getPlayer();
    $args = $this->argsChooseObjectiveForAll();
    if (!array_key_exists($cardId, $args['NOCards'])) {
      throw new BgaVisibleSystemException("You can\'t choose this objective card $cardId. Should not happen");
    }

    foreach ($args['NOCards'] as $cId => $card) {
      if ($cId == $cardId) {
        Cards::move($cId, 'NOCards');
        Notifications::newObjectiveCard($card, $player);
      } else {
        Cards::move($cId, 'trash');
      }
    }
  }
}
