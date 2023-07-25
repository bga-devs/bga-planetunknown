<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°91
 */

class EventCard91 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("There's already too many people up here.");
    $this->desc = clienttranslate("Regress biomass or civ tracker.");
    parent::__construct($player);
  }

  //ACTION : RegressBiomassCiv
  //CONTRAINT : 
  public function effect()
  {
  }
}
