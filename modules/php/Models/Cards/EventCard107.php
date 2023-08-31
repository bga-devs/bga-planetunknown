<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°107
 */

class EventCard107 extends \PU\Models\Cards\EventCard
{
  protected $color = RED;
  protected $isSolo = false;

  public function __construct($player)
  {
    $this->title = clienttranslate("That thing almost clipped me!");
    $this->desc = clienttranslate("Add one meteorite to an empty symbol on your planet.");
    parent::__construct($player);
  }

  //ACTION : AddMeteor
  //CONTRAINT : 
  public function effect()
  {
    return [
      'action' => PLACE_MEEPLE,
      'args' => [
        'type' => METEOR,
        'constraint' => 'emptyMeteorSymbol'
      ]
    ];
  }
}
