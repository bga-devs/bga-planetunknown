<?php

namespace PU\Models\Cards;

use PU\Core\Globals;
use PU\Managers\Cards;

/*
 * EventCard n°122
 */

class EventCard122 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("They're cutting funding where now?");
    $this->desc = clienttranslate("Do not gain any synergy boosts this round.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : NoBoost
  public function effect()
  {
    Globals::setTurnSpecialRule(NO_SYNERGY);
  }
}
