<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard48 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: PowerLine');
    $this->desc = clienttranslate('Create a terrain area containing six energy resources.');
    parent::__construct($player);
  }

  public function score($player)
  {
  }
}
