<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard44 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Secret Lab');
    $this->desc = clienttranslate('Create a terrain area containing six tech resources.');
    parent::__construct($player);
  }

  public function score($player)
  {
  }
}
