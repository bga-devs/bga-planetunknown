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
 * stats.inc.php
 *
 * Planet Unknown game statistics description
 *
 */

require_once 'modules/php/constants.inc.php';

$stats_type = [
  'table' => [
    'turns' => [
      'id' => STAT_TURNS,
      'name' => totranslate('Number of turns'),
      'type' => 'int',
    ],
  ],

  'player' => [
    'planetPoints' => [
      'id' => STAT_POINTS_BY_PLANETS,
      'name' => totranslate('Medals on planet'),
      'type' => 'int',
    ],
    'tracksPoints' => [
      'id' => STAT_POINTS_BY_TRACKS,
      'name' => totranslate('Medals on tracks'),
      'type' => 'int',
    ],
    'lifepodsPoints' => [
      'id' => STAT_POINTS_BY_LIFEPODS,
      'name' => totranslate('Medals thanks to lifepods'),
      'type' => 'int',
    ],
    'meteorsPoints' => [
      'id' => STAT_POINTS_BY_METEORS,
      'name' => totranslate('Medals thanks to meteors'),
      'type' => 'int',
    ],
    'civCardsPoints' => [
      'id' => STAT_POINTS_BY_CIVS,
      'name' => totranslate('Medals on civ cards'),
      'type' => 'int',
    ],
    'objectivesPoints' => [
      'id' => STAT_POINTS_BY_OBECTIVES,
      'name' => totranslate('Medals thanks to objectives'),
      'type' => 'int',
    ],
    'civLevel' => [
      'id' => STAT_CIV_LEVEL,
      'name' => totranslate('Level on civ track'),
      'type' => 'int',
    ],
    'waterLevel' => [
      'id' => STAT_WATER_LEVEL,
      'name' => totranslate('Level on water track'),
      'type' => 'int',
    ],
    'biomassLevel' => [
      'id' => STAT_BIOMASS_LEVEL,
      'name' => totranslate('Level on biomass track'),
      'type' => 'int',
    ],
    'roverLevel' => [
      'id' => STAT_ROVER_LEVEL,
      'name' => totranslate('Level on rover track'),
      'type' => 'int',
    ],
    'techLevel' => [
      'id' => STAT_TECH_LEVEL,
      'name' => totranslate('Level of tech tracker'),
      'type' => 'int',
    ],
    'interiorTiles' => [
      'id' => STAT_TILES_FROM_INTERIOR,
      'name' => totranslate('Small tiles taken'),
      'type' => 'int',
    ],
    'exteriorTiles' => [
      'id' => STAT_TILES_FROM_EXTERIOR,
      'name' => totranslate('Large tiles taken'),
      'type' => 'int',
    ],
    'biomassPatches' => [
      'id' => STAT_BIOMASS_PATCHES,
      'name' => totranslate('Biomass patches placed'),
      'type' => 'int',
    ],
  ],
];
