<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard50 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Supercharge');
    $this->desc = clienttranslate('Have the most unique areas of energy terrain on your planet.');
    parent::__construct($player);
  }
}
