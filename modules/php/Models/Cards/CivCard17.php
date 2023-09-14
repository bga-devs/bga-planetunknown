<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard17 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $level = 3;

  public function __construct($player)
  {
    $this->title = clienttranslate('Landing Zone Approved');
    $this->desc = clienttranslate('Destroy all meteorites from one row or one column.');
    parent::__construct($player);
  }

  //destroyMeteors_row
  public function effect()
  {
    return [
      'action' => \DESTROY_ALL_IN_ROW
    ];
  }

  public function score()
  {
    return 0;
  }
}
