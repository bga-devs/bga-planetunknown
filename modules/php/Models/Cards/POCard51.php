<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard51 extends \PU\Models\Cards\POCard
{
  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Genome');
    $this->desc = clienttranslate('Create a 2x5 area of tech terrains.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    $planet = $player->planet();
    return $planet->hasRectangle(TECH, 2, 5) || $planet->hasRectangle(TECH, 5, 2);
  }
}
