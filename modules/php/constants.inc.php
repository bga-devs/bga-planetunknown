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

const OPTION_PLANET = 105;
const OPTION_PLANET_A = 0;
const OPTION_PLANET_B_TOO = 1;

const OPTION_CORPORATION = 106;
const OPTION_CORPORATION_UNIVERSAL = 0;
const OPTION_ALL_CORPORATIONS = 1;

const OPTION_EVENT_CARDS = 107;
const OPTION_EVENT_CARDS_GAME = 0;
const OPTION_NO_EVENT_CARDS_GAME = 1;

const OPTION_PRIVATE_OBJECTIVE_CARDS = 108;
const OPTION_PRIVATE_OBJECTIVE_CARDS_GAME = 0;
const OPTION_NO_PRIVATE_OBJECTIVE_CARDS_GAME = 1;
/*
 * User preferences
 */
const OPTION_CONFIRM = 103;
const OPTION_CONFIRM_DISABLED = 0;
const OPTION_CONFIRM_ENABLED = 2;
const OPTION_CONFIRM_TIMER = 3;

const OPTION_CONFIRM_UNDOABLE = 104;

const NO_EVENT_CARD_GAME = 'noEventCard';
const EVENT_CARD_GAME = 'eventCard';

const SOLO_GAME = 'solo';
/*
 * State constants
 */

const GAME = 'game';
const MULTI = 'multipleactiveplayer';
const PRIVATESTATE = 'private';
const END_TURN = 'endTurn';
const ACTIVE_PLAYER = 'activeplayer';

const ST_GAME_SETUP = 1;
const ST_SETUP_BRANCH = 2;

//setup
const ST_CHOOSE_SETUP = 3;
const ST_CONFIRM_SETUP = 4;
const ST_SECOND_SETUP = 5;

const ST_EVENT_CARD = 10;
const ST_PLAY_AFTER_EVENT_CARD = 12;
const ST_PRE_CHOOSE_CIV_CARD = 13;
const ST_CHOOSE_CIV_CARD = 14;
const ST_POST_CHOOSE_CIV_CARD = 15;

const ST_NEXT_PLAYER = 19;

const ST_CHOOSE_ROTATION = 21;
const ST_START_PARALLEL = 22;

//atomic actions
const ST_PLACE_TILE = 23;
const ST_MOVE_TRACK = 24;
const ST_CHOOSE_TRACKS = 25;
const ST_PLACE_ROVER = 26;
const ST_MOVE_TRACKER_BY_ONE = 27;
const ST_TAKE_CIV_CARD = 28;
const ST_MOVE_ROVER = 29;

const ST_FOO_A = 34;
const ST_FOO_B = 35;
const ST_FOO_C = 36;

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

const PLACE_TILE = 'PlaceTile';
const MOVE_TRACK = 'MoveTrack';
const CHOOSE_TRACKS = 'ChooseTracks'; //choose which track to move between what energy offer to you (and/or synergy??)
const PLACE_ROVER = 'PlaceRover';
const MOVE_TRACKER_BY_ONE = 'MoveTrackerByOne';
const TAKE_CIV_CARD = 'TakeCivCard';
const MOVE_ROVER = 'MoveRover';

/*
 * Resources
 */
const CIV = 'civ';
const WATER = 'water';
const ROVER = 'rover';
const TECH = 'tech';
const ENERGY = 'energy';
const BIOMASS = 'biomass';

const ALL_TYPES = [CIV, WATER, BIOMASS, ROVER, TECH];

const MEDAL = 'medal';
const SYNERGY = 'Synergy';
const SYNERGY_CIV = 'synergy_civ';
const SYNERGY_ROVER = 'synergy_rover';
const SYNERGY_TECH = 'synergy_tech';
const SYNERGY_WATER = 'synergy_water';
const SKIP = 'skip'; //doesn't exists, placeholder in the array;

/*
 *	Cards
 */

const IMMEDIATE = 'immediate';
const END_GAME = 'at the end';

const GREEN = 'green';
const ORANGE = 'orange';
const RED = 'red';
const SOLO_EVENT_CARDS = [65, 66, 67, 79, 110, 116];

/*
 *	Terrains types
 */
const NOTHING = 'nothing';
const LAND = 'land';
const ICE = 'ice';

//they are LAND too
const LIFEPOD = 'lifepod';
const RING = 'ring';
const HOLE = 'hole';
const CITY = 'city';
const TOXIC = 'toxic';
const ELECTRIC = 'electric';
const ELECTRIC_LIFEPOD = 'electric_lifepod';

const METEOR = 'meteor';
const ROVER_MEEPLE = 'rover-meeple';

/*
 * Tiles and Space station
 */

const TILE_N = 0;
const TILE_F = 1;
const TILE_I = 2;
const TILE_v = 3;
const TILE_t = 4;
const TILE_s = 5;
const TILE_L = 6;
const TILE_U = 7;
const TILE_i = 8;
const TILE_BIG_I = 9;
const TILE_O = 10;
const TILE_S = 11;
const BIOMASS_PATCH = 'biomass_patch';

const LARGE_RING = [TILE_S, TILE_F, TILE_s, TILE_BIG_I, TILE_N, TILE_U];
const SMALL_RING = [TILE_I, TILE_t, TILE_v, TILE_L, TILE_i, TILE_O];

/*
 * PLANETS
 */

const ADVANCED_PLANETS = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
const ALL_PLANETS = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

/*
 * CORPORATIONS
 */
const ADVANCED_CORPORATIONS = [1, 2, 3, 4, 5, 6, 7, 8];
const ALL_CORPORATIONS = [0, 1, 2, 3, 4, 5, 6, 7, 8];

/*
 * MISC
 */

const MODE_APPLY = 0;
const MODE_PRIVATE = 1;
const MODE_REPLAY = 2;

/************************
 ********** TILES *******
 ************************/

/******************
 ****** STATS ******
 ******************/

const STAT_TURNS = 10;
