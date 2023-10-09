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

class TakeCivCard extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_TAKE_CIV_CARD;
  }

  public function isDoable($player)
  {
    return $this->getPossibleCards()->count() > 0;
  }

  public function getDescription()
  {
    return clienttranslate('Take one Civ Card');
  }

  public function getLevel()
  {
    return $this->getCtxArg('level');
  }

  public function getPossibleCards()
  {
    return Cards::getAll()->where('location', 'deck_civ_' . $this->getLevel());
  }

  public function argsTakeCivCard()
  {
    return [
      'cards' => $this->getPossibleCards(),
      'level' => $this->getLevel(),
    ];
  }

  public function actTakeCivCard($cardId)
  {
    $player = $this->getPlayer();
    $args = $this->argsTakeCivCard();

    if (!array_key_exists($cardId, $args['cards'])) {
      throw new \BgaVisibleSystemException("You cannot take this card ($cardId) from this deck. Should not happen");
    }

    // Place it on the player board or hand
    $card = $args['cards'][$cardId];
    $player->takeCivCard($card);
    Notifications::takeCivCard($player, $card, $args['level']);

    // Trigger potentiel effect
    $flow = $player->activateCivCard($card);

    if ($flow) {
      $this->insertAsChild($flow);
    }
  }
}
