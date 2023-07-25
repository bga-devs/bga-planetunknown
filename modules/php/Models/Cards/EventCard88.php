<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°88
 */

class EventCard88 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("It's a rough day to survive out there.");
    $this->desc = clienttranslate("Regress rover or biomass tracker.");
    parent::__construct($player);
  }

  //ACTION : RegressRoverBiomass
  //CONTRAINT : 
  public function effect()
  {
  }
}
