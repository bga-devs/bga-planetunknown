<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°66
 */

class EventCard66 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = true;

  public function __construct($player)
  {
    $this->title = clienttranslate("Reserachers think they're close to a breackthrough.");
    $this->desc = clienttranslate("Add an extra civ card to each rank.");
    parent::__construct($player);
  }

  //ACTION : Civ+1
  //CONTRAINT : 
  public function effect()
  {
  }
}
