<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°97
 */

class EventCard97 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("We need a clean up on row 5, I repeat, we need a clean up on row 5.");
    $this->desc = clienttranslate("Rovers cannot move onto water terrain this round.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : RoverNotWater
  public function effect()
  {
  }
}
