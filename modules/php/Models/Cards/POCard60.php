<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard60 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Alternative Energy');
    $this->desc = clienttranslate('Have the most resource tracks at maximum.');
    parent::__construct($player);
  }
}
