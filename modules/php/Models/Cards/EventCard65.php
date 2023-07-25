<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°65
 */

class EventCard65 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = true;

  public function __construct($player)
  {
    $this->title = clienttranslate("Have you seen the R-16? It makes the R-15 obsolete.");
    $this->desc = clienttranslate("Gain an extra Rover and position it on a tile you place this round");
    parent::__construct($player);
  }

  //ACTION : Rover+1
  //CONTRAINT : 
  public function effect()
  {
  }
}
