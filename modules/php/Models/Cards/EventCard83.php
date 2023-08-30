<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°83
 */

class EventCard83 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Go run some analyses on this sample.");
    $this->desc = clienttranslate("Collect a meteorite from the planet.");
    parent::__construct($player);
  }

  //ACTION : Meteor
  //CONTRAINT : 
  public function effect()
  {
    return [
      'action' => COLLECT_MEEPLE,
      'args' => [
        'type' => METEOR,
        'n' => 1,
        'action' => 'collect'
      ]
    ];
  }
}
