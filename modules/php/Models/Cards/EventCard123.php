<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°123
 */

class EventCard123 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("These blackouts are getting more frequent.");
    $this->desc = clienttranslate("Do not gain milestone benefits when advancing your tracks.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : NoMilestone
  public function effect()
  {
  }
}
