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
      //   // This function is refreshUI compatible
      //   let meepleIds = this.gamedatas.meeples.map((meeple) => {
      //     if (!$(`meeple-${meeple.id}`)) {
      //       this.addMeeple(meeple);
      //     }
      //     let o = $(`meeple-${meeple.id}`);
      //     if (!o) return null;
      //     let container = this.getMeepleContainer(meeple);
      //     if (o.parentNode != $(container)) {
      //       dojo.place(o, container);
      //     }
      //     o.dataset.state = meeple.state;
      //     return meeple.id;
      //   });
      //   document.querySelectorAll('.planetunknown-meeple[id^="meeple-"]').forEach((oMeeple) => {
      //     if (!meepleIds.includes(parseInt(oMeeple.getAttribute('data-id'))) && oMeeple.getAttribute('data-type') != 'cylinder') {
      //       this.destroy(oMeeple);
      //     }
      //   });
      //   this.updatePlayersCounters();
    },

    addCard(card, location = null) {
      card.uid = card.uid || card.id;
      if ($('card-' + card.uid)) return;

      let o = this.place('tplCard', card, location == null ? this.getCardContainer(card) : location);
      let tooltipDesc = this.getCardTooltip(card);
      if (tooltipDesc != null) {
        this.addCustomTooltip(o.id, tooltipDesc.map((t) => this.formatString(t)).join('<br/>'));
      }

      return o;
    },

    getCardTooltip(card) {
      return [_(card.title), _(card.desc)];
    },

    tplCard(card) {
      let uid = card.uid || card.id;
      return `<div id="card-${uid}" data-type="${card.type}" class="planetunknown-card">
        <div class='card-inner' data-id="${card.id}"></div>
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
  });
});
