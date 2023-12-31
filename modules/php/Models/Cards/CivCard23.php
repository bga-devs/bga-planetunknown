<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard23 extends \PU\Models\Cards\CivCard
{
  protected $effectType = END_GAME;
  protected $level = 4;

  public function __construct($player)
  {
    $this->title = clienttranslate('Gaming Commission');
    $this->desc = clienttranslate('2 Medals and Advance any tracker once.');
    parent::__construct($player);
  }

  //synergy
  public function effect()
  {
    return $this->synergy(1, 1);
  }

  public function score()
  {
    return 2;
  }
}
