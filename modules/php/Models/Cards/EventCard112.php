<?php

namespace PU\Models\Cards;

use PU\Core\Notifications;
use PU\Managers\Cards;

/*
 * EventCard n°112
 */

class EventCard112 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("We need more volunteers, the few of us can't do it alone.");
    $this->desc = clienttranslate("Remove one civ card from each rank at random.");
    parent::__construct($player);
  }

  //ACTION : RemoveCivCard
  //CONTRAINT : 
  public function effect()
  {
    for ($i = 1; $i <= 4; $i++) {
      $deck = 'deck_civ_' . $i;
      Cards::shuffle($deck);
      Cards::pickOneForLocation($deck, 'reserve_civ_' . $i);
    }
    Notifications::removeCivCards();
  }
}
