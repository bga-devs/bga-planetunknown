<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard26 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $type = 'civCard';
  protected $level = 4;

  public function __construct($player)
  {
    $this->title = clienttranslate('Global Parks System');
    $this->desc = clienttranslate('Gain three biomass patches.');
    parent::__construct($player);
  }

  //biomass_3
  public function effect(){

  }

  public function score(){
    return 0;
  }

}
