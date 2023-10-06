<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard58 extends \PU\Models\Cards\POCard
{
  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Flowrate');
    $this->desc = clienttranslate('Advance the water resource track to maximum.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->corporation()->isTrackerOnTop(WATER);
  }
}
