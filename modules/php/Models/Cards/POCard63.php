<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard63 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Meltdown');
    $this->desc = clienttranslate('End the game with tiles covering all planetary ice.');
    parent::__construct($player);
  }

  public function score($player)
  {
  }
}
