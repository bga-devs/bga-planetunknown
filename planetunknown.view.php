<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Planet Unknown implementation : © Timothée Pecatte <tim.pecatte@gmail.com>, Emmanuel Albisser <emmanuel.albisser@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * planetunknown.view.php
 *
 */

require_once APP_BASE_PATH . 'view/common/game.view.php';

class view_planetunknown_planetunknown extends game_view
{
  function getGameName()
  {
    return 'planetunknown';
  }
  function build_page($viewArgs)
  {
  }
}
