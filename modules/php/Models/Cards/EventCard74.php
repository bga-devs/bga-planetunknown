<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°74
 */

class EventCard74 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("What is the ETA on the supply shipment?");
    $this->desc = clienttranslate("Advance water or civ tracker.");
    parent::__construct($player);
  }

  //ACTION : WaterOrCivTracker
  //CONTRAINT : 
  public function effect()
  {
    return $this->synergy(1, 1, [WATER, CIV]);
  }
}
