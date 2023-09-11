<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard16 extends \PU\Models\Cards\CivCard
{
  protected $effectType = IMMEDIATE;
  protected $level = 3;

  public function __construct($player)
  {
    $this->title = clienttranslate('Depot Repair Online');
    $this->desc = clienttranslate('Take one tile from your depot and place it. Do not advance for its resources.');
    parent::__construct($player);
  }

  //free_tile
  public function effect()
  {
    return $this->freePlaceTile();
  }

  public function score()
  {
    return 0;
  }
}
