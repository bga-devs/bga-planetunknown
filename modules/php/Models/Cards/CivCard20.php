<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard20 extends \PU\Models\Cards\CivCard
{
  protected $effectType = END_GAME;
  protected $level = 3;

  public function __construct($player)
  {
    $this->title = clienttranslate('Best in Planet Award');
    $this->desc = clienttranslate('3 Medals');
    parent::__construct($player);
  }

  //
  public function effect()
  {
  }

  public function score()
  {
    return 3;
  }
}
