<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°71
 */

class EventCard71 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Can the rovers run over water?");
    $this->desc = clienttranslate("Advance rover or water tracker.");
    parent::__construct($player);
  }

  //ACTION : RoverOrWaterTracker
  //CONTRAINT : 
  public function effect()
  {
  }
}
