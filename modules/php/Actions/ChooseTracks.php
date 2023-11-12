<?php

namespace PU\Actions;

use PU\Managers\Meeples;
use PU\Managers\Players;
use PU\Managers\Tiles;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\Stats;
use PU\Helpers\Utils;
use PU\Helpers\FlowConvertor;
use PU\Managers\Susan;

class ChooseTracks extends \PU\Models\Action
{
  public function getState()
  {
    return \ST_CHOOSE_TRACKS;
  }

  //types is the possibles types to choose
  public function getTypes()
  {
    return $this->getCtxArg('types');
  }

  public function isDoable($player)
  {
    return !empty($this->getChoosableTypesAndMove($player)[0]);
  }


  //return choosableTypes AND move that can be downgraded if needed (the max possible move downward)
  public function getChoosableTypesAndMove($player = null)
  {
    $player = $player ?? $this->getPlayer();
    $move = $this->getMove();
    if ($move > 0) {
      $types = $this->getTypes();
      Utils::filter($types, fn ($type) => $player->corporation()->canMoveTrack($type, $move));
    } else {
      for ($m = $move; $move < 0; $m++) {
        $types = $this->getTypes();
        Utils::filter($types, fn ($type) => $player->corporation()->canMoveTrack($type, $m));
        if (!empty($types)) {
          return [$types, $m];
        }
      }
    }
    return [$types, $move];
  }

  //n is the nb of tracks to choose
  public function getN()
  {
    return $this->getCtxArg('n') ?? 1;
  }

  //move is the numbers of move for choosen tracks
  public function getMove()
  {
    return $this->getCtxArg('move') ?? 1;
  }

  public function getWithBonus()
  {
    return $this->getCtxArg('withBonus') ?? ($this->getMove() > 0);
  }

  public function getFrom()
  {
    return $this->getCtxArg('from') ?? '';
  }

  public function getDescription()
  {
    $types = $this->getTypes();
    $isEnergy = $this->getCtxArg('energy') ?? false;
    if ($isEnergy) {
      return [
        'log' => clienttranslate('${type}${type_name} : advance 1 track among ${types_desc}'),
        'args' => [
          'types_desc' => Utils::getTypesDesc($types),
          'type' => '',
          'type_name' => 'energy',
        ],
      ];
    } elseif (count($types) == 5) {
      $m = $this->getMove();
      return [
        'log' => $m > 0 ? clienttranslate('Advance ${n} track(s)') : clienttranslate('Regress ${n} track(s)'),
        'args' => [
          'n' => $this->getN(),
        ],
      ];
    } else {
      $m = $this->getMove();
      return [
        'log' =>
        $m > 0
          ? clienttranslate('Advance ${n} track(s) among ${types_desc}')
          : clienttranslate('Regress ${n} track(s) among ${types_desc}'),
        'args' => [
          'n' => $this->getN(),
          'types_desc' => Utils::getTypesDesc($types),
        ],
      ];
    }
  }

  public function argsChooseTracks()
  {
    [$choosableTypes, $m] = $this->getChoosableTypesAndMove();

    return [
      'types' => $this->getTypes(),
      'choosableTypes' => $choosableTypes,
      'n' => min($this->getN(), count($choosableTypes)), //if you can't move N types, you don't HAVE TO, just do your best
      'm' => $m,
      'descSuffix' => $m < 0 ? 'regress' : '',
      'from' => $this->getFrom(),
      'source' => $this->ctx->getSource(),
      'i18n' => ['from', 'source'],
    ];
  }

  public function stChooseTracks()
  {
    $args = $this->argsChooseTracks();
    if (count($args['types']) == 1 && $args['n'] == 1) {
      return [$args['types']];
    }
  }

  public function actChooseTracks($tracks)
  {
    $args = $this->argsChooseTracks();
    if (count($tracks) != $args['n']) {
      throw new \BgaVisibleSystemException('You must choose exactly ' . $args['n'] . ' track. Should not happen');
    }

    foreach ($tracks as $key => $type) {
      if (!in_array($type, $args['choosableTypes'])) {
        throw new \BgaVisibleSystemException("You cannot choose $type track. Should not happen");
      }

      // to receive synergy twice with jump drive power
      if ($this->getFrom() == SYNERGY && $this->getPlayer()->corporation()->canUse(TECH_TWICE_SYNERGY_ONCE_PER_ROUND)) {
        $this->pushParallelChild([
          'type' => NODE_XOR,
          'childs' => [
            [
              'action' => MOVE_TRACK,
              'args' => ['type' => $type, 'n' => 1, 'withBonus' => $this->getWithBonus()],
            ],
            [
              'action' => MOVE_TRACK,
              'args' => ['type' => $type, 'n' => 2, 'withBonus' => $this->getWithBonus(),],
              'source' => $this->getPlayer()->corporation()->name,
              'flag' => TECH_TWICE_SYNERGY_ONCE_PER_ROUND,
            ]
          ]
        ]);
      } else {
        $this->pushParallelChild([
          'action' => MOVE_TRACK,
          'args' => ['type' => $type, 'n' => $args['m'], 'withBonus' => $this->getWithBonus()],
        ]);
      }
    }
  }
}
