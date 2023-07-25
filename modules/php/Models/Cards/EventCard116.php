<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°116
 */

class EventCard116 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = true;

  public function __construct($player)
  {
    $this->title = clienttranslate("The entire lot is defective.");
    $this->desc = clienttranslate("Choose one shape from the current depot. Remove all tiles of that shape from the space station.");
    parent::__construct($player);
  }

  //ACTION : EmptySlot
  //CONTRAINT : 
  public function effect()
  {
  }
}
