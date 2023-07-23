<?php

namespace PU\Models;

use PU\Managers\Cards;

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
  ];
}
