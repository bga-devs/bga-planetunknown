<?php

namespace PU\Core;

use PU\Core\Engine;
use PU\Managers\Players;
use PU\Managers\Actions;
use PU\Helpers\Log;
use PU\Helpers\QueryBuilder;
use PU\Helpers\UserException;

/*
 * Engine: a class that allows to handle complex flow
 */

class Engine
{
  public static $trees = null;
  public static $replayed = false;

  public function invalidate()
  {
    static::$replayed = false;
    self::boot();
  }

  public function boot()
  {
    $cPId = Players::getCurrentId(true) ?? 0;

    $flows = PGlobals::getAll('engine');
    self::$trees = [];
    foreach ($flows as $pId => $t) {
      if (empty($t)) {
        continue;
      }

      $flowTree = self::buildTree($t);
      self::$trees[$pId] = $flowTree;

      // TODO : switch the two lines in a week
      // if ($cPId == $pId && !static::$replayed && Globals::getMode() == MODE_APPLY) {
      if ($cPId == $pId && !static::$replayed) {
        Globals::setReplayMode();
        $flowTree->replay();
        Globals::unsetReplayMode();
        static::$replayed = true;
      }
    }
  }

  public function apply()
  {
    Log::clearCache(false);
    Globals::setMode(MODE_APPLY);
    foreach (self::$trees as $pId => $t) {
      $t->replay();
      PGlobals::setEngine($pId, []);
      Game::get()->sendNotifications();
    }
    Log::clearUndoableStepNotifications();
  }

  /**
   * Save current tree into Globals table
   */

  public function save($pId)
  {
    $t = self::$trees[$pId]->toArray();
    PGlobals::setEngine($pId, $t);
  }

  /**
   * Setup the engine, given an array representing a tree
   * @param array $t
   */
  public function multipleSetup($aTrees, $callback, $descPrefix = '')
  {
    Globals::setEngineWaitingDescriptionSuffix($descPrefix);
    Globals::setCallbackEngineResolved($callback);
    $allPIds = Players::getAll()->getIds();
    $pIds = array_keys($aTrees);
    if (empty($pIds)) {
      self::callback();
      return;
    }

    // Clear existing engines
    foreach ($allPIds as $pId) {
      PGlobals::setEngine($pId, []);
      PGlobals::setEngineChoices($pId, 0);
    }

    self::$trees = [];
    foreach ($pIds as $pId) {
      // Build the tree while enforcing $pId at root
      $aTree = $aTrees[$pId];
      $aTree['pId'] = $pId;
      $tree = self::buildTree($aTree);
      if (!$tree instanceof \PU\Core\Engine\SeqNode) {
        $tree = new \PU\Core\Engine\SeqNode(['pId' => $pId], [$tree]);
      }

      // Savee it
      self::$trees[$pId] = $tree;
      PGlobals::setEngine($pId, $tree->toArray());
      PGlobals::setEngineChoices($pId, 0);
    }

    $gm = Game::get()->gamestate;
    $gm->jumpToState(ST_GENERIC_NEXT_PLAYER);
    $gm->setPlayersMultiactive($pIds, '', true);
    $gm->jumpToState(ST_SETUP_PRIVATE_ENGINE);
    $gm->initializePrivateStateForPlayers($pIds);
    Globals::setMode(MODE_PRIVATE);
    self::multipleProceed($pIds);
    Log::startEngine();
  }

  public function setup($t, $callback, $descPrefix = '', $pIds = null)
  {
    $allPIds = Players::getAll()->getIds();
    $pIds = $pIds ?? $allPIds;
    $aTrees = [];
    foreach ($pIds as $pId) {
      $aTrees[$pId] = $t;
    }

    self::multipleSetup($aTrees, $callback, $descPrefix);
  }

  /**
   * Convert an array into a tree
   * @param array $t
   */
  public function buildTree($t)
  {
    $t['childs'] = $t['childs'] ?? [];
    $type = $t['type'] ?? (empty($t['childs']) ? NODE_LEAF : NODE_SEQ);

    $childs = [];
    foreach ($t['childs'] as $child) {
      $childs[] = self::buildTree($child);
    }

    $className = '\PU\Core\Engine\\' . ucfirst($type) . 'Node';
    unset($t['childs']);
    return new $className($t, $childs);
  }

  /**
   * Recursively compute the next unresolved node we are going to address
   */
  public function getNextUnresolved($pId)
  {
    return isset(self::$trees[$pId]) ? self::$trees[$pId]->getNextUnresolved() : null;
  }

  /**
   * Change state
   */
  protected function setState($pId, $newState, $globalOnly = false)
  {
    PGlobals::setState($pId, $newState);
    if (!$globalOnly) {
      Game::get()->gamestate->setPrivateState($pId, $newState);
    }
  }

