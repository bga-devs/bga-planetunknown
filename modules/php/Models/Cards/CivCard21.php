<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard21 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $level = 3;

  public function __construct($player)
  {
    $this->title = clienttranslate('Unionization');
    $this->desc = clienttranslate('Advance all trackers to the 5th position if possible. Do not activate synergy boost.'); //TODOFirgon
    parent::__construct($player);
  }

  //synergy_max5
  public function effect()
  {
    return [
      'action' => \MOVE_TRACKERS_TO_FIVE
    ];
  }

  public function score()
  {
    return 0;
  }
}
