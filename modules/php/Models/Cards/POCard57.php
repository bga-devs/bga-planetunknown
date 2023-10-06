<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard57 extends \PU\Models\Cards\POCard
{
  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Overgrowth');
    $this->desc = clienttranslate('Advance the biomass resource track to maximum.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->corporation()->isTrackerOnTop(BIOMASS);
  }
}
