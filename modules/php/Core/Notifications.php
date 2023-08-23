<?php

namespace PU\Core;

use PU\Managers\Players;
use PU\Helpers\Utils;
use PU\Helpers\Collection;
use PU\Core\Globals;
use PU\Managers\Cards;
use PU\Managers\Meeples;
use PU\Managers\Tiles;

class Notifications
{
  public static function addCivCards()
  {
    $datas = [];
    for ($i = 1; $i <= 4; $i++) {
      $data["deck_civ_$i"] = Cards::countInLocation("deck_civ_$i");
    }
    static::notifyAll('newCards', clienttranslate('A new civ card is added to each deck'), $data);
  }

  public static function collectMeeple($player, $meeples, $action)
  {
    $message =
      $action == 'destroy'
        ? clienttranslate('${player_name} detroys ${n} ${type} from his planet')
        : clienttranslate('${player_name} collects ${n} ${type} from his planet');
    $data = [
      'player' => $player,
      'n' => count($meeples),
      'type' => $meeples[0]->getType(),
      'meeples' => $meeples,
    ];
    static::pnotify($player, 'slideMeeples', $message, $data);
  }

  public static function getNewCard($player, $card)
  {
    static::notify($player, 'newCards', clienttranslate('${player_name} receive a new private objective card'), [
      'player' => $player,
      'card' => $card,
    ]);
  }

  public static function chooseSetup($player, $planetId, $corporationId, $rejectedCardId)
  {
    self::pnotify($player, 'chooseSetup', '', [
      'planetId' => $planetId,
      'corporationId' => $corporationId,
      'rejectedCardId' => $rejectedCardId,
    ]);
  }

  //deprecated -> collectMeeples
  // public static function collectMeteor($player, $meteor)
  // {
  //   $msg = clienttranslate('${player_name} collects a new meteor');
  //   $data = [
  //     'player' => $player,
  //     'meeple' => $meteor,
  //   ];
  //   static::pnotify($player, 'slideMeeple', $msg, $data);
  // }

  public static function destroyedMeeples($player, $destroyedMeeples, $type)
  {
    self::pnotify(
      $player,
      'destroyedMeeples',
      $type == ROVER
        ? clienttranslate('By placing his new tile, ${player_name} covers and destroys ${nb} rover(s)')
        : clienttranslate('By placing his new tile, ${player_name} covers and destroys ${nb} lifepod(s)'),
      [
        'player' => $player,
        'nb' => count($destroyedMeeples),
        'meeples' => $destroyedMeeples->toArray(),
      ]
    );
  }

  public static function endOfTurn()
  {
    $data = [
      'tiles' => Tiles::getSusan()->toArray(),
    ];
    static::notifyAll('endOfTurn', '', $data);
  }

  public static function moveTrack($player, $fromCell, $pawn)
  {
    self::pnotify(
      $player,
      'slideMeeple',
      $fromCell['y'] < $pawn->getY()
        ? clienttranslate('${player_name} moves ${types_desc} tracker upward')
        : ($fromCell['y'] > $pawn->getY()
          ? clienttranslate('${player_name} moves ${types_desc} tracker downward')
          : clienttranslate('${player_name} moves ${types_desc} tracker sideward')),
      [
        'player' => $player,
        'meeple' => $pawn,
        'types' => [$pawn->getType()],
      ]
    );
  }

  public static function newEventCard($card)
  {
    $message = clienttranslate('A new event card is revealed');
    $data = [
      'event_card' => $card,
    ];
    static::notifyAll('eventCard', $message, $data);
  }

  public static function newRotation($rotation, $player = null)
  {
    $message =
      $player == null
        ? clienttranslate('S.U.S.A.N. rotates.')
        : clienttranslate('${player_name} chooses a new orientation for S.U.S.A.N.');
    $data = [
      'player' => $player,
      'newRotation' => $rotation,
    ];

    static::notifyAll('newRotation', $message, $data);
  }

  public static function placeRover($player, $rover)
  {
    $msg = clienttranslate('${player_name} places a new rover on his planet');
    $data = [
      'player' => $player,
      'meeple' => $rover,
    ];
    static::pnotify($player, 'slideMeeple', $msg, $data);
  }

  public static function placeTile($player, $tile, $meteor, $types)
  {
    self::pnotify(
      $player,
      'placeTile',
      count($types) < 2
        ? clienttranslate('${player_name} places a biomass patch on their planet')
        : (is_null($meteor)
          ? clienttranslate('${player_name} places a ${types_desc} tile on their planet')
          : clienttranslate('${player_name} places a ${types_desc} tile and a new meteor on their planet')),
      [
        'tile' => $tile,
        'types' => $types,
        'meteor' => $meteor,
      ]
    );
  }

  public static function secondSetup()
  {
    $data = [
      'UIplayers' => Players::getUiData(),
      'meeples' => Meeples::getUiData(),
    ];
    static::notifyAll(
      'secondSetup',
      clienttranslate('All planets and corporations are ready to preserve the future of humanity'),
      $data
    );
  }

  public static function takeCivCard($player, $cardId, $level)
  {
    $msg = clienttranslate('${player_name} take a new civ card from deck ${level}');
    $data = [
      'player' => $player,
      'card' => $cardId,
      'level' => $level,
    ];
    static::pnotify($player, 'takeCivCard', $msg, $data);
  }

