<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°124
 */

class EventCard124 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("The planete is flat. Obviously...");
    $this->desc = clienttranslate("Tiles cannot be placed on the planet edge.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : NotOnEdge
  public function effect()
  {
  }
}
