<?php

namespace PU\Models\Cards;

use PU\Managers\Actions;
use PU\Managers\Cards;
use PU\Managers\Players;

/*
 * Card
 */

class CivCard extends \PU\Models\Card
{
  protected $staticAttributes = ['title', 'desc', 'type', 'level', 'effectType'];
  protected $effectType = ''; //IMMEDIATE OR END_GAME
  protected $type = 'civCard';
  protected $level = ''; //1,2,3 or 4
  public $meteorRepo = false;
  public $commerceAgreement = false;

  public function effect()
  {
  }

  public function score()
  {
  }

  public function synergy($toChoose, $nMove, $types = ALL_TYPES)
  {
    return [
      'action' => CHOOSE_TRACKS,
      'args' => [
        'types' => $types,
        'n' => $toChoose,
        'move' => $nMove,
        'from' => clienttranslate('Civ Card'),
      ],
    ];
  }

  public function gainBiomass($n = 1)
  {
    $player = $this->getPlayer();
    $childs = [];
    for ($i = 0; $i < $n; $i++) {
      $childs[] = Actions::getBiomassPatchFlow();
    }

    return [
      'type' => NODE_PARALLEL,
      'childs' => $childs,
    ];
  }

  public function freePlaceTile($bonus = false)
  {
    return [
      'action' => PLACE_TILE,
      'args' => [
        'withBonus' => $bonus,
      ],
    ];
  }
}
