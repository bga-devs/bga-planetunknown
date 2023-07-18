<?php
namespace PU\Models;
use PU\Core\Stats;
use PU\Core\Notifications;
use PU\Core\Preferences;
use PU\Managers\Actions;
use PU\Managers\Meeples;
use PU\Managers\Buildings;
use PU\Core\Globals;
use PU\Core\Engine;
use PU\Helpers\FlowConvertor;
use PU\Helpers\Utils;

/*
 * Player: all utility functions concerning a player
 */

class Player extends \PU\Helpers\DB_Model
{
  private $planet = null;
  protected $table = 'player';
  protected $primary = 'player_id';
  protected $attributes = [
    'id' => ['player_id', 'int'],
    'no' => ['player_no', 'int'],
    'name' => 'player_name',
    'color' => 'player_color',
    'eliminated' => 'player_eliminated',
    'score' => ['player_score', 'int'],
    'scoreAux' => ['player_score_aux', 'int'],
    'zombie' => 'player_zombie',
    'planetId' => 'planet_id',
  ];

  // Cached attribute
  public function planet()
  {
    if ($this->planet == null) {
      $planetId = $this->getPlanetId();

      if (is_null($planetId) || $planetId == '') {
        return null;
      }
      $className = '\PU\planets\planet' . $planetId;
      $this->planet = new $className($this);
    }
    return $this->planet;
  }

  public function canUseplanet($planetId)
  {
    if ($this->getplanetId() != $planetId) {
      return false;
    }
    return $this->planet()->canUseEffect();
  }

  public function getUiData($currentPlayerId = null)
  {
    $data = parent::getUiData();
    $current = $this->id == $currentPlayerId;
    return $data;
  }

  public function getPref($prefId)
  {
    return Preferences::get($this->id, $prefId);
  }

  public function getStat($name)
  {
    $name = 'get' . \ucfirst($name);
    return Stats::$name($this->id);
  }

  public function canTakeAction($action, $ctx)
  {
    return Actions::isDoable($action, $ctx, $this);
  }

  public function isCardUpgraded($cardType)
  {
    $card = null;
    foreach (self::getActionCards() as $cId => $oCard) {
      if ($oCard->getType() == $cardType) {
        $card = $oCard;
      }
    }
    if (Globals::getEffectHypnosis() != 0) {
      $hypnoCard = ActionCards::get(Globals::getEffectHypnosis());
      if ($hypnoCard->getType() == $cardType) {
        $card = $hypnoCard;
      }
    }
    return $card->getLevel() == 2;
  }

  public function countXTokens()
  {
    return self::getXToken();
  }

  public function getIncomeFroplanetpeal()
  {
    // prettier-ignore
    $appealIncome = [5, 6, 7, 8, 9, 10, 10, 11, 11, 12, 12, 13, 13, 14, 14, 15, 15, 16, 16, 16, 17, 17, 17, 18, 18, 18, 19, 19, 19, 20, 20, 20, 21, 21, 21, 21, 22, 22, 22, 22, 23, 23, 23, 23, 24, 24, 24, 24, 25, 25, 25, 25, 26, 26, 26, 26, 27, 27, 27, 27, 27, 28, 28, 28, 28, 28, 29, 29, 29, 29, 29, 30, 30, 30, 30, 30, 31, 31, 31, 31, 31, 32, 32, 32, 32, 32, 33, 33, 33, 33, 33, 34, 34, 34, 34, 34, 35, 35, 35, 35, 35, 35, 36, 36, 36, 36, 36, 36];
    $money = $appealIncome[$this->getAppeal()] ?? 37;
    return $money;
  }

