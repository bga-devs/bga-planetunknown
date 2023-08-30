<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°77
 */

class EventCard77 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("I heard of the experiments.");
    $this->desc = clienttranslate("Advance biomass or tech tracker.");
    parent::__construct($player);
  }

  //ACTION : BiomassOrTechTracker
  //CONTRAINT : 
  public function effect()
  {
    return $this->synergy(1, 1, [BIOMASS, TECH]);
  }
}
