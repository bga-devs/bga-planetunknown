<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°99
 */

class EventCard99 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("It'll fry the fuel cells if we go that way.");
    $this->desc = clienttranslate("Rovers cannot move onto energy terrain this round.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : RoverNotEnergy
  public function effect()
  {
  }
}
