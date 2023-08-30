<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°73
 */

class EventCard73 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Can't anybody keep a secret?");
    $this->desc = clienttranslate("Advance civ or tech tracker.");
    parent::__construct($player);
  }

  //ACTION : CivOrTechTracker
  //CONTRAINT : 
  public function effect()
  {
    return $this->synergy(1, 1, [CIV, TECH]);
  }
}
