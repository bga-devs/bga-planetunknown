<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard43 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Highway');
    $this->desc = clienttranslate('Create a terrain area containing six civ resources.');
    parent::__construct($player);
  }

  public function score($player)
  {
  }
}
