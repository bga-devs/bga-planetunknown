<?php

namespace PU\States;

use PU\Core\Globals;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\Stats;
use PU\Helpers\Log;
use PU\Managers\Players;
use PU\Managers\ActionCards;
use PU\Managers\Meeples;
use PU\Managers\Scores;
use PU\Managers\Actions;
use PU\Managers\Cards;
use PU\Managers\Susan;
use PU\Managers\Tiles;
use PU\Managers\ZooCards;

trait EndGameTrait
{
  // Utility function that reveal CIV or objective cards
  public function revealCardsInHand($type)
  {
    // Reveal cards and update scores
    $fromLocation = $type == CIV ? 'hand_civ' : 'hand_obj';
    $cardIds = Cards::getInLocation($fromLocation)->getIds();
    if (empty($cardIds)) {
      return;
    }

    $toLocation = $type == CIV ? 'playedCivCards' : 'playedObjCards';
    Cards::move($cardIds, $toLocation);
    Notifications::revealCards($type);
    Notifications::scores();
  }

  public function stPreEndGameTurn()
  {
    $this->revealCardsInHand(CIV);
    $this->gamestate->nextState('');
  }

  public function stEndGameTurn()
  {
    $players = Players::getAll();
    Globals::setPhase(END_OF_GAME_PHASE);
    $flows = [];
    foreach ($players as $pId => $player) {
      $actions = $player->getEndOfGameActions();

      if ($actions) {
        $flows[$pId] = [
          'type' => NODE_PARALLEL,
          'childs' => $actions,
        ];
      }
    }

    Engine::multipleSetup($flows, ['method' => 'stPostEndGameTurn']);
  }

  public function stPostEndGameTurn()
  {
    Susan::refill();
    Globals::setPhase(NORMAL_PHASE);
    Notifications::endOfTurn();

    $players = Players::getAll();
    $newTurn = false;
    foreach ($players as $pId => $player) {
      $player->emptyEndOfGameActions();
      $newTurn = $newTurn || $player->getEndOfTurnActions();
    }

    // Game end if no player has gain an extra end of turn action
    if ($newTurn) {
      $this->gamestate->jumpToState(ST_PRE_CHOOSE_CIV_CARD);
    } else {
      $this->revealCardsInHand('Obj');
      $this->gamestate->jumpToState(ST_PRE_END_OF_GAME);
    }
  }

  function stPreEndOfGame()
  {
    Notifications::endOfGame();
    $this->gamestate->nextState('');
  }

  public function computeSoloScore()
  {
    $target = 60;
    $eventCardSet = Globals::getEventCardSet();

    $redScore = [0, 0, 0, -5, -5, -5, -5, -7, -7, -7, -7, -9, -9, -9, -9, -11, -11, -11, -11, -11, -11];
    $orangeScore = [0, 0, 0, -1, -1, -1, -1, -2, -2, -2, -2, -3, -3, -3, -3, -4, -4, -4, -4, -4, -4];
    $greenScore = [0, 0, 0, 3, 3, 3, 3, 6, 6, 6, 6, 9, 9, 9, 9, 12, 12, 12, 12, 12, 12];

    $target += $redScore[$eventCardSet[RED]];
    $target += $orangeScore[$eventCardSet[ORANGE]];
    $target += $greenScore[$eventCardSet[GREEN]];

    return $target;
  }

  public function getComment($deltaScore)
  {
    if ($deltaScore < -10) {
      return clienttranslate("Critical Fail. We're going to need another Planet.");
    } elseif ($deltaScore < -5) {
      return clienttranslate("Any sign of other survivors?");
    } elseif ($deltaScore < 0) {
      return clienttranslate("Low capacity but at least we survive.");
    } elseif ($deltaScore < 5) {
      return clienttranslate("Mission Complete and job well done.");
    } elseif ($deltaScore < 10) {
      return clienttranslate("Excellent Work, you've earned a promotion.");
    } elseif ($deltaScore < 15) {
      return clienttranslate("This might be the best planet we've seen yet.");
    } else {
      return clienttranslate("All stars have aligned because of you, Planeteer.");
    }
  }
}