  public function getIncome($break = false)
  {
    $bonuses = [];

    if ($break === false) {
      // MONEY FROM APPEAL
      $money = $this->getIncomeFroplanetpeal();
      if ($money != 0) {
        $bonuses[] = [
          MONEY => $money,
          'source' => clienttranslate('appeal income'),
          'income' => true,
        ];
      }

      if (!is_null($this->planet())) {
        // MONEY FROM KIOSKS
        $money = $this->planet()->getKioskIncome();
        if ($money != 0) {
          $bonuses[] = [
            MONEY => $money,
            'source' => clienttranslate('kiosk income'),
            'income' => true,
          ];
        }

        // planet INCOME BONUSES
        $incomeBonuses = $this->planet()->getIncomeBonuses();
        $bonusToGet = array_diff(array_keys($incomeBonuses), $this->getOccupiedBonusesSpaces());
        foreach ($bonusToGet as $bonus) {
          $bonuses[] = $incomeBonuses[$bonus]['bonus'];
        }

        // planet INCOME (EFFECT)
        $incomeplanet = $this->planet()->getIncome();
        foreach ($incomeplanet as $bonus) {
          $bonus['source'] = $this->planet()->getName();
          $bonus['income'] = true;
          $bonuses[] = $bonus;
        }
      }

      // INCOME FROM SPONSORS
      $sponsors = $this->getPlayedCards(CARD_SPONSOR);
      foreach ($sponsors as $sId => $sponsor) {
        $income = $sponsor->getIncome() ?? [];
        foreach ($income as $bonus) {
          $bonus['sourceId'] = $sId;
          $bonus['income'] = true;
          $bonuses[] = $bonus;
        }
      }
    } else {
      // MONEY FROM APPEAL
      $bonuses[] = [
        'action' => MONEY_INCOME,
        'pId' => $this->id,
        'args' => ['type' => 'appeal'],
        'source' => clienttranslate('appeal income'),
      ];

      // MONEY FROM KIOSKS
      $bonuses[] = [
        'action' => MONEY_INCOME,
        'pId' => $this->id,
        'args' => ['type' => 'kiosk'],
        'source' => clienttranslate('kiosk income'),
      ];

      // planet INCOME BONUSES
      $incomeBonuses = $this->planet()->getIncomeBonuses();
      $bonusToGet = array_diff(array_keys($incomeBonuses), $this->getOccupiedBonusesSpaces());
      foreach ($bonusToGet as $bonus) {
        $gain = $incomeBonuses[$bonus]['bonus'];
        $gain['income'] = true;
        $bonuses[] = $gain;
      }

      // planet INCOME (EFFECT)
      if ($this->planet()->getIncome() != []) {
        $bonuses[] = [
          'action' => MONEY_INCOME,
          'pId' => $this->id,
          'args' => ['type' => 'planet'],
          'source' => clienttranslate('planet income'),
        ];
      }

      // INCOME FROM SPONSORS
      $sponsors = $this->getPlayedCards(CARD_SPONSOR);
      foreach ($sponsors as $sId => $sponsor) {
        if (!is_null($sponsor->getIncome())) {
          $bonuses[] = [
            'action' => ACTIVATE_CARD,
            'pId' => $this->id,
            'args' => [
              'cardId' => $sId,
              'event' => ['method' => 'getIncome'],
            ],
          ];
        }
      }
    }
    return $bonuses;
  }

  public function getMoneyIncome()
  {
    $bonuses = $this->getIncome();
    $money = 0;
    foreach ($bonuses as $bonus) {
      $money += $bonus[MONEY] ?? 0;
    }
    return $money;
  }

  ////////////////////////////////////////
  //  ____       _   _
  // / ___|  ___| |_| |_ ___ _ __ ___
  // \___ \ / _ \ __| __/ _ \ '__/ __|
  //  ___) |  __/ |_| ||  __/ |  \__ \
  // |____/ \___|\__|\__\___|_|  |___/
  ////////////////////////////////////////
  public function getNewScore()
  {
    $conservationScore = $this->getNewScoreConservation();
    return $this->getAppeal() + $conservationScore;
  }

  public function getNewScoreConservation()
  {
    $conservation = $this->getConservation();
    // 2 slots for up to 10 conservation
    $targetAppeal = 114 - min(10, $conservation) * 2;
    if ($conservation > 10) {
      $targetAppeal -= ($conservation - 10) * 3;
    }
    return 100 - $targetAppeal;
  }

