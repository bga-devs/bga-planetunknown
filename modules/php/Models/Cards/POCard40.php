<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard40 extends \PU\Models\Cards\POCard
{
  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Lattice');
    $this->desc = clienttranslate('Create a 3x3 area of water terrains.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->planet()->hasRectangle(WATER, 3, 3);
  }
}