  public static function milestone($player, $type, $arg = null)
  {
    switch ($type) {
      case ROVER:
        $message = clienttranslate('${player_name} can place a new rover');
        break;
      case CIV:
      case TECH:
        $message = clienttranslate('${player_name} reaches level ${value} on the ${type} track');
        break;
      case BIOMASS:
        $message = clienttranslate('${player_name} receive a new biomass patch');
        break;
      case SYNERGY:
        $message = clienttranslate('${player_name} receive a synergy bonus');
        break;
      default:
        return;
    }
    $data = [
      'player' => $player,
      'type' => $type,
      'value' => $arg,
    ];
    static::pnotify($player, 'milestone', $message, $data);
  }

  public static function receiveBiomassPatch($player, $tile)
  {
    static::pnotify($player, 'receiveBiomassPatch', \clienttranslate('${player_name} receives a new biomass patch'), [
      'player' => $player,
      'tile' => $tile,
    ]);
  }

  /*************************
   **** GENERIC METHODS ****
   *************************/
  protected static function notifyAll($name, $msg, $data)
  {
    self::updateArgs($data);
    Game::get()->notifyAllPlayers($name, $msg, $data);
  }

  protected static function notify($player, $name, $msg, $data)
  {
    $pId = is_int($player) ? $player : $player->getId();
    self::updateArgs($data);
    Game::get()->notifyPlayer($pId, $name, $msg, $data);
  }

  // TODO : make the notif either private or public depending on some flag
  protected static function pnotify($player, $name, $msg, $data)
  {
    $pId = is_int($player) ? $player : $player->getId();
    $data['player'] = $player;
    self::updateArgs($data);

    $mode = Globals::getMode();
    // PRIVATE MODE => send private notif
    if ($mode == MODE_PRIVATE) {
      Game::get()->notifyPlayer($pId, $name, $msg, $data);
      self::flush();
    }
    // PUBLIC MODE => send public notif with ignore flag
    elseif ($mode == \MODE_APPLY) {
      $data['ignore'] = $pId;
      $data['preserve'][] = 'ignore';
      Game::get()->notifyAllPlayers($name, $msg, $data);
    }
  }

  public static function message($txt, $args = [])
  {
    self::notifyAll('message', $txt, $args);
  }

  public static function messageTo($player, $txt, $args = [])
  {
    $pId = is_int($player) ? $player : $player->getId();
    self::notify($pId, 'message', $txt, $args);
  }

  public static function newUndoableStep($player, $stepId)
  {
    self::notify($player, 'newUndoableStep', clienttranslate('Undo here'), [
      'stepId' => $stepId,
      'preserve' => ['stepId'],
    ]);
  }

  public static function clearTurn($player, $notifIds)
  {
    self::notify($player, 'clearTurn', clienttranslate('You restart your turn'), [
      'player' => $player,
      'notifIds' => $notifIds,
    ]);
  }

  // Remove extra information from cards
  protected function filterCardDatas($card)
  {
    return [
      'id' => $card['id'],
      'location' => $card['location'],
      'pId' => $card['pId'],
    ];
  }
  public static function refreshUI($pId, $datas)
  {
    // // Keep only the thing that matters
    $fDatas = [
      'players' => $datas['players'],
      'tiles' => $datas['tiles'],
      'meeples' => $datas['meeples'],
    ];

    // foreach ($fDatas['cards'] as $i => $card) {
    //   $fDatas['cards'][$i] = self::filterCardDatas($card);
    // }
    // foreach ($fDatas['players'] as &$player) {
    //   $player['hand'] = []; // Hide hand !
    // }

    self::notify($pId, 'refreshUI', '', [
      'datas' => $fDatas,
    ]);
  }

  public static function refreshHand($player, $hand)
  {
    foreach ($hand as &$card) {
      $card = self::filterCardDatas($card);
    }
    self::notify($player, 'refreshHand', '', [
      'player' => $player,
      'hand' => $hand,
    ]);
  }

  public static function flush()
  {
    self::notifyAll('flush', '', []);
  }

  ///////////////////////////////////////////////////////////////
  //  _   _           _       _            _
  // | | | |_ __   __| | __ _| |_ ___     / \   _ __ __ _ ___
  // | | | | '_ \ / _` |/ _` | __/ _ \   / _ \ | '__/ _` / __|
  // | |_| | |_) | (_| | (_| | ||  __/  / ___ \| | | (_| \__ \
  //  \___/| .__/ \__,_|\__,_|\__\___| /_/   \_\_|  \__, |___/
  //       |_|                                      |___/
  ///////////////////////////////////////////////////////////////

  /*
   * Automatically adds some standard field about player and/or card
   */
  protected static function updateArgs(&$data)
  {
    if (isset($data['player'])) {
      $data['player_name'] = $data['player']->getName();
      $data['player_id'] = $data['player']->getId();
      $data['scores'] = [
        $data['player']->getId() => $data['player']->score(),
      ];
      unset($data['player']);
    }
    if (isset($data['player2'])) {
      $data['player_name2'] = $data['player2']->getName();
      $data['player_id2'] = $data['player2']->getId();
      unset($data['player2']);
    }
    if (isset($data['player3'])) {
      $data['player_name3'] = $data['player3']->getName();
      $data['player_id3'] = $data['player3']->getId();
      unset($data['player3']);
    }
    if (isset($data['players'])) {
      $args = [];
      $logs = [];
      foreach ($data['players'] as $i => $player) {
        $logs[] = '${player_name' . $i . '}';
        $args['player_name' . $i] = $player->getName();
      }
      $data['players_names'] = [
        'log' => join(', ', $logs),
        'args' => $args,
      ];
      $data['i18n'][] = 'players_names';
      unset($data['players']);
    }

    if (isset($data['types'])) {
      $data['types_desc'] = Utils::getTypesDesc($data['types']);
      $data['i18n'][] = 'types_desc';
    }
  }
}
