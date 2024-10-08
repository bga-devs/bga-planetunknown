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
    'transitions' => ['' => ST_SETUP_BRANCH],
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
    'transitions' => ['done' => ST_FINISH_SETUP],
  ],

  ST_CHOOSE_SETUP => [
    'name' => 'chooseSetup',
    'type' => MULTI,
    'description' => clienttranslate('Waiting for everyone to choose their setup'),
    'descriptionmyturn' => clienttranslate('${you} must choose your setup'),
    'args' => 'argChooseSetup',
    'possibleactions' => ['actChooseSetup'],
    'transitions' => [
      'notNeeded' => ST_FINISH_SETUP,
      'done' => ST_FINISH_SETUP,
    ],
  ],

  ST_FINISH_SETUP => [
    'name' => 'finishSetup',
    'type' => GAME,
    'description' => '',
    'action' => 'stFinishSetup',
    'transitions' => [
      'play' => ST_CHOOSE_ROTATION,
    ],
  ],

  ////////////////////////////////////////////////////////////////////
  //  ____  _             _            __   _____
  // / ___|| |_ __ _ _ __| |_    ___  / _| |_   _|   _ _ __ _ __
  // \___ \| __/ _` | '__| __|  / _ \| |_    | || | | | '__| '_ \
  //  ___) | || (_| | |  | |_  | (_) |  _|   | || |_| | |  | | | |
  // |____/ \__\__,_|_|   \__|  \___/|_|     |_| \__,_|_|  |_| |_|
  ////////////////////////////////////////////////////////////////////

  ST_START_TURN => [
    'name' => 'startTurn',
    'description' => '',
    'type' => 'game',
    'action' => 'stStartTurn',
  ],

  ST_CHOOSE_ROTATION => [
    'name' => 'chooseRotation',
    'type' => ACTIVE_PLAYER,
    'description' => clienttranslate('${actplayer} must choose Space Station orientation'),
    'descriptionmyturn' => clienttranslate('${you} must choose Space Station orientation'),
    'action' => 'stChooseRotation',
    'possibleactions' => ['actChooseRotation'],
    'transitions' => [
      NO_EVENT_CARD_GAME => ST_START_TURN_ENGINE,
      EVENT_CARD_GAME => ST_REVEAL_EVENT_CARD,
    ],
  ],

  //////////////////////////
  ///////// EVENTS /////////
  //////////////////////////

  ST_REVEAL_EVENT_CARD => [
    'name' => 'revealEventCard',
    'type' => GAME,
    'description' => '',
    'action' => 'stRevealEventCard', //reveal top event card and prepare engine
    'transitions' => ['' => ST_PLAY_AFTER_EVENT_CARD],
  ],

  ST_PLAY_AFTER_EVENT_CARD => [
    'name' => 'playAfterEventCard',
    'type' => GAME,
    'action' => 'stPlayAfterEventCard',
  ],

  //////////////////////////
  // LAUNCH ENGINE
  /////////////////////////
  ST_START_TURN_ENGINE => [
    'name' => 'startTurnEngine',
    'type' => 'game',
    'action' => 'stStartTurnEngine',
  ],

  ////////////////////////////////////////////////////////////
  //  _____           _          __   _____
  // | ____|_ __   __| |   ___  / _| |_   _|   _ _ __ _ __
  // |  _| | '_ \ / _` |  / _ \| |_    | || | | | '__| '_ \
  // | |___| | | | (_| | | (_) |  _|   | || |_| | |  | | | |
  // |_____|_| |_|\__,_|  \___/|_|     |_| \__,_|_|  |_| |_|
  ////////////////////////////////////////////////////////////

  ST_CHOOSE_CIV_CARD => [
    'name' => 'chooseCivCard',
    'description' => '',
    'type' => GAME,
    'action' => 'chooseCivCard',
    'transitions' => [
      '' => ST_END_TURN,
    ],
  ],

  ST_END_TURN => [
    'name' => 'endTurn',
    'description' => '',
    'type' => GAME,
    'action' => 'stEndTurn',
    'updateGameProgression' => true,
  ],

  ////////////////////////////////////
  //  _____             _
  // | ____|_ __   __ _(_)_ __   ___
  // |  _| | '_ \ / _` | | '_ \ / _ \
  // | |___| | | | (_| | | | | |  __/
  // |_____|_| |_|\__, |_|_| |_|\___|
  //              |___/
  ////////////////////////////////////
  ST_GENERIC_NEXT_PLAYER => [
    'name' => 'genericNextPlayer',
    'type' => 'game',
  ],

  ST_SETUP_PRIVATE_ENGINE => [
    'name' => 'setupEngine',
    'type' => 'multipleactiveplayer',
    'description' => clienttranslate('Waiting for everyone to confirm their moves'),
    'descriptionCivCard' => clienttranslate('Waiting for players to take their civ card'),
    'descriptionmyturn' => '',
    'args' => 'argsSetupEngine',
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
    'descriptionmyturnimpossible' => clienttranslate('${you} can\'t place a tile. Choose one to keep for your last round'),
    'descriptionmyturnskippablebiomass' => clienttranslate(
      '${you} may place your biomass patch or keep it for the end of the game'
    ),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => [
      'actPlaceTile',
      'actKeepBiomassPatch',
      'actPlaceTileNoPlacement',
      'actRestart',
      'actPassOptionalAction',
    ],
  ],

  ST_MOVE_TRACK => [
    'name' => 'moveTrack',
    'descriptionmyturn' => clienttranslate('${you} must move your track ${type}'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actMoveTrack', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_MOVE_TRACKER_BY_ONE => [
    'name' => 'moveTrackerByOne',
    'descriptionmyturn' => clienttranslate('${you} must move your tracker ${type} by one'),
    'descriptionmyturnskippable' => clienttranslate('${you} may move your tracker ${type} by one'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actMoveTrackerByOne', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_TAKE_CIV_CARD => [
    'name' => TAKE_CIV_CARD,
    'descriptionmyturn' => clienttranslate('${you} must take a civ card from the deck ${level}'),
    'descriptionmyturnall' => clienttranslate('${you} must take up to 2 civ cards in the remaining cards from all decks'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actTakeCivCard', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_CHOOSE_TRACKS => [
    'name' => 'chooseTracks',
    'descriptionmyturn' => clienttranslate('${you} must choose ${n} track(s) to move thanks to ${from}'),
    'descriptionmyturnregress' => clienttranslate('${you} must choose ${n} track(s) to regress (${from})'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actChooseTracks', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_PLACE_ROVER => [
    'name' => 'placeRover',
    'descriptionmyturn' => clienttranslate('${you} must place a new Rover'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actPlaceRover', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_POSITION_LIFEPOD_ON_TRACK => [
    'name' => POSITION_LIFEPOD_ON_TRACK,
    'descriptionmyturn' => clienttranslate('${you} can place a lifepod on a track (${remaining} remaining)'),
    'descriptionmyturnaftercollect' => clienttranslate('${you} may place the lifepod you collected on a track'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actPositionLifepodOnTrack', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_POSITION_LIFEPOD_ON_TECH => [
    'name' => POSITION_LIFEPOD_ON_TECH,
    'descriptionmyturn' => clienttranslate('${you} may place the lifepod you collected on a tech'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actPositionLifepodOnTech', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_MOVE_ROVER => [
    'name' => 'moveRover',
    'descriptionmyturn' => clienttranslate('${you} must move your Rover(s) (${remaining} move(s) remaining)'),
    'descriptionmyturnskippable' => clienttranslate('${you} may move your Rover(s) (${remaining} move(s) remaining)'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actMoveRover', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_COLLECT_MEEPLE => [
    'name' => COLLECT_MEEPLE,
    'descriptionmyturn' => clienttranslate('${you} must ${action} ${n} ${type} from your ${where}'),
    'descriptionmyturnskippable' => clienttranslate('${you} may ${action} ${n} ${type} from your ${where}'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actCollectMeeple', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_DESTROY_ALL_IN_ROW => [
    'name' => DESTROY_ALL_IN_ROW,
    'descriptionmyturn' => clienttranslate('${you} must choose a row or a column to destroy all its meteorites'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actDestroyAllInRow', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_MOVE_TRACKERS_TO_FIVE => [
    'name' => MOVE_TRACKERS_TO_FIVE,
    'descriptionmyturn' => clienttranslate('${you} must choose which track to advance now to get all of them in 5th position'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actMoveTrackersToFive', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_CLAIM_ALL_IN_A_ROW => [
    'name' => CLAIM_ALL_IN_A_ROW,
    'descriptionmyturn' => clienttranslate('${you} must choose a row with a tracker on it to receive all benefits on that row'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actClaimAllInARow', 'actRestart'],
  ],

  ST_CHOOSE_OBJECTIVE_FOR_ALL => [
    'name' => CHOOSE_OBJECTIVE_FOR_ALL,
    'descriptionmyturn' => clienttranslate('${you} must choose one objective card that all players compete for'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actChooseObjectiveForAll', 'actRestart'],
  ],

  ST_REACH_NEXT_MILESTONE => [
    'name' => REACH_NEXT_MILESTONE,
    'descriptionmyturn' => clienttranslate('${you} can move your tracker on flux track to the next milestone'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actReachNextMilestone', 'actRestart'],
  ],

  ST_PLACE_MEEPLE => [
    'name' => PLACE_MEEPLE,
    'descriptionmyturn' => clienttranslate('${you} must place 1 ${meeple_type_name} on your planet'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actPlaceMeeple', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_CHOOSE_ROTATION_ENGINE => [
    'name' => 'chooseRotationEngine',
    'descriptionmyturn' => clienttranslate('${you} must choose Space Station orientation'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actChooseRotation', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_PEEK_NEXT_EVENT => [
    'name' => PEEK_NEXT_EVENT,
    'descriptionmyturn' => '',
    'type' => 'private',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actPeekNextEvent', 'actRestart', 'actPassOptionalAction'],
  ],

  ST_CHOOSE_FLUX_TRACK => [
    'name' => CHOOSE_FLUX_TRACK,
    'descriptionmyturn' => clienttranslate('${you} must choose on which track to place the flux token'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actChooseFluxTrack', 'actRestart'],
  ],

  ST_DESTROY_P_O_CARD => [
    'name' => DESTROY_P_O_CARD,
    'descriptionmyturn' => clienttranslate('${you} must choose a private objective to destroy'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actDestroyPOCard', 'actRestart'],
  ],

  ST_RESET_TRACK => [
    'name' => RESET_TRACK,
    'descriptionmyturn' => '',
    'type' => 'private',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actResetTrack'],
  ],

  ST_EMPTY_SLOT => [
    'name' => EMPTY_SLOT,
    'descriptionmyturn' => clienttranslate('${you} must choose which slot to empty'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actEmptySlot'],
  ],

  ST_GAIN_BIOMASS_PATCH => [
    'name' => GAIN_BIOMASS_PATCH,
    'descriptionmyturn' => '',
    'descriptionmyturnchoice' => clienttranslate('${you} must choose how many biomass patches you want to gain (Wormhole corp)'),
    'descriptionmyturnxorsynergy' => clienttranslate('${you} must choose to gain a biomass patch or a synergy (Jump Drive corp)'),
    'type' => 'private',
    'args' => 'argsAtomicAction',
    'action' => 'stAtomicAction',
    'possibleactions' => ['actGainBiomassPatch', 'actRestart'],
  ],

  //////////////////////////////////////////////////////////////////
  //  _____           _    ___   __    ____
  // | ____|_ __   __| |  / _ \ / _|  / ___| __ _ _ __ ___   ___
  // |  _| | '_ \ / _` | | | | | |_  | |  _ / _` | '_ ` _ \ / _ \
  // | |___| | | | (_| | | |_| |  _| | |_| | (_| | | | | | |  __/
  // |_____|_| |_|\__,_|  \___/|_|    \____|\__,_|_| |_| |_|\___|
  //////////////////////////////////////////////////////////////////

  ST_PRE_END_GAME_TURN => [
    'name' => 'preEndGameTurn',
    'description' => '',
    'type' => GAME,
    'action' => 'stPreEndGameTurn', //reveal civ cards
    'transitions' => [
      '' => ST_END_GAME_TURN,
    ],
  ],

  ST_END_GAME_TURN => [
    'name' => 'endGameTurn',
    'description' => '',
    'type' => GAME,
    'action' => 'stEndGameTurn',
  ],

  ST_PRE_END_OF_GAME => [
    'name' => 'preEndOfGame',
    'description' => '',
    'type' => GAME,
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