  public function updateScore($endOfGame = false)
  {
    $newScore = $this->getNewScore();
    $score = Globals::isSolo() ? $newScore - 100 : $newScore;
    $this->setScore($score);
    Stats::setAppeal($this, $this->getAppeal());
    Stats::setConservation($this, $this->getConservation());
    Stats::setScore($this, $score);

    // End of game ?
    if ($endOfGame) {
      $this->setScoreAux($this->countSupportedProjects());
      // For solo mode, we need to increase the score by 1, as for BGA 0 is a loss

      // This game was part of the solo challenge
      if (Globals::isSolo() && (Globals::getSoloChallenge() > 0 || Globals::getSoloScore() != -999)) {
        // new setup for solo challenge
        $oldScore = Globals::getSoloScore();

        if ($oldScore == -999) {
          $oldScore = $score;
        } else {
          $oldScore += $score;
        }
        Globals::setSoloScore($oldScore);
      }

      // Solo game that is not a solo challenge
      if (Globals::isSolo() && Globals::getSoloChallenge() == 0) {
        Notifications::message(
          \clienttranslate(
            'Game result notice: In spring 2023, the game scoring has been changed in general and the solo mode scoring in particular was changed from a 0-point success to a 100-point success system. Due to technical restrications, BGA still uses the old 0-point success system for now. Due to this fact 100 points are subtracted from your score at the end of the game and you achieve a victory if you score 0 or more points.'
          )
        );
        if ($score == 0) {
          $score++;
          $this->setScore($score);
          Notifications::message(clienttranslate('Score was increased by 1 to comply with BGA win policy'));
        }
      }

      Notifications::finalScoring(
        $this,
        $score,
        $newScore,
        $this->getAppeal(),
        $this->getConservation(),
        $this->getNewScoreConservation()
      );

      // Last game of a solo challenge
      if (Globals::getSoloChallenge() == 0 && Globals::getSoloScore() != -999) {
        // last challenge so we need to display the total score
        $oldScore = Globals::getSoloScore() + $score;
        if ($oldScore == 0) {
          $this->setScore(1);
          Notifications::message(clienttranslate('Score was increased by 1 to comply with BGA win policy'));
        }

        $this->setScore($oldScore);
        if ($oldScore >= 0) {
          Notifications::message('Solo challenge: after 3 games, you won with a total score of ${score}', ['score' => $oldScore]);
        } else {
          Notifications::message('Solo challenge: after 3 games, you lost with a total score of ${score}', [
            'score' => $oldScore,
          ]);
        }
      }
    }
    return $newScore;
  }

  public function pay($n, $notif = true, $source = null)
  {
    if ($this->money < $n) {
      throw new \BgaVisibleSystemException('You don\'t have enough money to pay. Should not happen');
    }

    parent::incMoney(-$n);
    if ($notif) {
      Notifications::payMoney($this, $n, $this->money, $source);
    }

    return $this->money;
  }

  public function payXToken($n, $notif = true, $source = null)
  {
    if ($this->xToken < $n) {
      throw new \BgaVisibleSystemException('You don\'t have enough xtoken to pay. Should not happen');
    }

    parent::incXToken(-$n);
    if ($notif) {
      Notifications::payXToken($this, $n, $this->xToken, $source);
    }
    Stats::incXTokenUsed($this->id, $n);

    return $this->xToken;
  }

  public function incMoney($n, $notif = true, $source = null)
  {
    if ($n == 0) {
      return $this->money;
    }

    parent::incMoney($n);
    if ($notif) {
      Notifications::incMoney($this, $n, $this->money, $source);
    }
    Stats::incMoneyGained($this->id, $n);

    return [];
  }

