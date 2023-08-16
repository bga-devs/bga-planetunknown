<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard14 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $type = 'civCard';
  protected $level = 2;

  public function __construct($player)
  {
    $this->title = clienttranslate('Relocation Bonus');
    $this->desc = clienttranslate('Teleport a rover to any grid square on the planet or tiles.');
    parent::__construct($player);
  }

  //move_x
  public function effect()
  {
    //TODO
  }

  public function score()
  {
    return 0;
  }
}
