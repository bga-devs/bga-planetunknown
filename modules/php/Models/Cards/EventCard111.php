<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;
use PU\Managers\Players;

/*
 * EventCard n°111
 */

class EventCard111 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("I don't know. One minute it was there and the next pieces were everywhere.");
    $this->desc = clienttranslate("Destroy a rover if you have more than one on your planet.");
    parent::__construct($player);
  }

  //ACTION : DestroyRover
  //CONTRAINT : 
  public function effect()
  {
    $players = Players::getAll();

    $flows = [];

    foreach ($players as $pId => $player) {
      if ($player->count($this->getRoversOnPlanet()) > 1) {
        $flows['nestedFlows'][$pId] = [
          'action' => COLLECT_MEEPLE,
          'args' => [
            'type' => ROVER_MEEPLE,
            'n' => 1,
            'action' => 'collect',
            'destroy' => 'destroy'
          ]
        ];
      } else {
        $flows[$pId] = [];
      }
    }
    return $flows;
  }
}
