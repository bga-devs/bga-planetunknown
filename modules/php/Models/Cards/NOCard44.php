<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard44 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Plasma');
    $this->desc = clienttranslate('Have the largest single area of energy terrain on your planet.');
    parent::__construct($player);
  }
}
