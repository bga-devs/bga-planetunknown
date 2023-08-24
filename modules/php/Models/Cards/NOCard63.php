<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard63 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Vaporize');
    $this->desc = clienttranslate('Collect the least meteorites.');
    parent::__construct($player);
  }
}
