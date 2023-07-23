<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard extends \PU\Models\Card
{
  protected $effectType = ''; //IMMEDIATE OR END_GAME
  protected $type = 'civCard';
  protected $level = ''; //1,2,3 or 4

  //{ACTION}
  public function effect()
  {
  }

  public function score()
  {
  }
}
