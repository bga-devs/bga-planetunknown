<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard33 extends \PU\Models\Cards\CivCard
{
  protected $effectType = END_GAME;
  protected $level = 1;
  public $commerceAgreement = true;

  public function __construct($player)
  {
    $this->title = clienttranslate('Commerce Agreement (1/4)');
    $this->desc = clienttranslate('Claim Commerce Agreement cards to score medals.');
    parent::__construct($player);
  }

  //commerceAgreement
  public function effect()
  {
  }

  public function score()
  {
    return 0;
  }
}
