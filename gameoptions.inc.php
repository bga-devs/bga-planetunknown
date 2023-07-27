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
 * gameoptions.inc.php
 *
 * Planet Unknown game options description
 *
 */

namespace PU;

require_once 'modules/php/constants.inc.php';

$game_options = [
  OPTION_PLANET => [
    'name' => totranslate('Planets'),
    'values' => [
      OPTION_PLANET_A => [
        'name' => totranslate('Symmetric side only'),
        'description' => totranslate('All players with same planet'),
        'tmdisplay' => totranslate('Symmetric planets'),
      ],
      OPTION_PLANET_B_TOO => [
        'name' => totranslate('Assymetric planets'),
        'description' => totranslate('Each player can choose their planet board side'),
        'tmdisplay' => totranslate('Assymetric planets'),
      ],
    ],
  ],
  OPTION_CORPORATION => [
    'name' => totranslate('Planets'),
    'values' => [
      OPTION_CORPORATION_UNIVERSAL => [
        'name' => totranslate('Symmetric side only'),
        'description' => totranslate('All players with same corporation'),
        'tmdisplay' => totranslate('Symmetric corporations'),
      ],
      OPTION_ALL_CORPORATIONS => [
        'name' => totranslate('Assymetric corporations'),
        'description' => totranslate('Each player can choose their corporation board side'),
        'tmdisplay' => totranslate('Assymetric corporations'),
      ],
    ],
  ],
  OPTION_EVENT_CARDS => [
    'name' => totranslate('Event Cards'),
    'values' => [
      OPTION_EVENT_CARDS_GAME => [
        'name' => totranslate('On'),
        'description' => totranslate('All players with same planet'),
        'tmdisplay' => totranslate('On'),
      ],
      OPTION_PLANET_B_TOO => [
        'name' => totranslate('Off'),
        'description' => totranslate('Without event cards'),
        'tmdisplay' => totranslate('Off'),
      ],
    ],
    'displaycondition' => [
      // Note: only display for non-solo mode
      [
        'type' => 'minplayers',
        'value' => [2, 3, 4, 5, 6],
      ],
    ],
  ],
];

$game_preferences = [
  OPTION_CONFIRM => [
    'name' => totranslate('Turn confirmation'),
    'needReload' => false,
    'default' => OPTION_CONFIRM_ENABLED,
    'values' => [
      OPTION_CONFIRM_ENABLED => ['name' => totranslate('Enabled')],
      OPTION_CONFIRM_DISABLED => ['name' => totranslate('Disabled')],
      OPTION_CONFIRM_TIMER => [
        'name' => totranslate('Enabled with timer'),
      ],
    ],
  ],
  OPTION_CONFIRM_UNDOABLE => [
    'name' => totranslate('Undoable actions confirmation'),
    'needReload' => false,
    'values' => [
      OPTION_CONFIRM_ENABLED => ['name' => totranslate('Enabled')],
      OPTION_CONFIRM_DISABLED => ['name' => totranslate('Disabled')],
    ],
  ],
];
