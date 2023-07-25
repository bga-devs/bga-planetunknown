<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard
 */

class EventCard extends \PU\Models\Card
{
  protected $type = 'EventCard';
  protected $color = '';
  protected $isSolo = false;

  public function __construct($player)
  {
    parent::__construct($player);
  }

  //ACTION : {ACTION}
  //CONTRAINT : {RULE}
  public function effect()
  {
  }
}
