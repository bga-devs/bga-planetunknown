<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Planet Unknown implementation : © Timothée Pecatte <tim.pecatte@gmail.com>, Emmanuel Albisser <emmanuel.albisser@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 *
 * planetunknown.action.php
 *
 * Planet Unknownicola main action entry point
 *
 */

class action_planetunknown extends APP_GameAction
{
  // Constructor: please do not modify
  public function __default()
  {
    if (self::isArg('notifwindow')) {
      $this->view = 'common_notifwindow';
      $this->viewArgs['table'] = self::getArg('table', AT_posint, true);
    } else {
      $this->view = 'planetunknown_planetunknown';
      self::trace('Complete reinitialization of board game');
    }
  }

  public function actChooseRotation()
  {
    self::setAjaxMode();
    $rotation = (int) self::getArg('rotation', AT_int, true);
    $this->game->actChooseRotation($rotation);
    self::ajaxResponse();
  }

  public function actChooseSetup()
  {
    self::setAjaxMode();
    $planetId = (int) self::getArg('planetId', AT_int, true);
    $corporationId = (int) self::getArg('corporationId', AT_int, true);
    $rejectedCardId = self::getArg('rejectedCardId', AT_int, false);
    $flux = self::getArg('flux', AT_alphanum, false);

    $this->game->actChooseSetup($planetId, $corporationId, $rejectedCardId, $flux);
    self::ajaxResponse();
  }

  public function loadBugSQL()
  {
    self::setAjaxMode();
    $reportId = (int) self::getArg('report_id', AT_int, true);
    $this->game->loadBugSQL($reportId);
    self::ajaxResponse();
  }

  public function actChangePref()
  {
    self::setAjaxMode();
    $pref = self::getArg('pref', AT_posint, false);
    $value = self::getArg('value', AT_posint, false);
    $this->game->actChangePreference($pref, $value);
    self::ajaxResponse();
  }

  public function actCancel()
  {
    self::setAjaxMode();
    $this->game->actCancelEngine();
    self::ajaxResponse();
  }

  public function actConfirmTurn()
  {
    self::setAjaxMode();
    $this->game->actConfirmTurn();
    self::ajaxResponse();
  }

  public function actConfirmPartialTurn()
  {
    self::setAjaxMode();
    $this->game->actConfirmPartialTurn();
    self::ajaxResponse();
  }

  public function actRestart()
  {
    self::setAjaxMode();
    $this->game->actRestart();
    self::ajaxResponse();
  }

  public function actUndoToStep()
  {
    self::setAjaxMode();
    $stepId = self::getArg('stepId', AT_posint, false);
    $this->game->actUndoToStep($stepId);
    self::ajaxResponse();
  }

  public function actTakeAtomicAction()
  {
    self::setAjaxMode();
    $action = self::getArg('actionName', AT_alphanum, true);
    $args = self::getArg('actionArgs', AT_json, true);
    $this->validateJSonAlphaNum($args, 'actionArgs');
    $this->game->actTakeAtomicAction($action, $args);
    self::ajaxResponse();
  }

  public function actChooseAction()
  {
    self::setAjaxMode();
    $choiceId = self::getArg('id', AT_int, true);
    $result = $this->game->actChooseAction($choiceId);
    self::ajaxResponse();
  }

  public function actPassOptionalAction()
  {
    self::setAjaxMode();
    $result = $this->game->actPassOptionalAction();
    self::ajaxResponse();
  }

  public function actAnytimeAction()
  {
    self::setAjaxMode();
    $choiceId = self::getArg('id', AT_int, true);
    $result = $this->game->actAnytimeAction($choiceId);
    self::ajaxResponse();
  }

  public function actSelectMap()
  {
    self::setAjaxMode();
    $mapId = self::getArg('mapId', AT_alphanum, true);
    $this->game->actSelectMap($mapId);
    self::ajaxResponse();
  }

  public function actSelect()
  {
    self::setAjaxMode();
    $cardIds = self::getArg('cardIds', AT_json, true);
    $this->validateJSonAlphaNum($cardIds, 'cardIds');
    $this->game->actSelectCardsToDiscard($cardIds);
    self::ajaxResponse();
  }

  public function actCancelSelection()
  {
    self::setAjaxMode();
    $this->game->actCancelSelection();
    self::ajaxResponse();
  }

  public function actOrderCards()
  {
    $ids = self::getArg('cardIds', AT_json, true);
    $this->validateJSonAlphaNum($ids, 'cardIds');
    $this->game->actOrderCards($ids);
    self::ajaxResponse();
  }

  //////////////////
  ///// UTILS  /////
  //////////////////
  public function validateJSonAlphaNum($value, $argName = 'unknown')
  {
    if (is_array($value)) {
      foreach ($value as $key => $v) {
        $this->validateJSonAlphaNum($key, $argName);
        $this->validateJSonAlphaNum($v, $argName);
      }
      return true;
    }
    if (is_int($value)) {
      return true;
    }
    $bValid = preg_match('/^[_0-9a-zA-Z- ]*$/', $value) === 1;
    if (!$bValid) {
      throw new feException("Bad value for: $argName", true, true, FEX_bad_input_argument);
    }
    return true;
  }
}
