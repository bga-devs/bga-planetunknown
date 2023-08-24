<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard55 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Crowd Source');
    $this->desc = clienttranslate('Advance the civ resource track to maximum.');
    parent::__construct($player);
  }
}
