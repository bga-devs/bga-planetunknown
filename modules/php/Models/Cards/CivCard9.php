<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard9 extends \PU\Models\Cards\CivCard
{
  protected $effectType = END_GAME;
  protected $type = 'civCard';
  protected $level = 2;

  public function __construct($player)
  {
    $this->title = clienttranslate('People\'s Planet Choice Award');
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
