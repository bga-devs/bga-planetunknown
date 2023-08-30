<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°86
 */

class EventCard86 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("What do you mean 'They cut the power?'");
    $this->desc = clienttranslate("Regress rover or tech tracker.");
    parent::__construct($player);
  }

  //ACTION : RegressRoverTech
  //CONTRAINT : 
  public function effect()
  {
    return $this->synergy(1, -1, [ROVER, TECH]);
  }
}