  public function incReputation($n, $notif = true, $source = null)
  {
    $previousRep = $this->reputation;

    // Max rep of 9 is not upgraded
    if (!$this->isCardUpgraded(CARDS) && $this->reputation + $n > 9) {
      $n = 9 - $this->reputation;
      Notifications::message(clienttranslate('${player_name} cannot exceed 9 reputation'), ['player' => $this]);
    }
    if ($n == 0) {
      return [];
    }

    // Check max rep of 15
    if ($this->reputation + $n > 15) {
      $appealGain = $n + $this->reputation - 15;
      $n = 15 - $this->reputation;
      self::incAppeal($appealGain, true, clienttranslate('maxing out reputation'));
    }

    parent::incReputation($n);
    Stats::incReputation($this->id, $n);
    if ($notif) {
      Notifications::incReputation($this, $n, $this->reputation, $source = null);
    }

    // bonus if you reach specific places
    $bonusplanet = [
      5 => [BONUS_UPGRADE_CARD => 1],
      8 => [BONUS_WORKER => 1],
      10 => [TAKE_IN_RANGE => 1],
      11 => [CONSERVATION => 1],
      12 => [XTOKEN => 1],
      13 => [TAKE_IN_RANGE => 1],
      14 => [CONSERVATION => 1],
      15 => [XTOKEN => 1],
    ];
    $bonuses = [];
    for ($i = $previousRep + 1; $i <= $this->reputation; $i++) {
      if (isset($bonusplanet[$i])) {
        $bonuses[] = $bonusplanet[$i];
      }
    }
    return $bonuses;
  }

  public function incAppeal($n, $notif = true, $source = null)
  {
    // Check max appeal of 113
    if ($this->appeal + $n > 113) {
      $n = 113 - $this->appeal;
      Notifications::message(clienttranslate('${player_name} cannot have more than 113 appeal'), ['player' => $this]);
    }
    if ($n == 0) {
      return [];
    }

    parent::incAppeal($n);
    if ($notif) {
      Notifications::incAppeal($this, $n, $this->appeal, $source);
    }

    return [];
  }

  public function incConservation($n, $notif = true, $source = null)
  {
    $previousConservation = $this->conservation;
    // Check max conservation of 41
    if ($this->conservation + $n > 41) {
      $n = 41 - $this->conservation;
      Notifications::message(clienttranslate('${player_name} cannot have more than 41 conservation'), ['player' => $this]);
    }
    if ($n == 0) {
      return [];
    }

    parent::incConservation($n);
    if ($notif) {
      Notifications::incConservation($this, $n, $this->conservation, $source);
    }

    // BONUSES
    // No need of this if already more than 10
    $childs = [];
    if ($previousConservation < 10) {
      $bonusplanet = Globals::getBonusTiles();
      for ($i = $previousConservation + 1; $i <= $this->conservation; $i++) {
        $node = FlowConvertor::getConservationBonusesXORNode($i);
        if (!is_null($node)) {
          $childs[] = $node;
        }
      }
    }

    return $childs;
  }

  public function incXToken($n, $notif = true, $source = null)
  {
    if ($this->xToken + $n > 5) {
      $n = 5 - $this->xToken;
      Notifications::message(clienttranslate('${player_name} cannot have more than 5 x-tokens'), ['player' => $this]);
    }
    if ($n == 0) {
      return [];
    }

    parent::incXToken($n);
    if ($notif) {
      Notifications::incXToken($this, $n, $this->xToken, $source);
    }
    Stats::incXTokenGained($this->id, $n);

    return [];
  }

  ////////////////////////////////////////////////////////////////
  //     _        _   _                ____              _
  //    / \   ___| |_(_) ___  _ __    / ___|__ _ _ __ __| |___
  //   / _ \ / __| __| |/ _ \| '_ \  | |   / _` | '__/ _` / __|
  //  / ___ \ (__| |_| | (_) | | | | | |__| (_| | | | (_| \__ \
  // /_/   \_\___|\__|_|\___/|_| |_|  \____\__,_|_|  \__,_|___/
  ////////////////////////////////////////////////////////////////
  public function getActionCards()
  {
    return ActionCards::getOfPlayer($this->id);
  }

  public function getActionCardInPosition($position)
  {
    return ActionCards::getInPosition($this->id, $position);
  }

  public function getActionCardOfType($type)
  {
    return $this->getActionCards()
      ->filter(function ($card) use ($type) {
        return $card->getType() == $type;
      })
      ->first();
  }

