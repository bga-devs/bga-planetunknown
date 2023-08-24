<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard52 extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project : X');
    $this->desc = clienttranslate('Have the most unique areas of tech terrain on your planet.');
    parent::__construct($player);
  }
}
