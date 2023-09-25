<?php

namespace PU\Models\Cards;

use PU\Core\Notifications;
use PU\Managers\Cards;
use PU\Managers\Players;

/*
 * EventCard n°67
 */

class EventCard67 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = true;

  public function __construct($player)
  {
    $this->title = clienttranslate('Are you up for some overtime?');
    $this->desc = clienttranslate('Draw an extra objective card and add it to your objectives.');
    parent::__construct($player);
  }

  //ACTION : POCard+1
  //CONTRAINT :
  public function effect()
  {
    $player = Players::getAll()->first(); // as it is solo card
    $card = Cards::pickOneForLocation('deck_objectives', 'hand_obj', $player->getId());

    Notifications::getNewCard($player, $card);
  }
}
