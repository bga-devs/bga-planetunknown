<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;
use PU\Managers\Players;

/*
 * Card
 */

class NOCard extends \PU\Models\Card
{
  protected $type = 'NOCard';
  protected $win = 5;
  protected $tie = 2;


  public function evalCriteria($player)
  {
    return 0;
  }

  //Give points depending on a specific criteria
  public function score($player)
  {
    if ($this->getPId() == $player->getId()) {
      $otherPlayer = Players::get($this->getPId2());
    } else if ($this->getPId2() == $player->getId()) {
      $otherPlayer = Players::get($this->getPId());
    } else {
      return -2; //HACK check
    }

    $playerValue = $this->evalCriteria($player);
    $otherValue = $this->evalCriteria($otherPlayer);

    $score = $playerValue > $otherValue
      ? $this->win
      : ($playerValue == $otherValue
        ? $this->tie
        : 0);

    return $score;
  }
}
