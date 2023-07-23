<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard45 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Megacity');
    $this->desc = clienttranslate('Have the largest single area of civ terrain on your planet.');
    parent::__construct($player);
  }

  public function score($playerLeft, $playerRight)
  {
  }
}
