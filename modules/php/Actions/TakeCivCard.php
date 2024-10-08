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
    $lvl = $this->getLevel();
    return [
      'log' => $lvl == 'all' ? clienttranslate('Take ${n} civ card(s)') : clienttranslate('Take ${n} civ card of level ${lvl}'),
      'args' => [
        'n' => $this->getN(),
        'lvl' => $lvl,
      ],
    ];
  }

  public function getLevel()
  {
    return $this->getCtxArg('level');
  }

  public function getN()
  {
    return $this->getCtxArg('n') ?? 1;
  }

  public function getPossibleCards()
  {
    $cardLevel = $this->getLevel();

    if ($cardLevel == 'all') {
      $cards = Cards::getAll()->where('location', 'deck_civ%');
    } else {
      $cards = Cards::getAll()->where('location', 'deck_civ_' . $cardLevel);
      $player = $this->getPlayer();
      if ($player->hasTech(TECH_REPUBLIC_CAN_CHOOSE_UPGRADED_CIV_CARD) && $cardLevel < 4) {
        $cards = Cards::getTopOf('deck_civ_' . ($cardLevel + 1))->merge($cards);
      }
    }
    return $cards;
  }

  public function argsTakeCivCard()
  {
    $cards = $this->getPossibleCards();
    return [
      'cards' => $this->getPossibleCards(),
      'level' => $this->getLevel(),
      'n' => min($this->getN(), $cards->count()),
      'descSuffix' => $this->getLevel() == 'all' ? 'all' : '',
    ];
  }

  public function actTakeCivCard($cards)
  {
    $player = $this->getPlayer();
    $args = $this->argsTakeCivCard();

    if (!is_array($cards)) {
      $cards = [$cards];
    }

    if (count($cards) > $args['n']) {
      throw new \BgaVisibleSystemException("You cannot take so many civ cards. Should not happen");
    }

    foreach ($cards as $cardId) {
      $card = $args['cards'][$cardId] ?? null;
      if (!$card) {
        throw new \BgaVisibleSystemException("You cannot take this card ($cardId) from this deck. Should not happen");
      }

      // Place it on the player board or hand
      $player->takeCivCard($card);
      Notifications::takeCivCard($player, $card, $args['level']);

      // Trigger potentiel effect
      $flow = $player->activateCivCard($card);

      if ($flow) {
        $this->insertAsChild($flow);
      }
    }
  }
}
