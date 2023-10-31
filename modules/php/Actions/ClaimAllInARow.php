<?php

namespace PU\Actions;

use PU\Managers\Meeples;
use PU\Managers\Players;
use PU\Managers\Tiles;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\Stats;
use PU\Helpers\Utils;
use PU\Helpers\FlowConvertor;
use PU\Managers\Susan;
use PU\Models\Corporations\Corporation;
use PU\Models\Planet;

class ClaimAllInARow extends \PU\Models\Action
{
  public function getState()
  {
    return ST_CLAIM_ALL_IN_A_ROW;
  }

  public function isOptional()
  {
    return !$this->isDoable($this->getPlayer());
  }

  public function getDescription()
  {
    return [
      'log' => \clienttranslate('Claim all benefits from a tracks row'),
      'args' => []
    ];
  }

  public function getChoosableRows()
  {
    $player = $this->getPlayer();

    $rows = [];

    foreach (ALL_TYPES as $type) {
      $row = $player->corporation()->getLevelOnTrack($type);
      if (!in_array($row, $rows)) {
        $rows[] = $row;
      }
    }
    return $rows;
  }

  public function argsClaimAllInARow()
  {
    return [
      'choosableRows' => $this->getChoosableRows()
    ];
  }

  public function stClaimAllInARow()
  {
    $args = $this->argsClaimAllInARow();
    if ($args['choosableRows'] == 1)
      return [$args['choosableRows'][0]]; // Ensure the UI is not entering the state !!!
  }

  public function actClaimAllInARow($row)
  {
    $player = $this->getPlayer();
    $args = $this->argsClaimAllInARow();
    if (!in_array($row, $args['choosableRows'])) {
      throw new BgaVisibleSystemException("You can\'t choose the row number $row. Should not happen");
    }

    $bonuses = [];

    foreach (ALL_TYPES as $type) {
      $coord = ['x' => $type, 'y' => $row];
      $bonuses = array_merge($bonuses, $player->corporation()->getBonuses($coord, true));
    }

    //this will work even if $coord['x'] not matching all bonuses, it's 'y' the important data 
    $this->createActionFromBonus($bonuses, $player, $coord);
  }
}
