<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard59 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Rural');
    $this->desc = clienttranslate('Have the least civ resources on your planet.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return -$player->planet()->countSymbols(CIV);
  }
}
