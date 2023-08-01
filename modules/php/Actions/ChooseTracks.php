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

  public function getTypes()
  {
    return $this->getCtxArg('types');
  }

  public function getN()
  {
    return $this->getCtxArg('n');
  }

  public function getDescription()
  {
    $types = $this->getTypes();
    $args = [];
    $logs = [];
    foreach ($types as $i => $type) {
      $logs[] = '${type' . $i . '}';
      $args['type' . $i] = $type;
    }

    $isEnergy = $this->getCtxArg('energy') ?? false;
    return [
      'log' => $isEnergy
        ? clienttranslate('Energy: advance 1 track among ${tracks}')
        : clienttranslate('Advance ${n} track(s) among ${tracks}'),
      'args' => [
        'n' => $this->getN(),
        'tracks' => [
          'log' => join(', ', $logs),
          'args' => $args,
        ],
      ],
    ];
  }

  public function argsChooseTracks()
  {
    $player = $this->getPlayer();

    return [
      'types' => $this->getTypes(),
    ];
  }

  public function actChooseTracks($tracks)
  {
    self::checkAction('actChooseTracks');
  }
}
