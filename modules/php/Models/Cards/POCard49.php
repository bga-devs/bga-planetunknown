<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard49 extends \PU\Models\Cards\POCard
{
  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Skyway');
    $this->desc = clienttranslate('Create a 2x5 area of civ terrains.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    $planet = $player->planet();
    return $planet->hasRectangle(CIV, 2, 5) || $planet->hasRectangle(CIV, 5, 2);
  }
}
