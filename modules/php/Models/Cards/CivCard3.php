<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard3 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $type = 'civCard';
  protected $level = 1;

  public function __construct($player)
  {
    $this->title = clienttranslate('Dept of Natural Resources');
    $this->desc = clienttranslate('Gain one biomass patch.');
    parent::__construct($player);
  }

  //GainBiomass
  public function effect(){
    return $this->gainBiomass();
  }

  public function score(){
    return 0;
  }

}
