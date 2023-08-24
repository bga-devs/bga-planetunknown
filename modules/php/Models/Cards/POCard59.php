<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard59 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Overdrive');
    $this->desc = clienttranslate('Have the least rover resources on your planet.');
    parent::__construct($player);
  }
}
