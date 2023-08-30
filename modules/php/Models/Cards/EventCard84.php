<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°84
 */

class EventCard84 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("We'll take a happy accident when we get it.");
    $this->desc = clienttranslate("Gain a synergy boost.");
    parent::__construct($player);
  }

  //ACTION : Synergy
  //CONTRAINT : 
  public function effect()
  {
    return $this->synergy(1, 1);
  }
}
