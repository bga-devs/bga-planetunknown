<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;
use PU\Managers\Meeples;

/*
 * Card
 */

class CivCard28 extends \PU\Models\Cards\CivCard
{
  protected $effectType = END_GAME;
  protected $level = 4;

  public function __construct($player)
  {
    $this->title = clienttranslate('Space Citizen Decree');
    $this->desc = clienttranslate('Your collected lifepods are worth two medals instead of one');
    parent::__construct($player);
  }

  public function score()
  {
    return Meeples::getAll()
      ->where('pId', $this->getPId())
      ->where('type', LIFEPOD)
      ->where('location', 'corporation')
      ->count();
  }
}
