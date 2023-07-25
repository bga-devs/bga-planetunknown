<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

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
  }
}
