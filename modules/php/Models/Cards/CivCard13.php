<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard13 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $type = 'civCard';
  protected $level = 2;

  public function __construct($player)
  {
    $this->title = clienttranslate('Approved Overtime');
    $this->desc = clienttranslate('Collect two lifepods from your planet.');
    parent::__construct($player);
  }

  //collectLifepod_2
  public function effect()
  {
    return [
      'action' => COLLECT_MEEPLE,
      'args' => [
        'type' => LIFEPOD,
        'n' => 2,
        'action' => 'collect'
      ]
    ];
  }

  public function score()
  {
    return 0;
  }
}
