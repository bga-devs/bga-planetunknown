<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°113
 */

class EventCard113 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("It was pinging regularly and then it just stopped.");
    $this->desc = clienttranslate("Destroy one lifepod on your planet.");
    parent::__construct($player);
  }

  //ACTION : DestroyLifepod
  //CONTRAINT : 
  public function effect()
  {
    return [
      'action' => COLLECT_MEEPLE,
      'args' => [
        'type' => LIFEPOD,
        'n' => 1,
        'action' => 'collect',
        'destroy' => 'destroy'
      ]
    ];
  }
}
