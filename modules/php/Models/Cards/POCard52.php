<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard52 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Panama');
    $this->desc = clienttranslate('Create a 2x5 area of water terrains.');
    parent::__construct($player);
  }

  public function score($player)
  {
  }
}
