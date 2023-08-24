<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard50 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Equator');
    $this->desc = clienttranslate('Create a 2x5 area of biomass terrains.');
    parent::__construct($player);
  }
}
