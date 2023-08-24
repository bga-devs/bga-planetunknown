<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard60 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Pinnacle');
    $this->desc = clienttranslate('Have the most resource tracks at maximum.');
    parent::__construct($player);
  }
}
