let $ = (elem) => document.getElementById(elem);

function formatIcon(name, n = null, lowerCase = true) {
  let type = lowerCase ? name.toLowerCase() : name;
  const BADGE_ICONS = [
    "africa",
    "europe",
    "asia",
    "americas",
    "australia",
    "bird",
    "predator",
    "herbivore",
    "bear",
    "reptile",
    "pet",
    "primate",
    "science",
    "partner",
  ];
  if (BADGE_ICONS.includes(type)) {
    let ftype = type[0].toUpperCase() + type.slice(1);
    return `<div class="icon-container icon-container-${type}">
      <div class="planetunknown-icon badge-icon" data-type="${ftype}"></div>
    </div>`;
  }

  const NO_TEXT_ICONS = ["xtoken", "Clever"];
  let noText = NO_TEXT_ICONS.includes(name);
  let text = n == null ? "" : `<span>${n}</span>`;
  return `${
    noText ? text : ""
  }<div class="icon-container icon-container-${type}">
          <div class="planetunknown-icon icon-${type}">${
    noText ? "" : text
  }</div>
        </div>`;
}

function formatBonus(bonus, bonusType = "bonus") {
  let iconsWithText = ["money", "reputation", "conservation"];
  let type = Object.keys(bonus)[0],
    n = iconsWithText.includes(type) ? bonus[type] : null;
  if (type == "FullThroated") type = "add-worker";
  if (["xtoken", "Clever", "Pouch"].includes(type) && bonus[type] > 1)
    n = bonus[type];

  return `<div class='planetunknown-bonus-container'>
      <div class='planetunknown-bonus planetunknown-icon icon-bonus ${bonusType}-type'>
        ${this.formatIcon(type, n)}
      </div>
    </div>`;
}

function tplZooMap(map, player = null) {
  let pId = player == null ? 0 : player.id;

  // Create cells
  let zooBoard = `<div class='zoo-board'>`;
  let dim = { x: 9, y: 7 };
  for (let x = 0; x < dim.x; x++) {
    let size = dim.y - (x % 2 == 0 ? 1 : 0);
    for (let y = 0; y < size; y++) {
      let row = 2 * y + (x % 2 == 0 ? 1 : 0);
      let style = `grid-row: ${row + 1} / span 2; grid-column: ${
        3 * x + 1
      } / span 4`;

      let uid = x + "_" + row;
      let className = "";
      let content = "";
      if (map.terrains.Rock.includes(uid)) {
        className += " rock";
      }
      if (map.terrains.Water.includes(uid)) {
        className += " water";
      }
      if (map.upgradeNeeded.includes(uid)) {
        className += " upgradeNeeded";
        content = "<div class='upgradeNeeded-marker'></div>";
      }
      if (map.bonuses[uid]) {
        className += " bonus";
        content += this.formatBonus(map.bonuses[uid]);
      }
      zooBoard += `<div class='zoo-map-cell${className}' style='${style}' data-x='${x}' data-y='${row}'>${content}</div>`;
      // zooBoard += `<div class='zoo-map-cell${className}' style='${style}' data-x='${x}' data-y='${row}'>${x}_${row}</div>`;
    }
  }
  zooBoard += "</div>";

  // Bonus spaces
  let bonusSpacesIncome = "";
  let bonusSpaceImmediate = "";
  map.bonusSpaces.forEach((space, i) => {
    let tpl = `<div class='bonus-space'>
        <div class='cube-holder' id='bonus-${pId}-${i}'></div>
        ${formatBonus(space.bonus, space.type)}
      </div>`;

    if (space.type == "income") bonusSpacesIncome += tpl;
    else bonusSpaceImmediate += tpl;
  });

  // Partner zoos
  let partnerZoos = "";
  for (let i = 4; i > 0; i--) {
    let bonus = map.partnerZooBonuses[i]
      ? formatBonus(map.partnerZooBonuses[i])
      : "";
    partnerZoos += `<div class='partner-zoo-space'>
        <div class='planetunknown-icon icon-partner-zoo icon-background'></div>
        <div class='space-counter'>${i}</div>
        ${bonus}
        <div class='partner-zoo-holder' id='partner-${pId}-${i}'></div>
      </div>`;
  }

  // Universities
  let universities = "";
  for (let i = 3; i > 0; i--) {
    let bonus = map.facBonuses[i] ? formatBonus(map.facBonuses[i]) : "";
    universities += `<div class='fac-space'>
        <div class='planetunknown-icon icon-fac icon-background'></div>
        <div class='space-counter'>${i}</div>
        ${bonus}
        <div class='fac-holder' id='university-${pId}-${i}'></div>
      </div>`;
  }

  // Workers
  let workers = "";
  for (let i = 1; i <= 3; i++) {
    let bonus =
      i == 3 && map.lastWorkerBonus ? formatBonus(map.lastWorkerBonus) : "";
    workers += `<div class='worker-space'>
        <div class='planetunknown-icon icon-bordered-worker icon-background'></div>
        <div class='space-counter'>${i}</div>
        ${bonus}
        <div class='worker-holder' id='worker-${pId}-${i}'></div>
      </div>`;
  }

  // Basic player infos
  let playerInfos = "";

  return `<div class='zoo-map' id='zoo-map-${pId}'>
        <div class='map-infos'>
          <div class='player-infos'>
            ${playerInfos}
            <div id='reserve-${pId}' class='player-reserve'></div>
          </div>
          <div class='map-name'></div>
        </div>
        <div class='zoo-map-bonus-spaces'>
          <div class='zoo-map-workers'>
            ${workers}
          </div>
          <div class='bonus-spaces-income ${player == null ? "preview" : ""}'>
            <div class='planetunknown-icon icon-immediate-income'></div>
            ${bonusSpacesIncome}
          </div>
          <div class='planetunknown-icon icon-place-cube'></div>
          <div class='bonus-spaces-immediate ${
            player == null ? "preview" : ""
          }'>
            <div class='planetunknown-icon icon-bordered-immediate'></div>
            ${bonusSpaceImmediate}
          </div>
        </div>

        <div class='zoo-map-board'>
          <div class='zoo-map-board-border'>
            <div class='zoo-map-board-container'>
              <div class='zoo-map-board-background' data-map='${map.id}'>
              ${zooBoard}
              </div>
            </div>
          </div>
        </div>

        <div class='zoo-map-association'>
          <div class='zoo-map-partner-zoos'>
            ${partnerZoos}
          </div>
          <div class='zoo-map-universities'>
            ${universities}
          </div>
        </div>
      </div>`;
}

const possibleMaps = Object.keys(MAPS_DATA);

let selectMap = (mapId) => {
  let container = $(`wrapper`);
  container.innerHTML = "";
  container.insertAdjacentHTML("afterbegin", tplZooMap(MAPS_DATA[mapId]));
};

possibleMaps.forEach((mapId) => {
  $("buttons").insertAdjacentHTML(
    "beforeend",
    `<button id="btn-${mapId}">${mapId}</button>`
  );
  $(`btn-${mapId}`).addEventListener("click", () => selectMap(mapId));
});
