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
    return count($this->getChoosableTypes()) != 0;
  }

  public function getChoosableTypes()
  {
    $types = $this->getTypes();
    $player = $this->getPlayer();

    return array_values(array_filter($types, fn ($type) => $player->corporation()->canMoveTrack($type, $this->getMove())));
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
    return $this->getCtxArg('withBonus') ?? true;
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
    $choosableTypes = $this->getChoosableTypes();
    $m = $this->getMove();

    return [
      'types' => $this->getTypes(),
      'choosableTypes' => $choosableTypes,
      'n' => min($this->getN(), count($choosableTypes)), //if you can't move N types, you don't HAVE TO, just do your best
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

      // if (count($tracks) > 1) {
      $this->pushParallelChild([
        'action' => MOVE_TRACK,
        'args' => ['type' => $type, 'n' => $this->getMove(), 'withBonus' => $this->getWithBonus()],
      ]);
      // } else {
      //   $this->insertAsChild([
      //     'action' => MOVE_TRACK,
      //     'args' => ['type' => $type, 'n' => $this->getMove(), 'withBonus' => $this->getWithBonus()],
      //   ]);
      // }

      // $withBonus = $this->getWithBonus();
      // if ($withBonus) {
      //   for ($i = 0; $i < $this->getMove(); $i++) {
      //     $this->insertAsChild([
      //       'action' => MOVE_TRACK,
      //       'args' => ['type' => $type, 'n' => 1, 'withBonus' => $withBonus],
      //     ]);
      //   }
      // } else {
      //   $this->insertAsChild([
      //     'action' => MOVE_TRACK,
      //     'args' => ['type' => $type, 'n' => $this->getMove(), 'withBonus' => $withBonus],
      //   ]);
      // }
    }
  }
}
