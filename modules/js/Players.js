define(['dojo', 'dojo/_base/declare', g_gamethemeurl + 'modules/js/data.js'], (dojo, declare) => {
  const PLAYER_COUNTERS = ['appeal', 'reputation', 'conservation', 'money', 'handCount', 'scoringHandCount', 'xtoken', 'income'];
  const RESOURCES = [];
  const ALL_PLAYER_COUNTERS = PLAYER_COUNTERS.concat(RESOURCES);
  const COUNTER_MEEPLES = ['reputation', 'conservation', 'appeal'];

  return declare('planetunknown.players', null, {
    getPlayers() {
      return Object.values(this.gamedatas.players);
    },

    isSolo() {
      return this.getPlayers().length == 1;
    },

    setupPlayers() {
      // Change No so that it fits the current player order view
      let currentNo = this.getPlayers().reduce((carry, player) => (player.id == this.player_id ? player.no : carry), 0);
      let nPlayers = Object.keys(this.gamedatas.players).length;
      this.forEachPlayer((player) => (player.order = (player.no + nPlayers - currentNo) % nPlayers));
      this.orderedPlayers = Object.values(this.gamedatas.players).sort((a, b) => a.order - b.order);

      // Add player board and player panel
      this.orderedPlayers.forEach((player, i) => {
        this.place('tplPlayerBoard', player, 'planetunknown-main-container');
        /*
        if (player.mapId) this.setupChangeBoardArrows(player.id);

        // Score counters
        $(`player_score_${player.id}`).insertAdjacentHTML(
          "beforebegin",
          `<span id="player_new_score_${player.id}"></span>`
        );

        // Panels
        this.place(
          "tplPlayerPanel",
          player,
          `overall_player_board_${player.id}`
        );
        $(`overall_player_board_${player.id}`).addEventListener("click", () =>
          this.goToPlayerBoard(player.id)
        );
        $(`player_name_${player.id}`).insertAdjacentHTML(
          "beforeend",
          '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM432 256c0 79.5-64.5 144-144 144s-144-64.5-144-144s64.5-144 144-144s144 64.5 144 144zM288 192c0 35.3-28.7 64-64 64c-11.5 0-22.3-3-31.6-8.4c-.2 2.8-.4 5.5-.4 8.4c0 53 43 96 96 96s96-43 96-96s-43-96-96-96c-2.8 0-5.6 .1-8.4 .4c5.3 9.3 8.4 20.1 8.4 31.6z"/></svg>'
        );
        $("workers-reserves").insertAdjacentHTML(
          "beforeend",
          `<div id='reserve-${player.id}' class='player-reserve'></div>`
        );
*/
        if (player.id == this.player_id) {
          $(`player-board-planet-${this.player_id}`).addEventListener('mouseover', (evt) => {
            if (evt.target.classList.contains('planet-grid-cell') && this.onHoverCell) {
              this.onHoverCell(evt.target);
            }
          });
          $(`player-board-planet-${this.player_id}`).addEventListener('click', (evt) => {
            if (!this.onClickCell) return;
            // Go up in the DOM tree until a zoo-map-cell is found
            let elt = evt.target;
            while (!elt.classList.contains('planet-grid-cell') && elt.id != `player-board-planet-${this.player_id}`) {
              elt = elt.parentNode;
            }

            if (elt.classList.contains('planet-grid-cell')) {
              this.onClickCell(elt);
            }
          });
        }
      });
      // this.setupPlayersCounters();
      // this.activateShowTileHelperButtons();
    },

    activateShowTileHelperButtons() {
      [...document.querySelectorAll('.tiles-helper-toggle')].forEach((elt) => {
        if (elt.id && this.tooltips[elt.id]) return;

        elt.addEventListener('click', function () {
          let isOpen = this.parentNode.classList.contains('open');
          this.parentNode.classList.toggle('open');
          if (isOpen) this.parentNode.classList.add('closedAnim');
        });
      });

      this.addTooltipToClass('tiles-helper-toggle', '', _('Click to show the list of possible enclosures'));
    },

    onChangeHandLocationSetting(v) {
      let hand = $(`hand-${this.player_id}`);
      let scoringHand = $(`scoring-hand-${this.player_id}`);
      if (hand) {
        let container = this.isFloatingHand() ? 'floating-hand' : `player-board-cards-${this.player_id}`;
        $(container).insertAdjacentElement('beforeend', hand);
        $(container).insertAdjacentElement('beforeend', scoringHand);
        $('floating-hand-wrapper').classList.toggle('active', this.isFloatingHand());
        hand.style.order = v == 1 ? 1 : 4;

        if (v == 3) {
          this.openHand();
        }
      }

      this.ensureNoSortableHandOnTouchDevice();
    },

    updateHandCards() {
      if (this.isSpectator) return;
      this.empty(`hand-${this.player_id}`);
      let hand = this.gamedatas.players[this.player_id].hand;
      hand.forEach((card) => {
        this.addZooCard(card);
      });

      this.empty(`scoring-hand-${this.player_id}`);
      let scoringHand = this.gamedatas.players[this.player_id].scoringHand;
      scoringHand.forEach((card) => {
        this.addZooCard(card);
      });
    },

    onChangePlayerBoardsLayoutSetting(v) {
      if (v == 0) {
        this.goToPlayerBoard(this.orderedPlayers[0].id);
      } else {
        this._focusedPlayer = null;
      }
    },

    goToPlayerBoard(pId, evt = null) {
      if (evt) evt.stopPropagation();

      let v = this.settings.playerBoardsLayout;
      if (v == 0) {
        // Tabbed view
        this._focusedPlayer = pId;
        [...$('planetunknown-main-container').querySelectorAll('.ark-player-board-resizable')].forEach((board) =>
          board.classList.toggle('active', board.id == `player-board-resizable-${pId}`)
        );
        [...$('planetunknown-main-container').querySelectorAll('.player-board-cards')].forEach((board) =>
          board.classList.toggle('active', board.id == `player-board-cards-${pId}`)
        );
        [...$('planetunknown-main-container').querySelectorAll('.player-board-action-cards-resizable')].forEach((board) =>
          board.classList.toggle('active', board.id == `action-cards-${pId}`)
        );
      } else if (v == 1) {
        // Multiple view
        this._focusedPlayer = null;
        window.scrollTo(0, $(`player-board-${pId}`).getBoundingClientRect()['top'] - 30);
      }
    },

    setupChangeBoardArrows(pId) {
      let leftArrow = $(`player-board-${pId}`).querySelector('.prev-player-board');
      if (leftArrow) leftArrow.addEventListener('click', () => this.switchPlayerBoard(-1));

      let rightArrow = $(`player-board-${pId}`).querySelector('.next-player-board');
      if (rightArrow) rightArrow.addEventListener('click', () => this.switchPlayerBoard(1));
    },

    getDeltaPlayer(pId, delta) {
      let playerOrder = this.orderedPlayers;
      let index = playerOrder.findIndex((elem) => elem.id == pId);
      if (index == -1) return -1;

      let n = playerOrder.length;
      return playerOrder[(((index + delta) % n) + n) % n].id;
    },

    switchPlayerBoard(delta) {
      let pId = this.getDeltaPlayer(this._focusedPlayer, delta);
      if (pId == -1) return;
      $(`player-board-${this._focusedPlayer}`).querySelector('.tiles-helper').classList.remove('open', 'closedAnim');
      this.goToPlayerBoard(pId);
    },

    tplPlanet(planet, player = null) {
      let pId = player == null ? 0 : player.id;

      // Create cells
      let planetGrid = `<div class='planet-grid'>`;
      for (let y = -1; y < 12; y++) {
        for (let x = -1; x < 12; x++) {
          let uid = x + '_' + y;
          let className = '';
          let content = '';
          let style = `grid-row: ${y + 2}; grid-column: ${x + 2}`;
          planetGrid += `<div class='planet-grid-cell${className}' style='${style}' data-x='${x}' data-y='${y}'></div>`;
        }
      }
      planetGrid += '</div>';

      return `<div class='planet' data-id='${planet.id}' id='planet-${pId}'>${planetGrid}</div>`;
    },

    tplPlayerBoard(player) {
      let planet = player.planetId ? this.tplPlanet(PLANETS_DATA[player.planetId], player) : '';
      let corporation = '';
      return `<div class='pu-player-board-resizable' id='player-board-resizable-${player.id}'>
          <div class='pu-player-board-planet' id='player-board-planet-${player.id}'>        
            ${planet}
          </div>
          <div class="pu-player-board-corporation" id='player-board-corporation-${player.id}'>
            ${corporation}
          </div>
        </div>`;
    },

    tplPlayerPanel(player) {
      return `<div class='player-info'>
      </div>`;
    },

    ////////////////////////////////////////////////////
    //   ____                  _
    //  / ___|___  _   _ _ __ | |_ ___ _ __ ___
    // | |   / _ \| | | | '_ \| __/ _ \ '__/ __|
    // | |__| (_) | |_| | | | | ||  __/ |  \__ \
    //  \____\___/ \__,_|_| |_|\__\___|_|  |___/
    //
    ////////////////////////////////////////////////////
    /**
     * Create all the counters for player panels
     */
    setupPlayersCounters() {
      return ''; // TODO

      this._playerCounters = {};
      this._playerCountersMeeples = {};
      this._scoreCounters = {};
      this.forEachPlayer((player) => {
        this._playerCounters[player.id] = {};
        this._playerCountersMeeples[player.id] = {};
        ALL_PLAYER_COUNTERS.forEach((res) => {
          let v = player[res];
          this._playerCounters[player.id][res] = this.createCounter(`counter-${player.id}-${res}`, v);

          if (COUNTER_MEEPLES.includes(res)) {
            this._playerCountersMeeples[player.id][res] = this.addMeeple({
              id: `${res}-${player.id}`,
              pId: player.id,
              type: 'cylinder',
              location: `${res}_${v}`,
            });
          }
        });
        this._scoreCounters[player.id] = this.createCounter('player_new_score_' + player.id, player.newScore);

        // DUPLICATED CYLINDER FOR CONSERVATION
        this._playerCountersMeeples[player.id]['conservation-duplicate'] = this.addMeeple({
          id: `conservation-duplicate-${player.id}`,
          pId: player.id,
          type: 'cylinder',
          location: `conservation-duplicate_${player.conservation}`,
        });

        // Worker counter
        if ($(`counter-${player.id}-worker`)) {
          this._playerCounters[player.id]['worker'] = this.createCounter(`counter-${player.id}-worker`, 0);
        }
      });
      this.updatePlayersCounters(false);
    },

    /**
     * Update all the counters in player panels according to gamedatas, useful for reloading
     */
    updatePlayersCounters(anim = true) {
      return ''; // TODO

      this.forEachPlayer((player) => {
        PLAYER_COUNTERS.forEach((res) => {
          let value = player[res];
          this._playerCounters[player.id][res].goTo(value, anim);

          // Slide meeples
          if (COUNTER_MEEPLES.includes(res)) {
            let meeple = this._playerCountersMeeples[player.id][res];
            let container = this.getMeepleContainer({
              location: `${res}_${value}`,
              pId: player.id,
            });
            if (meeple.parentNode != container) {
              if (anim) {
                this.slide(meeple, container);
              } else {
                dojo.place(meeple, container);
              }
            }

            // DUPLICATED CONSERVATION
            if (res == 'conservation') {
              meeple = this._playerCountersMeeples[player.id]['conservation-duplicate'];
              container = this.getMeepleContainer({
                location: `conservation-duplicate_${value}`,
                pId: player.id,
              });
              if (meeple.parentNode != container) {
                if (anim) {
                  this.slide(meeple, container);
                } else {
                  dojo.place(meeple, container);
                }
              }
            }
          }
        });
      });

      this.updateWorkerCounters(anim);
      this.updateDuplicateConservationBoard();
      this.updatePlayersIconsSummaries();
    },

    updateWorkerCounters(anim = true) {
      this.forEachPlayer((player) => {
        if (!this._playerCounters[player.id]['worker']) return;

        let pId = player.id;
        let workers = $(`reserve-${pId}`).querySelectorAll('.icon-worker').length;
        this._playerCounters[pId]['worker'].goTo(workers, anim);
      });
    },

    /**
     * Use this tpl for any counters that represent qty of meeples in "reserve", eg xtokens
     */
    tplResourceCounter(player, res, prefix = '') {
      return this.formatString(`
        <div class='player-resource resource-${res}'>
          <span id='${prefix}counter-${player.id}-${res}' 
            class='${prefix}resource-${res}'></span>${this.formatIcon(res)}
          <div class='reserve' id='${prefix}reserve-${player.id}-${res}'></div>
        </div>
      `);
    },

    /**
     * Animate a player counter receiving/loosing stuff
     */
    animatePlayerCounter(pId, type, n) {
      let oldVal = +this._playerCounters[pId][type].getValue();
      let newVal = oldVal + n;
      if (type == 'reputation' && newVal > 15) newVal = 15;
      if (type == 'xtoken' && newVal > 5) newVal = 5;
      if (oldVal == newVal) {
        return Promise.resolve();
      }

      let meeple = null,
        container = null;
      if (COUNTER_MEEPLES.includes(type)) {
        meeple = this._playerCountersMeeples[pId][type];
        container = this.getMeepleContainer({
          pId,
          type,
          location: `${type}_${newVal}`,
        });
      }

      if (this.isFastMode()) {
        this._playerCounters[pId][type].incValue(n);
        if (meeple !== null) {
          $(container).insertAdjacentElement('beforeend', meeple);

          if (type == 'conservation') {
            meeple = this._playerCountersMeeples[pId]['conservation-duplicate'];
            container = this.getMeepleContainer({
              location: `conservation-duplicate_${newVal}`,
              pId,
            });
            $(container).insertAdjacentElement('beforeend', meeple);
            this.updateDuplicateConservationBoard();
          }
        }
        return Promise.resolve();
      }

      let tmpElt = `<div style='position:absolute' id='animation-${type}'>${this.formatIcon(type, Math.abs(n))}</div>`;
      this.getVisibleTitleContainer().insertAdjacentHTML('beforebegin', tmpElt);
      let mobileId = `animation-${type}`;
      let counterId = `counter-${pId}-${type}`;

      if (meeple !== null) {
        this.slide(meeple, container);

        // DUPLICATE CONSERVATION
        if (type == 'conservation') {
          meeple = this._playerCountersMeeples[pId]['conservation-duplicate'];
          container = this.getMeepleContainer({
            location: `conservation-duplicate_${newVal}`,
            pId,
          });
          this.slide(meeple, container);
        }
      }

      if (n < 0) {
        // Loosing stuff
        this._playerCounters[pId][type].incValue(n);
        return this.slide(mobileId, this.getVisibleTitleContainer(), {
          from: counterId,
          destroy: true,
          phantom: false,
          duration: 1200,
        });
      } else {
        // Gaining stuff
        return this.slide(mobileId, counterId, {
          from: this.getVisibleTitleContainer(),
          destroy: true,
          phantom: false,
          duration: 1200,
        }).then(() => {
          this._playerCounters[pId][type].incValue(n);
          if (type == 'conservation') this.updateDuplicateConservationBoard();
        });
      }
    },

    notif_getBonuses(n) {
      debug('Notif: getting bonus/gaining resources', n);
      // Update counters promises
      let counters = Object.keys(n.args.bonuses);
      let promises = counters.map((type) => {
        if (type == 'source') return;

        let amount = n.args.bonuses[type];
        return this.animatePlayerCounter(n.args.player_id, type, amount);
      });

      // Callback
      Promise.all(promises).then(() => {
        if (n.args.score) {
          this._scoreCounters[n.args.player_id].toValue(n.args.score);
        }
        if (n.args.income) {
          this._playerCounters[n.args.player_id]['income'].toValue(n.args.income);
        }
        this.notifqueue.setSynchronousDuration(this.isFastMode() ? 0 : 100);
      });
    },

    notif_pilferingMoney(notif) {
      debug('Pilfering money', notif);
      let pId1 = notif.args.player_id;
      let pId2 = notif.args.player_id2;
      let type = 'money';
      let n = notif.args.bonuses.money;

      if (this.isFastMode()) {
        this._playerCounters[pId1][type].incValue(-n);
        this._playerCounters[pId2][type].incValue(n);
        return;
      }

      let tmpElt = `<div style='position:absolute' id='animation-${type}'>${this.formatIcon(type, Math.abs(n))}</div>`;
      this.getVisibleTitleContainer().insertAdjacentHTML('beforebegin', tmpElt);
      let mobileId = `animation-${type}`;
      let counterStartId = `counter-${pId1}-${type}`;
      let counterEndId = `counter-${pId2}-${type}`;

      this._playerCounters[pId1][type].incValue(-n);
      this.slide(mobileId, $(counterEndId), {
        from: counterStartId,
        destroy: true,
        phantom: false,
        duration: 1200,
      }).then(() => {
        this._playerCounters[pId2][type].incValue(n);
      });
    },

    //////////////////////////////////////////////////
    //  _____ _ _
    // |_   _(_) | ___  ___
    //   | | | | |/ _ \/ __|
    //   | | | | |  __/\__ \
    //   |_| |_|_|\___||___/
    //////////////////////////////////////////////////

    setupTiles() {
      // This function is refreshUI compatible
      let tileIds = this.gamedatas.tiles.map((tile) => {
        if (!$(`tile-${tile.id}`)) {
          this.addTile(tile);
        }

        let o = $(`tile-${tile.id}`);
        if (!o) return null;

        let container = this.getTileContainer(tile);
        if (o.parentNode != $(container)) {
          dojo.place(o, container);
        }
        this.updateTileObj(o, tile);

        return tile.id;
      });
      document.querySelectorAll('.tile-container').forEach((oTile) => {
        if (!tileIds.includes(parseInt(oTile.getAttribute('data-id')))) {
          this.destroy(oTile);
        }
      });
    },

    addTile(tile, container = null) {
      if (container === null) {
        container = this.getTileContainer(tile);
      }

      let o = this.place('tplTile', tile, container);
      if (tile.location == 'board') {
        this.placeTile(`tile-${tile.id}`, tile.x, tile.y);
      }
    },

    getTileContainer(tile) {
      if (tile.location == 'board') {
        return $(`planet-${tile.pId}`).querySelector('.planet-grid');
      } else if ($(tile.location)) {
        return $(tile.location);
      }

      console.error('Trying to get container of a tile', tile);
      return 'game_play_area';
    },

    tplTile(tile, id = null) {
      id = id || `tile-${tile.id}`;

      return `<div id="${id}" data-id="${tile.id}" class='tile-container' 
        data-type='${tile.type}' data-shape='${+tile.type % 12}' data-sprite='${parseInt(+tile.type / 48)}'
        data-state='${tile.state}' data-rotation='${tile.rotation}' data-flipped='${tile.flipped}'>
      <div class='tile-inner'></div>
      <div class='tile-border'></div>
      <div class='tile-crosshairs'>
        <svg><use href="#crosshairs-svg" /></svg>
      </div>
    </div>`;
    },

    // Place a tile at the correct grid position to make at pos (x,y)
    placeTile(tileId, x, y) {
      let col = parseInt(x) + 2;
      let row = parseInt(y) + 2;
      $(tileId).style.gridColumnStart = col;
      $(tileId).style.gridRowStart = row;
    },

    updateTileObj(o, tile) {
      ['state', 'rotation', 'flipped'].forEach((key) => {
        o.dataset[key] = tile[key];
      });
      if (tile.location == 'board') {
        this.placeTile(o, tile.x, tile.y);
      }
    },

    notif_placeTile(n) {
      debug('Notif: placing a tile', n);
      let toRemove = ['tile-controls', 'tile-hover', 'btnRotateClockwise', 'btnRotateCClockwise', 'btnFlip'];
      toRemove.forEach((eltId) => {
        if ($(eltId)) $(eltId).remove();
      });

      let tile = n.args.tile;
      let tileId = `tile-${tile.id}`;
      this.updateTileObj($(tileId), tile);
      this.slide($(tileId), this.getTileContainer(tile));
    },

    /////////////////////////////////////////////////////////////////////////////////
    //  ___                      ____
    // |_ _|___ ___  _ __  ___  / ___| _   _ _ __ ___  _ __ ___   __ _ _ __ _   _
    //  | |/ __/ _ \| '_ \/ __| \___ \| | | | '_ ` _ \| '_ ` _ \ / _` | '__| | | |
    //  | | (_| (_) | | | \__ \  ___) | |_| | | | | | | | | | | | (_| | |  | |_| |
    // |___\___\___/|_| |_|___/ |____/ \__,_|_| |_| |_|_| |_| |_|\__,_|_|   \__, |
    //                                                                      |___/
    /////////////////////////////////////////////////////////////////////////////////

    tplIconsSummary(player, zooMap = false) {
      let prefix = zooMap ? 'map-' : '';
      let iconSummary = `<div class="icons-summary" id="icons-summary-${prefix}${player.id}">`;
      ICONS_SUMMARY.forEach((summaryRow) => {
        iconSummary += '<div class="icons-summary-row">';
        summaryRow.forEach(
          (icon) =>
            (iconSummary += `<div class="icon-counter">
          <span id="icons-${prefix}${player.id}-${icon}"></span>
          <span class="badge-icon" data-type="${icon}"></span>
        </div>`)
        );
        iconSummary += '</div>';
      });
      iconSummary += '</div>';
      return iconSummary;
    },

    updatePlayersIconsSummaries() {
      this.forEachPlayer((player) => {
        ICONS_SUMMARY.forEach((summaryRow) => {
          summaryRow.forEach((icon) => {
            let val = player.icons[icon];
            let container = $(`icons-${player.id}-${icon}`);
            container.innerHTML = val;
            container.parentNode.classList.toggle('empty', val == 0);

            container = $(`icons-map-${player.id}-${icon}`);
            container.innerHTML = val;
            container.parentNode.classList.toggle('empty', val == 0);
          });
        });
      });
    },

    onChangePlayerIconSummarySetting(val) {
      this.updateIconSummaryPosition(this.player_id, val);
    },

    onChangeOpponentIconSummarySetting(val) {
      this.forEachPlayer((player) => {
        if (player.id != this.player_id) {
          this.updateIconSummaryPosition(player.id, val);
        }
      });
    },

    updateIconSummaryPosition(pId, val) {
      // val = 0 : zoo map
      // val = 1 : player panel
      // val = 2 : both
      let containerInPanel = $(`icons-summary-${pId}`);
      let containerInMap = $(`icons-summary-map-${pId}`);
      if (!containerInPanel || !containerInMap) return;

      // Player panel
      containerInPanel.classList.toggle('hidden', val == 0);
      // Zoo Map
      containerInMap.classList.toggle('hidden', val == 1);
    },

    notif_updateIncome(n) {
      debug('Notif: update income', n);
      this._playerCounters[n.args.player_id]['income'].toValue(n.args.income);
    },
  });
});
