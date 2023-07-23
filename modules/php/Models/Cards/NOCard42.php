<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard42 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Biome');
    $this->desc = clienttranslate('Have the largest single area of biomass terrain on your planet.');
    parent::__construct($player);
  }

  public function score($playerLeft, $playerRight)
  {
  }
}
