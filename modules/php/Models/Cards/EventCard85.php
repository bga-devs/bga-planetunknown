<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°85
 */

class EventCard85 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Should we fix the flat or can it still run?");
    $this->desc = clienttranslate("Regress rover or civ tracker.");
    parent::__construct($player);
  }

  //ACTION : RegressRoverCiv
  //CONTRAINT : 
  public function effect()
  {
    return $this->synergy(1, -1, [ROVER, CIV]);
  }
}
