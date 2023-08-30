<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;
use PU\Managers\Players;

/*
 * EventCard n°80
 */

class EventCard80 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Just put it anywhere.");
    $this->desc = clienttranslate("Gain one biomass patch.");
    parent::__construct($player);
  }

  //ACTION : Patch+1
  //CONTRAINT : 
  public function effect()
  {
    $players = Players::getAll();
    $result = [];

    foreach ($players as $pId => $player) {
      $patchToPlace = $player->corporation()->receiveBiomassPatch();
      if ($patchToPlace) {
        $result['nestedFlows'][$pId] = [
          'action' => PLACE_TILE,
          'args' => [
            'forcedTiles' => [$patchToPlace->getId()]
          ]
        ];
      }
    }
  }
}
