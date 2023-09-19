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
    'transitions' => ['' => ST_CHOOSE_SETUP],
  ],

  ST_CHOOSE_SETUP => [
    'name' => 'chooseSetup',
    'type' => MULTI,
    'description' => clienttranslate('Waiting for everyone to choose their setup'),
    'descriptionmyturn' => clienttranslate('${you} must choose your setup'),
    'args' => 'argChooseSetup',
    'action' => 'stChooseSetup',
    'possibleactions' => ['actChooseSetup'],
    'transitions' => [
      'notNeeded' => ST_SECOND_SETUP,
      'end' => ST_CONFIRM_SETUP,
    ],
  ],

  ST_CONFIRM_SETUP => [
    'name' => 'confirmSetup',
    'type' => GAME,
    'description' => '',
    'action' => 'stConfirmSetup',
    'transitions' => ['' => ST_SECOND_SETUP],
  ],

  ST_SECOND_SETUP => [
    'name' => 'secondSetup',
    'type' => GAME,
    'description' => '',
    'action' => 'stSecondSetup',
    'transitions' => [
      '' => ST_CHOOSE_ROTATION,
    ],
  ],

  ST_CHOOSE_ROTATION => [
    'name' => 'chooseRotation',
    'type' => ACTIVE_PLAYER,
    'description' => clienttranslate('${actplayer} must choose Space Station orientation'),
    'descriptionmyturn' => clienttranslate('${you} must choose Space Station orientation'),
    'action' => 'stChooseRotation',
    'possibleactions' => ['rotate'],
    'transitions' => [
      NO_EVENT_CARD_GAME => ST_SETUP_BRANCH,
      EVENT_CARD_GAME => ST_EVENT_CARD,
    ],
  ],

  ST_EVENT_CARD => [
    'name' => 'eventCard',
    'type' => GAME,
    'description' => '',
    'action' => 'stEventCard', //reveal top event card and prepare engine
    'transitions' => ['' => ST_PLAY_AFTER_EVENT_CARD],
  ],

  // ST_NEXT_PLAYER => [
  //   'name' => 'nextPlayer',
  //   'type' => GAME,
  //   'description' => '',
  //   'action' => 'stNextPlayer',
  //   'transitions' => ['' => ST_SETUP_BRANCH],
  // ],

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

  ST_PRE_CHOOSE_CIV_CARD => [
    'name' => 'preChooseCivCard',
    'description' => '',
    'type' => GAME,
    'action' => 'stPreChooseCivCard',
    'transitions' => [
      '' => ST_TAKE_CIV_CARD,
    ],
  ],

  ST_CHOOSE_CIV_CARD => [
    'name' => 'chooseCivCard',
    'description' => '',
    'type' => GAME,
    'action' => 'chooseCivCard',
    'transitions' => [
      '' => ST_POST_CHOOSE_CIV_CARD,
    ],
  ],

  ST_POST_CHOOSE_CIV_CARD => [
    'name' => 'postChooseCivCard',
    'description' => '',
    'type' => GAME,
    'action' => 'stPostChooseCivCard', //remove pendings endofturn actions
    'transitions' => [
      'gameEnd' => ST_PRE_END_OF_GAME,
      'nextTurn' => ST_CHOOSE_ROTATION,
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

  ST_PLAY_AFTER_EVENT_CARD => [
    'name' => 'playAfterEventCard',
    'type' => GAME,
    'action' => 'stPlayAfterEventCard',
  ], // should end with -> ST_SETUP_BRANCH

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
    'descriptionCivCard' => clienttranslate('Waiting for players to take their civ card'),
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
    'action' => 'stInitPrivateEngine',
    'descriptionmyturn' => '',
    'args' => 'test',
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
    'type' => 'private',
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
    'type' => 'private',
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
    'type' => 'private',
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

  ST_MOVE_TRACK => [
    'name' => 'moveTrack',
    'descriptionmyturn' => clienttranslate('${you} must move your track ${type}'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actMoveTrack', 'actRestart'],
  ],

  ST_MOVE_TRACKER_BY_ONE => [
    'name' => 'moveTrackerByOne',
    'descriptionmyturn' => clienttranslate('${you} must move your tracker ${type} by one'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actMoveTrackerByOne', 'actRestart'],
  ],

  ST_TAKE_CIV_CARD => [
    'name' => TAKE_CIV_CARD,
    'descriptionmyturn' => clienttranslate('${you} must take a civ card from the deck ${level}'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actTakeCivCard', 'actRestart'],
  ],

  ST_CHOOSE_TRACKS => [
    'name' => 'chooseTracks',
    'descriptionmyturn' => clienttranslate('${you} must choose ${n} track(s) to move thanks to ${from}'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actChooseTracks', 'actRestart'],
  ],

  ST_PLACE_ROVER => [
    'name' => 'placeRover',
    'descriptionmyturn' => clienttranslate('${you} must place a new Rover'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actPlaceRover', 'actRestart'],
  ],

  ST_POSITION_LIFEPOD_ON_TRACK => [
    'name' => POSITION_LIFEPOD_ON_TRACK,
    'descriptionmyturn' => clienttranslate('${you} can place a lifepod on a track (${remaining} remaining)'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actPositionLifepodOnTrack', 'actRestart'],
  ],

  ST_MOVE_ROVER => [
    'name' => 'moveRover',
    'descriptionmyturn' => clienttranslate('${you} must move your Rover(s) (${remaining} move(s) remaining)'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actMoveRover', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_COLLECT_MEEPLE => [
    'name' => COLLECT_MEEPLE,
    'descriptionmyturn' => clienttranslate('${you} must ${action} ${n} ${type} on your planet'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCollectMeeple', 'actRestart'],
  ],

  ST_DESTROY_ALL_IN_ROW => [
    'name' => DESTROY_ALL_IN_ROW,
    'descriptionmyturn' => clienttranslate('${you} must choose a row or a column to destroy all its meteorites'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actDestroyAllInRow', 'actRestart'],
  ],

  ST_MOVE_TRACKERS_TO_FIVE => [
    'name' => MOVE_TRACKERS_TO_FIVE,
    'descriptionmyturn' => clienttranslate('${you} must advance all trackers to 5th position'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actMoveTrackersToFive', 'actRestart'],
  ],

  ST_PLACE_MEEPLE => [
    'name' => PLACE_MEEPLE,
    'descriptionmyturn' => clienttranslate('${you} must place a ${type} on your planet'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actPlaceMeeple', 'actRestart'],
  ],

  ST_CHOOSE_ROTATION_ENGINE => [
    'name' => CHOOSE_ROTATION_ENGINE,
    'descriptionmyturn' => clienttranslate('${you} must choose Space Station orientation'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actChooseRotation', 'actRestart'],
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
