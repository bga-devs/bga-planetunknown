<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard57 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Overgrowth');
    $this->desc = clienttranslate('Have the least biomass resources on your planet.');
    parent::__construct($player);
  }

  public function score($player)
  {
  }
}
