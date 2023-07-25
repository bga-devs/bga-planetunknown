<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°98
 */

class EventCard98 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("It's too overgrown.The rover just won't fit there.");
    $this->desc = clienttranslate("Rovers cannot move onto biomass terrain this round.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : RoverNotBiomass
  public function effect()
  {
  }
}
