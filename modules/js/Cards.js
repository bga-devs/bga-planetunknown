define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
  function isVisible(elem) {
    return !!(elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length);
  }

  return declare('planetunknown.cards', null, {
    setupCards() {
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
      if ($('card-' + card.id)) return;

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
      return `<div id="card-${card.id}" class="planetunknown-card" data-id="${card.id}"></div>`;
    },

    getCardContainer(meeple) {
      let t = meeple.location.split('_');
      if (meeple.location == 'trash') {
        return this.getVisibleTitleContainer();
      }
      // Things on the planet
      if (meeple.location == 'planet') {
        return this.getPlanetCell(meeple.pId, meeple.x, meeple.y);
      }
      // Rover in reserve
      if (meeple.type == 'rover-meeple' && meeple.location == 'corporation') {
        return $(`rover-reserve-${meeple.pId}`);
      }
      // Meteor in reserve
      if (meeple.type == 'meteor' && meeple.location == 'corporation') {
        return $(`meteor-reserve-${meeple.pId}`);
      }
      // Things on tracks
      if (meeple.location == 'corporation') {
        return $(`corporation-${meeple.pId}-${meeple.x}-${meeple.y}`);
      }

      console.error('Trying to get container of a meeple', meeple);
      return 'game_play_area';
    },
  });
});