  public function getActionCardInUse()
  {
    foreach (self::getActionCards() as $cId => $card) {
      if ($card->getStatus() === 1) {
        return $card;
      }
    }
    return null;
  }

  public function countTokensOnCards($type)
  {
    return Meeples::countTokensOnCards($this->id, $type);
  }

  public function moveActionCard($type, $position = 1)
  {
    $oCard = $this->getActionCardOfType($type);
    $initialPosition = $oCard->getStrength();
    // move all others cards on the right
    foreach ($this->getActionCards() as $cId => $card) {
      $loc = $card->getStrength();
      if ($position == 1 && $loc < $initialPosition) {
        $card->setStrength($loc + 1);
      } elseif ($position == 5 && $loc > $initialPosition) {
        $card->setStrength($loc - 1);
      }
    }
    $oCard->setStrength($position);
    return $this->getActionCards();
  }

  ///////////////////////////////////////////////////
  //  _____              ____              _
  // |__  /___   ___    / ___|__ _ _ __ __| |___
  //   / // _ \ / _ \  | |   / _` | '__/ _` / __|
  //  / /| (_) | (_) | | |__| (_| | | | (_| \__ \
  // /____\___/ \___/   \____\__,_|_|  \__,_|___/
  ///////////////////////////////////////////////////
  public function getHand($type = null)
  {
    return ZooCards::getHand($this->id)->filter(function ($card) use ($type) {
      return is_null($type) || $card->getType() == $type;
    });
  }

  public function getHandLimit()
  {
    return self::hasUniversity(\UNIVERSITY_REP_HAND) ? 5 : 3;
  }

  public function getScoringHand()
  {
    return ZooCards::getScoringHand($this->id);
  }

  public function getPlayedCards($type = null)
  {
    return ZooCards::getPlayedCards($this->id, $type);
  }

  public function getPlayedAnimal($icon = null)
  {
    $animals = $this->getPlayedCards(\CARD_ANIMAL);
    if (!is_null($icon)) {
      $animals = $animals->filter(function ($animal) use ($icon) {
        return ($animal->getIcons()[$icon] ?? 0) > 0;
      });
    }
    return $animals;
  }

  // Useful for flocking
  public function getBiggestHerbivore()
  {
    $n = 0;
    foreach ($this->getPlayedAnimal(\HERBIVORE) as $animal) {
      $n = max($n, $animal->getEnclosureSize());
    }
    return $n;
  }

  public function hasPlayedCard($id)
  {
    return Zoocards::hasPlayedCard($this->id, $id);
  }

  public function getMaxFolderInRange()
  {
    $reputationplanet = [
      1 => 1,
      2 => 2,
      3 => 2,
      4 => 3,
      5 => 3,
      6 => 3,
      7 => 4,
      8 => 4,
      9 => 4,
      10 => 5,
      11 => 5,
      12 => 5,
    ];
    $maxFolder = $reputationplanet[$this->getReputation()] ?? 6;
    return $maxFolder;
  }

  public function getCardsInReputationRange($type = null)
  {
    $maxFolder = $this->getMaxFolderInRange();
    return ZooCards::getPool($maxFolder)->filter(function ($card) use ($type) {
      return is_null($type) || $card->getType() == $type;
    });
  }

  public function countCardIcon($icon)
  {
    $icons = $this->countCardIcons();
    return $icons[$icon] ?? 0;
  }

