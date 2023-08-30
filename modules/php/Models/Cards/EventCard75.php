<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°75
 */

class EventCard75 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("I hear it'll take 1,000 years before we have enough owygen to venture out.");
    $this->desc = clienttranslate("Advance biomass or civ tracker.");
    parent::__construct($player);
  }

  //ACTION : BiomassOrCivTracker
  //CONTRAINT : 
  public function effect()
  {
    return $this->synergy(1, 1, [BIOMASS, CIV]);
  }
}
