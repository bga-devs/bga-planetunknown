<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°69
 */

class EventCard69 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("More rovers or more researchers?");
    $this->desc = clienttranslate("Advance rover or civ tracker.");
    parent::__construct($player);
  }

  //ACTION : RoverOrCivTracker
  //CONTRAINT : 
  public function effect()
  {
  }
}
