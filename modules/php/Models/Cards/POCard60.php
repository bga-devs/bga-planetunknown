<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard60 extends \PU\Models\Cards\POCard
{
  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Alternative Energy');
    $this->desc = clienttranslate('Place five or less energy resources.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->planet()->countSymbols(ENERGY) <= 5;
  }
}
