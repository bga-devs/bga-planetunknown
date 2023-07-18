define(["dojo", "dojo/_base/declare"], (dojo, declare) => {
  function isVisible(elem) {
    return !!(
      elem.offsetWidth ||
      elem.offsetHeight ||
      elem.getClientRects().length
    );
  }

  return declare("planetunknown.meeples", null, {
    setupMeeples() {
      // This function is refreshUI compatible
      let meepleIds = this.gamedatas.meeples.map((meeple) => {
        if (!$(`meeple-${meeple.id}`)) {
          this.addMeeple(meeple);
        }

        let o = $(`meeple-${meeple.id}`);
        if (!o) return null;

        let container = this.getMeepleContainer(meeple);
        if (o.parentNode != $(container)) {
          dojo.place(o, container);
        }
        o.dataset.state = meeple.state;

        return meeple.id;
      });
      document
        .querySelectorAll('.planetunknown-meeple[id^="meeple-"]')
        .forEach((oMeeple) => {
          if (
            !meepleIds.includes(parseInt(oMeeple.getAttribute("data-id"))) &&
            oMeeple.getAttribute("data-type") != "cylinder"
          ) {
            this.destroy(oMeeple);
          }
        });
      this.updatePlayersCounters();
    },

    addMeeple(meeple, location = null) {
      if ($("meeple-" + meeple.id)) return;

      let o = this.place(
        "tplMeeple",
        meeple,
        location == null ? this.getMeepleContainer(meeple) : location
      );
      let tooltipDesc = this.getMeepleTooltip(meeple);
      if (tooltipDesc != null) {
        this.addCustomTooltip(
          o.id,
          tooltipDesc.map((t) => this.formatString(t)).join("<br/>")
        );
      }

      return o;
    },

    getMeepleTooltip(meeple) {
      let type = meeple.type;
      if (type == "Venom") {
        return [
          _("Venom token."),
          _("After using an Action card with Venom token, discard the token."),
          _(
            "If you did not discard a Venom token during your turn, and there is still a Venom token on at least one of your Action cards, pay <MONEY:2>."
          ),
          _("In the next break remove all Venom tokens."),
        ];
      }
      if (type == "Constriction") {
        return [
          _("Constriction token."),
          _(
            "Strength of an Action card with Constriction token is decreased by 2."
          ),
          _(
            "After using an Action card with a Constriction token, discard the token."
          ),
          _("In the next break remove all Constriction tokens."),
        ];
      }
      if (type == "Multiplier") {
        return [
          _("Multiplier token."),
          _(
            "The next time you execute the action, you may execute it twice. That is, you execute it twice in a row, each time with the same strength X, before placing the Action card in slot 1."
          ),
          _(
            "You may use X-tokens to strengthen the actions, but each X-token counts for only 1 of the two actions, not for both. You may choose for each of the two actions what you use the action for."
          ),
          _("Return the Multiplier token to the supply after use."),
          _(
            "If you have not used the token before the next break, you must return it unused."
          ),
        ];
      }
      return null;
    },

    tplMeeple(meeple) {
      let type = meeple.type.charAt(0).toLowerCase() + meeple.type.substr(1);
      const PERSONAL = ["token", "cylinder", "worker"];
      let color = PERSONAL.includes(type)
        ? ` data-color="${this.getPlayerColor(meeple.pId)}" `
        : "";
      return `<div class="planetunknown-meeple planetunknown-icon icon-${type}" id="meeple-${meeple.id}" data-id="${meeple.id}" data-type="${type}" data-state="${meeple.state}" ${color}></div>`;
    },

    getPlayerColor(pId) {
      if (this.gamedatas.players[pId]) {
        return this.gamedatas.players[pId].color;
      } else {
        let colors = [
          "1863a5",
          "b91b1b",
          "d1c81c",
          "000000",
          "30a638",
          "7f4e30",
          "5a5856",
          "ffffff",
          "c028d3",
          "cb7b19",
        ];
        let usedColors = this.getPlayers().map((p) => p.color);
        return colors.find((color) => !usedColors.includes(color));
      }
    },

    getMeepleContainer(meeple) {
      let t = meeple.location.split("_");
      // Workers in reserve
      if (meeple.location == "reserve") {
        return $(`reserve-${meeple.pId}`);
      }
      // Cubes on bonus spaces of zoo map
      else if (t[0] == "bonus") {
        return $(`bonus-${meeple.pId}-${t[1]}`);
      }
      // Cylinders on rep track
      else if (t[0] == "reputation") {
        return $(`reputation-${t[1]}-holder`);
      }
      // Cylinders on conservation track
      else if (t[0] == "conservation") {
        return $(`conservation-${t[1]}`);
      }
      // Cylinders on duplicated conservation track
      else if (t[0] == "conservation-duplicate") {
        return t[1] <= 10
          ? $(`conservation-duplicate-${t[1]}`)
          : $("conservation-duplicate-off");
      }
      // Cylinders on appeal track
      else if (t[0] == "appeal") {
        return $(`appeal-${t[1]}`);
      }
      // Tokens on solo tile
      else if (t[0] == "solo") {
        return $(`solo-tile-${t[1]}-${t[2]}`);
      }
      // Association board
      else if (t[0] == "association") {
        let o = $(meeple.location + "_" + meeple.type);
        if (o) return o;
        return $(meeple.location);
      }
      //// Player board ////
      // Workers in supply
      else if (t[0] == "supply") {
        return $(`worker-${meeple.pId}-${t[1]}`);
      }
      // Partner zoos
      else if (t[0] == "partner") {
        return $(`partner-${meeple.pId}-${t[1]}`);
      }
      // Universities
      else if (t[0] == "university") {
        return $(`university-${meeple.pId}-${t[1]}`);
      }
      //// Meeple on cards ////
      else if (t[0] == "actionCard") {
        return $(`action-card-${t[1]}`).querySelector(".meeples-container");
      } else if ($(`card-${meeple.location}`)) {
        let o = $(`card-${meeple.location}`);
        return o.classList.contains("sponsor-card")
          ? o.querySelector(".ark-card-wrapper")
          : o;
      } else if ($(meeple.location)) {
        return $(meeple.location);
      }

      console.error("Trying to get container of a meeple", meeple);
      return "game_play_area";
    },

    setupAssociationBoard() {
      [0, 2, 3, 4, 5].forEach((slot) => {
        let content = "";
        // Slot for workers
        if (slot != 0) {
          content += `<div id='association_${slot}' class='worker-slot'></div>`;
        }
        // Donations
        else {
          content += `<div id='association-donation'>`;
          for (let i = -1; i < 8; i++) {
            content += `<div id='association_${slot}_${i}'></div>`;
          }
          content += "</div>";
        }
        // Zoos
        if (slot == 3) {
          ["Africa", "Europe", "Asia", "Americas", "Australia"].forEach(
            (continent) => {
              content += `<div id='association_${slot}_partner-${continent}' class='continent-slot'></div>`;
            }
          );
        }
        // Universities
        if (slot == 4) {
          ["fac-rep-hand", "fac-science-rep", "fac-science-science"].forEach(
            (fac) => {
              content += `<div id='association_${slot}_${fac}' class='fac-slot'></div>`;
            }
          );
        }

        $("association-board").insertAdjacentHTML(
          "beforeend",
          `<div id="association-board-${slot}">${content}</div>`
        );
      });
    },

    /**
     * Wrap the sliding animations
     */
    slideResources(meeples, configFn, syncNotif = true) {
      let fakeId = -1; // Used for virtual meeple that will get destroyed after animation (eg SCORE)
      let needUpdateForActionCards = false;
      let workerMoved = false;
      let promises = meeples.map((resource, i) => {
        if (resource.type == "worker") workerMoved = true;

        // Get config for this slide
        let config =
          typeof configFn === "function"
            ? configFn(resource, i)
            : Object.assign({}, configFn);
        if (resource.destroy) {
          resource.id = fakeId--;
          config.destroy = true;
        }

        // Need update for action cards summaries ?
        if (["Multiplier", "Constriction", "Venom"].includes(resource.type)) {
          needUpdateForActionCards = true;
        }

        // Default delay if not specified
        let delay = config.delay ? config.delay : 100 * i;
        config.delay = 0;
        // Use meepleContainer if target not specified
        let target = config.target
          ? config.target
          : this.getMeepleContainer(resource);
        if (!isVisible(target)) {
          config.to = $(`overall_player_board_${resource.pId}`);
        }

        // Slide it
        let slideIt = () => {
          // Create meeple if needed
          if (!$("meeple-" + resource.id)) {
            this.addMeeple(resource);
          }

          // Slide it
          return this.slide("meeple-" + resource.id, target, config);
        };

        if (this.isFastMode()) {
          slideIt();
          return null;
        } else {
          return this.wait(delay - 10).then(slideIt);
        }
      });

      let endCallback = () => {
        if (workerMoved) {
          this.updateWorkerCounters();
        }
        if (needUpdateForActionCards) {
          this.updateActionCardsSummaries();
        }
        if (syncNotif) {
          this.notifqueue.setSynchronousDuration(this.isFastMode() ? 0 : 10);
        }
      };

      if (this.isFastMode()) {
        endCallback();
        return Promise.resolve();
      } else
        return Promise.all(promises)
          .then(() => this.wait(10))
          .then(endCallback);
    },

    notif_silentKill(n) {
      debug("Silent kill", n);
      debug("TODO");
    },

    notif_addMeeples(n) {
      debug("Notif: adding & sliding meeples", n);
      this.slideResources(n.args.meeples, {
        from: this.getVisibleTitleContainer(),
      });
    },

    notif_donation(n) {
      debug("Notif: making a donation", n);

      if (n.args.meeple) {
        this.slideResources(
          [n.args.meeple],
          {
            from: this.getVisibleTitleContainer(),
          },
          false
        ).then(() => {
          n.args.bonuses.conservation = 1;
          this.notif_getBonuses(n);
        });
      } else {
        n.args.bonuses.conservation = 1;
        this.notif_getBonuses(n);
      }
      this._scoreCounters[n.args.player_id].toValue(n.args.score);
    },

    notif_slideMeeples(n) {
      debug("Notif: sliding meeples", n);
      this.slideResources(n.args.meeples).then(() => this.updateCardCosts());

      if (n.args.icons) {
        this.gamedatas.players[n.args.player_id].icons = n.args.icons;
        this.updatePlayersIconsSummaries();
      }
    },

    notif_enableMultiplier(n) {
      debug("Notif: enabling multiplier", n);
      n.args.meepleIds.forEach((mId) => ($(`meeple-${mId}`).dataset.state = 1));
      this.updateActionCardsSummaries();
    },

    notif_discardTokens(n) {
      debug("Notif: discard a token", n);
      this.slideResources(n.args.meeples, {
        destroy: true,
        to: this.getVisibleTitleContainer(),
        phantom: false,
      });
    },
  });
});
