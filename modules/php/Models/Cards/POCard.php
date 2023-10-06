<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Private ObjectiveCard
 */

class POCard extends \PU\Models\Card
{
  protected $type = 'POCard';
  protected $win = 5;

  public function evalCriteria($player)
  {
    return false;
  }

  //Give points depending on a specific criteria
  public function score($player)
  {
    $objDone = $this->evalCriteria($player);
    $score = $objDone ? $this->$win : 0;
    return $score;
  }
}
