<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°105
 */

class EventCard105 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("How come nothing ever works in this place?");
    $this->desc = clienttranslate("Regress any single tracker twice.");
    parent::__construct($player);
  }

  //ACTION : -Synergy2
  //CONTRAINT : 
  public function effect()
  {
  }
}
