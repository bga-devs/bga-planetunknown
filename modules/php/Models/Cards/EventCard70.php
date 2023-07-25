<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°70
 */

class EventCard70 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Check out these mods.");
    $this->desc = clienttranslate("Advance rover or tech tracker.");
    parent::__construct($player);
  }

  //ACTION : RoverOrTechTracker
  //CONTRAINT : 
  public function effect()
  {
  }
}
