<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard{ID} extends \PU\Models\Cards\CivCard
{
  protected $effectType = {TYPE};
  protected $type = 'civCard';
  protected $level = {LEVEL};

  public function __construct($player)
  {
    $this->title = clienttranslate('{TITLE}');
    $this->desc = clienttranslate('{TEXT}');
    parent::__construct($player);
  }

  //{ACTION}
  public function effect(){

  }

  public function score(){
    return {MEDAL};
  }

}
