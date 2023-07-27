<?php

namespace PU\Managers;

use PU\Core\Game;
use PU\Core\Globals;
use PU\Core\Stats;
use PU\Helpers\Utils;
use PU\Core\Notifications;

/*
 * Players manager : allows to easily access players ...
 *  a player is an instance of Player class
 */

class Players extends \PU\Helpers\CachedDB_Manager
{
  protected static $table = 'player';
  protected static $primary = 'player_id';
  protected static $datas = null;
  protected static function cast($row)
  {
    return new \PU\Models\Player($row);
  }

  public function setupNewGame($players, $options)
  {
    // Planet is not determined at first unless first game/beginner mode
    if ($options[OPTION_PLANET] == OPTION_PLANET_A) $planet = 0;
    else $planet = ''; //TODO ATTRIBUTE A BOARD AT RANDOM
    if ($options[OPTION_CORPORATION] == OPTION_CORPORATION_UNIVERSAL) $corporation = 0;
    else $corporation = ''; //TODO ATTRIBUTE A BOARD AT RANDOM

    //positions around susan
    $positions = [
      1 => [0],
      2 => [0, 3],
      3 => [0, 2, 4],
      4 => [0, 1, 3, 4],
      5 => [0, 1, 2, 3, 4]
    ];

    // Create players
    $gameInfos = Game::get()->getGameinfos();
    $colors = $gameInfos['player_colors'];
    $query = self::DB()->multipleInsert([
      'player_id',
      'player_color',
      'player_canal',
      'player_name',
      'player_avatar',
      'planet_id',
      'corporation_id',
      'position'
    ]);
    $playerIndex = 0;
    $values = [];
    foreach ($players as $pId => $player) {
      $color = array_shift($colors);
      $values[] = [$pId, $color, $player['player_canal'], $player['player_name'], $player['player_avatar'], $planet, $corporation, $positions[count($players)][$playerIndex]];
      $playerIndex++;
    }
    $query->values($values);
    self::invalidate();
    Game::get()->reattributeColorsBasedOnPreferences($players, $gameInfos['player_colors']);
    Game::get()->reloadPlayersBasicInfos();
  }

  public function getActiveId()
  {
    return (int) Game::get()->getActivePlayerId();
  }

  public function getCurrentId($bReturnNullIfNotLogged = false)
  {
    return (int) Game::get()->getCurrentPId($bReturnNullIfNotLogged);
  }

  public function getActive()
  {
    return self::get(self::getActiveId());
  }

  public function getCurrent()
  {
    return self::get(self::getCurrentId());
  }

  public function get($id = null)
  {
    return parent::get($id ?? self::getActiveId());
  }

  public function getNextId($player)
  {
    $pId = is_int($player) ? $player : $player->getId();
    $table = Game::get()->getNextPlayerTable();
    return $table[$pId];
  }

  public function getNext($player)
  {
    return self::get(self::getNextId($player));
  }

  public function getPrevious($player)
  {
    $table = Game::get()->getPrevPlayerTable();
    $pId = (int) $table[$player->getId()];
    return self::get($pId);
  }

  /*
   * Return the number of players
   */
  public function count()
  {
    return self::getAll()->count();
  }

  /*
   * getUiData : get all ui data of all players
   */
  public function getUiData($pId)
  {
    return self::getAll()
      ->map(function ($player) use ($pId) {
        return $player->getUiData($pId);
      })
      ->toAssoc();
  }

  /*
   * Get current turn order according to first player variable
   */
  public function getTurnOrder($firstPlayer = null)
  {
    $firstPlayer = $firstPlayer ?? Globals::getFirstPlayer();
    $order = [];
    $p = $firstPlayer;
    do {
      $order[] = $p;
      $p = self::getNextId($p);
    } while ($p != $firstPlayer);
    return $order;
  }
}
