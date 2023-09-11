<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard10 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $level = 2;

  public function __construct($player)
  {
    $this->title = clienttranslate('New Angel Investors');
    $this->desc = clienttranslate('Advance any three different trackers once each.');
    parent::__construct($player);
  }

  //synergy_3
  public function effect()
  {
    return $this->synergy(3, 1);
  }

  public function score()
  {
    return 0;
  }
}
