<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * NOCard
 */

class NOCard{ID} extends \PU\Models\Cards\NOCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('{TITLE}');
    $this->desc = clienttranslate('{TEXT}');
    parent::__construct($player);
  }
}
