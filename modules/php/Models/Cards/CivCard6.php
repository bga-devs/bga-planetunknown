<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard6 extends \PU\Models\Cards\CivCard
{
  protected $effectType = END_GAME;
  protected $type = 'civCard';
  protected $level = 1;

  public function __construct($player)
  {
    $this->title = clienttranslate('Observance of First Planet Holiday');
    $this->desc = clienttranslate('1 Medal');
    parent::__construct($player);
  }

  //
  public function effect(){

  }

  public function score(){
    return 1;
  }

}
