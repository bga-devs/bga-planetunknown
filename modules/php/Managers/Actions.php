<?php
namespace PU\Managers;
use PU\Core\Game;
use PU\Core\Engine;
use PU\Managers\Players;
use PU\Core\Globals;

/* Class to manage all the cards for Agricola */

class Actions
{
  static $classes = [
    GAIN => 'Gain',
    // PAY => 'Pay',
    ACTIVATE_CARD => 'ActivateCard',
    SPECIAL_EFFECT => 'SpecialEffect',
    CHOOSE_ACTION_CARD => 'ChooseActionCard',

    // Action cards
    ANIMALS => 'Animals',
    ASSOCIATION => 'Association',
    BUILD => 'Build',
    CARDS => 'Cards',
    SPONSORS => 'Sponsors',

    // Animals powers
    SPRINT => 'Effects\Sprint',
    HUNTER => 'Effects\Hunter',
    INVENTIVE => 'Effects\Inventive',
    JUMPING => 'Effects\Jumping',
    SUNBATHING => 'Effects\Sunbathing',
    POUCH => 'Effects\Pouch',
    DIGGING => 'Effects\Digging',
    VENOM => 'Effects\Venom',
    VENOM_PAY => 'Effects\VenomPay',
    PILFERING => 'Effects\Pilfering',
    PILFERING_EXECUTE => 'Effects\PilferingExecute',
    SNAPPING => 'Effects\Snapping',
    HYPNOSIS => 'Effects\Hypnosis',
    SCAVENGING => 'Effects\Scavenging',
    POSTURING => 'Effects\Posturing',
    PERCEPTION => 'Effects\Perception',
    PACK => 'Effects\Pack',
    CLEVER => 'Effects\Clever',
    BOOST => 'Effects\Boost',
    ACTION => 'Effects\Action',
    MULTIPLIER => 'Effects\Multiplier',
    FULL_THROATED => 'Effects\FullThroated',
    ICONIC_ANIMAL => 'Effects\IconicAnimal',
    RESISTANCE => 'Effects\Resistance',
    ASSERTION => 'Effects\Assertion',
    SPONSOR_MAGNET => 'Effects\SponsorMagnet',
    CONSTRICTION => 'Effects\Constriction',
    DETERMINATION => 'Effects\Determination',
    PEACOCKING => 'Effects\Peacocking',
    PETTING_ZOO_ANIMAL => 'Effects\PettingZooAnimal',
    DOMINANCE => 'Effects\Dominance',
    MAP4 => 'Effects\Map4Effect',
    MAP8 => 'Effects\Map8Effect',

    // Bonuses
    GAIN_UNIVERSITY => 'Bonuses\GainUniversity',
    GAIN_PARTNER_ZOO => 'Bonuses\GainPartnerZoo',
    BUY_SPONSOR => 'Bonuses\BuySponsor',
    WAZA_SPECIAL => 'Bonuses\WazaSpecial',

    // Other
    ADVANCE_BREAK => 'AdvanceBreak',
    CLEANUP => 'Cleanup',
    DISCARD => 'Discard',
    UPGRADE_CARD => 'UpgradeCard',
    RELEASE => 'Release',
    TAKE_BONUS => 'TakeBonus',
    MOVE_ANIMALS => 'MoveAnimals',
    TAKE_IN_RANGE => 'TakeInRange',
    DISCARD_SCORING => 'DiscardScoring',
    REMOVE_BONUS => 'RemoveBonus',
    MONEY_INCOME => 'MoneyIncome',
  ];

  public static function get($actionId, $ctx = null)
  {
    if (!\array_key_exists($actionId, self::$classes)) {
      // throw new \feException(print_r(debug_print_backtrace()));
      // throw new \feException(print_r(Globals::getEngine()));
      throw new \BgaVisibleSystemException('Trying to get an atomic action not defined in Actions.php : ' . $actionId);
    }
    $name = '\PU\Actions\\' . self::$classes[$actionId];
    return new $name($ctx);
  }

  public static function getActionOfState($stateId, $throwErrorIfNone = true)
  {
    foreach (array_keys(self::$classes) as $actionId) {
      if (self::getState($actionId, null) == $stateId) {
        return $actionId;
      }
    }

    if ($throwErrorIfNone) {
      throw new \BgaVisibleSystemException('Trying to fetch args of a non-declared atomic action in state ' . $stateId);
    } else {
      return null;
    }
  }

  public static function isDoable($actionId, $ctx, $player)
  {
    $res = self::get($actionId, $ctx)->isDoable($player);
    return $res;
  }

  public static function getErrorMessage($actionId)
  {
    if ($actionId == VENOM_PAY) {
      return Game::get()::translate('You no longer have enough money to pay for Venom. You must undo or restart your turn.');
    }

    $actionId = ucfirst(mb_strtolower($actionId));
    $msg = sprintf(
      Game::get()::translate(
        'Attempting to take an action (%s) that is not possible. Either another card erroneously flagged this action as possible, or this action was possible until another card interfered.'
      ),
      $actionId
    );
    return $msg;
  }

  public static function getState($actionId, $ctx)
  {
    return self::get($actionId, $ctx)->getState();
  }

  public static function getArgs($actionId, $ctx)
  {
    $action = self::get($actionId, $ctx);
    $methodName = 'args' . $action->getClassName();
    $args = \method_exists($action, $methodName) ? $action->$methodName() : [];
    return array_merge($args, ['optionalAction' => $ctx->isOptional()]);
  }

  public static function takeAction($actionId, $actionName, $args, $ctx)
  {
    $player = Players::getActive();
    if (!self::isDoable($actionId, $ctx, $player)) {
      throw new \BgaUserException(self::getErrorMessage($actionId));
    }

    $action = self::get($actionId, $ctx);
    $methodName = $actionName; //'act' . self::$classes[$actionId];
    $action->$methodName(...$args);
  }

  public static function stAction($actionId, $ctx)
  {
    $player = Players::getActive();
    if (!self::isDoable($actionId, $ctx, $player)) {
      if (!$ctx->isOptional()) {
        if (self::isDoable($actionId, $ctx, $player, true)) {
          Game::get()->gamestate->jumpToState(ST_IMPOSSIBLE_MANDATORY_ACTION);
          return;
        } else {
          throw new \BgaUserException(self::getErrorMessage($actionId));
        }
      } else {
        // Auto pass if optional and not doable
        Game::get()->actPassOptionalAction(true);
        return;
      }
    }

    $action = self::get($actionId, $ctx);
    $methodName = 'st' . $action->getClassName();
    if (\method_exists($action, $methodName)) {
      $action->$methodName();
    }
  }

  public static function stPreAction($actionId, $ctx)
  {
    $action = self::get($actionId, $ctx);
    $methodName = 'stPre' . $action->getClassName();
    if (\method_exists($action, $methodName)) {
      $action->$methodName();
      if ($ctx->isIrreversible(Players::get($ctx->getPId()))) {
        Engine::checkpoint();
      }
    }
  }

  public static function pass($actionId, $ctx)
  {
    if (!$ctx->isOptional()) {
      self::error($ctx->toArray());
      throw new \BgaVisibleSystemException('This action is not optional');
    }

    $action = self::get($actionId, $ctx);
    $methodName = 'actPass' . $action->getClassName();
    if (\method_exists($action, $methodName)) {
      $action->$methodName();
    } else {
      Engine::resolve(PASS);
    }

    Engine::proceed();
  }
}
