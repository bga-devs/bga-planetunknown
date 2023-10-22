<?php

namespace PU\Models\Cards;

use PU\Managers\Cards;

/*
 * Card
 */

class OCard extends \PU\Models\Card
{
  protected $privateSide;
  protected $neighborSide;

  public function __construct($row)
  {
    $privateClassName = '\PU\Models\Cards\POCard' . $row['card_id'];
    $neighborClassName = '\PU\Models\Cards\NOCard' . $row['card_id'];
    $this->privateSide = new $privateClassName($row);
    $this->neighborSide = new $neighborClassName($row);
    parent::__construct($row);
  }

  public function getCard()
  {
    return in_array($this->getLocation(), ['NOCards', 'deck_objectives']) ? $this->getNeighborSide() : $this->getPrivateSide();
  }
  public function getTitle()
  {
    return $this->getCard()->getTitle();
  }
  public function getDesc()
  {
    return $this->getCard()->getDesc();
  }

  public function getPrivateSide()
  {
    return $this->privateSide;
  }

  public function getNeighborSide()
  {
    return $this->neighborSide;
  }

  public function score($player = null)
  {
    return $this->getCard()->score($player);
  }

  public function getType()
  {
    return $this->getCard()->getType();
  }
}
