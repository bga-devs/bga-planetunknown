<?php

// SLOT 2 : right before the last turn
// SLOT 1 : end of the last turn
// SLOT 3 : before end of game

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Planet Unknown implementation : © Timothée Pecatte <tim.pecatte@gmail.com>, Emmanuel Albisser <emmanuel.albisser@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * planetunknown.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 *
 */

$swdNamespaceAutoload = function ($class) {
  $classParts = explode('\\', $class);
  if ($classParts[0] == 'PU') {
    array_shift($classParts);
    $file = dirname(__FILE__) . '/modules/php/' . implode(DIRECTORY_SEPARATOR, $classParts) . '.php';
    if (file_exists($file)) {
      require_once $file;
    } else {
      var_dump('Cannot find file : ' . $file);
    }
  }
};
spl_autoload_register($swdNamespaceAutoload, true, true);

require_once APP_GAMEMODULE_PATH . 'module/table/table.game.php';

use PU\Managers\Meeples;
use PU\Managers\Tiles;
use PU\Managers\Players;
use PU\Managers\Scores;
use PU\Core\Globals;
use PU\Helpers\Log;
use PU\Core\Preferences;
use PU\Core\Stats;
use PU\Core\Engine;
use PU\Managers\Cards;
use PU\Managers\Susan;

class planetunknown extends Table
{
  use PU\DebugTrait;
  use PU\States\SetupTrait;
  use PU\States\EngineTrait;
  use PU\States\TurnTrait;
  use PU\States\ChooseSetupTrait;
  use PU\States\EndGameTrait;

  public static $instance = null;
  function __construct()
  {
    parent::__construct();
    self::$instance = $this;
    self::initGameStateLabels([
      'mode' => 10, // DO NOT TOUCH, USED FOR SIMULATING DB MODIFICATION
      'option_planet' => 105,
      'option_corporation' => 106,
      'option_event_card' => 107,
    ]);
    Engine::boot(); // TODO : move to initTable in a week
    // Stats::checkExistence();
  }

  protected function initTable()
  {
    // var_dump('test');
  }

  public static function get()
  {
    return self::$instance;
  }

  protected function getGameName()
  {
    return 'planetunknown';
  }

  /*
   * getAllDatas:
   */
  public function getAllDatas()
  {
    $pId = self::getCurrentPId();
    $scores = Players::scores($pId);
    $players = Players::getUiData($pId);
    foreach ($players as $pId => &$infos) {
      $infos['score'] = $scores[$pId]['total'];
    }

    return [
      'prefs' => Preferences::getUiData($pId),
      'players' => $players,
      'tiles' => Tiles::getUiData(),
      'meeples' => Meeples::getUiData(),
      'cards' => Cards::getUiData(),
      'susan' => Susan::getUiData(),
      'scores' => $scores,
      'firstPlayer' => Globals::getFirstPlayer(),
      'endOfGameTriggered' => Globals::isGameEndTriggered(),
      'eventGame' => Globals::getEventCardsGame(),
    ];
  }

  /*
   * getGameProgression:
   */
  function getGameProgression()
  {
    if (Globals::getEventCardsGame() == EVENT_CARD_GAME) {
      return (20 - Cards::countInLocation('deck_event')) * 100 / 20;
    } else {
      return (12 - Susan::getTilesNumberInMinDeck()) * 100 / 12;
    }
  }

  function actChangePreference($pref, $value)
  {
    Preferences::set($this->getCurrentPId(), $pref, $value);
  }

  ///////////////////////////////////////////////
  ///////////////////////////////////////////////
  ////////////   Custom Turn Order   ////////////
  ///////////////////////////////////////////////
  ///////////////////////////////////////////////
  public function initCustomTurnOrder($key, $order, $callback, $endCallback, $loop = false, $autoNext = true, $args = [])
  {
    $turnOrders = Globals::getCustomTurnOrders();
    $turnOrders[$key] = [
      'order' => $order ?? Players::getTurnOrder(),
      'index' => -1,
      'callback' => $callback,
      'args' => $args, // Useful mostly for auto card listeners
      'endCallback' => $endCallback,
      'loop' => $loop,
    ];
    Globals::setCustomTurnOrders($turnOrders);

    if ($autoNext) {
      $this->nextPlayerCustomOrder($key);
    }
  }

  public function initCustomDefaultTurnOrder($key, $callback, $endCallback, $loop = false, $autoNext = true)
  {
    $this->initCustomTurnOrder($key, null, $callback, $endCallback, $loop, $autoNext);
  }

  public function nextPlayerCustomOrder($key)
  {
    $turnOrders = Globals::getCustomTurnOrders();
    if (!isset($turnOrders[$key])) {
      throw new BgaVisibleSystemException('Asking for the next player of a custom turn order not initialized : ' . $key);
    }

    // Increase index and save
    $o = $turnOrders[$key];
    $i = $o['index'] + 1;
    if ($i == count($o['order']) && $o['loop']) {
      $i = 0;
    }
    $turnOrders[$key]['index'] = $i;
    Globals::setCustomTurnOrders($turnOrders);

    if ($i < count($o['order'])) {
      $this->gamestate->jumpToState(ST_GENERIC_NEXT_PLAYER);
      $this->gamestate->changeActivePlayer($o['order'][$i]);
      $this->jumpToOrCall($o['callback'], $o['args']);
    } else {
      $this->endCustomOrder($key);
    }
  }

