<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°117
 */

class EventCard117 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("I've got a bad feeling.");
    $this->desc = clienttranslate("Choose to gain a synergy boost or peek at the next event.");
    parent::__construct($player);
  }

  //ACTION : SynergieOrSee
  //CONTRAINT : 
  public function effect()
  {
  }
}
