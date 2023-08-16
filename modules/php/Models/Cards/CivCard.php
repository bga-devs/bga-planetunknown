<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;
use PU\Managers\Players;

/*
 * Card
 */

class CivCard extends \PU\Models\Card
{
  protected $effectType = ''; //IMMEDIATE OR END_GAME
  protected $type = 'civCard';
  protected $level = ''; //1,2,3 or 4

  //{ACTION}
  public function effect()
  {
  }

  public function score()
  {
  }

  public function synergy($toChoose, $nMove, $types = ALL_TYPES){
    $player = $this->getPlayer();
    return [
      'action' => CHOOSE_TRACKS,
      'args' => [
        'types' => $types,
        'n' => $toChoose,
        'move' => $nMove,
        'from' => clienttranslate('Civ Card')
      ]
    ];
  }

  public function gainBiomass()
  {
    $player = $this->getPlayer();
    $patchToPlace = $player->corporation()->receiveBiomassPatch();
          if ($patchToPlace) {
            return [
              'action' => PLACE_TILE,
              'args' => [
                'forcedTiles' => [$patchToPlace->getId()]
              ]
            ];
          }
  }
}
