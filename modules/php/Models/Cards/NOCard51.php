<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard51 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Sprawl');
    $this->desc = clienttranslate('Have the most unique areas of civ terrain on your planet.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->planet()->countZoneNb(CIV);
  }
}