  public function countCardIcons($onlyNonZero = false, $toKeep = null)
  {
    // get played animals
    $icons = [];

    foreach (ALL_PREREQUISITES as $type) {
      $icons[$type] = 0;
    }

    $cards = $this->getPlayedCards();
    foreach ($cards as $aId => $card) {
      foreach ($card->getIcons() as $type => $n) {
        $icons[$type] += $n;
      }
    }

    if ($this->hasUniversity(UNIVERSITY_SCIENCE_REP)) {
      $icons[SCIENCE]++;
    }

    if ($this->hasUniversity(UNIVERSITY_SCIENCE_SCIENCE)) {
      $icons[SCIENCE] += 2;
    }

    foreach ($this->getPartnerZoos() as $mId => $partner) {
      $continent = explode('-', $partner['type'])[1];
      $icons[$continent]++;
    }
    // TODO : manage planet specific

    if (!is_null($toKeep)) {
      foreach (array_keys($icons) as $type) {
        if (!in_array($type, $toKeep)) {
          unset($icons[$type]);
        }
      }
    }

    if ($onlyNonZero) {
      foreach (array_keys($icons) as $type) {
        if ($icons[$type] == 0) {
          unset($icons[$type]);
        }
      }
    }

    // Update stats
    if (!$onlyNonZero && is_null($toKeep)) {
      foreach (ALL_PREREQUISITES as $type) {
        if (!in_array($type, CONTINENTS_AND_TYPES) && !in_array($type, [WATER, ROCK, SCIENCE])) {
          continue;
        }

        $val = $icons[$type];
        $statName = 'getIcon' . $type;
        if (Stats::$statName($this) != $val) {
          $statName = 'setIcon' . $type;
          Stats::$statName($this, $val);
        }
      }
    }

    return $icons;
  }

  ////////////////////////////////////////////////////////////
  //     _                       _       _   _
  //    / \   ___ ___  ___   ___(_) __ _| |_(_) ___  _ __
  //   / _ \ / __/ __|/ _ \ / __| |/ _` | __| |/ _ \| '_ \
  //  / ___ \\__ \__ \ (_) | (__| | (_| | |_| | (_) | | | |
  // /_/   \_\___/___/\___/ \___|_|\__,_|\__|_|\___/|_| |_|
  ////////////////////////////////////////////////////////////

  /***********
   * WORKERS *
   ***********/

  public function hasAvailableWorkers()
  {
    return Meeples::hasAvailableWorkers($this->id);
  }

  public function getAvailableWorker()
  {
    return Meeples::getAvailableWorker($this->id);
  }

  public function countAvailableWorkers()
  {
    return count(Meeples::getAvailableWorkers($this->id));
  }

  public function getNextWorkerInSupply()
  {
    return Meeples::getNextWorkerInSupply($this->id);
  }

  public function countWorkersInSlot($slot)
  {
    return Meeples::countWorkersInSlot($this->id, $slot);
  }

  /**
   * useWorkers: move the given number of worker into $location
   */
  public function useWorkers($nb, $location)
  {
    $moved = [];
    for ($i = 0; $i < $nb; $i++) {
      $id = $this->getAvailableWorker()['id'];
      Meeples::move($id, $location);
      $moved[] = $id;
    }
    return $nb == 0 ? [] : Meeples::getMany($moved);
  }

  /**
   * gainWorker : take 1 worker from the supply and move it to the reserve
   */
  public function gainWorker()
  {
    $new = self::getNextWorkerInSupply();
    if (is_null($new)) {
      return;
    }

    // Notify new worker
    Meeples::move($new['id'], 'reserve');
    Notifications::gainWorker($this, Meeples::get($new['id']));
    Stats::incAssociationWorkers($this->id);

    // planet bonus for last worker
    $bonus = $this->planet()->getLastWorkerBonus();
    $isLastWorker = is_null(self::getNextWorkerInSupply());
    if (!is_null($bonus) && $isLastWorker) {
      return [$bonus];
    } else {
      return [];
    }
  }

  /***************
   * PARTNER ZOO *
   ***************/

  public function hasPartnerZoo($continent = null)
  {
    return Meeples::hasPartnerZoo($this->id, $continent);
  }

  public function countPartnerZoo()
  {
    return Meeples::countPartnerZoo($this->id);
  }

  public function getPartnerZoos()
  {
    return Meeples::getPartnerZoos($this->id);
  }

