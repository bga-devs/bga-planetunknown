<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard62 extends \PU\Models\Cards\POCard
{

  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Tight Ship');
    $this->desc = clienttranslate('Collect more lifepods than meteorites.');
    parent::__construct($player);
  }

  public function score($player)
  {
  }
}