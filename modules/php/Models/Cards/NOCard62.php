<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard62 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Rescue');
    $this->desc = clienttranslate('Collect the most lifepods.');
    parent::__construct($player);
  }

  public function score($playerLeft, $playerRight)
  {
  }
}
