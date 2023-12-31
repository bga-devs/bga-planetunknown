<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard59 extends \PU\Models\Cards\POCard
{
  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Overdrive');
    $this->desc = clienttranslate('Advance the rover resource track to maximum.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->corporation()->isTrackerOnTop(ROVER);
  }
}
