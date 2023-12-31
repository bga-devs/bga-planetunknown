<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard1 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $level = 1;

  public function __construct($player)
  {
    $this->title = clienttranslate('Temp Agency');
    $this->desc = clienttranslate('Advance two different trackers once each.');
    parent::__construct($player);
  }

  //synergy_2
  public function effect()
  {
    return $this->synergy(2, 1);
  }

  public function score()
  {
    return 0;
  }
}
