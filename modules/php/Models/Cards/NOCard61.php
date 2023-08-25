<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard61 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Geode');
    $this->desc = clienttranslate('Collect the most meteorites.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->corporation()->getNCollected(METEOR);
  }
}
