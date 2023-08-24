<?php

namespace PU\Models;

use PU\Managers\Cards;
use PU\Managers\Players;

/*
 * Card
 */

class Card extends \PU\Helpers\DB_Model
{
  protected $title;
  protected $desc;
  protected $table = 'cards';
  protected $primary = 'card_id';
  protected $attributes = [
    'id' => ['card_id', 'int'],
    'location' => 'card_location',
    'state' => ['card_state', 'int'],
    'extra_datas' => ['extra_datas', 'obj'],
    'pId' => 'player_id',
    'pId2' => 'player_id2',
  ];

  protected $staticAttributes = ['title', 'desc'];

  public function getPlayer()
  {
    return Players::get($this->pId);
  }

  public function formatScoreEntry($n)
  {
    return [
      $this->type . '_' . $this->getId() => $n
    ];
  }
}
