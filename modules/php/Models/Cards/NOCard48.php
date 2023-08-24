<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard48 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Garden');
    $this->desc = clienttranslate('Have the most unique areas of biomass terrain on your planet.');
    parent::__construct($player);
  }
}
