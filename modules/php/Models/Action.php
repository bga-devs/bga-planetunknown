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
    if (!$bonuses) {
      return;
    }

    $actions = [];

    foreach ($bonuses as $bonus) {
      switch ($bonus) {
        case CIV:
          $levelCiv = $player->corporation()->getCivLevel();
          $action = [
            'action' => TAKE_CIV_CARD,
            'args' => [
              'level' => $levelCiv,
            ],
          ];

          if (Globals::getPhase() == END_OF_TURN_PHASE) {
            $actions[] = $action;
          } else {
            $player->addEndOfTurnAction($action);
          }
          Notifications::milestone($player, CIV, $levelCiv);
          break;
        case BIOMASS:
          $patchToPlace = $player->corporation()->receiveBiomassPatch();
          if ($patchToPlace) {
            if ($player->corporation()->canUse(TECH_GET_SYNERGY_INSTEAD_OF_BIOMASS_PATCH_ONCE_PER_ROUND)) {
              //as the tech is optional, player can choose biomass or synergy
              $action = $player->getSynergy();
              if ($action) {
                $action['source'] = $player->corporation()->name;
                $action['flag'] = TECH_GET_SYNERGY_INSTEAD_OF_BIOMASS_PATCH_ONCE_PER_ROUND;
                $actions[] = [
                  'type' => NODE_XOR,
                  'childs' => [Actions::getBiomassPatchFlow($patchToPlace->getId()), $action],
                ];
              }
            }
            $actions[] = Actions::getBiomassPatchFlow($patchToPlace->getId());
          }
          break;
        case TECH:
          $levelTech = $player->corporation()->getTechLevel($this);

          $action = $player->corporation()->getTechAction($levelTech);
          if ($action) {
            $actions[] = $action;
          }

          // TODO Waiting for Adam
          // if ($player->corporation()->canUse(TECH_REPOSITION_THREE_LIFEPODS_ONCE)) {
          //   $actions[] = [
          //     'action' => POSITION_LIFEPOD_ON_TRACK,
          //     'args' => [
          //       'remaining' => 3,
          //     ],
          //     'source' => $player->corporation()->name,
          //     'flag' => TECH_REPOSITION_THREE_LIFEPODS_ONCE,
          //   ];
          // }

          if ($player->corporation()->getId() == FLUX) {
            $actions[] = [
              'action' => CHOOSE_FLUX_TRACK,
            ];
          }

          Notifications::milestone($player, TECH, $levelTech);
          break;
        case SYNERGY:
          $action = $player->getSynergy();
          if ($action) {
            $actions[] = $action;
            Notifications::milestone($player, $bonus);
          }
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
                'remaining' => $levelMove,
              ],
            ];
          }
          break;
      }
    }

    $this->pushParallelChilds($actions);
  }
}
