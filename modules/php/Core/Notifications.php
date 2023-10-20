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
  public static function confirmSetupObjectives($pId, $otherCards)
  {
    self::notify($pId, 'confirmSetupObjectives', '', ['cardIds' => $otherCards->getIds()]);
  }

  public static function addCivCards()
  {
    $datas = [];
    for ($i = 1; $i <= 4; $i++) {
      $data["deck_civ_$i"] = Cards::countInLocation("deck_civ_$i");
    }
    static::notifyAll('newCards', clienttranslate('A new civ card is added to each deck'), $data);
  }

  public static function removeCivCards()
  {
    $datas = [];
    for ($i = 1; $i <= 4; $i++) {
      $data["deck_civ_$i"] = Cards::countInLocation("deck_civ_$i");
    }
    static::notifyAll('newCards', clienttranslate('A civ card has been removed from each deck'), $data);
  }

  public static function changeFirstPlayer($playerId)
  {
    $data = [
      'player' => Players::get($playerId),
    ];
    $message = clienttranslate('${player_name} becomes the new Station Commander');
    static::notifyAll('changeFirstPlayer', $message, $data);
  }

  public static function collectMeeple($player, $meeples, $action = 'collect')
  {
    $data = [
      'player' => $player,
      'n' => count($meeples),
      'type' => $meeples[0]->getType(),
      'meeples' => $meeples,
      'i18n' => ['type'],
    ];

    if ($action == 'destroy') {
      static::pnotify(
        $player,
        'destroyedMeeples',
        clienttranslate('${player_name} detroys ${n} ${type} from their planet'),
        $data
      );
    } else {
      static::pnotify($player, 'slideMeeples', clienttranslate('${player_name} collects ${n} ${type} from their planet'), $data);
    }
  }

  public static function chooseFluxTrack($player, $track, $meeple, $previousTrack)
  {
    $isSame = $previousTrack == $track;
    $msg = $isSame
      ? clienttranslate('${player_name} keeps the flux token on track ${types_desc}')
      : clienttranslate('${player_name} moves flux token on track ${types_desc}');
    static::pnotify($player, 'chooseFluxTrack', $msg, [
      'player' => $player,
      'types' => [$track],
      'meeple' => $meeple,
      'isSame' => $isSame,
    ]);
  }

  public static function destroyCard($player, $cardId)
  {
    $message = clienttranslate('${player_name} detroys one of their objective cards');
    $data = [
      'player' => $player,
      'cardId' => $cardId,
    ];
    static::pnotify($player, 'destroyCard', $message, $data);
  }

  public static function destroyMeteor($player, $meteor)
  {
    $message = clienttranslate('${player_name} detroys one of their collected meteor');
    $data = [
      'player' => $player,
      'meteorId' => $meteor->getId(),
    ];
    static::pnotify($player, 'destroyMeteor', $message, $data);
  }

  public static function emptySlot($player, $slot)
  {
    static::pnotify(
      $player,
      'emptySlot',
      clienttranslate('You removed all tiles from one stack in your depot'),
      ['slot' => $slot]
    );
  }

  public static function getNewCard($player, $card)
  {
    static::notify($player, 'newCards', clienttranslate('${player_name} receive a new private objective card'), [
      'player' => $player,
      'card' => $card,
    ]);
  }

  public static function chooseSetup($player, $args)
  {
    self::notify($player, 'chooseSetup', '', [
      'args' => ['_private' => $args['_private'][$player->getId()]],
    ]);
  }

  public static function destroyedMeeples($player, $destroyedMeeples, $type)
  {
    self::pnotify(
      $player,
      'destroyedMeeples',
      $type == ROVER
        ? clienttranslate('By placing their new tile, ${player_name} covers and destroys ${nb} rover(s)')
        : clienttranslate('By placing their new tile, ${player_name} covers and destroys ${nb} lifepod(s)'),
      [
        'player' => $player,
        'nb' => count($destroyedMeeples),
        'meeples' => $destroyedMeeples->toArray(),
      ]
    );
  }

  public static function destroyedMeteorWormholePatch($player, $meeple)
  {
    self::pnotify(
      $player,
      'destroyedMeeples',
      clienttranslate(
        'By placing their biomass patch, ${player_name} covers and destroys one meteor (using Wormhole Corp technology)'
      ),
      [
        'player' => $player,
        'meeples' => [$meeple],
      ]
    );
  }

  public static function endOfGame()
  {
    $data = [
      'scores' => Players::scores(null, true),
    ];
    static::notifyAll('scores', '', $data);
  }

  public static function soloReveal($player)
  {
    $deltaScore = $player->getScore();
    $target = Globals::getTarget();
    $initialScore = $target + $deltaScore;
    $msg = '';

    if ($deltaScore < -10) {
      $msg =  clienttranslate("Critical Fail. We're going to need another Planet.");
    } elseif ($deltaScore < -5) {
      $msg =  clienttranslate("Any sign of other survivors?");
    } elseif ($deltaScore < 0) {
      $msg =  clienttranslate("Low capacity but at least we survive.");
    } elseif ($deltaScore < 5) {
      $msg =  clienttranslate("Mission Complete and job well done.");
    } elseif ($deltaScore < 10) {
      $msg =  clienttranslate("Excellent Work, you've earned a promotion.");
    } elseif ($deltaScore < 15) {
      $msg =  clienttranslate("This might be the best planet we've seen yet.");
    } else {
      $msg =  clienttranslate("All stars have aligned because of you, Planeteer.");
    }

    $data = [
      'player' => $player,
      'deltaScore' => $deltaScore,
      'target' => $target,
      'initialScore' => $initialScore
    ];
    static::notify($player, 'soloReveal', $msg, $data);
  }

  public static function scores()
  {
    $players = Players::getAll();

    foreach ($players as $pId => $player) {
      $privateData = [
        'scores' => Players::scores($pId),
      ];
      static::notify($player, 'scores', '', $privateData);
    }
  }

  public static function endOfTurn()
  {
    static::scores();

    $data = [
      'tiles' => Tiles::getSusan()->toArray(),
      'firstPlayerId' => Globals::getFirstPlayer(),
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
          : clienttranslate('${player_name} moves ${types_desc} tracker but stay at same level')),
      [
        'player' => $player,
        'meeple' => $pawn,
        'types' => [$pawn->getType()],
      ]
    );
  }

  public static function newEventCard($card)
  {
    $alert = [
      GREEN => clienttranslate('Green alert'),
      ORANGE => clienttranslate('Orange alert'),
      RED => clienttranslate('Red alert'),
    ];
    $message = clienttranslate('${alert} : ${desc}');
    $data = [
      'i18n' => ['alert', 'desc'],
      'alert' => $alert[$card->getColor()],
      'color' => $card->getColor(),
      'card' => $card,
      'desc' => $card->getDesc(),
    ];
    static::notifyAll('newEventCard', $message, $data);
  }

  public static function newObjectiveCard($card, $player)
  {
    $message = clienttranslate('${player_name} chooses a new objective card that all players compete for');
    $data = [
      'card' => $card,
      'player' => $player,
    ];
    static::pnotify($player, 'newObjectiveCard', $message, $data);
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

  public static function peekNextEvent($player, $card)
  {
    $msg = clienttranslate('${player_name} peeks at the next event card');
    $data = [
      'player' => $player,
      'eventCard' => Globals::getMode() == MODE_APPLY ? null : $card,
    ];
    static::pnotify($player, 'peekNextEvent', $msg, $data);
  }

  public static function placeMeeple($player, $type, $meeple)
  {
    $msg = clienttranslate('${player_name} places a new ${type} on their planet');
    $data = [
      'player' => $player,
      'meeple' => $meeple,
      'type' => $type,
      'i18n' => ['type'],
    ];
    static::pnotify($player, 'slideMeeple', $msg, $data);
  }

  public static function repositionLifepod($player, $lifepod)
  {
    $msg = clienttranslate('${player_name} reposition a collected lifepod');
    $data = [
      'player' => $player,
      'meeple' => $lifepod,
    ];
    static::pnotify($player, 'slideMeeple', $msg, $data);
  }

  public static function placeRover($player, $rover)
  {
    $msg = clienttranslate('${player_name} places a new rover on their planet');
    $data = [
      'player' => $player,
      'meeple' => $rover,
    ];
    static::pnotify($player, 'slideMeeple', $msg, $data);
  }

  public static function moveRover($player, $rover, $meteor = null)
  {
    $msg = clienttranslate('${player_name} moves their rover on their planet');
    $data = [
      'player' => $player,
      'meeples' => $meteor ? [$rover, $meteor] : [$rover],
    ];
    static::pnotify($player, 'slideMeeples', $msg, $data);
  }

  public static function placeTile($player, $tile, $meteor, $types, $oldLocation = null)
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
        'oldLocation' => $oldLocation,
      ]
    );
  }

  public static function placeTileNoPlacement($player, $tile, $types)
  {
    self::pnotify(
      $player,
      'placeTile',
      clienttranslate('${player_name} can\'t place a tile and choose ${types_desc} tile as its last tile'),
      [
        'tile' => $tile,
        'types' => $types,
        'meteor' => null,
      ]
    );
  }

  public static function revealCards($type)
  {
    $data = [
      'playersData' => Players::getUiData(),
    ];
    static::notifyAll(
      'revealCards',
      $type == CIV ? clienttranslate('Revealing CIV card(s) in hand') : clienttranslate('Revealing private objectives'),
      $data
    );
  }

  public static function setupPlayer($player, $meeples)
  {
    static::notifyAll(
      'setupPlayer',
      clienttranslate('${player_name} will play Planet ${planet_name} with Corporation ${corpo_name}'),
      [
        'player' => $player,
        'i18n' => ['planet_name', 'corpo_name'],
        'planet_name' => $player->planet()->getName(),
        'corpo_name' => $player->corporation()->getName(),
        'meeples' => $meeples->toArray(),
        'planetId' => $player->getPlanetId(),
        'corpoId' => $player->getCorporationId(),
      ]
    );
  }

  public static function finishSetup()
  {
    $data = [
      'UIplayers' => Players::getUiData(),
      'meeples' => Meeples::getUiData(),
    ];
    static::notifyAll(
      'finishSetup',
      clienttranslate('All planets and corporations are ready to preserve the future of humanity'),
      $data
    );
  }

  public static function takeCivCard($player, $card, $level)
  {
    // Card go to hand ? Hide it!
    if (Globals::getMode() == MODE_APPLY && $card->getLocation() == 'hand_civ') {
      $cards = Utils::filterPrivateDatas([$card]);
      $card = $cards[0];
    }

    $msg = clienttranslate('${player_name} take a new civ card from deck ${level}');
    $data = [
      'player' => $player,
      'card' => $card,
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
    $msg = \clienttranslate('${player_name} receives a new biomass patch');
    static::pnotify($player, 'receiveBiomassPatch', $msg, [
      'player' => $player,
      'tile' => $tile,
    ]);
  }

  public static function delayBiomassPatch($player)
  {
    $msg = clienttranslate('${player_name} keeps their new biomass patch to place at the game end');
    static::pnotify($player, 'midMessage', $msg, [
      'player' => $player,
    ]);
  }

  public static function endOfGameTriggered($player)
  {
    self::notifyAll(
      'endOfGameTriggered',
      clienttranslate('${player_name} can\'t place any tile, triggering the last round of the game.'),
      [
        'player' => $player,
      ]
    );
  }

  public static function endOfGameTriggeredEventCard()
  {
    self::notifyAll(
      'endOfGameTriggered',
      clienttranslate('This was the last event card. The game will finish at the end of this turn'),
      []
    );
  }

  /*************************
   **** GENERIC METHODS ****
   *************************/
  protected static function notifyAll($name, $msg, $data)
  {
    self::updateArgs($data, true);
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
    $mode = Globals::getMode();
    $pId = is_int($player) ? $player : $player->getId();
    $data['player'] = $player;
    self::updateArgs($data, $mode == \MODE_APPLY);

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
      'susan' => $datas['susan'],
      'scores' => $datas['scores'],
      'cards' => $datas['cards'],
    ];

    //TODOTissac
    // foreach ($fDatas['cards'] as $i => $card) {
    //   $fDatas['cards'][$i] = self::filterCardDatas($card);
    // }
    foreach ($fDatas['players'] as &$player) {
      $player['hand'] = []; // Hide hand !
    }

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
  protected static function updateArgs(&$data, $public = false)
  {
    if (isset($data['player'])) {
      $data['player_name'] = $data['player']->getName();
      $data['player_id'] = $data['player']->getId();
      if (!$public) {
        $data['scores'] = Players::scores($data['player']->getId(), false);
      }
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

    if (isset($data['meeple']) && is_object($data['meeple'])) {
      $data['meeple'] = $data['meeple']->jsonSerialize();
    }
    if (isset($data['card']) && is_object($data['card'])) {
      $data['card'] = $data['card']->jsonSerialize();
    }
    if (isset($data['meteor']) && is_object($data['meteor'])) {
      $data['meteor'] = $data['meteor']->jsonSerialize();
    }
    if (isset($data['meeples'])) {
      foreach ($data['meeples'] as $i => $meeple) {
        if (is_object($meeple)) {
          $data['meeples'][$i] = $meeple->jsonSerialize();
        }
      }
    }
  }
}
