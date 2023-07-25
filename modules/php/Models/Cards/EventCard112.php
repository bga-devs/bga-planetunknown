<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°112
 */

class EventCard112 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("We need more volunteers, the few of us can't do it alone.");
    $this->desc = clienttranslate("Remove one civ card from each rank at random.");
    parent::__construct($player);
  }

  //ACTION : RemoveCivCard
  //CONTRAINT : 
  public function effect()
  {
  }
}
