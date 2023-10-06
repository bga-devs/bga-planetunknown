<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard53 extends \PU\Models\Cards\POCard
{
  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Runway');
    $this->desc = clienttranslate('Create a 2x5 area of rover terrains.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    $planet = $player->planet();
    return $planet->hasRectangle(ROVER, 2, 5) || $planet->hasRectangle(ROVER, 5, 2);
  }
}
