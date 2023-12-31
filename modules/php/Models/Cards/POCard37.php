<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard37 extends \PU\Models\Cards\POCard
{
  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Complex');
    $this->desc = clienttranslate('Create a 3x3 area of civ terrains.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->planet()->hasRectangle(CIV, 3, 3);
  }
}
