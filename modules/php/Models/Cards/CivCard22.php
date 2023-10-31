<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;
use PU\Managers\Tiles;

/*
 * Card
 */

class CivCard22 extends \PU\Models\Cards\CivCard
{
  protected $effectType = END_GAME;
  protected $level = 4;

  public function __construct($player)
  {
    $this->title = clienttranslate('Sustainable Living Act');
    $this->desc = clienttranslate('Your biomass patches are worth one medal each.');
    parent::__construct($player);
  }

  public function effect()
  {
  }

  public function score()
  {
    return Tiles::getAll()
      ->where('pId', $this->getPId())
      ->where('location', 'planet')
      ->where('type', BIOMASS_PATCH)
      ->count();
  }
}
