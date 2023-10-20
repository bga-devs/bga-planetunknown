<?php

namespace PU\Models\Cards;

use PU\Core\Notifications;
use PU\Managers\Cards;
use PU\Managers\Meeples;
use PU\Managers\Players;

/*
 * EventCard n°115
 */

class EventCard115 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("It just spontaneously combusted.");
    $this->desc = clienttranslate("Destroy 1 collected meteorite.");
    parent::__construct($player);
  }

  //ACTION : DestroyCollectedMeteor
  //CONTRAINT : 
  public function effect()
  {
    $players = Players::getAll();

    foreach ($players as $pId => $player) {
      $meteor = Meeples::getOfPlayer($player, METEOR)->first();
      if ($meteor) {
        $meteor->setLocation('trash');
        Notifications::destroyMeteor($player, $meteor);
      }
    }
  }
}
