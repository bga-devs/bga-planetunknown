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
    } else {
      return [
        'log' => clienttranslate('Advance ${n} track(s) among ${tracks}'),
        'args' => [
          'n' => $this->getN(),
          'types_desc' => Utils::getTypesDesc($types),
        ],
      ];
    }
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
