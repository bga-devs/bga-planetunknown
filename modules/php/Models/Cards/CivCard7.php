<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard7 extends \PU\Models\Cards\CivCard
{
  protected $effectType = END_GAME;
  protected $level = 1;

  public function __construct($player)
  {
    $this->title = clienttranslate('Rising Planet Award');
    $this->desc = clienttranslate('2 Medals');
    parent::__construct($player);
  }

  //
  public function effect()
  {
  }

  public function score()
  {
    return 2;
  }
}
