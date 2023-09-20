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
  protected $type;
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

  protected $staticAttributes = ['title', 'desc', 'type'];

  public function getPlayer()
  {
    return Players::get($this->pId);
  }

  // Just for testing purpose
  public function jsonSerialize()
  {
    return array_merge(parent::jsonSerialize(), $this->getStaticData());
  }

  public function getType()
  {
    return $this->type;
  }
}
