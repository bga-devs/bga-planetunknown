<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°76
 */

class EventCard76 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Sounds like they finally figured out hydro");
    $this->desc = clienttranslate("Advance tech or water tracker.");
    parent::__construct($player);
  }

  //ACTION : TechOrWaterTracker
  //CONTRAINT : 
  public function effect()
  {
  }
}
