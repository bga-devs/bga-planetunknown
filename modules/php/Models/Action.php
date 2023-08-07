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
    return false;
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

    foreach ($bonuses as $bonus) {
      switch ($bonus) {
        case CIV:
          $levelCiv = $player->corporation()->getCivLevel();
          // TODO create action civ
          break;
        case BIOMASS:
          // TODO create action biomass
          break;
        case TECH:
          $levelTech = $player->corporation()->getTechLevel();
          // TODO create action tech
          break;
        case SYNERGY:
          $this->insertAsChild([
            'action' => CHOOSE_TRACKS,
            'args' => [
              'types' => [ALL_TYPES],
              'n' => 1
            ]
          ]);
          break;
        case ROVER:
          $this->insertAsChild([
            'action' => PLACE_ROVER,
          ]);
          break;
        default:
          //handle 'move_x' bonuses
          if (is_string($bonus) && str_starts_with($bonus, 'move')) {
            //TODO create action move with arg
          }
          break;
      }
    }
  }
}
