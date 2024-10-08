<?php

namespace PU\Core;

use PU\Core\Game;
use PU\Helpers\Utils;

/*
 * Globals
 */

class Globals extends \PU\Helpers\DB_Manager
{
  protected static $isReplayMode = false;
  public static function setReplayMode()
  {
    static::$isReplayMode = true;
  }
  public static function unsetReplayMode()
  {
    static::$isReplayMode = false;
  }

  protected static $isDebugMode = false;
  public static function setDebugMode()
  {
    static::$isDebugMode = true;
  }
  public static function unsetDebugMode()
  {
    static::$isDebugMode = false;
  }

  public static function setMode($v)
  {
    if ($v == \MODE_REPLAY) {
      static::$isReplayMode = true;
    } else {
      Game::get()->setGameStateValue('mode', $v);
    }
  }

  public static function getMode()
  {
    if (static::$isReplayMode) {
      return \MODE_REPLAY;
    } else {
      return Game::get()->getGameStateValue('mode');
    }
  }

  protected static $initialized = false;
  protected static $variables = [
    'callbackEngineResolved' => 'obj', // DO NOT MODIFY, USED IN ENGINE MODULE
    'anytimeRecursion' => 'int', // DO NOT MODIFY, USED IN ENGINE MODULE
    'customTurnOrders' => 'obj', // DO NOT MODIFY, USED FOR CUSTOM TURN ORDER FEATURE
    'engineWaitingDescriptionSuffix' => 'str', // DO NOT MODIFY, USED IN ENGINE MODULE

    'firstPlayer' => 'int',

    // Setup
    'susanShift' => 'int', //from 0 to 5 to know how small ring and large ring are set
    'susanRotation' => 'int', //from 0 to 5 to know the actual rotation of susan
    'setupChoices' => 'obj',
    'eventCardSet' => 'obj',

    // Game options
    'solo' => 'bool',
    'planetOption' => 'int',
    'corporationOption' => 'int',
    'eventCardsGame' => 'obj',
    'privateObjectiveCardsGame' => 'bool',
    'turnSpecialRule' => 'str',

    'gameEndTriggered' => 'bool',
    'phase' => 'int',
    'target' => 'int' //for solo mode
  ];

  protected static $table = 'global_variables';
  protected static $primary = 'name';
  protected static function cast($row)
  {
    if (!isset(self::$variables[$row['name']])) {
      return null;
    }

    $val = json_decode(\stripslashes($row['value']), true);
    return self::$variables[$row['name']] == 'int' ? ((int) $val) : $val;
  }

  /*
   * Fetch all existings variables from DB
   */
  protected static $data = [];
  public static function fetch()
  {
    // Turn of LOG to avoid infinite loop (Globals::isLogging() calling itself for fetching)
    $tmp = self::$log;
    self::$log = false;

    foreach (self::DB()
      ->select(['value', 'name'])
      ->get(false)
      as $name => $variable) {
      if (\array_key_exists($name, self::$variables)) {
        self::$data[$name] = $variable;
      }
    }
    self::$initialized = true;
    self::$log = $tmp;
  }

  /*
   * Create and store a global variable declared in this file but not present in DB yet
   *  (only happens when adding globals while a game is running)
   */
  public static function create($name)
  {
    if (!\array_key_exists($name, self::$variables)) {
      return;
    }

    $default = [
      'int' => 0,
      'obj' => [],
      'bool' => false,
      'str' => '',
    ];
    $val = $default[self::$variables[$name]];
    self::DB()->insert(
      [
        'name' => $name,
        'value' => \json_encode($val),
      ],
      true
    );
    self::$data[$name] = $val;
  }

  /*
   * Magic method that intercept not defined static method and do the appropriate stuff
   */
  public static function __callStatic($method, $args)
  {
    if (!self::$initialized) {
      self::fetch();
    }

    if (preg_match('/^([gs]et|inc|is)([A-Z])(.*)$/', $method, $match)) {
      // Sanity check : does the name correspond to a declared variable ?
      $name = mb_strtolower($match[2]) . $match[3];
      if (!\array_key_exists($name, self::$variables)) {
        throw new \InvalidArgumentException("Property {$name} doesn't exist");
      }

      // Create in DB if don't exist yet
      if (!\array_key_exists($name, self::$data)) {
        self::create($name);
      }

      if ($match[1] == 'get') {
        // Basic getters
        $t = self::$data[$name];
        return isset($args[0]) && is_array($t) ? $t[$args[0]] : $t;
      } elseif ($match[1] == 'is') {
        // Boolean getter
        if (self::$variables[$name] != 'bool') {
          throw new \InvalidArgumentException("Property {$name} is not of type bool");
        }
        return (bool) self::$data[$name];
      } elseif ($match[1] == 'set') {
        // Setters in DB and update cache
        $value = $args[0];
        if (self::$variables[$name] == 'int') {
          $value = (int) $value;
        }
        if (self::$variables[$name] == 'bool') {
          $value = (bool) $value;
        }
        if (self::$variables[$name] == 'obj' && isset($args[1])) {
          self::$data[$name][$args[0]] = $args[1];
          $value = self::$data[$name];
        }

        self::$data[$name] = $value;
        if (Globals::getMode() == MODE_APPLY || self::$isDebugMode) {
          self::DB()->update(['value' => \addslashes(\json_encode($value))], $name);
        }
        return $value;
      } elseif ($match[1] == 'inc') {
        if (self::$variables[$name] != 'int') {
          throw new \InvalidArgumentException("Trying to increase {$name} which is not an int");
        }

        $getter = 'get' . $match[2] . $match[3];
        $setter = 'set' . $match[2] . $match[3];
        if (count($args) == 2) {
          return self::$setter($args[0], self::$getter($args[0]) + $args[1]);
        } else {
          return self::$setter(self::$getter() + (empty($args) ? 1 : $args[0]));
        }
      }
    }
    throw new \feException(print_r(debug_print_backtrace()));
    return null;
  }

  /*
   * Setup new game
   */
  public static function setupNewGame($players, $options)
  {
    $isSolo = count($players) == 1;
    static::setSolo($isSolo);
    static::setFirstPlayer(array_keys($players)[0]);
    static::setPlanetOption($options[OPTION_PLANET]);
    static::setCorporationOption($options[OPTION_CORPORATION]);
    // $choiceForCards = $isSolo || $options[OPTION_EVENT_CARDS] == OPTION_EVENT_CARDS_GAME ? EVENT_CARD_GAME : NO_EVENT_CARD_GAME;
    $choiceForCards = ($options[OPTION_EVENT_CARDS] ?? 1) == OPTION_EVENT_CARDS_GAME ? EVENT_CARD_GAME : NO_EVENT_CARD_GAME;
    static::setEventCardsGame($choiceForCards);
    static::setPrivateObjectiveCardsGame(
      $isSolo || $options[OPTION_PRIVATE_OBJECTIVE_CARDS] == OPTION_PRIVATE_OBJECTIVE_CARDS_GAME
    );
  }
}
