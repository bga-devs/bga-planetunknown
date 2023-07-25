<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°96
 */

class EventCard96 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Pick your poison today.");
    $this->desc = clienttranslate("Regress any single tracker if possible.");
    parent::__construct($player);
  }

  //ACTION : -Synergy
  //CONTRAINT : 
  public function effect()
  {
  }
}
