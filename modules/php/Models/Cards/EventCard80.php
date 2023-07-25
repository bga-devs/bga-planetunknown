<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°80
 */

class EventCard80 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Just put it anywhere.");
    $this->desc = clienttranslate("Gain one biomass patch.");
    parent::__construct($player);
  }

  //ACTION : Patch+1
  //CONTRAINT : 
  public function effect()
  {
  }
}
