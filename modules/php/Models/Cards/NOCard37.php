<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard37 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Perimeter');
    $this->desc = clienttranslate('Have the most civ resources on the edge of your planet.');
    parent::__construct($player);
  }

  public function score($playerLeft, $playerRight)
  {
  }
}