  public function addPartnerZoo($meepleId)
  {
    $index = $this->countPartnerZoo() + 1;
    $meeple = Meeples::moveZoo($meepleId, $this->id, $index);
    Notifications::addPartnerZoo($this, $meeple);

    $bonuses = [];
    $possibleBonuses = $this->planet()->getPartnerZooBonuses();
    if (isset($possibleBonuses[$index])) {
      $bonuses[] = $possibleBonuses[$index];
    }

    $continent = explode('-', $meeple['type'])[1];
    $icons = [$continent => 1];
    $bonuses = array_merge($bonuses, ZooCards::getIconsReaction($icons, $this));

    return $bonuses;
  }

  /****************
   * UNIVERSITIES *
   ****************/

  public function hasUniversity($type)
  {
    return Meeples::hasUniversity($this->id, $type);
  }

  public function countUniversities()
  {
    return Meeples::countUniversities($this->id);
  }

  public function addUniversity($meepleId)
  {
    $index = $this->countUniversities() + 1;
    $meeple = Meeples::moveUniversity($meepleId, $this->id, $index);
    Notifications::addUniversity($this, $meeple);

    $bonuses = [];
    $possibleBonuses = $this->planet()->getFacBonuses();
    if (isset($possibleBonuses[$index])) {
      $bonuses[] = $possibleBonuses[$index];
    }

    // Reputation gain from university
    $repGains = [
      \UNIVERSITY_REP_HAND => 1,
      \UNIVERSITY_SCIENCE_REP => 2,
    ];
    $repGain = $repGains[$meeple['type']] ?? 0;
    if ($repGain > 0) {
      $bonuses[] = [
        REPUTATION => $repGain,
        'source' => clienttranslate(' from university'),
      ];
    }

    // Science icons
    $scienceIcons = [
      \UNIVERSITY_SCIENCE_REP => [SCIENCE => 1],
      \UNIVERSITY_SCIENCE_SCIENCE => [SCIENCE => 2],
    ];
    $icons = $scienceIcons[$meeple['type']] ?? [];
    $bonuses = array_merge($bonuses, ZooCards::getIconsReaction($icons, $this));

    return $bonuses;
  }

  /************
   * PROJECTS *
   ************/

  /**
   * getOccupiedBonusesSpaces : return the list of zoo planet bonus spaces with meeples on it
   *  => these are the ones available when suppporting a new conservation project
   */
  public function getOccupiedBonusesSpaces()
  {
    return Meeples::getOccupiedBonusesSpaces($this->id);
  }

  /**
   * countCardTokens : return the number of tokens/cubes on a card
   *  => make sure a player cant support a project twice
   */
  public function countCardTokens($cardId)
  {
    return count(Meeples::getTokensOnCard($this->id, $cardId));
  }

  /**
   * countSupportedProjects: return how many conservation project the player supported
   */
  public function countSupportedProjects()
  {
    $incomeBonuses = $this->planet()->getBonusSpaces();
    $alreadySupported = array_diff(array_keys($incomeBonuses), $this->getOccupiedBonusesSpaces());
    return count($alreadySupported);
  }

  /**
   * getIconBonusForBaseProjects: return an array of tokens on sponsor card that can be used to reduce base project "cost"
   */
  public function getIconBonusForBaseProjects()
  {
    $bonus = 0;
    foreach (SPONSOR_CARD_WITH_ICON_BONUS as $cId) {
      if (!$this->hasPlayedCard($cId)) {
        continue;
      }

      $card = ZooCards::getSingle($cId);
      if (!$card->getTokensOnIt()->empty()) {
        $bonus++;
      }
    }

    return $bonus;
  }

  /**
   * useReductionToken: find a token and use it (take the card with most token on it)
   */
  public function useReductionToken()
  {
    $tokens = [];
    foreach (SPONSOR_CARD_WITH_ICON_BONUS as $cId) {
      if (!$this->hasPlayedCard($cId)) {
        continue;
      }

      $card = ZooCards::getSingle($cId);
      $meeples = $card->getTokensOnIt();
      if (count($meeples) > count($tokens)) {
        $tokens = $meeples;
      }
    }

    if (empty($tokens)) {
      throw new \BgaVisibleSystemException('Dont have any token left for base project icon bonus. Should not happen');
    }

    $token = $tokens->first();
    Meeples::destroy($token['id']);
    return $token;
  }
}
