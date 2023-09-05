<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°115
 */

class EventCard115 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("It just spontaneously combusted.");
    $this->desc = clienttranslate("Destroy 1 collected meteorite.");
    parent::__construct($player);
  }

  //ACTION : DestroyCollectedMeteor
  //CONTRAINT : 
  public function effect()
  {
    return [
      'action' => COLLECT_MEEPLE,
      'args' => [
        'type' => METEOR,
        'n' => 2,
        'action' => 'destroy',
        'location' => 'corporation'
      ]
    ];
  }
}
