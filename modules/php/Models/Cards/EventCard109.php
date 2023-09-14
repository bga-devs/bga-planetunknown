<?php

namespace PU\Models\Cards;

use PU\Core\Globals;
use PU\Managers\Cards;

/*
 * EventCard n°109
 */

class EventCard109 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Are you positive it's in position? Nothing happened.");
    $this->desc = clienttranslate("Do not gain milestone benefits when advancing your tracks.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : NoMilestone
  public function effect()
  {
    Globals::setTurnSpecialRule(NO_MILESTONE);
  }
}
