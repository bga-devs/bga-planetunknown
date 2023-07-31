<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard47 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Pony Express');
    $this->desc = clienttranslate('Create a terrain area containing six rover resources.');
    parent::__construct($player);
  }

  public function score($player)
  {
  }
}
