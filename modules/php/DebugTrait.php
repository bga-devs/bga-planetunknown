<?php

namespace PU;

use PU\Core\Globals;
use PU\Core\PGlobals;
use PU\Core\Engine;
use PU\Core\Game;
use PU\Core\Notifications;
use PU\Helpers\Utils;
use PU\Helpers\Log;
use PU\Helpers\Collection;
use PU\Managers\Tiles;
use PU\Managers\Players;
use PU\Managers\Meeples;
use PU\Managers\Cards;

trait DebugTrait
{
  function chooseEvent($eventId)
  {
    $mode = Globals::getMode();
    Globals::setMode(MODE_APPLY);
    Cards::insertOnTop($eventId, 'deck_event');
    Globals::setMode($mode);
  }

  function createDeckEvent()
  {
    $mode = Globals::getMode();
    Globals::setMode(MODE_APPLY);
    for ($i = 124; $i >= 120; $i--) {
      Cards::insertOnTop($i, 'deck_event');
    }
    Globals::setMode($mode);
  }

  function tp()
  {
    //    Log::clearUndoableStepNotifications(true);
    // $this->actTakeAtomicAction('actPlaceRover', ['4_1']);
    // $player = Players::getCurrent();
    // var_dump($player->canTakeAction(PLACE_TILE, []));
    var_dump(Globals::getTurnSpecialRule());
  }

  function susan()
  {
    var_dump(Tiles::getSusan()->getIds());
  }

  function getMode()
  {
    var_dump(Globals::getMode());
  }

  function dv()
  {
    $mode = Globals::getMode();
    Globals::setMode(MODE_APPLY);
    Globals::setGameEndTriggered(true);
    Globals::setMode($mode);
  }

  function resolveDebug()
  {
    Engine::resolveAction([]);
    Engine::proceed();
  }

  function flagGameEnded()
  {
    $mode = Globals::getMode();
    Globals::setMode(MODE_APPLY);
    Globals::setGameEndTriggered(true);
    Globals::setMode($mode);
  }

  function resetEngine()
  {
    $pId = Players::getCurrentId();
    $tree = PGlobals::getEngine($pId);
    $tree = $this->resetEngineAux($tree);
    PGlobals::setEngine($pId, $tree);
    Engine::proceed($pId);
  }
  function resetEngineAux($t)
  {
    if (isset($t['action'])) {
      unset($t['actionResolved']);
      unset($t['actionResolutionArgs']);
      unset($t['childs']);
      $t['type'] = 'leaf';
    } else {
      for ($i = 0; $i < count($t['childs'] ?? []); $i++) {
        $t['childs'][$i] = $this->resetEngineAux($t['childs'][$i]);
      }
    }
    unset($t['choices']);

    return $t;
  }

  function engDisplay()
  {
    $pId = Players::getCurrentId();
    var_dump(PGlobals::getEngine($pId));
  }

  function engProceed()
  {
    $pId = Players::getCurrentId();
    Engine::proceed($pId);
  }

  /*
   * loadBug: in studio, type loadBug(20762) into the table chat to load a bug report from production
   * client side JavaScript will fetch each URL below in sequence, then refresh the page
   */
  public function loadBug($reportId)
  {
    $db = explode('_', self::getUniqueValueFromDB("SELECT SUBSTRING_INDEX(DATABASE(), '_', -2)"));
    $game = $db[0];
    $tableId = $db[1];
    self::notifyAllPlayers(
      'loadBug',
      "Trying to load <a href='https://boardgamearena.com/bug?id=$reportId' target='_blank'>bug report $reportId</a>",
      [
        'urls' => [
          // Emulates "load bug report" in control panel
          "https://studio.boardgamearena.com/admin/studio/getSavedGameStateFromProduction.html?game=$game&report_id=$reportId&table_id=$tableId",

          // Emulates "load 1" at this table
          "https://studio.boardgamearena.com/table/table/loadSaveState.html?table=$tableId&state=1",

          // Calls the function below to update SQL
          "https://studio.boardgamearena.com/1/$game/$game/loadBugSQL.html?table=$tableId&report_id=$reportId",

          // Emulates "clear PHP cache" in control panel
          // Needed at the end because BGA is caching player info
          "https://studio.boardgamearena.com/admin/studio/clearGameserverPhpCache.html?game=$game",
        ],
      ]
    );
  }

