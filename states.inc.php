<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Planet Unknown implementation : © Timothée Pecatte <tim.pecatte@gmail.com>, Emmanuel Albisser <emmanuel.albisser@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * states.inc.php
 *
 * Planet Unknown game states description
 *
 */

$machinestates = [
  // The initial state. Please do not modify.
  ST_GAME_SETUP => [
    'name' => 'gameSetup',
    'description' => '',
    'type' => 'manager',
    'action' => 'stGameSetup',
    'transitions' => ['' => ST_CHOOSE_BOARDS],
  ],

  ST_CHOOSE_BOARDS => [
    'name' => 'chooseBoards',
    'type' => MULTI,
    'description' => clienttranslate('Waiting for everyone to choose their planet or/and corporation'),
    'descriptionmyturn' => clienttranslate('${you} must choose your planet or/and corporation'),
    'args' => 'argChooseBoards',
    'action' => 'stChooseBoards',
    'possibleactions' => ['chooseBoards'],
    'transitions' => ['' => ST_SECOND_SETUP],
  ],

  ST_SECOND_SETUP => [
    'name' => 'secondSetup',
    'type' => GAME,
    'description' => '',
    'action' => 'stSecondSetup',
    'transitions' => [
      NO_EVENT_CARD_GAME => ST_SETUP_BRANCH, //HACK TODO MODIFYshoud be NEXT PLAYER
      EVENT_CARD_GAME => ST_SETUP_BRANCH, // should be ST_EVENT_CARD
    ],
  ],

  ST_EVENT_CARD => [
    'name' => 'eventCard',
    'type' => MULTI,
    'description' => '',
    'args' => 'argEventCard', //reveal top event card
    'action' => 'stEventCard', //end turn if no actions required
    'transitions' => ['' => ST_SETUP_BRANCH],
  ],

  ST_CHOOSE_ROTATION => [
    'name' => 'chooseRotation',
    'type' => ACTIVE_PLAYER,
    'description' => clienttranslate('${actplayer} must choose Space Station orientation'),
    'descriptionmyturn' => clienttranslate('${you} must choose Space Station orientation'),
    'args' => 'argChooseRotation', // Is it needed ? send visible Tiles
    'possibleactions' => ['rotate'],
    'transitions' => ['' => ST_SETUP_BRANCH],
  ],

  ST_GENERIC_NEXT_PLAYER => [
    'name' => 'genericNextPlayer',
    'type' => 'game',
  ],

  //////////////////////////////////
  //  ____       _
  // / ___|  ___| |_ _   _ _ __
  // \___ \ / _ \ __| | | | '_ \
  //  ___) |  __/ |_| |_| | |_) |
  // |____/ \___|\__|\__,_| .__/
  //                      |_|
  //////////////////////////////////
  ST_SETUP_BRANCH => [
    'name' => 'setupBranch',
    'description' => '',
    'type' => 'game',
    'action' => 'stSetupBranch',
    'transitions' => [
      'selection' => ST_START_PARALLEL,
    ],
  ],

  //////////////////////////////
  //  _____
  // |_   _|   _ _ __ _ __
  //   | || | | | '__| '_ \
  //   | || |_| | |  | | | |
  //   |_| \__,_|_|  |_| |_|
  //////////////////////////////

  ST_START_PARALLEL => [
    'name' => 'startParallel',
    'type' => 'game',
    'action' => 'stStartParallel',
  ],

  ////////////////////////////////////
  //  _____             _
  // | ____|_ __   __ _(_)_ __   ___
  // |  _| | '_ \ / _` | | '_ \ / _ \
  // | |___| | | | (_| | | | | |  __/
  // |_____|_| |_|\__, |_|_| |_|\___|
  //              |___/
  ////////////////////////////////////
  ST_SETUP_PRIVATE_ENGINE => [
    'name' => 'setupEngine',
    'type' => 'multipleactiveplayer',
    'description' => clienttranslate('Waiting for everyone to confirm their moves'),
    'descriptionmyturn' => '',
    'initialprivate' => ST_INIT_PRIVATE_ENGINE,
    'possibleactions' => ['actCancel'],
    'transitions' => ['done' => ST_APPLY_ENGINE],
  ],

  ST_APPLY_ENGINE => [
    'name' => 'applyEngine',
    'type' => 'game',
    'action' => 'stApplyEngine',
  ],

  ST_INIT_PRIVATE_ENGINE => [
    'name' => 'initPrivateEngine',
    'descriptionmyturn' => '',
    'type' => 'private',
  ],

  ST_RESOLVE_STACK => [
    'name' => 'resolveStack',
    'type' => 'game',
    'action' => 'stResolveStack',
    'transitions' => [],
  ],

  ST_CONFIRM_TURN => [
    'name' => 'confirmTurn',
    'descriptionmyturn' => clienttranslate('${you} must confirm or restart your turn'),
    'type' => 'private',
    'args' => 'argsConfirmTurn',
    'action' => 'stConfirmTurn',
    'possibleactions' => ['actConfirmTurn', 'actRestart'],
  ],

  ST_CONFIRM_PARTIAL_TURN => [
    'name' => 'confirmPartialTurn',
    'description' => clienttranslate('${actplayer} must confirm the switch of player'),
    'descriptionmyturn' => clienttranslate('${you} must confirm the switch of player. You will not be able to restart turn'),
    'type' => 'activeplayer',
    'args' => 'argsConfirmTurn',
    // 'action' => 'stConfirmPartialTurn',
    'possibleactions' => ['actConfirmPartialTurn', 'actRestart'],
  ],

  ST_RESOLVE_CHOICE => [
    'name' => 'resolveChoice',
    'description' => clienttranslate('${actplayer} must choose which effect to resolve'),
    'descriptionmyturn' => clienttranslate('${you} must choose which effect to resolve'),
    'descriptionxor' => clienttranslate('${actplayer} must choose exactly one effect'),
    'descriptionmyturnxor' => clienttranslate('${you} must choose exactly one effect'),
    'type' => 'activeplayer',
    'args' => 'argsResolveChoice',
    'action' => 'stResolveChoice',
    'possibleactions' => ['actChooseAction', 'actRestart'],
  ],

  ST_IMPOSSIBLE_MANDATORY_ACTION => [
    'name' => 'impossibleAction',
    'description' => clienttranslate('${actplayer} can\'t take the mandatory action and must restart his turn or exchange/cook'),
    'descriptionmyturn' => clienttranslate(
      '${you} can\'t take the mandatory action. Restart your turn or exchange/cook to make it possible'
    ),
    'type' => 'activeplayer',
    'args' => 'argsImpossibleAction',
    'possibleactions' => ['actRestart'],
  ],

  ////////////////////////////////////////////////////////////////////////////
  //     _   _                  _         _        _   _
  //    / \ | |_ ___  _ __ ___ (_) ___   / \   ___| |_(_) ___  _ __  ___
  //   / _ \| __/ _ \| '_ ` _ \| |/ __| / _ \ / __| __| |/ _ \| '_ \/ __|
  //  / ___ \ || (_) | | | | | | | (__ / ___ \ (__| |_| | (_) | | | \__ \
  // /_/   \_\__\___/|_| |_| |_|_|\___/_/   \_\___|\__|_|\___/|_| |_|___/
  //
  ////////////////////////////////////////////////////////////////////////////
  ST_PLACE_TILE => [
    'name' => 'placeTile',
    'descriptionmyturn' => clienttranslate('${you} must place a tile'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actPlaceTile', 'actRestart'],
  ],

  ST_FOO_A => [
    'name' => 'fooA',
    'descriptionmyturn' => clienttranslate('${you} must fooA'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actFooA', 'actRestart'],
  ],

  ST_FOO_B => [
    'name' => 'fooB',
    'descriptionmyturn' => clienttranslate('${you} must fooB'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actFooB', 'actRestart'],
  ],

  ST_FOO_C => [
    'name' => 'fooC',
    'descriptionmyturn' => clienttranslate('${you} must fooC'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actFooC', 'actRestart'],
  ],

  //////////////////////////////////////////////////////////////////
  //  _____           _    ___   __    ____
  // | ____|_ __   __| |  / _ \ / _|  / ___| __ _ _ __ ___   ___
  // |  _| | '_ \ / _` | | | | | |_  | |  _ / _` | '_ ` _ \ / _ \
  // | |___| | | | (_| | | |_| |  _| | |_| | (_| | | | | | |  __/
  // |_____|_| |_|\__,_|  \___/|_|    \____|\__,_|_| |_| |_|\___|
  //////////////////////////////////////////////////////////////////

  ST_PRE_END_OF_GAME => [
    'name' => 'preEndOfGame',
    'type' => 'game',
    'action' => 'stPreEndOfGame',
    'transitions' => ['' => ST_END_GAME],
  ],

  // Final state.
  // Please do not modify (and do not overload action/args methods).
  ST_END_GAME => [
    'name' => 'gameEnd',
    'description' => clienttranslate('End of game'),
    'type' => 'manager',
    'action' => 'stGameEnd',
    'args' => 'argGameEnd',
  ],
];
