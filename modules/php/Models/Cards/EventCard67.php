<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°67
 */

class EventCard67 extends \PU\Models\Cards\EventCard
{
  protected $color = GREEN;
  protected $isSolo = true;

  public function __construct($player)
  {
    $this->title = clienttranslate("Are you up for some overtime?");
    $this->desc = clienttranslate("Draw an extra objective card and add it to your objectives.");
    parent::__construct($player);
  }

  //ACTION : POCard+1
  //CONTRAINT : 
  public function effect()
  {
  }
}
