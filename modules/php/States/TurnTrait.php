<?php

namespace PU\States;

use PU\Core\Globals;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\Stats;
use PU\Helpers\Log;
use PU\Managers\Players;
use PU\Managers\ActionCards;
use PU\Managers\Meeples;
use PU\Managers\Scores;
use PU\Managers\Actions;
use PU\Managers\Tiles;
use PU\Managers\ZooCards;

trait TurnTrait
{
  /**
   * Boot engine for all players
   */
  function stStartParallel()
  {
    $pIds = Players::getAll()->getIds();
    Engine::setup(
      [
        'action' => \PLACE_TILE,
      ],
      ['method' => 'stEndOfTurn'],
      $pIds
    );
  }

  /*******************************
   ********************************
   ********** END OF TURN *********
   ********************************
   *******************************/

  /**
   * End of turn : replenish and check break
   */
  function stEndOfTurn()
  {
    Notifications::endOfTurn();

    $this->gamestate->jumpToState(ST_CHOOSE_ROTATION);
  }
}
