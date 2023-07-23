<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard22 extends \PU\Models\Cards\CivCard
{
  protected $effectType = END_GAME;
  protected $type = 'civCard';
  protected $level = 4;

  public function __construct($player)
  {
    $this->title = clienttranslate('Sustainable Living Act');
    $this->desc = clienttranslate('Your biomass patches are worth one medal each.');
    parent::__construct($player);
  }

  //1perBiomass
  public function effect(){

  }

  public function score(){
    return 0;
  }

}
