<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°101
 */

class EventCard101 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("We lost 3 vehicles last year. They just disappeared.");
    $this->desc = clienttranslate("Rovers cannot move onto tech terrain this round.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : RoverNotTech
  public function effect()
  {
  }
}
