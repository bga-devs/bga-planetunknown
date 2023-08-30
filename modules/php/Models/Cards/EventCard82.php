<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°82
 */

class EventCard82 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("These delivery drones are out of this world.");
    $this->desc = clienttranslate("Collect a lifepod from the planet.");
    parent::__construct($player);
  }

  //ACTION : Lifepod
  //CONTRAINT : 
  public function effect()
  {
    return [
      'action' => COLLECT_MEEPLE,
      'args' => [
        'type' => LIFEPOD,
        'n' => 1,
        'action' => 'collect'
      ]
    ];
  }
}
