<?php

/*
 * Game options
 */

// const OPTION_COMPETITIVE_LEVEL = 102;
// const OPTION_COMPETITIVE_FIRST_GAME = 0;
// const OPTION_COMPETITIVE_BEGINNER = 1;
// const OPTION_COMPETITIVE_NORMAL = 2;
// const OPTION_COMPETITIVE_CUSTOM_SETUP = 3;
// const OPTION_COMPETITIVE_CUSTOM_SETUP_NON_BEGINNER = 4;
// const OPTION_COMPETITIVE_ALL_SAME_SETUP = 5;

/*
 * User preferences
 */
const OPTION_CONFIRM = 103;
const OPTION_CONFIRM_DISABLED = 0;
const OPTION_CONFIRM_ENABLED = 2;
const OPTION_CONFIRM_TIMER = 3;

const OPTION_CONFIRM_UNDOABLE = 104;

/*
 * State constants
 */
const ST_GAME_SETUP = 1;
const ST_SETUP_BRANCH = 2;

const ST_START_ROUND = 20;
const ST_CHOOSE_ROTATION = 21;
const ST_START_PARALLEL = 22;

const ST_PLACE_TILE = 23;
const ST_FOO_A = 24;
const ST_FOO_B = 25;
const ST_FOO_C = 26;

const ST_SETUP_PRIVATE_ENGINE = 89;
const ST_RESOLVE_STACK = 90;
const ST_RESOLVE_CHOICE = 91;
const ST_IMPOSSIBLE_MANDATORY_ACTION = 92;
const ST_CONFIRM_TURN = 93;
const ST_CONFIRM_PARTIAL_TURN = 94;
const ST_INIT_PRIVATE_ENGINE = 95;
const ST_APPLY_ENGINE = 96;

const ST_GENERIC_NEXT_PLAYER = 97;
const ST_PRE_END_OF_GAME = 98;
const ST_END_GAME = 99;

/*
 * ENGINE
 */
const NODE_SEQ = 'seq';
const NODE_OR = 'or';
const NODE_XOR = 'xor';
const NODE_PARALLEL = 'parallel';
const NODE_LEAF = 'leaf';

const ZOMBIE = 98;
const PASS = 99;

/*
 * Atomic action
 */

const PLACE_TILE = 'PLACE_TILE';

const FOO_A = 'FOO_A';
const FOO_B = 'FOO_B';
const FOO_C = 'FOO_C';

/*
 * Resources
 */
const CIV = 'civ';
const WATER = 'water';
const ROVER = 'rover';
const TECH = 'tech';
const ENERGY = 'energy';
const BIOMASS = 'biomass';

const MEDAL = 'medal';

/*
 * MISC
 */

/************************
 ********** TILES *******
 ************************/

/******************
 ****** MAPS ******
 ******************/
// const ADVANCED_MAPS = [1, 2, 3, 4, 5, 6, 7, 8];
// const ALL_MAPS = ['A', 0, 1, 2, 3, 4, 5, 6, 7, 8];

/******************
 ****** STATS ******
 ******************/

const STAT_TURNS = 10;
