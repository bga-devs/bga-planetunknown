<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard58 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Flowrate');
    $this->desc = clienttranslate('Have the least water resources on your planet.');
    parent::__construct($player);
  }

  public function score($player)
  {
  }
}
