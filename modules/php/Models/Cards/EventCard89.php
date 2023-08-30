<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°89
 */

class EventCard89 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("That airlock explosion set us back.");
    $this->desc = clienttranslate("Regress civ or tech tracker.");
    parent::__construct($player);
  }

  //ACTION : RegressCivTech
  //CONTRAINT : 
  public function effect()
  {
    return $this->synergy(1, -1, [TECH, CIV]);
  }
}
