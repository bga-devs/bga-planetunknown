<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°78
 */

class EventCard78 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("How about that environment?");
    $this->desc = clienttranslate("Advance biomass or water tracker.");
    parent::__construct($player);
  }

  //ACTION : BiomassOrWaterTracker
  //CONTRAINT : 
  public function effect()
  {
    return $this->synergy(1, 1, [WATER, BIOMASS]);
  }
}