  /**
   * Proceed to next unresolved part of tree
   */
  public function multipleProceed($pIds)
  {
    foreach ($pIds as $pId) {
      self::proceed($pId);
    }
  }

  public function proceed($pId, $confirmedPartial = false, $isUndo = false)
  {
    $node = self::getNextUnresolved($pId);

    // Are we done ?
    if ($node == null) {
      if (PGlobals::getEngineChoices($pId) == 0) {
        self::confirm($pId); // No choices were made => auto confirm
      } else {
        // Confirm/restart
        self::setState($pId, ST_CONFIRM_TURN);
      }
      return;
    }

    $player = Players::get($pId);
    if ($confirmedPartial) {
      Log::checkpoint();
      Globals::setEngineChoices(0);
    }

    // If node with choice, switch to choice state
    $choices = $node->getChoices($player);
    $allChoices = $node->getChoices($player, true);
    if (!empty($allChoices) && $node->getType() != NODE_LEAF) {
      // Only one choice : auto choose
      $id = array_keys($choices)[0] ?? null;
      if (
        count($choices) == 1 &&
        count($allChoices) == 1 &&
        array_keys($allChoices) == array_keys($choices) &&
        count($node->getChilds()) == 1 &&
        !$choices[$id]['irreversibleAction']
      ) {
        self::chooseNode($player, $id, true);
      } else {
        // Otherwise, go in the RESOLVE_CHOICE state
        self::setState($pId, ST_RESOLVE_CHOICE);
      }
    } else {
      // No choice => proceed to do the action
      self::proceedToAction($pId, $node, $isUndo);
    }
  }

  public function proceedToAction($pId, $node, $isUndo = false)
  {
    $actionId = $node->getAction();
    if (is_null($actionId)) {
      throw new \BgaVisibleSystemException('Trying to get action on a leaf without action');
    }

    $player = Players::get($pId);
    // Do some pre-action code if needed and if we are not undoing to an irreversible node
    if (!$isUndo || !$node->isIrreversible($player)) {
      Actions::stPreAction($actionId, $node);
    }

    $state = Actions::getState($actionId, $node);
    if (is_null($state)) {
      die('TODO: action without state');
    }
    self::setState($pId, $state);
  }

  /**
   * Get the list of choices of current node
   */
  public function getNextChoice($player, $displayAllChoices = false)
  {
    $node = self::getNextUnresolved($player->getId());
    return $node->getChoices($player, $displayAllChoices);
  }

  /**
   * Choose one option
   */
  public function chooseNode($player, $nodeId, $auto = false)
  {
    $pId = $player->getId();
    $node = self::getNextUnresolved($pId);
    $args = $node->getChoices($player);
    if (!isset($args[$nodeId])) {
      throw new \BgaVisibleSystemException('This choice is not possible');
    }

    if (!$auto) {
      PGlobals::incEngineChoices($pId);
      Log::step();
    }

    if ($nodeId == PASS) {
      $node->resolve(PASS);
      self::save($pId);
      self::proceed($pId);
      return;
    }

    if ($node->getChilds()[$nodeId]->isResolved()) {
      throw new \BgaVisibleSystemException('Node is already resolved');
    }
    $node->choose($nodeId, $auto);
    self::save($pId);
    self::proceed($pId);
  }

  /**
   * Resolve action : resolve the action of a leaf action node
   */
  public function resolveAction($args = [], $checkpoint = false, &$node = null, $automatic = false)
  {
    if (is_null($node)) {
      die('Not possible');
    }

    // Resolve node
    $node->resolveAction($args);
    if ($node->isResolvingParent()) {
      $node->getParent()->resolve([]);
    }

    // Save
    $pId = $node->getRoot()->getPId();
    self::save($pId);

    if (!$automatic) {
      PGlobals::incEngineChoices($pId);
    }
    if ($checkpoint) {
      self::checkpoint();
    }
  }

  public function checkpoint()
  {
    $player = Players::getActive();
    $node = self::getUndoableMandatoryNode($player);
    if (!is_null($node) && $node->getPId() == $player->getId()) {
      throw new UserException(
        totranslate(
          "You can't take an irreversible action if there is a mandatory undoable action pending (eg unpayable Venom fee)"
        )
      );
    }

    // TODO : flag the tree
    Globals::setEngineChoices(0);
    Log::checkpoint();
  }

