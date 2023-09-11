<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard32 extends \PU\Models\Cards\CivCard
{
  protected $effectType = END_GAME;
  protected $level = 4;
  protected $meteorRepo = true;

  public function __construct($player)
  {
    $this->title = clienttranslate('Meteorite Repo (4/4)');
    $this->desc = clienttranslate('Claim Meteorite repo cards to improve meteorite scoring.');
    parent::__construct($player);
  }

  //meteorRepo
  public function effect()
  {
  }

  public function score()
  {
    return 0;
  }
}
