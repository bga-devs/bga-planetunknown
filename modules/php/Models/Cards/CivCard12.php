<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard12 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $type = 'civCard';
  protected $level = 2;

  public function __construct($player)
  {
    $this->title = clienttranslate('Dept of Parks and Rec');
    $this->desc = clienttranslate('Gain two biomass patches.');
    parent::__construct($player);
  }

  //biomass_2
  public function effect()
  {
    return $this->gainBiomass(2);
  }

  public function score()
  {
    return 0;
  }
}
