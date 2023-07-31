<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard38 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Rubik');
    $this->desc = clienttranslate('Create a 3x3 area of tech terrains.');
    parent::__construct($player);
  }

  public function score($player)
  {
  }
}
