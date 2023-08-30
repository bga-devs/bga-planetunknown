<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°94
 */

class EventCard94 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("I thought they said the air was breathable.");
    $this->desc = clienttranslate("Regress biomass or water tracker.");
    parent::__construct($player);
  }

  //ACTION : RegressBiomassWater
  //CONTRAINT : 
  public function effect()
  {
    return $this->synergy(1, -1, [BIOMASS, WATER]);
  }
}
