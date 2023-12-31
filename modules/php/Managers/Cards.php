<?php

namespace PU\Managers;

use PU\Core\Stats;
use PU\Core\Globals;
use PU\Helpers\UserException;
use PU\Helpers\Collection;

/* Class to manage all the cards for PlanetUnknown */

class Cards extends \PU\Helpers\CachedPieces
{
  protected static $table = 'cards';
  protected static $prefix = 'card_';
  protected static $customFields = ['player_id', 'player_id2', 'extra_datas'];
  protected static $datas = null;
  protected static $autoremovePrefix = false;
  protected static $maxIndex = 0;

  protected static function cast($row)
  {
    if ($row['card_id'] > 0 && $row['card_id'] <= 36) {
      $className = '\PU\Models\Cards\CivCard' . $row['card_id'];
    } elseif ($row['card_id'] > 36 && $row['card_id'] <= 64) {
      $className = '\PU\Models\Cards\OCard';
    } elseif ($row['card_id'] > 64 && $row['card_id'] <= 124) {
      $className = '\PU\Models\Cards\EventCard' . $row['card_id'];
    }
    return new $className($row);
  }
  public static function getUiData()
  {
    return [
      'NOCards' => static::getInLocation('NOCards'),
      'deck_civ_1' => static::countInLocation('deck_civ_1'),
      'deck_civ_2' => static::countInLocation('deck_civ_2'),
      'deck_civ_3' => static::countInLocation('deck_civ_3'),
      'deck_civ_4' => static::countInLocation('deck_civ_4'),
      'deck_event' => static::countInLocation('deck_event'),
      'event' => static::getTopOf('discard_event')->first(),
    ];
  }

  ////////////////////////////////////
  //  ____       _
  // / ___|  ___| |_ _   _ _ __
  // \___ \ / _ \ __| | | | '_ \
  //  ___) |  __/ |_| |_| | |_) |
  // |____/ \___|\__|\__,_| .__/
  //                      |_|
  ////////////////////////////////////

  /* Creation of all cards */
  public static function setupNewGame($players, $options)
  {
    $data = [];
    for ($i = 1; $i <= 28; $i++) {
      $data[] = [
        // 'id' => $i,
        'location' => 'deck_civ_' . ceil($i / 7),
      ];
    }
    for ($i = 29; $i <= 36; $i++) {
      $data[] = [
        // 'id' => $i,
        'location' => 'deck_civ_' . ((($i - 29) % 4) + 1),
      ];
    }
    for ($i = 37; $i <= 64; $i++) {
      $data[] = [
        // 'id' => $i,
        'location' => 'deck_objectives',
      ];
    }

    static::create($data);

    //keep only the right civ card number
    $neededCivCards = count($players) + 1;
    for ($i = 1; $i <= 4; $i++) {
      $deck = 'deck_civ_' . $i;
      static::shuffle($deck);
      static::pickForLocation(static::countInLocation($deck) - $neededCivCards, $deck, 'reserve_civ_' . $i);
    }

    //pick right number of Neighbor Objectives cards
    static::shuffle('deck_objectives');
    if (count($players) != 1) {
      if (count($players) == 2) {
        $NOcards = static::pickForLocation(3, 'deck_objectives', 'NOCards');
        foreach ($NOcards->toArray() as $card) {
          $card->setPId(array_keys($players)[0]);
          $card->setPId2(array_keys($players)[1]);
        }
      } else {
        for ($i = 0; $i < count($players); $i++) {
          $NOcard = static::pickOneForLocation('deck_objectives', 'NOCards', $i);
          $NOcard->setPId(array_keys($players)[$i]);
          $NOcard->setPId2(array_keys($players)[($i + 1) % count($players)]);
        }
      }
    }

    //pick right number of Private Objectives cards
    if (count($players) == 1 || $options[OPTION_PRIVATE_OBJECTIVE_CARDS] == OPTION_PRIVATE_OBJECTIVE_CARDS_GAME) {
      //4 cards in solo game, only 2 in multiplayer game
      $nbCards = count($players) == 1 ? 4 : 2;
      foreach ($players as $pId => $player) {
        $POCards = static::pickForLocation($nbCards, 'deck_objectives', 'tochoose_obj');
        $POCards->update('pId', $pId);
      }
    }

    //prepare Event Card Deck
    if ($options[OPTION_EVENT_CARDS] == OPTION_EVENT_CARDS_GAME) {
      $data = [];
      $cardColors = [GREEN, ORANGE, RED];
      for ($i = 65; $i <= 124; $i++) {
        $data[] = [
          'location' => 'deck_event_' . $cardColors[floor(($i - 65) / 20)],
        ];
      }
      static::create($data);

      //first remove solo card if needed
      if (count($players) != 1) {
        static::move(SOLO_EVENT_CARDS, 'trash');
      }

      //TODO this is random, can make some preset
      $eventCardSet = [];
      $eventCardSet[GREEN] = bga_rand(0, 13);
      $eventCardSet[ORANGE] = bga_rand(0, 13);
      $eventCardSet[RED] = 20 - $eventCardSet[GREEN] - $eventCardSet[ORANGE];
      foreach ($cardColors as $color) {
        static::shuffle('deck_event_' . $color);
        static::pickForLocation($eventCardSet[$color], 'deck_event_' . $color, 'deck_event');
      }
      Globals::setEventCardSet($eventCardSet);
      Globals::setTarget(static::computeSoloTarget($eventCardSet[RED], $eventCardSet[ORANGE], $eventCardSet[GREEN]));
      static::shuffle('deck_event');
    }
  }

  public static function computeSoloTarget($redNb, $orangeNb, $greenNb)
  {
    $target = 60;

    $redScore = [0, 0, 0, -5, -5, -5, -5, -7, -7, -7, -7, -9, -9, -9, -9, -11, -11, -11, -11, -11, -11];
    $orangeScore = [0, 0, 0, -1, -1, -1, -1, -2, -2, -2, -2, -3, -3, -3, -3, -4, -4, -4, -4, -4, -4];
    $greenScore = [0, 0, 0, 3, 3, 3, 3, 6, 6, 6, 6, 9, 9, 9, 9, 12, 12, 12, 12, 12, 12];

    $target += $redScore[$redNb];
    $target += $orangeScore[$orangeNb];
    $target += $greenScore[$greenNb];

    return $target;
  }
}
