<?php

namespace PU\Models\Cards;

use PU\Core\Globals;
use PU\Managers\Cards;

/*
 * EventCard n°120
 */

class EventCard120 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Apparently it's deeper than we thought.");
    $this->desc = clienttranslate("Tiles cannot be placed onto planetary ice.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : NotOnIce
  public function effect()
  {
    Globals::setTurnSpecialRule(CANNOT_PLACE_ON_ICE);
    //TODOTissac
  }
}
