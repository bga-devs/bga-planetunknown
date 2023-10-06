<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard39 extends \PU\Models\Cards\POCard
{
  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Landfill');
    $this->desc = clienttranslate('Create a 3x3 area of biomass terrains.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->planet()->hasRectangle(BIOMASS, 3, 3);
  }
}
