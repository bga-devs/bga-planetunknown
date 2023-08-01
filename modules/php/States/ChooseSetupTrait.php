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
        'planet' => $planetId ? [0, $planetId] : [0],
        'corporation' => $corporationId ? [0, $corporationId] : [0],
        'POCards' => Cards::getInLocation('hand', $pId)->getIds(),
      ];
    }

    return [
      '_private' => $private,
    ];
  }

  public function stChooseSetup()
  {
    if (
      Globals::getPlanetOption() == OPTION_PLANET_A &&
      Globals::getCorporationOption() == OPTION_CORPORATION_UNIVERSAL &&
      !Globals::isPrivateObjectiveCardsGame()
    ) {
      $this->gamestate->setAllPlayersNonMultiactive('notNeeded');
    }
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
      $this->gamestate->nextState('end');
    }
  }

  public function actChooseSetup($planetId, $corporationId, $rejectedCardId = null, $pId = null)
  {
    $this->queryStandardTables();
    // Sanity checks
    $this->gamestate->checkPossibleAction('actChooseSetup');
    $pId = $pId ?? Players::getCurrentId();

    //check that these planet/corporation/card were available to be choosen
    $args = $this->argChooseSetup();
    if (!array_key_exists($planetId, $args['_private'][$pId]['planets'])) {
      throw new \BgaVisibleSystemException('You can\'t choose this planet. Should not happen');
    }
    if (!array_key_exists($corporationId, $args['_private'][$pId]['corporations'])) {
      throw new \BgaVisibleSystemException('You can\'t choose this corporation. Should not happen');
    }
    if (count($args['_private'][$pId]['POCards']) > 0 && !array_key_exists($rejectedCardId, $args['_private'][$pId]['POCards'])) {
      throw new \BgaVisibleSystemException('You have to reject a card that you have in hand!! Should not happen');
    }

    $choices = Globals::getSetupChoices();
    if (!is_array($choices[$pId])) {
      $choices[$pId] = [];
    }
    $choices[$pId]['planetId'] = $planetId;
    $choices[$pId]['corporationId'] = $corporationId;
    $choices[$pId]['rejectedCardId'] = $rejectedCardId;
    Globals::setSetupChoices($choices);
    Notifications::chooseSetup(Players::get($pId), $planetId, $corporationId, $rejectedCardId);
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

      Cards::move($choice['rejectedCardId'], 'box');
      $player->setCorporationId($choice['corporationId']);
      $player->setPlanetId($choice['planetId']);
    }

    $this->gamestate->nextState('');
  }
}
