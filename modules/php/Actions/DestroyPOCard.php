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
use PU\Models\Meeple;
use PU\Models\Planet;

class DestroyPOCard extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_DESTROY_P_O_CARD;
  }

  public function argsDestroyPOCard()
  {
    $player = $this->getPlayer();

    $cards = Cards::getAll()
      ->where('location', 'hand_obj')
      ->where('pId', $player->getId());

    return [
      'cardIds' => $cards->getIds(),
    ];
  }

  public function actDestroyPOCard($cardId)
  {
    $player = $this->getPlayer();
    $args = $this->argsDestroyPOCard();
    if (!in_array($cardId, $args['cardIds'])) {
      throw new \BgaVisibleSystemException('You cannot discard this card ' . $cardId . '. Should not happen');
    }

    Cards::move($cardId, 'trash');
    Notifications::destroyCard($player, $cardId);
  }
}
