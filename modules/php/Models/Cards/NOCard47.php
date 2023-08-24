<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard47 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : Pacific');
    $this->desc = clienttranslate('Have the largest single area of water terrain on your planet.');
    parent::__construct($player);
  }
}
