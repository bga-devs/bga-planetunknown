<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard61 extends \PU\Models\Cards\POCard
{
  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Cleanup');
    $this->desc = clienttranslate('Collect 9 meteorites.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->corporation()->getNCollected(METEOR) >= 9;
  }
}
