<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * EventCard n°{ID}
 */

class EventCard{ID} extends \PU\Models\Cards\EventCard
{
  protected $color = {COLOR};
  protected $isSolo = {SOLO};

  public function __construct($player)
  {
    $this->title = clienttranslate("{TITLE}");
    $this->desc = clienttranslate("{TEXT}");
    parent::__construct($player);
  }

  //ACTION : {ACTION}
  //CONTRAINT : {RULE}
  public function effect()
  {
  }
}
