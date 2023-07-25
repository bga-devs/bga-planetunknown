<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°100
 */

class EventCard100 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("These aren't the vehicles you're looking for.");
    $this->desc = clienttranslate("Rovers cannot move onto rover terrain this round.");
    parent::__construct($player);
  }

  //ACTION : 
  //CONTRAINT : RoverNotRover
  public function effect()
  {
  }
}
