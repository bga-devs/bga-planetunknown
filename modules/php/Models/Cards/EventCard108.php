<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°108
 */

class EventCard108 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("The geodesic dome is definitely half empty.");
    $this->desc = clienttranslate("Ignore one of the resources on your tile this round.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : OneResource
  public function effect()
  {
  }
}
