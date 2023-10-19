/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Planet Unknown implementation : © Timothée Pecatte <tim.pecatte@gmail.com>, Emmanuel Albisser <emmanuel.albisser@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * planetunknown.js
 *
 * Planet Unknown user interface script
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

var isDebug = window.location.host == 'studio.boardgamearena.com' || window.location.hash.indexOf('debug') > -1;
var debug = isDebug ? console.info.bind(window.console) : function () {};

define([
  'dojo',
  'dojo/_base/declare',
  'ebg/core/gamegui',
  'ebg/counter',
  g_gamethemeurl + 'modules/js/Core/game.js',
  g_gamethemeurl + 'modules/js/Core/modal.js',
  g_gamethemeurl + 'modules/js/Players.js',
  g_gamethemeurl + 'modules/js/Meeples.js',
  g_gamethemeurl + 'modules/js/Cards.js',
], function (dojo, declare) {
  const CIV = 'civ';
  const WATER = 'water';
  const ROVER = 'rover';
  const TECH = 'tech';
  const ENERGY = 'energy';
  const BIOMASS = 'biomass';

  const ALL_TYPES = [CIV, WATER, BIOMASS, ROVER, TECH];

  const FLUX = 2;

  return declare('bgagame.planetunknown', [customgame.game, planetunknown.players, planetunknown.meeples, planetunknown.cards], {
    constructor() {
      this._activeStates = ['chooseRotation'];
      this._notifications = [
        ['chooseSetup', 200],
        ['confirmSetupObjectives', 1200],
        ['clearTurn', 200],
        ['refreshUI', 200],
        ['setupPlayer', 1200],
        ['placeTile', null],
        ['moveTrack', null],
        ['slideMeeple', null],
        ['slideMeeples', null],
        ['newRotation', 1400],
        ['endOfTurn', 100],
        ['destroyedMeeples', null],
        ['receiveBiomassPatch', null],
        ['takeCivCard', 1400],
        ['changeFirstPlayer', 1400],
        ['endOfGameTriggered', 1400],
        ['revealCards', 1400],
        ['newEventCard', 3500],
        ['chooseFluxTrack', null],
        ['midMessage', 1200],
      ];

      // Fix mobile viewport (remove CSS zoom)
      this.default_viewport = 'width=740';
      this.cardStatuses = {};
    },
    notif_midMessage(n) {},

    getSettingsSections() {
      return {
        layout: _('Layout'),
        playerBoard: _('Player Board/Panel'),
        gameFlow: _('Game Flow'),
        other: _('Other'),
      };
    },

    getSettingsConfig() {
      return {
        ////////////////////
        ///    LAYOUT    ///
        playerBoardsLayout: {
          default: 0,
          name: _('Player boards layout'),
          attribute: 'player-boards-layout',
          type: 'select',
          values: {
            0: _('Individual view (tabbed layout)'),
            1: _('Multiple view'),
          },
          section: 'layout',
        },

        boardSizes: {
          default: 100,
          name: _('Board size'),
          type: 'slider',
          sliderConfig: {
            step: 3,
            padding: 0,
            range: {
              min: [30],
              max: [100],
            },
          },
          section: 'layout',
        },

        //////////////////////
        /// BOARD / PANELS ///

        //////////////////////
        ///// GAME FLOW //////
        confirmMode: { type: 'pref', prefId: 103, section: 'gameFlow' },
        confirmUndoableMode: {
          type: 'pref',
          prefId: 104,
          section: 'gameFlow',
        },
        restartButtons: {
          default: 1,
          name: _('Restart turn buttons'),
          type: 'select',
          attribute: 'undoButtons',
          values: {
            0: _('Only "Restart turn" button'),
            1: _('"Restart turn" and "Undo last step" buttons'),
            2: _('Only "Undo last step" button'),
          },
          section: 'gameFlow',
        },

        //////////////////////
        /////// OTHER ////////
      };
    },

    isFloatingHand() {
      return [0, 3].includes(parseInt(this.settings.handLocation));
    },

    openHand() {
      if (this.isFloatingHand()) {
        $('floating-hand-wrapper').dataset.open = 'hand';
      }
    },

    openScoringHand() {
      if (this.isFloatingHand()) {
        $('floating-hand-wrapper').dataset.open = 'scoringHand';
      }
    },

    /**
     * Setup:
     *	This method set up the game user interface according to current game situation specified in parameters
     *	The method is called each time the game interface is displayed to a player, ie: when the game starts and when a player refreshes the game page (F5)
     *
     * Params :
     *	- mixed gamedatas : contains all datas retrieved by the getAllDatas PHP method.
     */
    setup(gamedatas) {
      debug('SETUP', gamedatas);
      // Create a new div for "anytime" buttons
      dojo.place("<div id='anytimeActions' style='display:inline-block;float:right'></div>", $('generalactions'), 'after');
      // Create a new div for "subtitle"
      dojo.place("<div id='pagesubtitle'></div>", 'maintitlebar_content');

      // Attribute to know what asset we are using for max appeal
      $('ebd-body').dataset.startingAppeal = gamedatas.startingAppeal;
      this.setupInfoPanel();

      this.setupScoresModal();
      this.setupPlayers();
      this.setupCards();
      this.setupPlayersScores();
      this.setupTiles();
      this.setupMeeples();
      this.updateLastRoundBanner();
      this.updateSusanCounters();
      // this.setupTour();
      this.inherited(arguments);
    },

    onLoadingComplete() {
      this.updateLayout();
      this.inherited(arguments);
    },

    onScreenWidthChange() {
      if (this.settings) this.updateLayout();
    },

    onAddingNewUndoableStepToLog(notif) {
      if (!$(`log_${notif.logId}`)) return;
      let stepId = notif.msg.args.stepId;
      $(`log_${notif.logId}`).dataset.step = stepId;
      if ($(`dockedlog_${notif.mobileLogId}`)) $(`dockedlog_${notif.mobileLogId}`).dataset.step = stepId;

      if (this.gamedatas && this.gamedatas.gamestate) {
        let state = this.gamedatas.gamestate;
        if (state.private_state) state = state.private_state;

        if (state.args && state.args.previousSteps && state.args.previousSteps.includes(parseInt(stepId))) {
          this.onClick($(`log_${notif.logId}`), () => this.undoToStep(stepId));

          if ($(`dockedlog_${notif.mobileLogId}`))
            this.onClick($(`dockedlog_${notif.mobileLogId}`), () => this.undoToStep(stepId));
        }
      }
    },

    undoToStep(stepId) {
      this.stopActionTimer();
      this.checkAction('actRestart');
      this.takeAction('actUndoToStep', { stepId }, false);
    },

    notif_clearTurn(n) {
      debug('Notif: restarting turn', n);
      this.cancelLogs(n.args.notifIds);
    },

    notif_refreshUI(n) {
      debug('Notif: refreshing UI', n);
      this.clearPossible();
      ['cards', 'meeples', 'players', 'tiles'].forEach((value) => {
        this.gamedatas[value] = n.args.datas[value];
      });
      this.setupMeeples();
      this.setupTiles();
      this.updatePlayersScores();
      this.rotateSusan();
      this.updateSusanCounters();
      this.updatePlayersCounters();
      this.updateHand();
      this.updateCivCounters();

      // this.forEachPlayer((player) => {
      //   this._scoreCounters[player.id].toValue(player.newScore);
      //   this._playerCounters[player.id]['income'].toValue(player.income);
      // });
    },

    notif_endOfGameTriggered() {
      debug('Notif: end of game triggered');
      this.gamedatas.endOfGameTriggered = true;
      this.updateLastRoundBanner();
    },

    onEnteringStateGameEnd(args) {
      if ($('last-round')) $('last-round').remove();
    },

    updateLastRoundBanner() {
      if (this.gamedatas.endOfGameTriggered) {
        if (!$('last-round')) {
          $('page-title').insertAdjacentHTML(
            'beforeend',
            `<div id="last-round">${_('This is the last round of the game!')}</div>`
          );
        }
      } else {
        if ($('last-round')) {
          $('last-round').remove();
        }
      }
    },

    onUpdateActionButtons(stateName, args) {
      //        this.addPrimaryActionButton('test', 'test', () => this.testNotif());
      this.inherited(arguments);
    },

    testNotif() {},

    clearPossible() {
      dojo.empty('pagesubtitle');
      this.onHoverCell = null;
      this.onClickCell = null;

      let toRemove = ['tile-controls', 'tile-hover', 'btnRotateClockwise', 'btnRotateCClockwise', 'btnFlip'];
      toRemove.forEach((eltId) => {
        if ($(eltId)) $(eltId).remove();
      });

      if (this._chooseCardModal) this._chooseCardModal.destroy();
      this._susanModal.hide();
      $('susan-modal-footer').classList.remove('active');

      this.inherited(arguments);
    },

    onEnteringState(stateName, args) {
      debug('Entering state: ' + stateName, args);
      if (this.isFastMode() && ![].includes(stateName)) return;

      if (this._focusedPlayer != null && this._focusedPlayer != this.player_id && !this.isSpectator) {
        this.goToPlayerBoard(this.player_id);
      }

      if (args.args && args.args.descSuffix) {
        this.changePageTitle(args.args.descSuffix);
      }

      if (args.args && args.args.optionalAction) {
        let base = args.args.descSuffix ? args.args.descSuffix : '';
        this.changePageTitle(base + 'skippable');
      }

      if (this._activeStates.includes(stateName) && !this.isCurrentPlayerActive()) return;

      if (args.args && args.args.optionalAction && !args.args.automaticAction) {
        this.addSecondaryActionButton(
          'btnPassAction',
          _('Pass'),
          () => this.takeAction('actPassOptionalAction'),
          'restartAction'
        );
      }

      // Undo last steps
      if (args.args && args.args.previousSteps) {
        args.args.previousSteps.forEach((stepId) => {
          let logEntry = $('logs').querySelector(`.log.notif_newUndoableStep[data-step="${stepId}"]`);
          if (logEntry) this.onClick(logEntry, () => this.undoToStep(stepId));

          logEntry = document.querySelector(`.chatwindowlogs_zone .log.notif_newUndoableStep[data-step="${stepId}"]`);
          if (logEntry) this.onClick(logEntry, () => this.undoToStep(stepId));
        });
      }

      // Restart turn button
      if (args.args && args.args.previousEngineChoices && args.args.previousEngineChoices >= 1 && !args.args.automaticAction) {
        if (args.args && args.args.previousSteps) {
          let lastStep = Math.max(...args.args.previousSteps);
          if (lastStep > 0)
            this.addDangerActionButton('btnUndoLastStep', _('Undo last step'), () => this.undoToStep(lastStep), 'restartAction');
        }

        // Restart whole turn
        this.addDangerActionButton(
          'btnRestartTurn',
          _('Restart turn'),
          () => {
            this.stopActionTimer();
            this.takeAction('actRestart');
          },
          'restartAction'
        );
      }

      if (this.isCurrentPlayerActive() && args.args) {
        // Anytime buttons
        if (args.args.anytimeActions) {
          args.args.anytimeActions.forEach((action, i) => {
            let msg = action.desc;
            msg = msg.log ? this.fsr(msg.log, msg.args) : _(msg);
            msg = this.formatString(msg);

            // if (action.source && action.source != '') {
            //   msg += ' (' + _(action.source) + ')';
            // }

            this.addPrimaryActionButton(
              'btnAnytimeAction' + i,
              msg,
              () => this.takeAction('actAnytimeAction', { id: i }, false),
              'anytimeActions'
            );
          });
        }
      }

      // Call appropriate method
      var methodName = 'onEnteringState' + stateName.charAt(0).toUpperCase() + stateName.slice(1);
      if (this[methodName] !== undefined) this[methodName](args.args);
    },

    //////////////////////////////
    //  ____  _             _
    // / ___|| |_ __ _ _ __| |_
    // \___ \| __/ _` | '__| __|
    //  ___) | || (_| | |  | |_
    // |____/ \__\__,_|_|   \__|
    //////////////////////////////

    onEnteringStateChooseSetup(args) {
      if (!args._private) return;
      let selectedPlanet = null;
      let selectedCorpo = null;
      let selectedObj = null;
      let selectedFlux = null;
      let possibleObjs = Object.values(args._private.POCards);

      // Display button only if all choices are made
      let updateSelection = () => {
        let canConfirm = false;

        if (args._private.choice != undefined) {
          let choice = args._private.choice;
          canConfirm =
            selectedPlanet != choice.planetId ||
            selectedCorpo != choice.corporationId ||
            selectedObj != choice.rejectedCardId ||
            selectedFlux != choice.flux;
        } else {
          canConfirm = selectedPlanet != null && selectedCorpo != null && (selectedObj != null || possibleObjs.length == 0);
          debug(selectedFlux, selectedFlux === null);
          if (selectedCorpo == FLUX && selectedFlux === null) canConfirm = false;
        }

        if (canConfirm) {
          // Add confirm button (only if choice is different from potential existing selection)
          this.addPrimaryActionButton('btnConfirmChoice', _('Confirm'), () =>
            this.takeAction(
              'actChooseSetup',
              { planetId: selectedPlanet, corporationId: selectedCorpo, rejectedCardId: selectedObj, flux: selectedFlux },
              false
            )
          );
        } else if ($('btnConfirmChoice')) {
          $('btnConfirmChoice').remove();
        }
      };

      // PLANET
      let selectPlanet = (planetId) => {
        if (selectedPlanet !== null && selectedPlanet == planetId) {
          $('pagesubtitle').innerHTML = this.formatString(_(PLANETS_DATA[planetId].desc));
          return;
        }

        let container = $(`player-board-planet-${this.player_id}`);
        let previousPlanet = container.querySelector('.planet');
        if (previousPlanet) previousPlanet.remove();
        container.insertAdjacentHTML('beforeend', this.tplPlanet(PLANETS_DATA[planetId], { id: this.player_id }));
        $('pagesubtitle').innerHTML = this.formatString(_(PLANETS_DATA[planetId].desc));
        this.attachRegisteredTooltips();

        // Highlight button
        if (selectedPlanet !== null) {
          $(`selectPlanet${selectedPlanet}`).classList.remove('selected');
        }
        selectedPlanet = planetId;
        $(`selectPlanet${selectedPlanet}`).classList.add('selected');
        updateSelection();
      };

      let possiblePlanets = args._private.planets;
      possiblePlanets.forEach((planetId) => {
        this.addPrimaryActionButton(`selectPlanet${planetId}`, _(PLANETS_DATA[planetId].name), () => selectPlanet(planetId));
      });

      // Already made a selection => allow to change its mind
      if (args._private.choice != null) {
        selectPlanet(args._private.choice.planetId);
      }
      // No selection yet => let the user click on any
      else {
        selectPlanet(args._private.planets[0]);
      }

      $('customActions').insertAdjacentHTML('beforeend', '<div class="separator">|</div>');

      // CORPO
      let selectFlux = (type) => {
        // Highlight button
        if (selectedFlux !== null) {
          $(`btn${selectedFlux}`).classList.remove('selected');
        }
        selectedFlux = type;
        $(`btn${selectedFlux}`).classList.add('selected');
        updateSelection();
      };
      let selectCorpo = (corpoId) => {
        if (selectedCorpo !== null && selectedCorpo == corpoId) {
          $('pagesubtitle').innerHTML = this.formatString(_(CORPOS_DATA[corpoId].desc));
          return;
        }
        let container = $(`player-board-corporation-${this.player_id}`);
        let corpo = container.querySelector('.corporation');
        corpo.dataset.id = corpoId;
        $('pagesubtitle').innerHTML = this.formatString(_(CORPOS_DATA[corpoId].desc));
        this.attachRegisteredTooltips();

        /////////////
        // FLUX
        if (corpoId == FLUX && !$('flux-selection')) {
          $('customActions').insertAdjacentHTML('beforeend', `<div id="flux-selection">${this.formatIcon('flux')} : </div>`);

          ALL_TYPES.forEach((type) => {
            this.addSecondaryActionButton(
              'btn' + type,
              this.fsr('${type}', { type, type_name: type }),
              () => selectFlux(type),
              'flux-selection'
            );
          });
          $('flux-selection').insertAdjacentHTML('beforeend', '<div class="separator">|</div>');
        }
        if (corpoId != FLUX && $('flux-selection')) {
          $('flux-selection').remove();
        }
        /////////////

        // Highlight button
        if (selectedCorpo !== null) {
          $(`selectCorpo${selectedCorpo}`).classList.remove('selected');
        }
        selectedCorpo = corpoId;
        $(`selectCorpo${selectedCorpo}`).classList.add('selected');
        updateSelection();
      };

      let possibleCorpos = args._private.corporations;
      possibleCorpos.forEach((corpoId) => {
        this.addPrimaryActionButton(`selectCorpo${corpoId}`, _(CORPOS_DATA[corpoId].name), () => selectCorpo(corpoId));
      });

      $('customActions').insertAdjacentHTML('beforeend', '<div class="separator">|</div>');

      // Already made a selection => allow to change its mind
      if (args._private.choice != null) {
        selectCorpo(args._private.choice.corporationId);
        if (args._private.choice.flux) selectFlux(args._private.choice.flux);
      }
      // No selection yet => let the user click on any
      else {
        selectCorpo(args._private.corporations[0]);
      }

      // OBJECTIVES
      let selectObj = (objId) => {
        if (selectedObj !== null) {
          $(`card-${selectedObj}`).classList.remove('selected', 'selectedToDiscard');
        }
        selectedObj = objId;
        $(`card-${selectedObj}`).classList.add('selected', 'selectedToDiscard');
        updateSelection();
      };

      possibleObjs.forEach((card) => {
        card.pId = this.player_id;
        this.addCard(card);
        this.onClick(`card-${card.id}`, () => selectObj(card.id));
      });

      // Already made a selection => allow to change its mind
      if (args._private.choice != null && args._private.choice.rejectedCardId != null) {
        selectObj(args._private.choice.rejectedCardId);
      }
    },

    notif_chooseSetup(n) {
      this.clearPossible();
      this.updatePageTitle();
      this.onEnteringStateChooseSetup(n.args.args);
    },

    notif_confirmSetupObjectives(n) {
      debug('Notif: confirming objectives at setup', n);
      n.args.cardIds.forEach((cardId) => {
        this.slide(`card-${cardId}`, `private-objectives-${this.player_id}`);
      });
      [...$('pending-cards').querySelectorAll('.pocard-wrapper')].forEach((elt) => {
        let id = parseInt(elt.id.split('-')[1]);
        if (n.args.cardIds.includes(id)) return;

        this.slide(elt, this.getVisibleTitleContainer(), {
          destroy: true,
        });
      });
    },

    notif_setupPlayer(n) {
      debug('Notif: finish setup of player', n);

      let player = this.gamedatas.players[n.args.player_id];
      if (this._focusedPlayer != null && this._focusedPlayer != player.id) {
        this.goToPlayerBoard(player.id);
      }

      // Planet
      let container = $(`player-board-planet-${player.id}`);
      let previousPlanet = container.querySelector('.planet');
      if (previousPlanet) previousPlanet.remove();
      container.insertAdjacentHTML('beforeend', this.tplPlanet(PLANETS_DATA[n.args.planetId], player));

      // Corpo
      container = $(`player-board-corporation-${player.id}`);
      let corpo = container.querySelector('.corporation');
      corpo.dataset.id = n.args.corpoId;

      // Meeples
      n.args.meeples.forEach((meeple) => this.addMeeple(meeple));

      // // Action Cards
      // player.actionCards = n.args.action_cards;
      // this.updateActionCards();

      // player.planetId = n.args.planetId;
      // $(`icons-summary-map-${player.id}`).insertAdjacentHTML('afterend', this.tplZooPlanet(MAPS_DATA[player.planetId], player));
      // this.activateShowTileHelperButtons();
      // this.setupChangeBoardArrows(player.id);

      // // Meeples
      // n.args.meeples.forEach((meeple) => this.addMeeple(meeple));

      // // Tiles (for map A)
      // n.args.tiles.forEach((tile) => this.addTile(tile));

      // // Worker counter
      // this._playerCounters[player.id]['worker'] = this.createCounter(`counter-${player.id}-worker`, 0);
      // this.updateWorkerCounters();
    },

    onEnteringStateChooseRotation(args) {
      if (this.getPlayers().length < 3) {
        return;
      }

      this.addPrimaryActionButton('btnZoomIn', _('Zoom in on S.U.S.A.N.'), () => this._susanModal.show());
      this._susanModal.show();

      // Enable buttons in modal
      $('susan-modal-footer').classList.add('active');
      this.onClick('susan-rotate-cclockwise', () => {
        this.gamedatas.susan.rotation++;
        this.rotateSusan();
      });
      this.onClick('susan-rotate-clockwise', () => {
        this.gamedatas.susan.rotation--;
        this.rotateSusan();
      });

      this.onClick('btnConfirmSusanRotation', () => {
        this._susanModal.hide();
        this.takeAction('actChooseRotation', { rotation: this.gamedatas.susan.rotation });
      });

      // Add buttons in top bar
      this.addSecondaryActionButton('btnSusanRotateCclockwise', '<svg><use href="#rotate-cclockwise-svg" /></svg>', () => {
        this.gamedatas.susan.rotation++;
        this.rotateSusan();
      });
      this.addPrimaryActionButton('btnSusanConfirmRotation', _('Confirm'), () => {
        this.takeAction('actChooseRotation', { rotation: this.gamedatas.susan.rotation });
      });
      this.addSecondaryActionButton('btnSusanRotateClockwise', '<svg><use href="#rotate-clockwise-svg" /></svg>', () => {
        this.gamedatas.susan.rotation--;
        this.rotateSusan();
      });

      console.log(this._baseRotation);
      [0, 1, 2, 3, 4, 5].forEach((i) => {
        let extTile = $(`top-exterior-${i}`).querySelector('.tile-container');
        if (extTile)
          this.onClick(extTile, () => {
            this.gamedatas.susan.rotation = i + this._baseRotation;
            this.rotateSusan();
          });

        let intTile = $(`top-interior-${i}`).querySelector('.tile-container');
        if (intTile)
          this.onClick(intTile, () => {
            this.gamedatas.susan.rotation = -this.gamedatas.susan.shift + i + this._baseRotation;
            this.rotateSusan();
          });
      });
    },

    ////////////////////////////////////////
    //  _____             _
    // | ____|_ __   __ _(_)_ __   ___
    // |  _| | '_ \ / _` | | '_ \ / _ \
    // | |___| | | | (_| | | | | |  __/
    // |_____|_| |_|\__, |_|_| |_|\___|
    //              |___/
    ////////////////////////////////////////
    onEnteringStateSetupEngine(args) {
      if (!this.isCurrentPlayerActive() && !this.isSpectator) {
        this.addSecondaryActionButton('btnCancel', _('Cancel'), () => this.takeAction('actCancel', {}, false));
      }
    },

    onUpdateActivitySetupEngine(args, status) {
      if (status) {
        if ($('btnCancel')) $('btnCancel').remove();
      } else {
        this.clearPossible();
        this.addSecondaryActionButton('btnCancel', _('Cancel'), () => this.takeAction('actCancel', {}, false));
      }
    },

    addActionChoiceBtn(choice, disabled = false) {
      if ($('btnChoice' + choice.id)) return;

      let desc = this.translate(choice.description);
      desc = this.formatString(desc);

      // Add source if any
      let source = _(choice.source ? choice.source : '');
      // if (choice.sourceId) {
      //   let card = { id: choice.sourceId };
      //   this.loadSaveCard(card);
      //   source = this.fsr('${card_name}', {
      //     i18n: ['card_name'],
      //     card_name: _(card.name),
      //     card_id: card.id,
      //   });
      // }

      if (source != '') {
        desc += ` (${source})`;
      }

      this.addSecondaryActionButton(
        'btnChoice' + choice.id,
        desc,
        disabled
          ? () => {}
          : () => {
              this.askConfirmation(choice.irreversibleAction, () => this.takeAction('actChooseAction', { id: choice.id }));
            }
      );
      if (disabled) {
        $(`btnChoice${choice.id}`).classList.add('disabled');
      }
      if (choice.description.args && choice.description.args.bonus_pentagon) {
        $(`btnChoice${choice.id}`).classList.add('withbonus');
      }
    },

    onEnteringStateResolveChoice(args) {
      Object.values(args.choices).forEach((choice) => this.addActionChoiceBtn(choice, false));
      Object.values(args.allChoices).forEach((choice) => this.addActionChoiceBtn(choice, true));
    },

    onEnteringStateImpossibleAction(args) {
      this.addActionChoiceBtn(
        {
          choiceId: 0,
          description: args.desc,
        },
        true
      );
    },

    addConfirmTurn(args, action) {
      this.addPrimaryActionButton('btnConfirmTurn', _('Confirm'), () => {
        this.stopActionTimer();
        this.takeAction(action);
      });

      const OPTION_CONFIRM = 103;
      let n = args.previousEngineChoices;
      let timer = Math.min(10 + 2 * n, 20);
      this.startActionTimer('btnConfirmTurn', timer, this.prefs[OPTION_CONFIRM].value);
    },

    onEnteringStateConfirmTurn(args) {
      this.addConfirmTurn(args, 'actConfirmTurn');
    },

    onEnteringStateConfirmPartialTurn(args) {
      this.addConfirmTurn(args, 'actConfirmPartialTurn');
    },

    askConfirmation(warning, callback) {
      if (warning === false || this.prefs[104].value == 0) {
        callback();
      } else {
        //        let msg = warning === true ? _('drawing card(s) from the deck or the discard') : warning;
        let msg =
          warning === true
            ? _(
                "If you take this action, you won't be able to undo past this step because you will either draw card(s) from the deck or the discard, or someone else is going to make a choice"
              )
            : warning;
        this.confirmationDialog(
          msg,
          // this.fsr(
          //   _("If you take this action, you won't be able to undo past this step because of the following reason: ${msg}"),
          //   { msg }
          // ),
          () => {
            callback();
          }
        );
      }
    },

    // Generic call for Atomic Action that encode args as a JSON to be decoded by backend
    takeAtomicAction(action, args, warning = false) {
      if (!this.checkAction(action)) return false;

      this.askConfirmation(warning, () =>
        this.takeAction('actTakeAtomicAction', { actionName: action, actionArgs: JSON.stringify(args) }, false)
      );
    },

    ///////////////////////////////////////
    //  _____  __  __           _
    // | ____|/ _|/ _| ___  ___| |_ ___
    // |  _| | |_| |_ / _ \/ __| __/ __|
    // | |___|  _|  _|  __/ (__| |_\__ \
    // |_____|_| |_|  \___|\___|\__|___/
    ///////////////////////////////////////
    onLeavingStatePlaceTile() {
      [...$(`planet-${this.player_id}`).querySelectorAll('.planet-grid-cell')].forEach((elt) => {
        delete elt.style.removeProperty('cursor');
      });
    },

    onEnteringStatePlaceTile(args) {
      // END OF GAME : keep a tile eventhough you cant place it
      const impossible = args.descSuffix == 'impossible';
      if (impossible) {
        $('pagesubtitle').insertAdjacentHTML('beforeend', '<div id="tile-selector"></div>');
        let selection = null;
        const tiles = Object.keys(args.tiles);
        tiles.forEach((tileId) => {
          let o = $(`tile-${tileId}`).cloneNode(true);
          o.id += '-selector';
          $('tile-selector').insertAdjacentElement('beforeend', o);

          this.onClick(o, () => {
            // Existing placement => keep the same one
            if (selection !== null) {
              $(`tile-${selection}-selector`).classList.remove('selected');
            }
            selection = tileId;
            $(`tile-${selection}-selector`).classList.add('selected');
            this.addPrimaryActionButton('btnConfirm', _('Confirm'), () =>
              this.takeAtomicAction('actPlaceTileNoPlacement', [tileId])
            );
          });
        });
        return;
      }

      if (args.descSuffix == 'skippablebiomass') {
        this.addSecondaryActionButton('btnKeepIt', _('Keep it for later'), () =>
          this.takeAtomicAction('actKeepBiomassPatch', [])
        );
      }

      // REGULAR FLOW
      let selection = null;
      let rotation = 0;
      let flipped = false;
      let hoveredCell = null;
      let pos = null;
      let oPlanet = $(`planet-${this.player_id}`).querySelector('.planet-grid');
      let planetId = $(`planet-${this.player_id}`).dataset.id;

      // Add a visual representation on hover
      oPlanet.insertAdjacentHTML(
        'beforeend',
        `<div id='tile-controls' class='inactive hovering'>
        <div id='tile-controls-circle'>
          <div id="tile-rotate-clockwise"><svg><use href="#rotate-clockwise-svg" /></svg></div>
          <div id="tile-rotate-cclockwise"><svg><use href="#rotate-cclockwise-svg" /></svg></div>
          <div id="tile-flip"><svg><use href="#flip-svg" /></svg></div>
          <div id="tile-move-up"><i class="fa fa-long-arrow-up"></i></div>
          <div id="tile-move-right"><i class="fa fa-long-arrow-right"></i></div>
          <div id="tile-move-down"><i class="fa fa-long-arrow-down"></i></div>
          <div id="tile-move-left"><i class="fa fa-long-arrow-left"></i></div>
          <div id="tile-confirm-btn" class="action-button bgabutton bgabutton_blue">✓</div>
        </div>
      </div>`
      );
      oPlanet.insertAdjacentHTML('beforeend', this.tplTile({ type: '', state: 0 }, 'tile-hover'));

      // Move selection to a given position
      let moveSelection = (x, y, cell = null) => {
        this.placeTile('tile-hover', x, y, this.player_id);
        this.placeTile('tile-controls', x, y, this.player_id);

        let pos = args.tiles[selection].find((p) => p.pos.x == x && p.pos.y == y);
        let r = ((rotation % 4) + 4) % 4;
        let valid = pos && pos.r.find((d) => d[0] == r && d[1] == flipped);
        $('tile-hover').classList.toggle('invalid', !valid);
        $('tile-hover').style.transform =
          (this.getSideCell(planetId, x, y) == 1 ? 'translateX(7px)' : '') +
          `rotate(${rotation * 90}deg) scaleX(${flipped ? -1 : 1})`;
        $('tile-hover').querySelector('.tile-crosshairs').style.transform = `rotate(${-rotation * 90}deg)`;

        let bottomCircle = $('tile-controls').offsetTop + $('tile-controls-circle').offsetHeight / 2;
        $('tile-controls-circle').classList.toggle('bottom', bottomCircle > $('tile-controls').parentNode.offsetHeight);
        $('tile-controls').classList.toggle('invalid', !valid);

        if (cell === null) {
          cell = oPlanet.querySelector(`[data-x='${x}'][data-y='${y}']`);
        }
        if (cell) {
          cell.style.cursor = valid ? 'pointer' : 'not-allowed';
        }

        // Update button status
        if ($('btnConfirmBuild')) {
          $('btnConfirmBuild').classList.toggle('disabled', !valid);
          $('tile-confirm-btn').classList.toggle('disabled', !valid);
        }
      };
      let updateSelection = () => {
        if (hoveredCell) {
          moveSelection(hoveredCell.dataset.x, hoveredCell.dataset.y, hoveredCell);
        } else if (pos.x == 0 && pos.y == 0) {
          moveSelection(0, 0);
        }
      };

      // Add tile selectors in pagetitle
      $('pagesubtitle').insertAdjacentHTML('beforeend', '<div id="tile-selector"></div>');
      let callback = (tileId) => {
        // Existing placement => keep the same one
        if (selection !== null) {
          $(`tile-${selection}-selector`).classList.remove('selected');
          selection = tileId;
          updateSelection();
        }
        // Otherwise, set it at (0,0) (not a real cell)
        else {
          selection = tileId;
          pos = { x: 0, y: 0 };
          rotation = 0;
          flipped = false;
          moveSelection(0, 0);
        }
        let oTile = $(`tile-${tileId}-selector`);
        $('tile-hover').dataset.type = oTile.dataset.type;
        $('tile-controls').dataset.type = oTile.dataset.type;
        $('tile-hover').dataset.shape = oTile.dataset.shape;
        $('tile-controls').dataset.shape = oTile.dataset.shape;
        $('tile-hover').dataset.sprite = oTile.dataset.sprite;
        $('tile-controls').dataset.sprite = oTile.dataset.sprite;
        oTile.classList.add('selected');

        // Compute new size of circle control
        $('tile-controls').classList.remove('inactive');
        let w = $('tile-hover').offsetWidth;
        let h = $('tile-hover').offsetHeight;
        let cross = $('tile-hover').querySelector('.tile-crosshairs');
        let offsetW = cross.offsetLeft + cross.offsetWidth / 2;
        let offsetH = cross.offsetTop + cross.offsetHeight / 2;
        let dx = Math.max(offsetW, w - offsetW);
        let dy = Math.max(offsetH, h - offsetH);
        let radius = Math.sqrt(dx * dx + dy * dy) + 10;
        $('tile-controls-circle').style.width = 2 * radius + 'px';
        $('tile-controls-circle').style.height = 2 * radius + 'px';

        this.addPrimaryActionButton('btnRotateCClockwise', '<i class="fa fa-undo"></i>', () => incRotation(-1));
        this.addPrimaryActionButton('btnFlip', '<i class="fa fa-arrows-h"></i>', () => flipTile());
        this.addPrimaryActionButton('btnRotateClockwise', '<i class="fa fa-repeat"></i>', () => incRotation(1));
      };

      const buildableTiles = Object.keys(args.tiles);
      buildableTiles.forEach((tileId) => {
        let o = $(`tile-${tileId}`).cloneNode(true);
        o.id += '-selector';
        $('tile-selector').insertAdjacentElement('beforeend', o);

        this.onClick(o, () => callback(tileId));
      });
      if (buildableTiles.length == 1) {
        callback(buildableTiles[0]);
      }

      // Listen on hovering on map cells
      this.onHoverCell = (cell) => {
        cell.style.cursor = 'default';
        if (selection !== null && (pos == null || (pos.x == 0 && pos.y == 0))) {
          let x = parseInt(cell.dataset.x);
          let y = parseInt(cell.dataset.y);
          hoveredCell = cell;
          moveSelection(x, y, cell);
          $('tile-hover').classList.add('hovering');
          $('tile-controls').classList.add('hovering');
        }
      };

      this.onClickCell = (cell) => {
        cell.style.cursor = 'default';
        if (selection !== null) {
          let x = parseInt(cell.dataset.x);
          let y = parseInt(cell.dataset.y);
          pos = { x, y };
          hoveredCell = cell;
          $('tile-hover').classList.remove('hovering');
          $('tile-controls').classList.remove('hovering');

          // Add confirm button
          this.addPrimaryActionButton('btnConfirmBuild', _('Confirm'), () => {
            if (!$('btnConfirmBuild').classList.contains('disabled')) {
              this.takeAtomicAction('actPlaceTile', [selection, pos, ((rotation % 4) + 4) % 4, flipped]);
            }
          });
          moveSelection(x, y, cell);
        }
      };

      // Click on arrow to rotate
      let incRotation = (c) => {
        rotation += c;
        updateSelection();
      };
      this.onClick('tile-rotate-clockwise', () => incRotation(1));
      this.onClick('tile-rotate-cclockwise', () => incRotation(-1));

      // Click on arrow to move
      let shiftTile = (dx, dy) => {
        let x = pos.x + dx,
          y = pos.y + dy;
        hoveredCell = oPlanet.querySelector(`[data-x='${x}'][data-y='${y}']`);
        pos = { x, y };
        moveSelection(x, y);
      };
      this.onClick('tile-move-up', () => shiftTile(0, -1));
      this.onClick('tile-move-down', () => shiftTile(0, 1));
      this.onClick('tile-move-left', () => shiftTile(-1, 0));
      this.onClick('tile-move-right', () => shiftTile(1, 0));

      // Click on flip to mirror
      let flipTile = () => {
        flipped = !flipped;
        if (rotation % 2 == 1) rotation += 2;
        updateSelection();
      };
      this.onClick('tile-flip', () => flipTile());

      // Confirm
      this.onClick('tile-confirm-btn', () => {
        if (!$('tile-confirm-btn').classList.contains('disabled')) {
          this.takeAtomicAction('actPlaceTile', [selection, pos, ((rotation % 4) + 4) % 4, flipped]);
        }
      });
      this.attachRegisteredTooltips();
    },

    onEnteringStateChooseTracks(args) {
      let tracks = [];
      args.types.forEach((type) => {
        this.addSecondaryActionButton('btn' + type, this.fsr('${type}', { type, type_name: type }), () => {
          // Only a single track to resolve => auto confirm
          if (args.n == 1) {
            this.takeAtomicAction('actChooseTracks', [[type]]);
          }
          // Otherwise, toggle selected
          else if ($('btnConfirmTracks')) {
            let trackIndex = tracks.findIndex((t) => t == type);

            if (trackIndex !== -1) {
              tracks.splice(trackIndex, 1);
              $(`btn${type}`).classList.remove('selected');
            } else {
              tracks.push(type);
              $(`btn${type}`).classList.add('selected');
            }

            $('btnConfirmTracks').classList.toggle('disabled', tracks.length != args.n);
          }
        });
      });

      if (args.n > 1) {
        this.addPrimaryActionButton('btnConfirmTracks', _('Confirm'), () => {
          if (!$('btnConfirmTracks').classList.contains('disabled')) this.takeAtomicAction('actChooseTracks', [tracks]);
        });
        $('btnConfirmTracks').classList.add('disabled');
      }
    },

    onEnteringStatePlaceRover(args) {
      let selected = null,
        selectedCell = null;
      args.spaceIds.forEach((spaceId) => {
        let t = spaceId.split('_');
        let oCell = this.getPlanetCell(this.player_id, t[0], t[1]);
        oCell.style.cursor = null;

        this.onClick(oCell, () => {
          if (selectedCell) selectedCell.classList.remove('selected');
          selected = spaceId;
          selectedCell = oCell;
          oCell.classList.add('selected');
          this.addPrimaryActionButton('btnConfirm', _('Confirm'), () => this.takeAtomicAction('actPlaceRover', [selected]));
        });
      });
    },

    onEnteringStateMoveRover(args) {
      let selectedRover = null;
      let selectRover = (roverId) => {
        if (selectedRover) $(`meeple-${selectedRover}`).classList.remove('selected');
        selectedRover = roverId;
        $(`meeple-${selectedRover}`).classList.add('selected');

        [...$(`planet-${this.player_id}`).querySelectorAll('.planet-grid-cell.selectable')].forEach((elt) => {
          elt.classList.add('unselectable');
          elt.classList.remove('selected');
        });
        args.spaceIds[roverId].forEach((spaceId) => {
          let t = spaceId.split('_');
          let oCell = this.getPlanetCell(this.player_id, t[0], t[1]);
          if (!oCell.classList.contains('selectable')) this.onClick(oCell, () => selectSpace(spaceId, oCell));
          oCell.classList.remove('unselectable');
        });
      };

      Object.keys(args.spaceIds).forEach((roverId) => {
        this.onClick(`meeple-${roverId}`, () => selectRover(roverId));
      });
      let roverIds = Object.keys(args.spaceIds);
      if (args.currentRoverId != '') selectRover(args.currentRoverId);
      else if (roverIds.length == 1) selectRover(roverIds[0]);

      let selectedSpace = null;
      let selectedCell = null;
      let selectSpace = (spaceId, cell) => {
        if (cell.classList.contains('unselectable')) return false;
        if (selectedSpace) selectedCell.classList.remove('selected');

        selectedSpace = spaceId;
        selectedCell = cell;
        cell.classList.add('selected');
        this.takeAtomicAction('actMoveRover', [selectedRover, selectedSpace]);

        // this.addPrimaryActionButton('btnConfirm', _('Confirm'), () =>
        //   this.takeAtomicAction('actMoveRover', [selectedRover, selectedSpace])
        // );
      };
    },

    onEnteringStateTakeCivCard(args) {
      this._chooseCardModal = new customgame.modal('chooseCard', {
        class: 'planetunknown_popin',
        closeIcon: 'fa-times',
        title: this.fsr(_('CIV cards of level ${level}'), { level: args.level }),
        closeAction: 'hide',
        verticalAlign: 'flex-start',
        contentsTpl: `<div id='planetunknown-choose-card'></div><div id="planetunknown-choose-card-footer" class="active"></div>`,
        autoShow: true,
      });

      let selectedCard = null;
      Object.values(args.cards).forEach((card) => {
        this.addCard(card, 'planetunknown-choose-card');
        this.onClick(`card-${card.id}`, () => {
          if (selectedCard) $(`card-${selectedCard}`).classList.remove('selected');
          selectedCard = card.id;
          $(`card-${selectedCard}`).classList.add('selected');

          this.addPrimaryActionButton(
            'btnConfirm',
            _('Confirm'),
            () => this.takeAtomicAction('actTakeCivCard', [selectedCard]),
            'planetunknown-choose-card-footer'
          );
        });
      });

      this.addPrimaryActionButton('showDeck', _('Show deck'), () => this._chooseCardModal.show());
    },

    onEnteringStateCollectMeeple(args) {
      let spaces = {};
      args.meeples.forEach((spaceId) => {
        let t = spaceId.split('_');
        let oCell = this.getPlanetCell(this.player_id, t[0], t[1]);
        spaces[spaceId] = oCell;
      });

      this.onSelectN({
        elements: spaces,
        n: args.n,
        callback: (selectedSpaces) => this.takeAtomicAction('actCollectMeeple', [selectedSpaces]),
      });
    },

    onEnteringStateDestroyAllInRow(args) {
      let selected = null,
        selectedCell = null;
      Object.keys(args.rows).forEach((id) => {
        let t = id.split('_');
        let cell = t[0] == 'COLUMN' ? this.getPlanetCell(this.player_id, t[1], -1) : this.getPlanetCell(this.player_id, -1, t[1]);
        this.onClick(cell, () => {
          if (selected) {
            if (selected == id) return;

            selectedCell.classList.remove('selected');
            [...$(`planet-${this.player_id}`).querySelectorAll('.icon-meteor.selected')].forEach((elt) =>
              elt.classList.remove('selected')
            );
          }

          selected = id;
          selectedCell = cell;
          selectedCell.classList.add('selected');
          args.rows[id].forEach((meepleId) => $(`meeple-${meepleId}`).classList.add('selected'));
          this.addPrimaryActionButton('btnConfirmDestroy', _('Confirm'), () =>
            this.takeAtomicAction('actDestroyAllInRow', [selected])
          );
        });
      });
    },

    onEnteringStateMoveTrackerByOne(args) {
      let selected = null,
        selectedElem = null;
      args.spaceIds.forEach((spaceId) => {
        let t = spaceId.split('_');
        let elem = $(`corporation-${this.player_id}-${t[0]}-${t[1]}`);
        this.onClick(elem, () => {
          if (selected) {
            selectedElem.classList.remove('selected');
          }

          selected = spaceId;
          selectedElem = elem;
          selectedElem.classList.add('selected');
          this.addPrimaryActionButton('btnConfirm', _('Confirm'), () =>
            this.takeAtomicAction('actMoveTrackerByOne', [args.type, selected])
          );
        });
      });
    },

    onEnteringStatePositionLifepodOnTrack(args) {
      let selectedLifepod = null,
        selectedSpace = null,
        selectedElem = null;
      let action = args.action || 'actPositionLifepodOnTrack';

      // Select lifepod
      let selectLifepod = (lifepodId) => {
        if (selectedLifepod) $(`meeple-${selectedLifepod}`).classList.remove('selected');
        selectedLifepod = lifepodId;
        $(`meeple-${selectedLifepod}`).classList.add('selected');

        if (selectedSpace)
          this.addPrimaryActionButton('btnConfirm', _('Confirm'), () =>
            this.takeAtomicAction(action, [selectedLifepod, selectedSpace])
          );
      };
      if (args.lifepodIds.length == 1) {
        selectLifepod(args.lifepodIds[0]);
      } else {
        args.lifepodIds.forEach((lifepodId) => this.onClick(`meeple-${lifepodId}`, () => selectLifepod(lifepodId)));
      }

      // Select space
      args.spaceIds.forEach((spaceId) => {
        let elem = null;
        if (spaceId == 'reserve') {
          elem = $(`lifepod-reserve-${this.player_id}`);
        } else {
          let t = spaceId.split('_');
          elem = $(`corporation-${this.player_id}-${t[0]}-${t[1]}`);
        }

        this.onClick(elem, () => {
          if (selectedSpace) selectedElem.classList.remove('selected');

          selectedSpace = spaceId;
          selectedElem = elem;
          selectedElem.classList.add('selected');
          if (selectedLifepod)
            this.addPrimaryActionButton('btnConfirm', _('Confirm'), () =>
              this.takeAtomicAction(action, [selectedLifepod, selectedSpace])
            );
        });
      });
    },

    onEnteringStatePositionLifepodOnTech(args) {
      args.action = 'actPositionLifepodOnTech';
      this.onEnteringStatePositionLifepodOnTrack(args);
    },

    onEnteringStateChooseFluxTrack(args) {
      ALL_TYPES.forEach((type) => {
        this.addSecondaryActionButton('btn' + type, this.fsr('${type}', { type, type_name: type }), () =>
          this.takeAtomicAction('actChooseFluxTrack', [type])
        );
      });
    },

    onEnteringStateMoveTrackersToFive(args) {
      args.playableTracks.forEach((type) => {
        this.addSecondaryActionButton('btn' + type, this.fsr('${type}', { type, type_name: type }), () =>
          this.takeAtomicAction('actMoveTrackersToFive', [type])
        );
      });
    },

    onEnteringStateGainBiomassPatch(args) {
      for (let i = 1; i <= args.n; i++) {
        let n = i;
        this.addPrimaryActionButton('btn' + i, i, () => this.takeAtomicAction('actGainBiomassPatch', [n]));
      }
    },

    ////////////////////////////////////////////////////////////
    // _____                          _   _   _
    // |  ___|__  _ __ _ __ ___   __ _| |_| |_(_)_ __   __ _
    // | |_ / _ \| '__| '_ ` _ \ / _` | __| __| | '_ \ / _` |
    // |  _| (_) | |  | | | | | | (_| | |_| |_| | | | | (_| |
    // |_|  \___/|_|  |_| |_| |_|\__,_|\__|\__|_|_| |_|\__, |
    //                                                 |___/
    ////////////////////////////////////////////////////////////

    /**
     * Replace some expressions by corresponding html formating
     */
    formatIcon(name, n = null, lowerCase = true) {
      let type = lowerCase ? name.toLowerCase() : name;
      const NO_TEXT_ICONS = ['xtoken', 'Clever', 'take-in-range'];
      let noText = NO_TEXT_ICONS.includes(name);
      let text = n == null ? '' : `<span>${n}</span>`;
      return `${noText ? text : ''}<div class="icon-container icon-container-${type}">
            <div class="planetunknown-icon icon-${type}">${noText ? '' : text}</div>
          </div>`;
    },

    formatString(str) {
      const ICONS = ['WATER', 'ROVER', 'CIV', 'BIOMASS', 'TECH'];

      ICONS.forEach((name) => {
        const regex = new RegExp('<' + name + ':([^>]+)>', 'g');
        str = str.replaceAll(regex, this.formatIcon(name, '<span>$1</span>'));
        str = str.replaceAll(new RegExp('<' + name + '>', 'g'), this.formatIcon(name));
      });
      str = str.replace(/__([^_]+)__/g, '<span class="action-card-name-reference">$1</span>');
      str = str.replace(/\*\*([^\*]+)\*\*/g, '<b>$1</b>');

      return str;
    },

    /**
     * Format log strings
     *  @Override
     */
    format_string_recursive(log, args) {
      try {
        if (log && args && !args.processed) {
          args.processed = true;

          log = this.formatString(_(log));

          if (args.type !== undefined && args.type_name !== undefined) {
            args.type = this.formatIcon(args.type_name);
            args.type_name = '';
          }

          if (args.alert !== undefined) {
            args.alert = `<span class='event-alert alert-${args.color}'>${_(args.alert)}</span>`;
          }
        }
      } catch (e) {
        console.error(log, args, 'Exception thrown', e.stack);
      }

      return this.inherited(arguments);
    },

    //////////////////////////////////////////////////////
    //  ___        __         ____                  _
    // |_ _|_ __  / _| ___   |  _ \ __ _ _ __   ___| |
    //  | || '_ \| |_ / _ \  | |_) / _` | '_ \ / _ \ |
    //  | || | | |  _| (_) | |  __/ (_| | | | |  __/ |
    // |___|_| |_|_|  \___/  |_|   \__,_|_| |_|\___|_|
    //////////////////////////////////////////////////////

    setupInfoPanel() {
      dojo.place(this.tplInfoPanel(), 'player_boards', 'first');
      let chk = $('help-mode-chk');
      dojo.connect(chk, 'onchange', () => this.toggleHelpMode(chk.checked));
      this.addTooltip('help-mode-switch', '', _('Toggle help/safe mode.'));

      this._settingsModal = new customgame.modal('showSettings', {
        class: 'planetunknown_popin',
        closeIcon: 'fa-times',
        title: _('Settings'),
        closeAction: 'hide',
        verticalAlign: 'flex-start',
        contentsTpl: `<div id='planetunknown-settings'>
             <div id='planetunknown-settings-header'></div>
             <div id="settings-controls-container"></div>
           </div>`,
      });

      this._susanModal = new customgame.modal('showSusan', {
        class: 'planetunknown_popin',
        closeIcon: 'fa-times',
        title: _('S.U.S.A.N.'),
        closeAction: 'hide',
        verticalAlign: 'flex-start',
        contentsTpl: `<div id='susan-modal-footer'>
          <div id="susan-rotate-cclockwise"><svg><use href="#rotate-cclockwise-svg" /></svg></div>
          <a href="#" class="action-button bgabutton bgabutton_blue" id="btnConfirmSusanRotation">${_('Confirm')}</a>
          <div id="susan-rotate-clockwise"><svg><use href="#rotate-clockwise-svg" /></svg></div>
        </div>
        <div id='susan-enlarge'></div>`,
        onStartShow: () => $('susan-enlarge').insertAdjacentElement('beforeend', $('susan-container')),
        onStartHide: () => $('susan-holder').insertAdjacentElement('beforeend', $('susan-container')),
      });
      this.onClick('susan-holder', () => this._susanModal.show(), false);

      if ($('events-info')) {
        this.onClick('events-info', () => this.zoomOnEventCard(), false);
      }
      // let handWrapper = $('floating-hand-wrapper');
      // $('floating-hand-button').addEventListener('click', () => {
      //   if (handWrapper.dataset.open && handWrapper.dataset.open == 'hand') {
      //     delete handWrapper.dataset.open;
      //   } else {
      //     handWrapper.dataset.open = 'hand';
      //   }
      // });
      // $('floating-scoring-hand-button').addEventListener('click', () => {
      //   if (handWrapper.dataset.open && handWrapper.dataset.open == 'scoringHand') {
      //     delete handWrapper.dataset.open;
      //   } else {
      //     handWrapper.dataset.open = 'scoringHand';
      //   }
      // });
    },

    tplInfoPanel() {
      let susanIndicators = '',
        susanExterior = '',
        susanInterior = '';
      for (let j = 0; j < 6; j++) {
        susanIndicators += `<div class="susan-indicator-slot" id='indicator-${j}'></div>`;
        susanExterior += `<div class="susan-space" id='top-exterior-${j}'><span class="susan-counter" id="susan-counter-exterior-${j}">0</span></div>`;
        susanInterior += `<div class="susan-space" id='top-interior-${j}'><span class="susan-counter" id="susan-counter-interior-${j}">0</span></div>`;
      }

      return (
        `
   <div class='player-board' id="player_board_config">
     <div id="player_config" class="player_board_content">
       <div class="player_config_row">
         <div id="show-scores">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
              <g class="fa-group">
                <path class="fa-secondary" fill="currentColor" d="M0 192v272a48 48 0 0 0 48 48h352a48 48 0 0 0 48-48V192zm324.13 141.91a11.92 11.92 0 0 1-3.53 6.89L281 379.4l9.4 54.6a12 12 0 0 1-17.4 12.6l-49-25.8-48.9 25.8a12 12 0 0 1-17.4-12.6l9.4-54.6-39.6-38.6a12 12 0 0 1 6.6-20.5l54.7-8 24.5-49.6a12 12 0 0 1 21.5 0l24.5 49.6 54.7 8a12 12 0 0 1 10.13 13.61zM304 128h32a16 16 0 0 0 16-16V16a16 16 0 0 0-16-16h-32a16 16 0 0 0-16 16v96a16 16 0 0 0 16 16zm-192 0h32a16 16 0 0 0 16-16V16a16 16 0 0 0-16-16h-32a16 16 0 0 0-16 16v96a16 16 0 0 0 16 16z" opacity="0.4"></path>
                <path class="fa-primary" fill="currentColor" d="M314 320.3l-54.7-8-24.5-49.6a12 12 0 0 0-21.5 0l-24.5 49.6-54.7 8a12 12 0 0 0-6.6 20.5l39.6 38.6-9.4 54.6a12 12 0 0 0 17.4 12.6l48.9-25.8 49 25.8a12 12 0 0 0 17.4-12.6l-9.4-54.6 39.6-38.6a12 12 0 0 0-6.6-20.5zM400 64h-48v48a16 16 0 0 1-16 16h-32a16 16 0 0 1-16-16V64H160v48a16 16 0 0 1-16 16h-32a16 16 0 0 1-16-16V64H48a48 48 0 0 0-48 48v80h448v-80a48 48 0 0 0-48-48z"></path>
              </g>
            </svg>
         </div>

         <div id="help-mode-switch">
           <input type="checkbox" class="checkbox" id="help-mode-chk" />
           <label class="label" for="help-mode-chk">
             <div class="ball"></div>
           </label><svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="question-circle" class="svg-inline--fa fa-question-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M256 8C119 8 8 119.08 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 422a46 46 0 1 1 46-46 46.05 46.05 0 0 1-46 46zm40-131.33V300a12 12 0 0 1-12 12h-56a12 12 0 0 1-12-12v-4c0-41.06 31.13-57.47 54.65-70.66 20.17-11.31 32.54-19 32.54-34 0-19.82-25.27-33-45.7-33-27.19 0-39.44 13.14-57.3 35.79a12 12 0 0 1-16.67 2.13L148.82 170a12 12 0 0 1-2.71-16.26C173.4 113 208.16 90 262.66 90c56.34 0 116.53 44 116.53 102 0 77-83.19 78.21-83.19 106.67z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M256 338a46 46 0 1 0 46 46 46 46 0 0 0-46-46zm6.66-248c-54.5 0-89.26 23-116.55 63.76a12 12 0 0 0 2.71 16.24l34.7 26.31a12 12 0 0 0 16.67-2.13c17.86-22.65 30.11-35.79 57.3-35.79 20.43 0 45.7 13.14 45.7 33 0 15-12.37 22.66-32.54 34C247.13 238.53 216 254.94 216 296v4a12 12 0 0 0 12 12h56a12 12 0 0 0 12-12v-1.33c0-28.46 83.19-29.67 83.19-106.67 0-58-60.19-102-116.53-102z"></path></g></svg>
         </div>

         <div id="show-settings">
           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
             <g>
               <path class="fa-secondary" fill="currentColor" d="M638.41 387a12.34 12.34 0 0 0-12.2-10.3h-16.5a86.33 86.33 0 0 0-15.9-27.4L602 335a12.42 12.42 0 0 0-2.8-15.7 110.5 110.5 0 0 0-32.1-18.6 12.36 12.36 0 0 0-15.1 5.4l-8.2 14.3a88.86 88.86 0 0 0-31.7 0l-8.2-14.3a12.36 12.36 0 0 0-15.1-5.4 111.83 111.83 0 0 0-32.1 18.6 12.3 12.3 0 0 0-2.8 15.7l8.2 14.3a86.33 86.33 0 0 0-15.9 27.4h-16.5a12.43 12.43 0 0 0-12.2 10.4 112.66 112.66 0 0 0 0 37.1 12.34 12.34 0 0 0 12.2 10.3h16.5a86.33 86.33 0 0 0 15.9 27.4l-8.2 14.3a12.42 12.42 0 0 0 2.8 15.7 110.5 110.5 0 0 0 32.1 18.6 12.36 12.36 0 0 0 15.1-5.4l8.2-14.3a88.86 88.86 0 0 0 31.7 0l8.2 14.3a12.36 12.36 0 0 0 15.1 5.4 111.83 111.83 0 0 0 32.1-18.6 12.3 12.3 0 0 0 2.8-15.7l-8.2-14.3a86.33 86.33 0 0 0 15.9-27.4h16.5a12.43 12.43 0 0 0 12.2-10.4 112.66 112.66 0 0 0 .01-37.1zm-136.8 44.9c-29.6-38.5 14.3-82.4 52.8-52.8 29.59 38.49-14.3 82.39-52.8 52.79zm136.8-343.8a12.34 12.34 0 0 0-12.2-10.3h-16.5a86.33 86.33 0 0 0-15.9-27.4l8.2-14.3a12.42 12.42 0 0 0-2.8-15.7 110.5 110.5 0 0 0-32.1-18.6A12.36 12.36 0 0 0 552 7.19l-8.2 14.3a88.86 88.86 0 0 0-31.7 0l-8.2-14.3a12.36 12.36 0 0 0-15.1-5.4 111.83 111.83 0 0 0-32.1 18.6 12.3 12.3 0 0 0-2.8 15.7l8.2 14.3a86.33 86.33 0 0 0-15.9 27.4h-16.5a12.43 12.43 0 0 0-12.2 10.4 112.66 112.66 0 0 0 0 37.1 12.34 12.34 0 0 0 12.2 10.3h16.5a86.33 86.33 0 0 0 15.9 27.4l-8.2 14.3a12.42 12.42 0 0 0 2.8 15.7 110.5 110.5 0 0 0 32.1 18.6 12.36 12.36 0 0 0 15.1-5.4l8.2-14.3a88.86 88.86 0 0 0 31.7 0l8.2 14.3a12.36 12.36 0 0 0 15.1 5.4 111.83 111.83 0 0 0 32.1-18.6 12.3 12.3 0 0 0 2.8-15.7l-8.2-14.3a86.33 86.33 0 0 0 15.9-27.4h16.5a12.43 12.43 0 0 0 12.2-10.4 112.66 112.66 0 0 0 .01-37.1zm-136.8 45c-29.6-38.5 14.3-82.5 52.8-52.8 29.59 38.49-14.3 82.39-52.8 52.79z" opacity="0.4"></path>
               <path class="fa-primary" fill="currentColor" d="M420 303.79L386.31 287a173.78 173.78 0 0 0 0-63.5l33.7-16.8c10.1-5.9 14-18.2 10-29.1-8.9-24.2-25.9-46.4-42.1-65.8a23.93 23.93 0 0 0-30.3-5.3l-29.1 16.8a173.66 173.66 0 0 0-54.9-31.7V58a24 24 0 0 0-20-23.6 228.06 228.06 0 0 0-76 .1A23.82 23.82 0 0 0 158 58v33.7a171.78 171.78 0 0 0-54.9 31.7L74 106.59a23.91 23.91 0 0 0-30.3 5.3c-16.2 19.4-33.3 41.6-42.2 65.8a23.84 23.84 0 0 0 10.5 29l33.3 16.9a173.24 173.24 0 0 0 0 63.4L12 303.79a24.13 24.13 0 0 0-10.5 29.1c8.9 24.1 26 46.3 42.2 65.7a23.93 23.93 0 0 0 30.3 5.3l29.1-16.7a173.66 173.66 0 0 0 54.9 31.7v33.6a24 24 0 0 0 20 23.6 224.88 224.88 0 0 0 75.9 0 23.93 23.93 0 0 0 19.7-23.6v-33.6a171.78 171.78 0 0 0 54.9-31.7l29.1 16.8a23.91 23.91 0 0 0 30.3-5.3c16.2-19.4 33.7-41.6 42.6-65.8a24 24 0 0 0-10.5-29.1zm-151.3 4.3c-77 59.2-164.9-28.7-105.7-105.7 77-59.2 164.91 28.7 105.71 105.7z"></path>
             </g>
           </svg>
         </div>
       </div>
       <div class='player_config_row' id="decks-info">
        <div class='civ-deck-counter-wrapper'>
          <span id='civ-deck-counter-1'>0</span>
          ${this.formatIcon('civ', 1)}
        </div>
        <div class='civ-deck-counter-wrapper'>
          <span id='civ-deck-counter-2'>0</span>
          ${this.formatIcon('civ', 2)}
        </div>
        <div class='civ-deck-counter-wrapper'>
          <span id='civ-deck-counter-3'>0</span>
          ${this.formatIcon('civ', 3)}
        </div>
        <div class='civ-deck-counter-wrapper'>
          <span id='civ-deck-counter-4'>0</span>
          ${this.formatIcon('civ', 4)}
        </div>
        ` +
        (this.gamedatas.eventGame == 'eventCard'
          ? `<div id="events-info">
            <div id="event-deck" data-type="EventCard" class="planetunknown-card">
              <div class='card-inner' data-id="back"></div>
              <span id="counter-deck-event">0</span>
            </div>
            <div id='event-card-holder'></div>
          </div>`
          : '') +
        `
       </div>
       <div class="player_config_row" id="susan-holder">
         <div id="susan-container">
           <div id="susan-indicators">${susanIndicators}</div>
           <div id="susan-exterior">
              ${susanExterior}
              <div id="susan-interior" data-shift="${this.gamedatas.susan.shift}">${susanInterior}</div>
           </div>
         </div>
       </div>
     </div>
   </div>
   `
      );
    },

    tplSusanIndicator(player) {
      return `<div class='susan-indicator' style='border-top-color:#${player.color}'></div>`;
    },

    rotateSusan() {
      let rotation = -this.gamedatas.susan.rotation + (this._baseRotation || 0);
      $('susan-exterior').style.transform = `rotate(${60 * rotation}deg)`;

      let modRotation = ((-rotation % 6) + 6) % 6;
      $('susan-exterior').dataset.rotation = modRotation;
      $('susan-interior').dataset.rotation = (modRotation + this.gamedatas.susan.shift) % 6;
    },

    updatePlayerOrdering() {
      this.inherited(arguments);
      dojo.place('player_board_config', 'player_boards', 'first');
    },

    updateSusanCounters() {
      let decks = this.gamedatas.susan.decks;
      Object.keys(decks).forEach((deck) => {
        $(`susan-counter-${deck}`).innerHTML = decks[deck];
      });
    },

    notif_newRotation(n) {
      debug('Notif: SUSAN is rotating', n);
      this.gamedatas.susan.rotation = n.args.newRotation;
      this.rotateSusan();
    },

    notif_endOfTurn(n) {
      debug('Notif: end of turn, refilling SUSAN', n);
      n.args.tiles.forEach((tile) => {
        let o = $(`tile-${tile.id}`);
        if (!o) this.addTile(tile);
      });
    },

    onChangeBoardSizesSetting(val) {
      this.updateLayout();
    },

    updateLayout() {
      if (!this.settings) return;
      const ROOT = document.documentElement;

      const WIDTH = $('planetunknown-main-container').getBoundingClientRect()['width'] - 5;
      const BOARD_WIDTH = 1510;
      const BOARD_SIZE = (WIDTH * this.settings.boardSizes) / 100;
      let boardScale = BOARD_SIZE / BOARD_WIDTH;
      ROOT.style.setProperty('--planetUnknownBoardScale', boardScale);
    },

    ///////////////////////////////////////////////////////////
    //  ____                     _                         _
    // / ___|  ___ ___  _ __ ___| |__   ___   __ _ _ __ __| |
    // \___ \ / __/ _ \| '__/ _ \ '_ \ / _ \ / _` | '__/ _` |
    //  ___) | (_| (_) | | |  __/ |_) | (_) | (_| | | | (_| |
    // |____/ \___\___/|_|  \___|_.__/ \___/ \__,_|_|  \__,_|
    ///////////////////////////////////////////////////////////

    setupScoreBoard() {
      this._scoreboardModal = new customgame.modal('showScoreboard', {
        class: 'planetunknown_popin',
        closeIcon: 'fa-times',
        closeAction: 'hide',
        verticalAlign: 'flex-start',
        contentsTpl: ``,
        scale: 0.95,
        breakpoint: 1400,
      });

      $('open-scoreboard').addEventListener('click', () => this._scoreboardModal.show());
    },

    // notif_finalScoring(n) {
    //   debug('Notif: final scoring');
    //   // Update score
    //   this._scoreCounters[n.args.player_id].toValue(n.args.score);

    //   // Display scoring card
    //   n.args.scoringHand.forEach((card) => {
    //     if (!$(`card-${card.id}`)) this.addZooCard(card);
    //   });
    // },
  });
});
