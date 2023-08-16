<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard24 extends \PU\Models\Cards\CivCard
{
  protected $effectType = END_GAME;
  protected $type = 'civCard';
  protected $level = 4;

  public function __construct($player)
  {
    $this->title = clienttranslate('Planet Lottery');
    $this->desc = clienttranslate('3 Medals and Advance any tracker once.');
    parent::__construct($player);
  }

  //synergy
  public function effect()
  {
    return $this->synergy(1, 1);
  }

  public function score()
  {
    return 3;
  }
}
