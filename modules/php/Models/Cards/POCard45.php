<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * POCard
 */

class POCard45 extends \PU\Models\Cards\POCard
{
  public function __construct($player)
  {
    $this->title = clienttranslate('Project: Germinate');
    $this->desc = clienttranslate('Create a terrain area containing six biomass resources.');
    parent::__construct($player);
  }

  public function evalCriteria($player)
  {
    return $player->planet()->hasZoneWithEnoughSymbols(BIOMASS, 6);
  }
}
