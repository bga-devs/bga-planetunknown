<?php

namespace PU\Models\Cards;

use PU\Core\Globals;
use PU\Managers\Cards;

/*
 * EventCard n°102
 */

class EventCard102 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("It's faster to just drive around.");
    $this->desc = clienttranslate("Rovers cannot move onto civ terrain this round.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : RoverNotCiv
  public function effect()
  {
    Globals::setTurnSpecialRule(NOT_ONTO_CIV);
  }
}
