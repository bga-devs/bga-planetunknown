<?php
namespace PU\States;
use PU\Core\Globals;
use PU\Core\PGlobals;
use PU\Core\Engine;
use PU\Core\Game;
use PU\Core\Notifications;
use PU\Managers\Players;
use PU\Managers\Meeples;
use PU\Managers\Actions;
use PU\Helpers\Log;

trait EngineTrait
{
  function addCommonArgs($pId, &$args)
  {
    $args['previousEngineChoices'] = PGlobals::getEngineChoices($pId);
    // $args['previousSteps'] = Log::getUndoableSteps();
  }

  /**
   * Trying to get the atomic action corresponding to the state where the game is
   */
  function getCurrentAtomicAction($pId)
  {
    $stateId = PGlobals::getState($pId);
    return Actions::getActionOfState($stateId);
  }

  /**
   * Ask the corresponding atomic action for its args
   */
  function argsAtomicAction($pId)
  {
    $player = Players::get($pId);
    $action = $this->getCurrentAtomicAction($pId);
    $node = Engine::getNextUnresolved($pId);
    $args = Actions::getArgs($action, $node);
    $args['automaticAction'] = Actions::get($action, $node)->isAutomatic($player);
    $this->addCommonArgs($pId, $args);
    $this->addArgsAnytimeAction($pId, $args, $action);

    return $args;
  }

  /**
   * Add anytime actions
   */
  function addArgsAnytimeAction($pId, &$args, $action)
  {
    //   $this->addCommonArgs($args);

    //   // If the action is auto => don't display anytime buttons
    //   if ($args['automaticAction'] ?? false) {
    //     return;
    //   }
    //   $player = Players::getActive();
    //   $actions = [];

    //   // Keep only doable actions
    //   $anytimeActions = [];
    //   foreach ($actions as $flow) {
    //     $tree = Engine::buildTree($flow);
    //     if ($tree->isDoable($player)) {
    //       $anytimeActions[] = [
    //         'flow' => $flow,
    //         'desc' => $flow['desc'] ?? $tree->getDescription(true),
    //         'optionalAction' => $tree->isOptional(),
    //         'independentAction' => $tree->isIndependent($player),
    //       ];
    //     }
    //   }
    //   $args['anytimeActions'] = $anytimeActions;
    // }

    // function actAnytimeAction($choiceId, $auto = false)
    // {
    //   $args = $this->gamestate->state()['args'];
    //   if (!isset($args['anytimeActions'][$choiceId])) {
    //     throw new \BgaVisibleSystemException('You can\'t take this anytime action');
    //   }

    //   $flow = $args['anytimeActions'][$choiceId]['flow'];
    //   if (!$auto) {
    //     Globals::incEngineChoices();
    //   }
    //   Engine::insertAtRoot($flow, false);
    //   Engine::proceed();
  }

  /**
   * Pass the argument of the action to the atomic action
   */
  function actTakeAtomicAction($actionName, $args)
  {
    self::checkAction($actionName);
    $pId = Players::getCurrentId();
    $action = $this->getCurrentAtomicAction($pId);
    Actions::takeAction($action, $actionName, $args, Engine::getNextUnresolved($pId));
  }

  /**
   * To pass if the action is an optional one
   */
  function actPassOptionalAction($auto = false)
  {
    if ($auto) {
      $this->gamestate->checkPossibleAction('actPassOptionalAction');
    } else {
      self::checkAction('actPassOptionalAction');
    }

    $action = $this->getCurrentAtomicAction();
    Actions::pass($action, Engine::getNextUnresolved());
  }

  /**
   * Pass the argument of the action to the atomic action
   */
  function stAtomicAction($pId)
  {
    $action = $this->getCurrentAtomicAction($pId);
    Actions::stAction($action, Engine::getNextUnresolved($pId));
  }

  /********************************
   ********************************
   ********** FLOW CHOICE *********
   ********************************
   ********************************/
  function argsResolveChoice($pId)
  {
    $player = Players::get($pId);
    $node = Engine::getNextUnresolved($pId);
    $args = array_merge($node->getArgs() ?? [], [
      'choices' => Engine::getNextChoice($player),
      'allChoices' => Engine::getNextChoice($player, true),
    ]);
    if ($node instanceof \PU\Core\Engine\XorNode) {
      $args['descSuffix'] = 'xor';
    }
    // $sourceId = $node->getSourceId() ?? null;
    // if (!isset($args['source']) && !is_null($sourceId)) {
    //   $args['sourceId'] = $sourceId;
    //   $args['source'] = ZooCards::get($sourceId)->getName();
    // }
    $this->addCommonArgs($pId, $args);
    $this->addArgsAnytimeAction($pId, $args, 'resolveChoice');
    return $args;
  }

  function actChooseAction($choiceId)
  {
    $player = Players::getCurrent();
    Engine::chooseNode($player, $choiceId);
  }

  public function stResolveStack()
  {
  }

  public function stResolveChoice()
  {
  }

  function argsImpossibleAction($pId)
  {
    $node = Engine::getNextUnresolved($pId);
    $args = [
      'desc' => $node->getDescription(),
    ];
    $this->addArgsAnytimeAction($pId, $args, 'impossibleAction');
    return $args;
  }

  /*******************************
   ******* CONFIRM / RESTART ******
   ********************************/
  public function argsConfirmTurn($pId)
  {
    $data = [
      'previousEngineChoices' => PGlobals::getEngineChoices($pId),
      // 'previousSteps' => Log::getUndoableSteps(),
      'automaticAction' => false,
    ];
    $this->addArgsAnytimeAction($pId, $data, 'confirmTurn');
    return $data;
  }

  public function stConfirmTurn($pId)
  {
    // Check user preference to bypass if DISABLED is picked
    $pref = Players::get($pId)->getPref(OPTION_CONFIRM);
    if ($pref == OPTION_CONFIRM_DISABLED) {
      $this->actConfirmTurn(true, $pId);
    }
  }

  public function actConfirmTurn($auto = false, $pId = null)
  {
    if (!$auto) {
      self::checkAction('actConfirmTurn');
      $pId = Players::getCurrentId();
    }
    Engine::confirm($pId);
  }

  public function actConfirmPartialTurn($auto = false)
  {
    if (!$auto) {
      self::checkAction('actConfirmPartialTurn');
    }
    Engine::confirmPartialTurn();
  }

  public function actRestart()
  {
    self::checkAction('actRestart');
    $pId = Players::getCurrentId();
    if (PGlobals::getEngineChoices($pId) < 1) {
      throw new \BgaVisibleSystemException('No choice to undo');
    }
    Engine::restart($pId);
  }

  public function actUndoToStep($stepId)
  {
    self::checkAction('actRestart');
    Engine::undoToStep($stepId);
  }

  public function actCancelEngine()
  {
    $pId = Players::getCurrentId();
    $this->gamestate->setPlayersMultiactive([$pId], '');
    $state = PGlobals::getState($pId);
    $this->gamestate->setPrivateState($pId, $state);
  }

  public function stApplyEngine()
  {
    // TODO : apply engine

    Engine::callback();
  }
}
