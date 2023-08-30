<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°93
 */

class EventCard93 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("We can't keep this thing alive with extension cords.");
    $this->desc = clienttranslate("Regress biomass or tech tracker.");
    parent::__construct($player);
  }

  //ACTION : RegressBiomassTech
  //CONTRAINT : 
  public function effect()
  {
    return $this->synergy(1, -1, [TECH, BIOMASS]);
  }
}
