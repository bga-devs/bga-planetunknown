<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard27 extends \PU\Models\Cards\CivCard
{
  protected $effectType = END_GAME;
  protected $level = 4;

  public function __construct($player)
  {
    $this->title = clienttranslate('Best Planet to Live on Award');
    $this->desc = clienttranslate('4 Medals');
    parent::__construct($player);
  }

  //
  public function effect()
  {
  }

  public function score()
  {
    return 4;
  }
}
