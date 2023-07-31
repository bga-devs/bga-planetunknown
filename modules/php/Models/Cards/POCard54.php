<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard54 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Power Strip');
    $this->desc = clienttranslate('Create a 2x5 area of energy terrains.');
    parent::__construct($player);
  }

  public function score($player)
  {
  }
}
