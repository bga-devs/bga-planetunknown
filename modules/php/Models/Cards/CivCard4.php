<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard4 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $level = 1;

  public function __construct($player)
  {
    $this->title = clienttranslate('Dept of Engineering');
    $this->desc = clienttranslate('Gain four rover movement.');
    parent::__construct($player);
  }

  //move_4
  public function effect()
  {
    $childs = [];
    for ($i = 4; $i >= 1; $i--) {
      $childs[] = [
        'action' => MOVE_ROVER,
        'args' => [
          'remaining' => $i
        ]
      ];
    }
    return [
      'type' => NODE_SEQ,
      'childs' => $childs,
    ];
  }

  public function score()
  {
    return 0;
  }
}
