<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard64 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Coveralls');
    $this->desc = clienttranslate('End the game with tiles covering all planetary land.');
    parent::__construct($player);
  }

  public function score($player)
  {
  }
}
