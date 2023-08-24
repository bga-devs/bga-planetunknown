<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard38 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Beacon');
    $this->desc = clienttranslate('Have the most energy resources on the edge of your planet.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->planet()->countSymbolsOnEdge(ENERGY);
  }
}
