<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard18 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $level = 3;

  public function __construct($player)
  {
    $this->title = clienttranslate('Search and Rescue Team');
    $this->desc = clienttranslate('Collect three lifepods from your planet.');
    parent::__construct($player);
  }

  //collectLifepod_3
  public function effect()
  {
    return [
      'action' => COLLECT_MEEPLE,
      'args' => [
        'type' => LIFEPOD,
        'n' => 3,
        'action' => 'collect'
      ]
    ];
  }

  public function score()
  {
    return 0;
  }
}
