define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
  function isVisible(elem) {
    return !!(elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length);
  }

  return declare('planetunknown.cards', null, {
    setupCards() {
      let neighbourObjectives = this.gamedatas.cards.NOCards;
      Object.keys(neighbourObjectives).forEach((cardId) => {
        let card = neighbourObjectives[cardId];
        this.addCard(card);

        // Switch pid1 and pid2 and create again
        card.uid = card.id + 'd';
        let tmp = card.pId2;
        card.pId2 = card.pId;
        card.pId = tmp;
        this.addCard(card);
      });

      this._fakeCardCounter = -2;
      this._handModals = {};
      this.orderedPlayers.forEach((player, i) => {
        this._handModals[player.id] = new customgame.modal('showCards' + player.id, {
          class: 'planetunknown_popin_cards',
          closeIcon: 'fa-times',
          title: _('Cards of ') + `<span style='color:#${player.color}'>${player.name}</span>`,
          closeAction: 'hide',
          verticalAlign: 'flex-start',
          contentsTpl: `<div class='modal-cards-holder' id='cards-${player.id}'></div>`,
        });
        this.onClick(`civ-cards-indicator-${player.id}`, () => this._handModals[player.id].show(), false);

        Object.values(player.playedCiv).forEach((card) => {
          this.addCard(card, `cards-${player.id}`);
        });

        Object.values(player.handCiv).forEach((card) => {
          this.addCard(card, `cards-${player.id}`);
        });
      });
    },

    updateHand() {
      let pId = this.player_id;
      let player = this.gamedatas.players[pId];
      this.empty(`cards-${pId}`);
      Object.values(player.playedCiv).forEach((card) => {
        this.addCard(card, `cards-${player.id}`);
      });

      Object.values(player.handCiv).forEach((card) => {
        this.addCard(card, `cards-${player.id}`);
      });
    },

    addCard(card, location = null) {
      card.uid = card.uid || card.id;
      if (card.uid == -1) card.uid = this._fakeCardCounter--;

      if ($('card-' + card.uid)) return;

      let o = this.place('tplCard', card, location == null ? this.getCardContainer(card) : location);
      let tooltipDesc = this.getCardTooltip(card);
      if (tooltipDesc != null) {
        this.addCustomTooltip(o.id, tooltipDesc.map((t) => this.formatString(t)).join('<br/>'));
      }

      return o;
    },

    getCardTooltip(card) {
      if (card.id < 0) {
        if (card.type == 'civCard') {
          return [this.fsr(_('Civ Card of level ${lvl}'), { lvl: card.level })];
        }
        return [_('TODO')];
      }

      return [_(card.title), _(card.desc)];
    },

    tplCard(card) {
      let uid = card.uid || card.id;
      let level = '';
      if (card.level) level = `data-level="${card.level}"`;

      return `<div id="card-${uid}" data-type="${card.type}" class="planetunknown-card ${card.id < 0 ? 'fake' : ''}">
        <div class='card-inner' data-id="${card.id}" ${level}></div>
      </div>`;
    },

    getCardContainer(card) {
      let t = card.location.split('_');
      if (card.location == 'trash') {
        return this.getVisibleTitleContainer();
      }
      if (card.type == 'NOCard' && card.location == 'NOCards') {
        let pId1 = card.pId,
          pId2 = card.pId2;

        // pId2 is sitting at the right of pId1
        if (this.getDeltaPlayer(pId1, 1) == pId2) {
          return $(`next-objectives-${pId1}`);
        } else {
          return $(`prev-objectives-${pId1}`);
        }
      }

      console.error('Trying to get container of a meeple', meeple);
      return 'game_play_area';
    },

    notif_takeCivCard(n) {
      debug('Notif: take civ card', n);
      if ($('planetunknown-choose-card-footer')) $('planetunknown-choose-card-footer').remove();

      let pId = n.args.player_id;
      let card = n.args.card;

      let oCard = null;
      // Private notif
      if (card.id < 0) {
        this.addCard(card, 'planetunknown-main-container');
        oCard = $(`card-${card.uid}`);
      }
      // Public notif
      else {
        if (!$(`card-${card.id}`)) this.addCard(card, 'planetunknown-main-container');
        oCard = $(`card-${card.id}`);
      }
      console.log(oCard);

      this.slide(oCard, `civ-cards-indicator-${pId}`).then(() => {
        dojo.place(oCard, `cards-${pId}`);
        let counter = card.location == 'playedCivCards' ? 'playedCivCount' : 'handCivCount';
        this.gamedatas.players[pId][counter]++;
        this._playerCounters[pId][counter].incValue(1);
      });
    },
  });
});
