<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°110
 */

class EventCard110 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = true;

  public function __construct($player)
  {
    $this->title = clienttranslate("They just killed the project. I don't get paid enough for this!");
    $this->desc = clienttranslate("Destroy one of your objective cards.");
    parent::__construct($player);
  }

  //ACTION : DestroyObjective
  //CONTRAINT : 
  public function effect()
  {
    return [
      'action' => \DESTROY_P_O_CARD
    ];
  }
}
