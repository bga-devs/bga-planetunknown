<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class NOCard extends \PU\Models\Card
{
  protected $type = 'NOCard';
  protected $win = 5;
  protected $tie = 2;

  //Give points depending on a specific criteria
  public function score($playerLeft, $playerRight)
  {
  }
}