  public function endCustomOrder($key)
  {
    $turnOrders = Globals::getCustomTurnOrders();
    if (!isset($turnOrders[$key])) {
      throw new BgaVisibleSystemException('Asking for ending a custom turn order not initialized : ' . $key);
    }

    $o = $turnOrders[$key];
    $turnOrders[$key]['index'] = count($o['order']);
    Globals::setCustomTurnOrders($turnOrders);
    $callback = $o['endCallback'];
    $this->jumpToOrCall($callback);
  }

  public function jumpToOrCall($mixed, $args = [])
  {
    if (is_int($mixed) && array_key_exists($mixed, $this->gamestate->states)) {
      $this->gamestate->jumpToState($mixed);
    } elseif (method_exists($this, $mixed)) {
      $method = $mixed;
      $this->$method($args);
    } else {
      throw new BgaVisibleSystemException('Failing to jumpToOrCall  : ' . $mixed);
    }
  }

  /********************************************
   ******* GENERIC CARD LISTENERS CHECK ********
   ********************************************/
  /*
   * A lot of time you want to loop through all the player to see if a card react or not
   *  => this is achieved using custom turn order with an arg containing the eventType
   *  => the custom order will call the genericPlayerCheckListeners that will getReaction from cards if any
   */
  // public function checkCardListeners($typeEvent, $endCallback, $event = [], $order = null)
  // {
  //   $event['type'] = $typeEvent;
  //   $event['method'] = $typeEvent;
  //   $this->initCustomTurnOrder($typeEvent, $order, 'genericPlayerCheckListeners', $endCallback, false, true, $event);
  // }

  // function genericPlayerCheckListeners($event)
  // {
  //   $pId = Players::getActiveId();
  //   $event['pId'] = $pId;
  //   $reaction = ZooCards::getReaction($event);

  //   if (is_null($reaction)) {
  //     // No reaction => just go to next player
  //     $this->nextPlayerCustomOrder($event['type']);
  //   } else {
  //     // Reaction => boot up the Engine
  //     Engine::setup($reaction, ['order' => $event['type']]);
  //     Engine::proceed();
  //   }
  // }

  ////////////////////////////////////
  ////////////   Zombie   ////////////
  ////////////////////////////////////
  /*
   * zombieTurn:
   *   This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
   *   You can do whatever you want in order to make sure the turn of this player ends appropriately
   */
  public function zombieTurn($state, $activePlayer)
  {
    // $skipped = Globals::getSkippedPlayers();
    // if (!in_array((int) $activePlayer, $skipped)) {
    //   $skipped[] = (int) $activePlayer;
    //   Globals::setSkippedPlayers($skipped);
    // }

    $stateName = $state['name'];
    if ($state['type'] == 'activeplayer') {
      if ($stateName == 'confirmTurn') {
        $this->actConfirmTurn(true);
      } elseif ($stateName == 'confirmPartialTurn') {
        $this->actConfirmPartialTurn(true);
      }
      // Clear all node of player
      elseif (Engine::getNextUnresolved() != null) {
        Engine::clearZombieNodes($activePlayer);
        Engine::proceed();
      } else {
        $this->gamestate->nextState('zombiePass');
      }
    } elseif ($state['type'] == 'multipleactiveplayer') {
      if ($stateName == 'breakDiscard') {
        $selection = Globals::getBreakDiscardSelection();
        $selection[$activePlayer] = [];
        Globals::setBreakDiscardSelection($selection);
        $this->updateActivePlayersBreakDiscardSelection();
      }
      // Make sure player is in a non blocking status for role turn
      else {
        $this->gamestate->setPlayerNonMultiactive($activePlayer, 'zombiePass');
      }
    }
  }

  /////////////////////////////////////
  //////////   DB upgrade   ///////////
  /////////////////////////////////////
  // You don't have to care about this until your game has been published on BGA.
  // Once your game is on BGA, this method is called everytime the system detects a game running with your old Database scheme.
  // In this case, if you change your Database scheme, you just have to apply the needed changes in order to
  //   update the game database and allow the game to continue to run with your new version.
  /////////////////////////////////////
  /*
   * upgradeTableDb
   *  - int $from_version : current version of this game database, in numerical form.
   *      For example, if the game was running with a release of your game named "140430-1345", $from_version is equal to 1404301345
   */
  public function upgradeTableDb($from_version)
  {
    // if ($from_version <= 2107011810) {
    //   $sql = 'ALTER TABLE `DBPREFIX_player` ADD `new_score` INT(10) NOT NULL DEFAULT 0';
    //   self::applyDbUpgradeToAllDB($sql);
    // }
  }

  /////////////////////////////////////////////////////////////
  // Exposing protected methods, please use at your own risk //
  /////////////////////////////////////////////////////////////

  // Exposing protected method getCurrentPlayerId
  public static function getCurrentPId($bReturnNullIfNotLogged = false)
  {
    return self::getCurrentPlayerId($bReturnNullIfNotLogged);
  }

  // Exposing protected method translation
  public static function translate($text)
  {
    return self::_($text);
  }
}
