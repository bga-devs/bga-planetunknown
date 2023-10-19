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

  public function getColor()
  {
    return $this->color;
  }

  public function getDesc()
  {
    return $this->desc;
  }

  //ACTION : {ACTION}
  //CONTRAINT : {RULE}
  public function effect()
  {
  }

  public function synergy($toChoose, $nMove, $types = ALL_TYPES)
  {
    return [
      'action' => CHOOSE_TRACKS,
      'args' => [
        'types' => $types,
        'n' => $toChoose,
        'move' => $nMove,
        'from' => clienttranslate('Event Card'),
      ],
    ];
  }

  public function synergyOrPeek()
  {
    return [
      'type' => NODE_XOR,
      'childs' => [
        $this->synergy(1, 1),
        [
          'action' => PEEK_NEXT_EVENT,
        ],
      ],
    ];
  }
}