  /**
   * Insert a new node right before current pending node
   */
  public function insertBeforeCurrent($pId, $t)
  {
    $node = self::getNextUnresolved($pId);

    // NULL => insert at root
    if (is_null($node)) {
      $node = self::$trees[$pId];
      $node->pushChild(self::buildTree($t));
    }
    // Parallel/Or node => just append and call chooseNode
    elseif ($node instanceof \PU\Core\Engine\ParallelNode || $node instanceof \PU\Core\Engine\OrNode) {
      $node->pushChild(self::buildTree($t));
      $node->choose(count($node->getChilds()) - 1, true);
    }
    // Otherwise => check parent
    else {
      $parent = $node->getParent();

      // Seq node => just insert before current node
      if ($parent instanceof \PU\Core\Engine\SeqNode) {
        $index = $node->getIndex() - 1;
        $parent->insertChildAtPos(self::buildTree($t), $index);
      }
      // Other case : try to insert a SEQ node on top of it
      else {
        $node2 = self::buildTree([
          'type' => NODE_SEQ,
          'childs' => [$t->toArray()],
        ]);
        $parent = $node->replace($node2);
        $index = -1;
        $parent->insertChildAtPos(self::buildTree($t), $index);
        die('NOT TESTED : INSERT BEFORE CURRENT');
      }
    }

    self::save($pId);
  }

  /**
   * insertAsChild: turn the node into a SEQ if needed, then insert the flow tree as a child
   */
  public function insertAsChild($t, &$node)
  {
    if (is_null($t)) {
      return;
    }

    // If the node is an action leaf, turn it into a SEQ node first
    if ($node->getType() == NODE_LEAF) {
      $newNode = $node->toArray();
      $newNode['type'] = NODE_SEQ;
      $node = $node->replace(self::buildTree($newNode));
    }

    // Push child
    $pId = $node->getRoot()->getPId();
    $node->pushChild(self::buildTree($t));
    self::save($pId);
  }

  /**
   * insertOrUpdateParallelChilds:
   *  - if the node is a parallel node => insert all the nodes as childs
   *  - if one of the child is a parallel node => insert as their childs instead
   *  - otherwise, make the action a parallel node
   */

  public function insertOrUpdateParallelChilds($childs, &$node)
  {
    if (empty($childs)) {
      return;
    }

    $pId = $node->getRoot()->getPId();
    if ($node->getType() == NODE_SEQ) {
      // search if we have children and if so if we have a parallel node
      foreach ($node->getChilds() as $child) {
        if ($child->getType() == NODE_PARALLEL) {
          foreach ($childs as $newChild) {
            $child->pushChild(self::buildTree($newChild));
          }
          self::save($pId);
          return;
        }
      }

      $node->pushChild(
        self::buildTree([
          'type' => \NODE_PARALLEL,
          'childs' => $childs,
        ])
      );
    }
    // Otherwise, turn the node into a PARALLEL node if needed, and then insert the childs
    else {
      // If the node is an action leaf, turn it into a Parallel node first
      if ($node->getType() == NODE_LEAF) {
        $newNode = $node->toArray();
        $newNode['type'] = NODE_PARALLEL;
        $node = $node->replace(self::buildTree($newNode));
      }

      // Push childs
      foreach ($childs as $newChild) {
        $node->pushChild(self::buildTree($newChild));
      }
      self::save($pId);
    }
  }

  /**
   * Confirm the full resolution of current flow
   */
  public function confirm($pId)
  {
    $node = self::getNextUnresolved($pId);
    // Are we done ?
    if ($node != null) {
      var_dump($node);
      throw new \feException("You can't confirm an ongoing turn");
    }

    // Make him inactive
    Game::get()->gamestate->setPlayerNonMultiactive($pId, 'done');
  }

  public function callback()
  {
    // Callback
    $callback = Globals::getCallbackEngineResolved();
    if (isset($callback['state'])) {
      Game::get()->gamestate->jumpToState($callback['state']);
    } elseif (isset($callback['order'])) {
      Game::get()->nextPlayerCustomOrder($callback['order']);
    } elseif (isset($callback['method'])) {
      $name = $callback['method'];
      Game::get()->$name();
    }
  }

  /**
   * Restart the whole flow
   */
  public function restart($pId)
  {
    Log::undoTurn($pId);

    $flow = PGlobals::getEngine($pId);
    self::$trees[$pId] = self::buildTree($flow);

    self::proceed($pId, false, true);
    Notifications::flush();
  }

  /**
   * Restart at a given step
   */
  public function undoToStep($pId, $stepId)
  {
    Log::undoToStep($pId, $stepId);

    // Force to clear cached informations
    self::proceed($pId, false, true);
    Notifications::flush();
  }

  /**
   * Clear all nodes related to the current active zombie player
   *
  public function clearZombieNodes($pId)
  {
    self::$tree->clearZombieNodes($pId);
  }
   */
}
