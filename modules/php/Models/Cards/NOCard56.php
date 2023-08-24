<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard56 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Galvanize');
    $this->desc = clienttranslate('Have the least energy resources on your planet.');
    parent::__construct($player);
  }
}
