<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°81
 */

class EventCard81 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Wow! A special fuel canister was in the glove compartment.");
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
