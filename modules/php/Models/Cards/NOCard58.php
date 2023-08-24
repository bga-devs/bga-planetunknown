<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard58 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Rideshare');
    $this->desc = clienttranslate('Have the least rover resources on your planet.');
    parent::__construct($player);
  }
}
