<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class CivCard35 extends \PU\Models\Cards\CivCard
{
  protected $effectType = END_GAME;
  protected $type = 'civCard';
  protected $level = 3;
  protected $commerceAgreement = true;

  public function __construct($player)
  {
    $this->title = clienttranslate('Commerce Agreement (3/4)');
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
