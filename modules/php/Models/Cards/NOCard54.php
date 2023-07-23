<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard54 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Luddite');
    $this->desc = clienttranslate('Have the least tech resources on your planet.');
    parent::__construct($player);
  }

  public function score($playerLeft, $playerRight)
  {
  }
}
