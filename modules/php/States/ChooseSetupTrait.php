<?php

namespace PU\States;

use PU\Core\Globals;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\PGlobals;
use PU\Core\Stats;
use PU\Helpers\Log;
use PU\Managers\Players;
use PU\Managers\ActionCards;
use PU\Managers\Meeples;
use PU\Managers\Scores;
use PU\Managers\Actions;
use PU\Managers\Cards;
use PU\Managers\ZooCards;

trait ChooseSetupTrait
{
  public function argChooseSetup()
  {
    $choices = Globals::getSetupChoices();
    $private = [];

    foreach (Players::getAll() as $pId => $player) {
      $planetId = $player->getPlanetId();
      $corporationId = $player->getCorporationId();

      $private[$pId] = [
        'choice' => $choices[$pId] ?? null,
        'planets' => $planetId ? [0, $planetId] : [0],
        'corporations' => $corporationId ? [0, $corporationId] : [0],
        'POCards' => Cards::getInLocation('tochoose_obj')->where('pId', $pId),
      ];
    }

    return [
      '_private' => $private,
    ];
  }

  public function updateActivePlayersAndChangeState()
  {
    // Compute players that still need to select their card
    // => use that instead of BGA framework feature because in some rare case a player
    //    might become inactive eventhough the selection failed (seen in Agricola at least already)
    $selections = Globals::getSetupChoices();
    $players = Players::getAll();
    $ids = $players->getIds();
    $ids = array_diff($ids, array_keys($selections));

    // At least one player need to make a choice
    if (!empty($ids)) {
      $this->gamestate->setPlayersMultiactive($ids, 'end', true);
    }
    // Everyone is done => proceed
    else {
      $this->stConfirmSetup();
    }
  }

  public function actChooseSetup($planetId, $corporationId, $rejectedCardId = null, $flux = null, $pId = null)
  {
    // Sanity checks
    $this->gamestate->checkPossibleAction('actChooseSetup');
    $pId = $pId ?? Players::getCurrentId();

    //check that these planet/corporation/card were available to be choosen
    $args = $this->argChooseSetup()['_private'][$pId];
    if (!in_array($planetId, $args['planets'])) {
      throw new \BgaVisibleSystemException('You can\'t choose this planet. Should not happen');
    }
    if (!in_array($corporationId, $args['corporations'])) {
      throw new \BgaVisibleSystemException('You can\'t choose this corporation. Should not happen');
    }
    if (count($args['POCards']) > 0 && !array_key_exists($rejectedCardId, $args['POCards'])) {
      throw new \BgaVisibleSystemException('You have to reject a card that you have in hand!! Should not happen');
    }
    if ($corporationId == FLUX && !in_array($flux, ALL_TYPES)) {
      throw new \BgaVisibleSystemException('If you choose Flux Industries, you need to choose a flux track. ');
    }

    $choices = Globals::getSetupChoices();
    if (!isset($choices[$pId]) || !is_array($choices[$pId])) {
      $choices[$pId] = [];
    }
    $choices[$pId]['planetId'] = $planetId;
    $choices[$pId]['corporationId'] = $corporationId;
    $choices[$pId]['rejectedCardId'] = $rejectedCardId;
    $choices[$pId]['flux'] = $flux;

    Globals::setSetupChoices($choices);
    Notifications::chooseSetup(Players::get($pId), $this->argChooseSetup());
    $this->updateActivePlayersAndChangeState();
  }

  public function stConfirmSetup()
  {
    $choices = Globals::getSetupChoices();
    foreach (Players::getAll() as $pId => $player) {
      $choice = $choices[$pId] ?? null;
      if (is_null($choice)) {
        throw new \BgaVisibleSystemException('Someone hasnt made any choice yet. Should not happen');
      }

      if (!is_null($choice['rejectedCardId'])) {
        Cards::move($choice['rejectedCardId'], 'trash');
        $otherCards = Cards::getInLocation('tochoose_obj')->where('pId', $pId);
        Cards::move($otherCards->getIds(), 'hand_obj');
        Notifications::confirmSetupObjectives($pId, $otherCards);
      }
      $player->setCorporationId($choice['corporationId']);
      $player->setPlanetId($choice['planetId']);
    }

    $this->gamestate->nextState('done');
  }
}
