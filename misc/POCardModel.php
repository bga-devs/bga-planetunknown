<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard{ID} extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('{TITLE}');
    $this->desc = clienttranslate('{TEXT}');
    parent::__construct($player);
  }
}
