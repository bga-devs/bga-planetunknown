<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°68
 */

class EventCard68 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Something jumped on the scanners - probably just a glitch.");
    $this->desc = clienttranslate("Advance the civ, biomass, or water tracker 3 positions. (Do not gain benefits)");
    parent::__construct($player);
  }

  //ACTION : Tracker+3
  //CONTRAINT : 
  public function effect()
  {
  }
}
