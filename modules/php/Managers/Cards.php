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
  protected static $customFields = ['player_id', 'extra_datas'];
  protected static $datas = null;
  protected static $autoremovePrefix = false;

  protected static function cast($row)
  {
    if ($row['card_id'] > 0 && $row['card_id'] <= 36) {
      $className =  '\PU\Models\Cards\CivCard' . $row['card_id'];
    } else if ($row['card_id'] > 36 && $row['card_id'] <= 64) {
      $className =  '\PU\Models\Cards\NOCard' . $row['card_id'];
    }
    return new $className($row);
  }
  public static function getUiData()
  {
    return [
      'neighborObjectives' => static::getInLocation('table'),
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
    };
    for ($i = 29; $i <= 36; $i++) {
      $data[] = [
        // 'id' => $i,
        'location' => 'deck_civ_' . (($i - 28) % 4 + 1),
      ];
    }
    for ($i = 37; $i <= 64; $i++) {
      $data[] = [
        // 'id' => $i,
        'location' => 'deck_NObjectives',
      ];
    }
    static::create($data);

    //keep only the right civ card number 
    $neededCivCards = count($players) + 1;
    for ($i = 0; $i < 4; $i++) {
      $deck = 'deck_civ_' . $i;
      static::shuffle($deck);
      static::pickForLocation(static::countInLocation($deck) - $neededCivCards, $deck, 'box');
    }

    //pick right number of Neighbor Objectives cards
    static::shuffle('deck_NObjectives');
    switch (count($players)) {
      case 1:
        //TODO handle solo game
        break;

      case 2:
        static::pickForLocation(3, 'deck_NObjectives', 'table');
        break;

      default:
        for ($i = 0; $i < count($players); $i++) {
          static::pickOneForLocation('deck_NObjectives', 'table', $i);
        }
        break;
    }
  }
}
