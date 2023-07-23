<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard25 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $type = 'civCard';
  protected $level = 4;

  public function __construct($player)
  {
    $this->title = clienttranslate('Meteor Defense Program');
    $this->desc = clienttranslate('Destroy any 3 meteorites.');
    parent::__construct($player);
  }

  //destroyMeteors_3
  public function effect(){

  }

  public function score(){
    return ;
  }

}
