<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°90
 */

class EventCard90 extends \PU\Models\Cards\EventCard
{
  protected $color = ORANGE;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("Rations are starting to sound delicious.");
    $this->desc = clienttranslate("Regress water or civ tracker.");
    parent::__construct($player);
  }

  //ACTION : RegressWaterCiv
  //CONTRAINT : 
  public function effect()
  {
    return $this->synergy(1, -1, [WATER, CIV]);
  }
}
