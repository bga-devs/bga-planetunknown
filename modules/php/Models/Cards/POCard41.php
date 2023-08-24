<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard41 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Doghouse');
    $this->desc = clienttranslate('Create a 3x3 area of rover terrains.');
    parent::__construct($player);
  }
}
