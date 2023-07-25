<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°121
 */

class EventCard121 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("They found some signs of life under the ice.");
    $this->desc = clienttranslate("Tiles cannot be placed onto planetary ice.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : NotOnIce
  public function effect()
  {
  }
}
