<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard55 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Conservation');
    $this->desc = clienttranslate('Have the least water resources on your planet.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return -$player->planet()->countSymbols(WATER);
  }
}
