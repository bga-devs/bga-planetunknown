<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard28 extends \PU\Models\Cards\CivCard
{
  protected $effectType = END_GAME;
  protected $type = 'civCard';
  protected $level = 4;

  public function __construct($player)
  {
    $this->title = clienttranslate('Space Citizen Decree');
    $this->desc = clienttranslate('Your collected lifepods are worth two medals instead of one');
    parent::__construct($player);
  }

  //2perLifepod
  public function effect(){

  }

  public function score(){
    return 0;
  }

}
