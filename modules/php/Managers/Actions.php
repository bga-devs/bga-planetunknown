<?php

namespace PU\Managers;

use PU\Core\Game;
use PU\Core\Engine;
use PU\Core\Globals;
use PU\Core\Notifications;
use PU\Helpers\Log;
use PU\Managers\Players;

/* Class to manage all the Actions */

class Actions
{
  static $classes = [
    PLACE_TILE,
    MOVE_TRACK,
    CHOOSE_TRACKS,
    PLACE_ROVER,
    MOVE_TRACKER_BY_ONE,
    TAKE_CIV_CARD,
    MOVE_ROVER,
    GAIN_BIOMASS_PATCH,
    //from civ card
    COLLECT_MEEPLE,
    DESTROY_ALL_IN_ROW,
    MOVE_TRACKERS_TO_FIVE,
    //from event card
    CHOOSE_ROTATION_ENGINE,
    DESTROY_P_O_CARD,
    PLACE_MEEPLE,
    PEEK_NEXT_EVENT,
    //from corpo
    POSITION_LIFEPOD_ON_TRACK,
    CHOOSE_FLUX_TRACK,
    POSITION_LIFEPOD_ON_TECH,
    CLAIM_ALL_IN_A_ROW,
    CHOOSE_OBJECTIVE_FOR_ALL,
    REACH_NEXT_MILESTONE, //no front
    RESET_TRACK,
    EMPTY_SLOT
  ];

  public static function getBiomassPatchFlow()
  {
    return [
      'action' => GAIN_BIOMASS_PATCH,
    ];
  }

  public static function get($actionId, &$ctx = null)
  {
    if (!in_array($actionId, self::$classes)) {
      // throw new \feException(print_r(debug_print_backtrace()));
      // throw new \feException(print_r(Globals::getEngine()));
      throw new \BgaVisibleSystemException('Trying to get an atomic action not defined in Actions.php : ' . $actionId);
    }
    $name = '\PU\Actions\\' . $actionId;
    return new $name($ctx);
  }

  public static function isDoable($actionId, $ctx, $player)
  {
    $res = self::get($actionId, $ctx)->isDoable($player);
    return $res;
  }

  public static function getErrorMessage($actionId)
  {
    $actionId = ucfirst(mb_strtolower($actionId));
    $msg = sprintf(
      Game::get()::translate(
        'Attempting to take an action (%s) that is not possible. Either another card erroneously flagged this action as possible, or this action was possible until another card interfered.'
      ),
      $actionId
    );
    return $msg;
  }

  public static function getState($actionId, $ctx)
  {
    return self::get($actionId, $ctx)->getState();
  }

  public static function getArgs($actionId, $ctx)
  {
    $action = self::get($actionId, $ctx);
    $methodName = 'args' . $action->getClassName();
    $args = \method_exists($action, $methodName) ? $action->$methodName() : [];
    return array_merge($args, ['optionalAction' => $ctx->isOptional()]);
  }

  public static function takeAction($actionId, $actionName, $args, &$ctx, $automatic = false)
  {
    $player = self::getPlayer($ctx);
    if (!self::isDoable($actionId, $ctx, $player)) {
      throw new \BgaUserException(self::getErrorMessage($actionId));
    }

    // Check action
    if (!$automatic && Globals::getMode() == \MODE_PRIVATE) {
      Game::get()->checkAction($actionName);
      $stepId = Log::step();
      Notifications::newUndoableStep($player, $stepId);
    }

    // Run action
    $action = self::get($actionId, $ctx);
    $methodName = $actionName; //'act' . self::$classes[$actionId];
    $action->$methodName(...$args);

    // Resolve action
    $automatic = $ctx->isAutomatic($player);
    $checkpoint = false; // TODO
    $ctx = $action->getCtx();
    Engine::resolveAction(['actionName' => $actionName, 'args' => $args], $checkpoint, $ctx, $automatic);
    Engine::proceed($player->getId());
  }

  public static function getPlayer($node)
  {
    return Players::get($node->getRoot()->getPId());
  }

  public static function stAction($actionId, $ctx)
  {
    $player = self::getPlayer($ctx);
    if (!self::isDoable($actionId, $ctx, $player)) {
      if (!$ctx->isOptional()) {
        if (self::isDoable($actionId, $ctx, $player, true)) {
          Game::get()->gamestate->jumpToState(ST_IMPOSSIBLE_MANDATORY_ACTION);
          return;
        } else {
          throw new \BgaUserException(self::getErrorMessage($actionId));
        }
      } else {
        // Auto pass if optional and not doable
        Game::get()->actPassOptionalAction(true);
        return true;
      }
    }

    $action = self::get($actionId, $ctx);
    $methodName = 'st' . $action->getClassName();
    if (\method_exists($action, $methodName)) {
      $result = $action->$methodName();
      if (!is_null($result)) {
        $actionName = 'act' . $action->getClassName();
        self::takeAction($actionId, $actionName, $result, $ctx, true);
        return true; // We are changing state
      }
    }
  }

  public static function stPreAction($actionId, $ctx)
  {
    $action = self::get($actionId, $ctx);
    $methodName = 'stPre' . $action->getClassName();
    if (\method_exists($action, $methodName)) {
      $action->$methodName();
      $player = self::getPlayer($ctx);
      if ($ctx->isIrreversible($player)) {
        Engine::checkpoint();
      }
    }
  }

  public static function pass($actionId, $ctx)
  {
    if (!$ctx->isOptional()) {
      self::error($ctx->toArray());
      throw new \BgaVisibleSystemException('This action is not optional');
    }

    $action = self::get($actionId, $ctx);
    $methodName = 'actPass' . $action->getClassName();
    if (\method_exists($action, $methodName)) {
      $action->$methodName();
    } else {
      Engine::resolveAction(PASS, false, $ctx, false);
    }
    $player = self::getPlayer($ctx);
    Engine::proceed($player->getId());
  }
}
