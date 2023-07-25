<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°95
 */

class EventCard95 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("I swear I just cleaned that spot.");
    $this->desc = clienttranslate("Add one meteorite to an empty symbol on your tiles.");
    parent::__construct($player);
  }

  //ACTION : AddMeteor
  //CONTRAINT : 
  public function effect()
  {
  }
}
