<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°114
 */

class EventCard114 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("All that work was for nothing.");
    $this->desc = clienttranslate("Destroy 1 collected lifepod.");
    parent::__construct($player);
  }

  //ACTION : DestroyCollectedLifepod
  //CONTRAINT : 
  public function effect()
  {
    return [
      'action' => COLLECT_MEEPLE,
      'args' => [
        'type' => LIFEPOD,
        'n' => 2,
        'action' => 'destroy',
        'location' => 'corporation'
      ]
    ];
  }
}
