<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°104
 */

class EventCard104 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("The geodesic dome must be half full.");
    $this->desc = clienttranslate("Do not advance one resource from your tile placement.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : OneResource
  public function effect()
  {
  }
}
