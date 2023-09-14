<?php

namespace PU\Models\Cards;

use PU\Core\Globals;
use PU\Managers\Cards;

/*
 * EventCard n°103
 */

class EventCard103 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Keep your ideas inside the box today.");
    $this->desc = clienttranslate("Tiles cannot be placed on the planet edge.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : NotOnEdge
  public function effect()
  {
    Globals::setTurnSpecialRule(CANNOT_PLACE_ON_EDGE);
  }
}
