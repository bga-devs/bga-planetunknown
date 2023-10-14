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
    } elseif ($this->getPId2() == $player->getId()) {
      $otherPlayer = Players::get($this->getPId());
    } else {
      return -2; //HACK check
    }

    $playerValue = $this->evalCriteria($player);
    $otherValue = $this->evalCriteria($otherPlayer);

    $score = $playerValue > $otherValue ? $this->win : ($playerValue == $otherValue ? $this->tie : 0);

    return [$score, $playerValue, $otherValue];
  }

  public function competeAll()
  {
    $playersIds = Players::getAll()->getIds;

    $scores = [];
    $values = [];
    foreach ($playersIds as $pId) {
      $values[$pId] = $this->evalCriteria($pId);
    }

    $max = max($values);

    $score = array_count_values($values)[$max] == 1 ? $this->win : $this->tie;

    foreach ($values as $pId => $value) {
      $scores[$pId] = ($value == $max) ? $score : 0;
    }

    return [$scores, $values];
  }
}
