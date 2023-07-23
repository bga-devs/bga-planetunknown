<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard2 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $type = 'civCard';
  protected $level = 1;

  public function __construct($player)
  {
    $this->title = clienttranslate('Dept of Operations');
    $this->desc = clienttranslate('Advance any one tracker twice.');
    parent::__construct($player);
  }

  //twiceSynergy
  public function effect(){

  }

  public function score(){
    return 0;
  }

}