  /*
   * loadBugSQL: in studio, this is one of the URLs triggered by loadBug() above
   */
  public function loadBugSQL($reportId)
  {
    $studioPlayer = self::getCurrentPlayerId();
    $players = self::getObjectListFromDb('SELECT player_id FROM player', true);

    // Change for your game
    // We are setting the current state to match the start of a player's turn if it's already game over
    $sql = ['UPDATE global SET global_value=21 WHERE global_id=1 AND global_value=99'];
    // $sql[] = 'ALTER TABLE `gamelog` ADD `cancel` TINYINT(1) NOT NULL DEFAULT 0;';
    $map = [];
    foreach ($players as $pId) {
      $map[(int) $pId] = (int) $studioPlayer;

      // All games can keep this SQL
      $sql[] = "UPDATE player SET player_id=$studioPlayer WHERE player_id=$pId";
      $sql[] = "UPDATE global SET global_value=$studioPlayer WHERE global_value=$pId";
      $sql[] = "UPDATE stats SET stats_player_id=$studioPlayer WHERE stats_player_id=$pId";

      // Add game-specific SQL update the tables for your game
      $sql[] = "UPDATE meeples SET player_id=$studioPlayer WHERE player_id=$pId";
      $sql[] = "UPDATE cards SET player_id=$studioPlayer WHERE player_id=$pId";
      $sql[] = "UPDATE cards SET player_id2=$studioPlayer WHERE player_id2=$pId";
      $sql[] = "UPDATE tiles SET player_id=$studioPlayer WHERE player_id=$pId";
      $sql[] = "UPDATE user_preferences SET player_id=$studioPlayer WHERE player_id=$pId";
      $sql[] = "UPDATE pglobal_variables SET name = REPLACE(name, '$pId', '$studioPlayer')";

      // This could be improved, it assumes you had sequential studio accounts before loading
      // e.g., quietmint0, quietmint1, quietmint2, etc. are at the table
      $studioPlayer++;
    }
    $msg =
      "<b>Loaded <a href='https://boardgamearena.com/bug?id=$reportId' target='_blank'>bug report $reportId</a></b><hr><ul><li>" .
      implode(';</li><li>', $sql) .
      ';</li></ul>';
    self::warn($msg);
    self::notifyAllPlayers('message', $msg, []);

    foreach ($sql as $q) {
      self::DbQuery($q);
    }

    /******************
     *** Fix Globals ***
     ******************/

    // Turn orders
    Globals::setDebugMode();
    $turnOrders = Globals::getCustomTurnOrders();
    foreach ($turnOrders as $key => &$order) {
      $t = [];
      foreach ($order['order'] as $pId) {
        $t[] = $map[$pId];
      }
      $order['order'] = $t;
    }
    Globals::setCustomTurnOrders($turnOrders);

    // Engine
    PGlobals::fetch();
    $flows = PGlobals::getAll('engine');
    foreach ($flows as $pId => $engine) {
      self::loadDebugUpdateEngine($engine, $map);
      PGlobals::setEngine($pId, $engine);
    }

    // First player
    $fp = Globals::getFirstPlayer();
    Globals::setFirstPlayer($map[$fp]);

    Globals::unsetDebugMode();

    self::reloadPlayersBasicInfos();
  }

  function loadDebugUpdateEngine(&$node, $map)
  {
    if (isset($node['pId'])) {
      $node['pId'] = $map[(int) $node['pId']];
    }

    if (isset($node['childs'])) {
      foreach ($node['childs'] as &$child) {
        self::loadDebugUpdateEngine($child, $map);
      }
    }
  }
}
