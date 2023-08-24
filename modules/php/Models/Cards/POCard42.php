<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard42 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Voltaic');
    $this->desc = clienttranslate('Create a 3x3 area of energy terrains.');
    parent::__construct($player);
  }
}
