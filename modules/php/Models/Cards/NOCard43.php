<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard43 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Transit');
    $this->desc = clienttranslate('Have the largest single area of rover terrain on your planet.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->planet()->countLargestAdjacent(ROVER);
  }
}
