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
], function (dojo, declare) {
  return declare('bgagame.planetunknown', [customgame.game, planetunknown.players, planetunknown.meeples], {
    constructor() {
      this._activeStates = ['placeTile'];
      this._notifications = [
        ['clearTurn', 200],
        ['refreshUI', 200],
        ['refreshHand', 200],
        ['setupPlayer', 1200],
      ];

      // Fix mobile viewport (remove CSS zoom)
      this.default_viewport = 'width=740';
      this.cardStatuses = {};
    },

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

      this.setupSusan();
      // this.setupScoreBoard();
      this.setupPlayers();
      this.setupTiles();
      this.setupMeeples();
      // this.setupTour();
      this.inherited(arguments);
    },

    setupSusan() {
      $('planetunknown-main-container').insertAdjacentHTML(
        'beforeend',
        `<div id="susan-container">
          <div id="susan-exterior"></div>
          <div id="susan-interior"></div>
        </div>`
      );

      for (let j = 0; j < 6; j++) {
        $('susan-exterior').insertAdjacentHTML('beforeend', `<div class="susan-space" id='exterior-${j}'></div>`);
        $('susan-interior').insertAdjacentHTML('beforeend', `<div class="susan-space" id='interior-${j}'></div>`);
      }
    },

    onLoadingComplete() {
      // this.updateLayout();
      this.inherited(arguments);
    },

    onScreenWidthChange() {
      // if (this.settings) this.updateLayout();
    },

    onAddingNewUndoableStepToLog(notif) {
      if (!$(`log_${notif.logId}`)) return;
      let stepId = notif.msg.args.stepId;
      $(`log_${notif.logId}`).dataset.step = stepId;
      if ($(`dockedlog_${notif.mobileLogId}`)) $(`dockedlog_${notif.mobileLogId}`).dataset.step = stepId;

      if (
        this.gamedatas &&
        this.gamedatas.gamestate &&
        this.gamedatas.gamestate.args &&
        this.gamedatas.gamestate.args.previousSteps &&
        this.gamedatas.gamestate.args.previousSteps.includes(parseInt(stepId))
      ) {
        this.onClick($(`log_${notif.logId}`), () => this.undoToStep(stepId));

        if ($(`dockedlog_${notif.mobileLogId}`)) this.onClick($(`dockedlog_${notif.mobileLogId}`), () => this.undoToStep(stepId));
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

      // ['meeples', 'players', 'cards', 'tiles', 'break', 'conservationBonuses', 'endOfGame'].forEach((value) => {
      //   this.gamedatas[value] = n.args.datas[value];
      // });
      // this.setupMeeples();
      // this.setupTiles();
      // this.updatePlayersCounters();
      // this.updateActionCards();
      // this.updateBreakCounter();
      // this.updateScoreboardBonuses();
      // this.updateLastRoundBanner();
      // this.updateCardCosts();

      // this.forEachPlayer((player) => {
      //   this._scoreCounters[player.id].toValue(player.newScore);
      //   this._playerCounters[player.id]['income'].toValue(player.income);
      // });
    },

    notif_refreshHand(n) {
      debug('Notif: refreshing UI', n);
      // this.gamedatas.players[n.args.player_id].hand = n.args.hand;
      // this.updateHandCards();
      // this.updateCardCosts();
    },

    notif_endOfGame() {
      debug('Notif: end of game');
      this.gamedatas.endOfGame = true;
      this.updateLastRoundBanner();
    },

    onEnteringStateGameEnd(args) {
      if ($('last-round')) $('last-round').remove();
    },

    updateLastRoundBanner() {
      if (this.gamedatas.endOfGame) {
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

      let toRemove = [];
      toRemove.forEach((eltId) => {
        if ($(eltId)) $(eltId).remove();
      });

      this.inherited(arguments);
    },

    onEnteringState(stateName, args) {
      debug('Entering state: ' + stateName, args);
      if (this.isFastMode() && ![].includes(stateName)) return;

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

    notif_setupPlayer(n) {
      debug('Notif: finish setup of player', n);

      // let player = this.gamedatas.players[n.args.player_id];

      // // Action Cards
      // player.actionCards = n.args.action_cards;
      // this.updateActionCards();

      // // Map
      // let container = $(`player-board-${player.id}`);
      // let previousMap = container.querySelector('.zoo-map');
      // if (previousMap) previousMap.remove();

      // player.mapId = n.args.mapId;
      // $(`icons-summary-map-${player.id}`).insertAdjacentHTML('afterend', this.tplZooPlanet(MAPS_DATA[player.mapId], player));
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

    ////////////////////////////////////////
    //  _____             _
    // | ____|_ __   __ _(_)_ __   ___
    // |  _| | '_ \ / _` | | '_ \ / _ \
    // | |___| | | | (_| | | | | |  __/
    // |_____|_| |_|\__, |_|_| |_|\___|
    //              |___/
    ////////////////////////////////////////
    onEnteringStateSetupEngine(args) {
      if (!this.isCurrentPlayerActive()) {
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
    onEnteringStatePlaceTile(args) {
      let selection = null;
      let rotation = 0;
      let flipped = false;
      let hoveredCell = null;
      let pos = null;
      let oPlanet = $(`planet-${this.player_id}`).querySelector('.planet-grid');

      // Add a visual representation on hover
      oPlanet.insertAdjacentHTML(
        'beforeend',
        `<div id='tile-controls' class='inactive hovering'>
        <div id='tile-controls-circle'>
          <div id="tile-rotate-clockwise"><svg><use href="#rotate-clockwise-svg" /></svg></div>
          <div id="tile-rotate-cclockwise"><svg><use href="#rotate-cclockwise-svg" /></svg></div>
          <div id="tile-flip"><svg><use href="#flip-svg" /></svg></div>
          <div id="tile-confirm-btn" class="action-button bgabutton bgabutton_blue">✓</div>
        </div>
      </div>`
      );
      oPlanet.insertAdjacentHTML('beforeend', this.tplTile({ type: '', state: 0 }, 'tile-hover'));
      $('tile-hover')
        .querySelector('.tile-crosshairs')
        .insertAdjacentHTML(
          'beforeend',
          `<div id="tile-rotate-clockwise-on-tile"><svg><use href="#rotate-clockwise-svg" /></svg></div>
          <div id="tile-rotate-cclockwise-on-tile"><svg><use href="#rotate-cclockwise-svg" /></svg></div>`
        );

      // Move selection to a given position
      let moveSelection = (x, y, cell = null) => {
        this.placeTile('tile-hover', x, y);
        this.placeTile('tile-controls', x, y);

        let pos = args.tiles[selection].find((p) => p.pos.x == x && p.pos.y == y);
        let r = ((rotation % 4) + 4) % 4;
        let valid = pos && pos.r.find((d) => d[0] == r && d[1] == flipped);
        $('tile-hover').classList.toggle('invalid', !valid);
        $('tile-hover').style.transform = `rotate(${rotation * 90}deg) scaleX(${flipped ? -1 : 1})`;
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
      let callback = (tileId) => {
        // Existing placement => keep the same one
        if (selection !== null) {
          $(`tile-${selection}`).classList.remove('selected');
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
        let oTile = $(`tile-${tileId}`);
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
        this.onClick(`tile-${tileId}`, () => callback(tileId));
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
      this.onClick('tile-rotate-clockwise-on-tile', () => incRotation(1));
      this.onClick('tile-rotate-cclockwise-on-tile', () => incRotation(-1));
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

    onEnteringStateFooA(args) {
      this.addPrimaryActionButton('actionA', 'Done A', () => this.takeAtomicAction('actFooA', []));
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
      const ICONS = ['APPEAL'];

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
      return `
   <div class='player-board' id="player_board_config">
     <div id="player_config" class="player_board_content">
       <div class="player_config_row">
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
       <div class="player_config_row">
         <div id="open-scoreboard">
          <svg  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 119.79425 66.99308">
            <path d="M 1.5,33.409858 V 1.5 h 8.3454309 8.3454311 l 0.163419,1.379818 c 0.265488,2.2416348 2.000466,4.6289557 4.185096,5.7586718 1.894316,0.9795889 5.24383,0.9795889 7.138145,0 2.138442,-1.105831 4.272475,-4.2320506 4.272968,-6.2596166 L 33.950703,1.5 h 8.383099 8.383098 v 31.909858 31.909859 h -8.383098 c -8.187748,0 -8.383099,-0.01255 -8.383099,-0.537943 0,-0.920584 -0.984513,-3.182657 -1.873914,-4.305619 -0.504652,-0.637175 -1.52315,-1.409737 -2.505979,-1.900862 -4.202927,-2.100226 -9.117141,-0.159431 -10.769259,4.253159 -0.294445,0.786424 -0.535354,1.668678 -0.535354,1.960563 0,0.515907 -0.233696,0.530702 -8.3830986,0.530702 H 1.5 Z" />
            <path d="m 85.834954,64.739661 c -4.174784,-1.45373 -7.008674,-3.03363 -10.684968,-5.95688 -4.585408,-3.64614 -8.95837,-9.92835 -12.038002,-17.29384 -1.873738,-4.48138 -4.058014,-13.2089 -4.162315,-16.630991 -0.02238,-0.734279 1.191073,-1.811107 5.194576,-4.60971 l 2.109751,-1.474797 -0.138231,-1.08169 c -0.07603,-0.59493 -0.453664,-2.420282 -0.839194,-4.056338 -0.38553,-1.636056 -0.769568,-3.475959 -0.853418,-4.0886722 -0.145067,-1.060046 -0.114909,-1.12058 0.622402,-1.249314 0.42617,-0.07441 3.695419,-0.892108 7.264995,-1.817109 3.569577,-0.925001 8.685961,-2.2340409 11.369742,-2.9089789 l 4.879602,-1.2271604 7.695046,1.9523824 c 4.23228,1.0738099 8.8511,2.2362139 10.26406,2.5831209 4.45947,1.094881 6.07839,1.540375 6.28676,1.729991 0.11124,0.101227 0.0374,0.847514 -0.16403,1.6584162 -0.20146,0.810902 -0.5784,2.447887 -0.83765,3.637747 -0.25925,1.189859 -0.58025,2.589295 -0.71333,3.109859 -0.13309,0.520563 -0.24265,1.079151 -0.24348,1.241307 -10e-4,0.262848 4.96946,4.133743 6.79599,5.292214 0.64469,0.408893 0.70128,0.577436 0.59071,1.759347 -0.33619,3.593666 -1.70154,9.383776 -3.24153,13.746566 -4.78474,13.55511 -13.69379,22.83108 -25.14917,26.18493 -1.376886,0.40312 -1.434186,0.39596 -4.008316,-0.5004 z" />
          </svg>         
         </div>
       </div>
     </div>
   </div>
   `;
    },

    updatePlayerOrdering() {
      this.inherited(arguments);
      dojo.place('player_board_config', 'player_boards', 'first');
    },

    updateLayout() {
      if (!this.settings) return;
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
