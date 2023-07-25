<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°92
 */

class EventCard92 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Sounds like that hydro experiment went way south.");
    $this->desc = clienttranslate("Regress tech or water tracker.");
    parent::__construct($player);
  }

  //ACTION : RegressTechWater
  //CONTRAINT : 
  public function effect()
  {
  }
}
