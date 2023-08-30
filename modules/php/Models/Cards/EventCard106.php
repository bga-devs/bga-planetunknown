<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°106
 */

class EventCard106 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("This is everyone's fault but mine");
    $this->desc = clienttranslate("Regress any single tracker three times.");
    parent::__construct($player);
  }

  //ACTION : -Synergy3
  //CONTRAINT : 
  public function effect()
  {
    return $this->synergy(1, -3);
  }
}
