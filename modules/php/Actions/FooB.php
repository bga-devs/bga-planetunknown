<?php
namespace PU\Actions;
use PU\Managers\Meeples;
use PU\Managers\Players;
use PU\Core\Notifications;
use PU\Core\Engine;
use PU\Core\Stats;
use PU\Helpers\Utils;
use PU\Helpers\FlowConvertor;

class FooB extends \PU\Models\Action
{
  public function getState()
  {
    return ST_FOO_B;
  }

  public function argsFooB()
  {
    return [];
  }

  public function actFooB()
  {
  }
}
