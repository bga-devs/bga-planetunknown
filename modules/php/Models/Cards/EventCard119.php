<?php

namespace PU\Models\Cards;

use PU\Core\Globals;
use PU\Managers\Cards;

/*
 * EventCard n°119
 */

class EventCard119 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate('Too much of any one thing is always a bad thing.');
    $this->desc = clienttranslate('Tiles cannot be placed next to a matching resource terrains.');
    parent::__construct($player);
  }

  //ACTION :
  //CONTRAINT : NoMatching
  public function effect()
  {
    Globals::setTurnSpecialRule(NO_MATCHING_TERRAINS);
  }
}
