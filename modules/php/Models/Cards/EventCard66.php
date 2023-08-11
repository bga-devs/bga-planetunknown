<?php

namespace PU\Models\Cards;

use PU\Core\Notifications;
use PU\Managers\Cards;

/*
 * EventCard n°66
 */

class EventCard66 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = true;

  public function __construct($player)
  {
    $this->title = clienttranslate("Reserachers think they're close to a breackthrough.");
    $this->desc = clienttranslate("Add an extra civ card to each rank.");
    parent::__construct($player);
  }

  //ACTION : Civ+1
  //CONTRAINT : 
  public function effect()
  {
    for ($i = 1; $i <= 4; $i++) {
      $deck = 'deck_civ_' . $i;
      Cards::shuffle($deck);
      Cards::pickOneForLocation('reserve_civ_' . $i, $deck);
    }
    Notifications::addCivCards();
  }
}
