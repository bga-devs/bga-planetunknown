<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°79
 */

class EventCard79 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = true;

  public function __construct($player)
  {
    $this->title = clienttranslate("I love it when there's not queue.");
    $this->desc = clienttranslate("Rotate the space station to any depot.");
    parent::__construct($player);
  }

  //ACTION : Rotate
  //CONTRAINT : 
  public function effect()
  {
    //TODO
  }
}
