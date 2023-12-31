<?php

namespace PU\Models\Cards;

use PU\Core\Globals;
use PU\Core\Notifications;
use PU\Managers\Cards;
use PU\Managers\Meeples;
use PU\Managers\Players;
use PU\Models\Meeple;

/*
 * EventCard n°65
 */

class EventCard65 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = true;

  public function __construct($player)
  {
    $this->title = clienttranslate('Have you seen the R-16? It makes the R-15 obsolete.');
    $this->desc = clienttranslate('Gain an extra Rover and position it on a tile you place this round');
    parent::__construct($player);
  }

  //ACTION : Rover+1
  //CONTRAINT :
  public function effect()
  {
    $players = Players::getAll();

    $rover = Meeples::add(ROVER_MEEPLE, $players)->first();

    Globals::setTurnSpecialRule(ADD_ROVER);

    Notifications::getNewRover($players->first(), $rover);
  }
}
