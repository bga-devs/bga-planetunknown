<?php

namespace PU\Models;

use PU\Core\Engine;
use PU\Core\Game;
use PU\Core\Globals;
use PU\Core\Notifications;
use PU\Managers\ZooCards;
use PU\Managers\Players;
use PU\Helpers\Log;
use PU\Helpers\FlowConvertor;
use PU\Managers\Actions;

/*
 * Action: base class to handle atomic action
 */

class Action
{
  protected $ctx = null; // Contain ctx information : current node of flow tree
  protected $description = '';
  public function __construct(&$ctx)
  {
    $this->ctx = $ctx;
  }

  public function getCtx()
  {
    return $this->ctx;
  }

  public function isDoable($player)
  {
    return true;
  }

  public function isOptional()
  {
    return !$this->isDoable($this->getPlayer());
  }

  public function isIndependent($player = null)
  {
    return false;
  }

  public function isAutomatic($player = null)
  {
    return false;
  }

  public function isIrreversible($player = null)
  {
    return false;
  }

  public function getDescription()
  {
    return $this->description;
  }

  public function getPlayer()
  {
    $pId = $this->ctx->getRoot()->getPId();
    return Players::get($pId);
  }

  public function getState()
  {
    return null;
  }

  /**
   * Syntaxic sugar
   */
  public function getCtxArgs()
  {
    if ($this->ctx == null) {
      return [];
    } elseif (is_array($this->ctx)) {
      return $this->ctx;
    } else {
      return $this->ctx->getArgs() ?? [];
    }
  }
  public function getCtxArg($v)
  {
    return $this->getCtxArgs()[$v] ?? null;
  }

  /**
   * Insert flow as child of current node
   */
  public function insertAsChild($flow)
  {
    if (Globals::getMode() == \MODE_PRIVATE) {
      Engine::insertAsChild($flow, $this->ctx);
    }
  }

  /**
   * Insert childs as parallel node childs
   */
  public function pushParallelChild($node)
  {
    $this->pushParallelChilds([$node]);
  }

  public function pushParallelChilds($childs)
  {
    if (Globals::getMode() == \MODE_PRIVATE) {
      Engine::insertOrUpdateParallelChilds($childs, $this->ctx);
    }
  }

  public function getClassName()
  {
    $classname = get_class($this);
    if ($pos = strrpos($classname, '\\')) {
      return substr($classname, $pos + 1);
    }
    return $classname;
  }

  public function createActionFromBonus($bonuses, $player)
  {
    if (!$bonuses) return;

    $actions = [];

    foreach ($bonuses as $bonus) {
      switch ($bonus) {
        case CIV:
          $levelCiv = $player->corporation()->getCivLevel();
          $player->addEndOfTurnAction([
            'action' => TAKE_CIV_CARD,
            'args' => [
              'level' => $levelCiv
            ]
          ]);
          Notifications::milestone($player, CIV, $levelCiv);
          break;
        case BIOMASS:
          $patchToPlace = $player->corporation()->receiveBiomassPatch();
          if ($patchToPlace) {
            $actions[] = Actions::getBiomassPatchFlow($patchToPlace->getId());
          }
          break;
        case TECH:
          $levelTech = $player->corporation()->getTechLevel($this);
          Notifications::milestone($player, TECH, $levelTech);
          break;
        case SYNERGY:
          $actions[] = [
            'action' => CHOOSE_TRACKS,
            'args' => [
              'types' => ALL_TYPES,
              'n' => 1,
              'from' => SYNERGY
            ]
          ];
          Notifications::milestone($player, $bonus);
          break;
        case ROVER:
          $actions[] = [
            'action' => PLACE_ROVER,
          ];
          Notifications::milestone($player, $bonus);
          break;
        default:
          //handle 'move_x' bonuses
          if (is_string($bonus) && str_starts_with($bonus, 'move')) {

            $levelMove = $player->corporation()->moveRoverBy(explode('_', $bonus)[1]);

            $actions[] = [
              'action' => MOVE_ROVER,
              'args' => [
                'remaining' => $levelMove
              ]
            ];
          }
          break;
      }
    }

    // if (count($actions) > 1) {
    $this->pushParallelChilds($actions);
    // } elseif (count($actions) == 1) {
    //   $this->insertAsChild($actions[0]);
    // }
  }
}
