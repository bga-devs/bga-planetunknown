<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard56 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Exponient');
    $this->desc = clienttranslate('Have the least tech resources on your planet.');
    parent::__construct($player);
  }

  public function score($player)
  {
  }
}
