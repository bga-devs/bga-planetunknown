<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°87
 */

class EventCard87 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Try to avoid driving into the lake next time.");
    $this->desc = clienttranslate("Regress rover or water tracker.");
    parent::__construct($player);
  }

  //ACTION : RegressRoverWater
  //CONTRAINT : 
  public function effect()
  {
  }
}
