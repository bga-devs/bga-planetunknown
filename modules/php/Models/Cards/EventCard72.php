<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°72
 */

class EventCard72 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Do you think we'll get stuck?");
    $this->desc = clienttranslate("Advance rover or biomass tracker.");
    parent::__construct($player);
  }

  //ACTION : RoverOrBiomassTracker
  //CONTRAINT : 
  public function effect()
  {
    return $this->synergy(1, 1, [BIOMASS, ROVER]);
  }
}
