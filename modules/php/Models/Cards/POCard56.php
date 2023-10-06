<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard56 extends \PU\Models\Cards\POCard
{
  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Exponient');
    $this->desc = clienttranslate('Advance the tech resource track to maximum.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->corporation()->isTrackerOnTop(TECH);
  }
}
