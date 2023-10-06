<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard46 extends \PU\Models\Cards\POCard
{
  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Amazon');
    $this->desc = clienttranslate('Create a terrain area containing six water resources.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->planet()->hasZoneWithEnoughSymbols(WATER, 6);
  }
}
