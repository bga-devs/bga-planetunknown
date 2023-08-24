<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard64 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Deductible');
    $this->desc = clienttranslate('Collect the most lifepods.');
    parent::__construct($player);
  }
}
