<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard53 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : 10,000 lakes');
    $this->desc = clienttranslate('Have the most unique areas of water terrain on your planet.');
    parent::__construct($player);
  }

  public function score($playerLeft, $playerRight)
  {
  }
}
