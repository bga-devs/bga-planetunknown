<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard11 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $type = 'civCard';
  protected $level = 2;

  public function __construct($player)
  {
    $this->title = clienttranslate('Wage Increase');
    $this->desc = clienttranslate('Advance any two different trackers once each.');
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
