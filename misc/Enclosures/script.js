const WIDTH = document.getElementById('canvas-container').offsetWidth;
const HEIGHT = document.getElementById('canvas-container').offsetHeight;
const ANGLE = (2 * Math.PI) / 6;
const SCALE = 1;
const SIZE = SCALE * 136;
const CLIP_PATH_MARGIN = 5 / 100;

let DRAW_GRID = document.getElementById('checkbox-grid').checked;
let CROP = document.getElementById('checkbox-crop').checked;

const OFFSET_X = SIZE / 2;
///////////////////////////////////////////////////////////////////
//  ___                                   ____        _
// |_ _|_ __ ___   __ _  __ _  ___  ___  |  _ \  __ _| |_ __ _
//  | || '_ ` _ \ / _` |/ _` |/ _ \/ __| | | | |/ _` | __/ _` |
//  | || | | | | | (_| | (_| |  __/\__ \ | |_| | (_| | || (_| |
// |___|_| |_| |_|\__,_|\__, |\___||___/ |____/ \__,_|\__\__,_|
//                      |___/
///////////////////////////////////////////////////////////////////

// V1 : just putting them side by side
// let imagesConfiguration = {"back-1":{"x":0,"y":0},"back-2":{"x":"0","y":"245"},"back-3":{"x":"325","y":0},"back-4":{"x":"860","y":0},"back-5":{"x":"1390","y":"0"},"front-1":{"x":"0","y":"776"},"front-2":{"x":"0","y":"1070"},"front-3":{"x":"330","y":"533"},"front-4":{"x":"860","y":"652"},"front-5":{"x":"1390","y":"771"},"large-bird-aviary":{"x":"867","y":"1521"},"petting-zoo":{"x":"331","y":"1065"},"reptile-house":{"x":"0","y":"1715"}};

// V2 : with the hex grid
//let imagesConfiguration = {  'back-1': { x: '-3', y: '9' },  'back-2': { x: '-9', y: '245' },  'back-3': { x: '198', y: '120' },  'back-4': { x: '599', y: '136' },  'back-5': { x: '1007', y: '12' },  'front-1': { x: '-4', y: '715' },  'front-2': { x: '-4', y: '951' },  'front-3': { x: '196', y: '592' },  'front-4': { x: '602', y: '598' },  'front-5': { x: '1020', y: '719' },  'large-bird-aviary': { x: '608', y: '1296' },  'petting-zoo': { x: '201', y: '951' },  'reptile-house': { x: '0', y: '1421' },};

// V3 : with the crop
// prettier-ignore
//let imagesConfiguration = {"back-1":{"x":"60","y":"-2"},"back-2":{"x":"57","y":"235"},"back-3":{"x":"265","y":"117"},"back-4":{"x":"668","y":"113"},"back-5":{"x":"1073","y":"-1"},"front-1":{"x":"60","y":"701"},"front-2":{"x":"56","y":"935"},"front-3":{"x":"262","y":"576"},"front-4":{"x":"663","y":"573"},"front-5":{"x":"1070","y":"702"},"large-bird-aviary":{"x":"664","y":"1285"},"petting-zoo":{"x":"266","y":"936"},"reptile-house":{"x":"60","y":"1399"}};

// V4 : with the X-offset
// prettier-ignore
//let imagesConfiguration = {"back-1":{"x":"0","y":"-2"},"back-2":{"x":"-3","y":"235"},"back-3":{"x":"203","y":"117"},"back-4":{"x":"607","y":"113"},"back-5":{"x":"1013","y":"-1"},"front-1":{"x":"0","y":"701"},"front-2":{"x":"-3","y":"935"},"front-3":{"x":"202","y":"576"},"front-4":{"x":"607","y":"573"},"front-5":{"x":"1013","y":"702"},"large-bird-aviary":{"x":"604","y":"1285"},"petting-zoo":{"x":"197","y":"936"},"reptile-house":{"x":"0","y":"1399"}}

// V5 : adding kiosk / changing grid size
// prettier-ignore
//{"back-1":{"x":"0","y":"-2"},"back-2":{"x":"-3","y":"235"},"back-3":{"x":"203","y":"117"},"back-4":{"x":"607","y":"113"},"back-5":{"x":"1013","y":"-1"},"front-1":{"x":"0","y":"701"},"front-2":{"x":"-3","y":"935"},"front-3":{"x":"202","y":"576"},"front-4":{"x":"607","y":"573"},"front-5":{"x":"1013","y":"709"},"large-bird-aviary":{"x":"604","y":"1290"},"petting-zoo":{"x":"191","y":"945"},"reptile-house":{"x":"0","y":"1399"},"kiosk":{"x":"372","y":"433"},"monkey":{"x":"1400","y":"88"},"meerkat":{"x":"372","y":"1031"},"owl":{"x":"1393","y":"323"},"sea-turtle":{"x":"1192","y":"1028"},"okapi":{"x":"984","y":"1504"},"adventure":{"x":"1597","y":"911"},"pavilion":{"x":"989","y":"568"},"penguin":{"x":"1601","y":"-27"}}

// V6 : with special enclosures
// prettier-ignore
//{"back-1":{"x":"-29","y":"-28"},"back-2":{"x":"-29","y":"213"},"back-3":{"x":"178","y":"97"},"back-4":{"x":"587","y":"94"},"back-5":{"x":"995","y":"-29"},"front-1":{"x":"-29","y":"682"},"front-2":{"x":"-29","y":"917"},"front-3":{"x":"178","y":"555"},"front-4":{"x":"587","y":"563"},"front-5":{"x":"993","y":"683"},"large-bird-aviary":{"x":"582","y":"1269"},"petting-zoo":{"x":"177","y":"921"},"reptile-house":{"x":"-31","y":"1387"},"kiosk":{"x":"372","y":"433"},"monkey":{"x":"1400","y":"88"},"meerkat":{"x":"372","y":"1031"},"owl":{"x":"1393","y":"323"},"sea-turtle":{"x":"1192","y":"1028"},"okapi":{"x":"984","y":"1504"},"adventure":{"x":"1597","y":"911"},"pavilion":{"x":"989","y":"568"},"penguin":{"x":"1602","y":"-27"},"aquarium":{"x":"1800","y":"438"},"polar-bear":{"x":"-36","y":"1738"},"hyena":{"x":"1796","y":"673"},"zoo-school":{"x":"782","y":"1738"},"baboon":{"x":"1800","y":"1148"},"water-playground":{"x":"1393","y":"1739"},"entrance":{"x":"1800","y":"1739"},"cable":{"x":"2200","y":"1028"}};

// V7 : with promo content
// prettier-ignore
let imagesConfiguration = JSON.parse(localStorage.getItem('imagesConfiguration')) ?? {"back-1":{"x":"-29","y":"-28"},"back-2":{"x":"-29","y":"213"},"back-3":{"x":"178","y":"97"},"back-4":{"x":"587","y":"94"},"back-5":{"x":"995","y":"-29"},"front-1":{"x":"-29","y":"682"},"front-2":{"x":"-29","y":"917"},"front-3":{"x":"178","y":"555"},"front-4":{"x":"587","y":"563"},"front-5":{"x":"993","y":"683"},"large-bird-aviary":{"x":"582","y":"1269"},"petting-zoo":{"x":"177","y":"921"},"reptile-house":{"x":"-31","y":"1387"},"kiosk":{"x":"372","y":"433"},"monkey":{"x":"1400","y":"88"},"meerkat":{"x":"372","y":"1031"},"owl":{"x":"1393","y":"323"},"sea-turtle":{"x":"1192","y":"1028"},"okapi":{"x":"984","y":"1504"},"adventure":{"x":"1597","y":"911"},"pavilion":{"x":"989","y":"568"},"penguin":{"x":"1602","y":"-27"},"aquarium":{"x":"1800","y":"438"},"polar-bear":{"x":"-36","y":"1738"},"hyena":{"x":"1796","y":"673"},"zoo-school":{"x":"782","y":"1738"},"baboon":{"x":"1800","y":"1148"},"water-playground":{"x":"1393","y":"1739"},"entrance":{"x":"1800","y":"1739"},"cable":{"x":"2200","y":"1028"},"arcade":{"x":"805","y":"-8"},"victory":{"x":"400","y":"-15"}}

let images = [
  'back-1',
  'back-2',
  'back-3',
  'back-4',
  'back-5',
  'front-1',
  'front-2',
  'front-3',
  'front-4',
  'front-5',
  'large-bird-aviary',
  'petting-zoo',
  'reptile-house',
  'kiosk',
  'monkey',
  'meerkat',
  'owl',
  'sea-turtle',
  'okapi',
  'adventure',
  'pavilion',
  'penguin',
  'aquarium',
  'polar-bear',
  'hyena',
  'zoo-school',
  'baboon',
  'water-playground',
  'entrance',
  'cable',
  'arcade',
  'victory',
];

// For each image, starting at the corner, give the list of hex that should be kept
let imagesHex = {
  'back-1': ['1_1'],
  'back-2': ['1_1', '1_3'],
  'back-3': ['1_1', '1_3', '2_2'],
  'back-4': ['1_3', '1_1', '2_2', '2_4'],
  'back-5': ['2_4', '1_3', '1_5', '2_2', '2_6'],
  'front-1': ['1_1'],
  'front-2': ['1_1', '1_3'],
  'front-3': ['1_1', '1_3', '2_2'],
  'front-4': ['1_3', '1_1', '2_2', '2_4'],
  'front-5': ['2_4', '1_3', '1_5', '2_2', '2_6'],
  'large-bird-aviary': ['2_2', '1_1', '1_3', '2_4', '3_1'],
  'petting-zoo': ['1_3', '1_5', '2_2'],
  'reptile-house': ['2_2', '1_1', '1_3', '3_1', '3_3'],
  kiosk: ['1_1'],
  pavilion: ['1_1'],
  monkey: ['2_2', '1_1', '2_4', '2_6'],
  meerkat: ['2_2', '1_3', '3_3'],
  owl: ['1_3', '1_1', '1_5'],
  'sea-turtle': ['2_4', '1_5', '2_2', '3_5'],
  okapi: ['3_1', '2_2', '1_1', '4_2'],
  adventure: ['1_1', '1_3'],
  penguin: ['2_2', '1_1', '3_1', '3_3'],
  aquarium: ['2_2', '1_3', '3_3', '3_5'],
  'polar-bear': ['2_2', '1_3', '3_3', '4_2'],
  hyena: ['1_3', '1_5', '1_7', '2_2'],
  'zoo-school': ['2_2', '1_3', '3_3'],
  baboon: ['2_4', '1_5', '2_2', '2_6'],
  'water-playground': ['1_1', '2_2'],
  entrance: ['1_1', '2_2'],
  cable: ['1_3', '1_1', '1_5', '1_7'],
  arcade: ['1_1'],
  victory: ['1_1'],
};

////////////////////////
//  ___       _ _
// |_ _|_ __ (_) |_
//  | || '_ \| | __|
//  | || | | | | |_
// |___|_| |_|_|\__|
////////////////////////

// Create the canvas and control for each image
images.forEach((img) => {
  if (imagesConfiguration[img] == undefined) {
    imagesConfiguration[img] = {
      x: 0,
      y: 0,
    };
  }

  let c = imagesConfiguration[img];
  document.getElementById('controls').insertAdjacentHTML(
    'beforeend',
    `<div class='control'>
      <label>${img}</label>
      <input type='number' id='control-${img}-x' value='${c.x}' />
      <input type='number' id='control-${img}-y' value='${c.y}' />
      <span class='center-coordinates' id='coordinates-${img}'></span>
    </div>`
  );

  document.getElementById(`control-${img}-x`).addEventListener('change', function () {
    c.x = this.value;
    let a = JSON.stringify(imagesConfiguration);
    document.getElementById('configuration').innerHTML = a;
    localStorage.setItem('imagesConfiguration', a);
    drawImage(img);
  });
  document.getElementById(`control-${img}-y`).addEventListener('change', function () {
    c.y = this.value;
    let a = JSON.stringify(imagesConfiguration);
    document.getElementById('configuration').innerHTML = a;
    localStorage.setItem('imagesConfiguration', a);
    drawImage(img);
  });

  document
    .getElementById('canvas-container')
    .insertAdjacentHTML('beforeend', `<canvas id="canvas-${img}" width="${WIDTH}" height="${HEIGHT}"></canvas>`);
});
document
  .getElementById('controls')
  .insertAdjacentHTML('beforeend', `<textarea id='configuration'>${JSON.stringify(imagesConfiguration)}</textarea>`);

document
  .getElementById('canvas-container')
  .insertAdjacentHTML('beforeend', `<canvas id="canvas-grid" width="${WIDTH}" height="${HEIGHT}"></canvas>`);

document.getElementById('checkbox-crop').addEventListener('change', function () {
  CROP = this.checked;
  drawImages();
});

document.getElementById('checkbox-grid').addEventListener('change', function () {
  DRAW_GRID = this.checked;
  drawGrid();
});

document.getElementById('generate').addEventListener('click', () => {
  document.getElementById('editor').style.marginLeft = '-100%';
  generateSprite();
});

document.getElementById('close-result').addEventListener('click', () => {
  document.getElementById('editor').style.marginLeft = '0%';
});
document.getElementById('regenerate').addEventListener('click', () => {
  generateSprite();
});

//////////////////////////////////
//  ____
// |  _ \ _ __ __ ___      __
// | | | | '__/ _` \ \ /\ / /
// | |_| | | | (_| |\ V  V /
// |____/|_|  \__,_| \_/\_/
//////////////////////////////////
function drawImages() {
  images.forEach((img, i) => {
    drawImage(img);
  });

  drawGrid();
}

let imagesCorners = {};
function drawImage(img) {
  let canvas = document.getElementById(`canvas-${img}`);
  let ctx = canvas.getContext('2d');
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  let c = imagesConfiguration[img];
  let data = imagesDatas[img];
  let cornerX = parseInt(SCALE * parseInt(c.x));
  let cornerY = parseInt(SCALE * parseInt(c.y));
  let width = parseInt(SCALE * data.width);
  let height = parseInt(SCALE * data.height);
  ctx.drawImage(data, cornerX, cornerY, width, height);

  let axial_c = pixel_to_flat_hex_axial(cornerX, cornerY);
  let hex = axial_to_doubleheight(axial_c);
  imagesCorners[img] = hex;
  document.getElementById(`coordinates-${img}`).innerHTML = `(${hex.col}, ${hex.row})`;

  if (CROP && imagesHex[img]) {
    var imageData = ctx.getImageData(cornerX, cornerY, width, height);
    var pixel = imageData.data;
    for (let x = 0; x <= width; x++) {
      for (let y = 0; y <= height; y++) {
        let axial_coords = pixel_to_flat_hex_axial(cornerX + x, cornerY + y);
        let coords = axial_to_doubleheight(axial_coords);

        let udelta = `${coords.col - hex.col}_${coords.row - hex.row}`;
        if (!imagesHex[img].includes(udelta)) {
          let p = 4 * (x + y * width);
          pixel[p + 3] = 0;
        }
      }
    }
    ctx.putImageData(imageData, cornerX, cornerY);
  }
}

/////////////////////////////////////////////////
//  _   _              ____      _     _
// | | | | _____  __  / ___|_ __(_) __| |
// | |_| |/ _ \ \/ / | |  _| '__| |/ _` |
// |  _  |  __/>  <  | |_| | |  | | (_| |
// |_| |_|\___/_/\_\  \____|_|  |_|\__,_|
/////////////////////////////////////////////////

function drawGrid(canvas = null, clear = true) {
  canvas = canvas || document.getElementById(`canvas-grid`);
  let ctx = canvas.getContext('2d');
  if (clear) ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.lineWidth = 2;

  if (DRAW_GRID) {
    ctx.strokeStyle = 'black';
    ctx.fillStyle = 'transparent';

    let maxX = canvas.width / ((Math.sqrt(3) * SIZE) / 2);
    let maxY = canvas.height / (2 * SIZE) + 2;
    for (let x = 0; x <= maxX; x++) {
      for (let y = 0; y < maxY; y++) {
        drawHexagon(ctx, x, y);
      }
    }

    if (highlightedHex !== null) {
      ctx.strokeStyle = 'red';
      ctx.fillStyle = 'rgba(255,0,0,0.3)';
      drawHexagon(ctx, highlightedHex.col, highlightedHex.row);
    }
  }
}

// Draw one hexagon, using qoffset coordinates
let highlightedHex = null;
function drawHexagon(ctx, a, b) {
  let center = oddq_offset_to_pixel(a, b);
  ctx.beginPath();
  for (var i = 0; i < 6; i++) {
    ctx.lineTo(center.x + SIZE * Math.cos(ANGLE * i) - OFFSET_X, center.y + SIZE * Math.sin(ANGLE * i));
  }
  ctx.closePath();
  ctx.stroke();
  ctx.fill();
}

// Coordinates helper
document.getElementById('canvas-grid').addEventListener('mousemove', function (evt) {
  let canvas = document.getElementById(`canvas-wrapper`);
  let x = evt.x - canvas.offsetLeft;
  let y = evt.y - canvas.offsetTop;
  let axial_c = pixel_to_flat_hex_axial(x, y);
  let c = axial_to_oddq(axial_c);
  let d = axial_to_doubleheight(axial_c);

  document.getElementById('coordinates').innerHTML = `(${d.col}, ${d.row})`;
  highlightedHex = c;
  drawGrid();
});
document.getElementById('canvas-grid').addEventListener('mouseout', function (evt) {
  highlightedHex = null;
  drawGrid();
});

// Convert pixel position to hex grid coordinate
function pixel_to_flat_hex_axial(x, y) {
  x += OFFSET_X;
  var q = ((2 / 3) * x) / SIZE;
  var r = ((-1 / 3) * x + (Math.sqrt(3) / 3) * y) / SIZE;
  let t = axial_round(q, r);
  return { q: t[0], r: t[1] };
}

function axial_round(x, y) {
  const xgrid = Math.round(x),
    ygrid = Math.round(y);
  (x -= xgrid), (y -= ygrid); // remainder
  const dx = Math.round(x + 0.5 * y) * (x * x >= y * y);
  const dy = Math.round(y + 0.5 * x) * (x * x < y * y);
  return [xgrid + dx, ygrid + dy];
}

function axial_to_oddq(hex) {
  var col = hex.q;
  var row = hex.r + (hex.q - (hex.q % 2)) / 2;
  return { col, row };
}

function axial_to_doubleheight(hex) {
  var col = hex.q;
  var row = 2 * hex.r + hex.q;
  return { col, row };
}

// Convert a hex grid coordinate to the pixel position of the center of the hex
function oddq_offset_to_pixel(col, row) {
  var x = ((SIZE * 3) / 2) * col;
  var y = SIZE * Math.sqrt(3) * (row + 0.5 * (col % 2));
  return { x, y };
}

const BORDER_DIRECTIONS = [
  [-1, -1],
  [1, -1],
  [2, 0],
  [1, 1],
  [-1, 1],
  [-2, 0],
];

////////////////////////////////
//  ____             _ _
// / ___| _ __  _ __(_) |_ ___
// \___ \| '_ \| '__| | __/ _ \
//  ___) | |_) | |  | | ||  __/
// |____/| .__/|_|  |_|\__\___|
//       |_|
////////////////////////////////
function getDimensions(dx, dy) {
  let width = parseInt((0.5 + dx * 1.5) * SIZE),
    height = parseInt((((dy + 1) * Math.sqrt(3)) / 2) * SIZE);
  return { width, height };
}

function generateSprite() {
  // Remove existing canvas
  let canvas = document.getElementById('canvas-result');
  if (canvas) {
    canvas.remove();
  }

  // Compute max width and max height
  let gMaxX = 0,
    gMaxY = 0;
  images.forEach((img) => {
    let corner = imagesCorners[img];
    imagesHex[img].forEach((delta) => {
      let t = delta.split('_');
      let dx = parseInt(t[0]),
        dy = parseInt(t[1]);
      let x = corner.col + dx,
        y = corner.row + dy;
      gMaxX = Math.max(gMaxX, x);
      gMaxY = Math.max(gMaxY, y);
    });
  });

  // Compute corresponding width and height and create canvas
  let dims = getDimensions(gMaxX, gMaxY);
  document
    .getElementById('canvas-result-container')
    .insertAdjacentHTML(
      'afterbegin',
      `<canvas id="canvas-result" width="${dims.width}" height="${dims.height}"></canvas>`
    );
  console.log(gMaxX, gMaxY, dims.width, dims.height);

  // Draw on the canvas
  canvas = document.getElementById('canvas-result');
  let ctx = canvas.getContext('2d');
  images.forEach((img) => {
    let imgCanvas = document.getElementById(`canvas-${img}`);
    ctx.drawImage(imgCanvas, 0, 0);
  });
  //  drawGrid(canvas, false);

  // Generate css
  let result = document.getElementById('scss-result');
  let textarea = document.getElementById('css-result');
  result.innerHTML = `
.building-container {
  .building-inner {
    background:url('img/enclosures.jpg');
    background-repeat:no-repeat;
  }
  
`;
  textarea.innerHTML = '';
  textarea.innerHTML += `
.building-container {
  margin:5px;
  position:relative;
}
.building-container .building-border,
.building-container .building-inner {
  position:absolute;
  top:0;
  left:0;
}

.building-container:hover .building-border {
  background:red;
}
.building-container[data-rotation='1'] {
  transform:rotate(60deg);
}
.building-container[data-rotation='2'] {
  transform:rotate(120deg);
}
.building-container[data-rotation='3'] {
  transform:rotate(180deg);
}
.building-container[data-rotation='4'] {
  transform:rotate(240deg);
}
.building-container[data-rotation='5'] {
  transform:rotate(300deg);
}

.building-crosshairs {
  width:30px;
  height:30px;
  position:absolute;
  background:url('crosshairs.svg');
  margin-left:-15px;
  margin-top:-15px;
}

.building-inner {
  background:-moz-element(#canvas-result);
  background-repeat:no-repeat;
}
`;
  // WEBKIT :   background:-webkit-canvas(canvas-result);

  let offsets = {};
  images.forEach((img) => {
    // Compute min/max of X/Y to know the size of the tile
    let minX = 100000,
      minY = 100000,
      maxX = 0,
      maxY = 0;
    let corner = imagesCorners[img];
    let points = [];
    imagesHex[img].forEach((delta, i) => {
      let t = delta.split('_');
      let dx = parseInt(t[0]),
        dy = parseInt(t[1]);
      let x = corner.col + dx,
        y = corner.row + dy;
      maxX = Math.max(maxX, x);
      maxY = Math.max(maxY, y);
      minX = Math.min(minX, x);
      minY = Math.min(minY, y);

      // Compute the 6 points around the border
      BORDER_DIRECTIONS.forEach((dir) => {
        let localX = 3 * x + dir[0],
          localY = y + dir[1];
        const point = points.find((o) => o.x == localX && o.y == localY);
        if (point) {
          point.hexes.push(i);
          point.dirs.push(dir);
        } else {
          points.push({
            x: localX,
            y: localY,
            hexes: [i],
            dirs: [dir],
          });
        }
      });
    });

    // Compute boundary
    points = points.filter((p) => p.hexes.length < 3);
    let cPoint = points.shift();
    let firstPoint = cPoint;
    let boundary = [cPoint];
    while (points.length > 0) {
      let index = points.findIndex(
        (p) =>
          ((Math.abs(p.x - cPoint.x) == 2 && p.y == cPoint.y) ||
            (Math.abs(p.x - cPoint.x) == 1 && Math.abs(p.y - cPoint.y) == 1)) &&
          intersect(p.hexes, cPoint.hexes).length == 1
      );
      if (index == -1) {
        console.log("Shouldn't happen");
        console.log(cPoint, points);
        return false;
      }

      boundary.push(points[index]);
      cPoint = points[index];
      points.splice(index, 1);
    }
    boundary.push(firstPoint);
    let computeClipPath = (boundary, margin) => {
      return boundary.map((point) => {
        let x = point.x;
        let y = point.y;
        if (margin > 0) {
          let sumDir = point.dirs.reduce((carry, d) => [carry[0] - d[0], carry[1] - d[1]], [0, 0]);
          x += sumDir[0] * margin;
          y += sumDir[1] * margin;
        }
        let percentX = (x - 3 * minX + 2) / (1 + 3 * (maxX - minX + 1));
        let percentY = (y - minY + 1) / (maxY - minY + 2);
        return `${percentX * 100}% ${percentY * 100}%`;
      });
    };
    let clipPath = computeClipPath(boundary, 0);
    let innerClipPath = computeClipPath(boundary, CLIP_PATH_MARGIN);

    let innerClipPath2 = computeClipPath(boundary, 2.5 * CLIP_PATH_MARGIN);
    let borderClipPath = [...clipPath, clipPath[0], innerClipPath2[0], ...innerClipPath2.reverse()];

    let imgDims = getDimensions(maxX - minX + 1, maxY - minY + 1);
    // Compute % for background positions
    let deltaX = minX - 1;
    let outX = gMaxX - (maxX - minX + 1);
    let deltaY = minY - 1;
    let outY = gMaxY - (maxY - minY + 1);

    // Compute % for rotation
    let refHex = imagesHex[img][0].split('_');
    let refX = 3 * (parseInt(refHex[0]) + corner.col),
      refY = parseInt(refHex[1]) + corner.row;
    let offsetX = refX - 3 * minX + 2,
      offsetY = refY - minY + 1;
    offsets[img] = {
      x: offsetX,
      y: offsetY,
    };
    let refPercentX = offsetX / (1 + 3 * (maxX - minX + 1));
    let refPercentY = offsetY / (maxY - minY + 2);

    textarea.innerHTML += `
.building-container[data-type="${img}"] {
  width:${imgDims.width}px;
  height:${imgDims.height}px;
  clip-path:polygon(${clipPath.join(', ')});
  transform-origin: ${refPercentX * 100}% ${refPercentY * 100}%;
}
.building-container[data-type="${img}"] .building-border {
  width:${imgDims.width}px;
  height:${imgDims.height}px;
  clip-path:polygon(${borderClipPath.join(', ')});
}
.building-container[data-type="${img}"] .building-inner {
  width:${imgDims.width}px;
  height:${imgDims.height}px;
  background-position: ${(outX == 0 ? 0 : deltaX / outX) * 100}% ${(outY == 0 ? 0 : deltaY / outY) * 100}%;
  clip-path:polygon(${innerClipPath.join(', ')});
  background:none;
}
.building-container[data-type="${img}"] .building-crosshairs {
  left: ${refPercentX * 100}%;
  top: ${refPercentY * 100}%;
}
`;

    result.innerHTML += `
  &[data-type='${img}'] {
    width:${imgDims.width}px;
    height:${imgDims.height}px;
    margin-left:${-(offsetX - 2) * 100}%;
    margin-top:${-(offsetY - 1) * Math.sqrt(3) * 100}%;
    clip-path:polygon(${clipPath.join(', ')});
    transform-origin: ${refPercentX * 100}% ${refPercentY * 100}%;

    .building-inner {
      background-position: ${(outX == 0 ? 0 : deltaX / outX) * 100}% ${(outY == 0 ? 0 : deltaY / outY) * 100}%;
      clip-path:polygon(${innerClipPath.join(', ')});
    }
    .building-border {
      clip-path:polygon(${borderClipPath.join(', ')});
    }
    .building-crosshairs {
      left: ${refPercentX * 100}%;
      top: ${refPercentY * 100}%;
    }
  }
`;
  });
  result.innerHTML += '}';

  /* create the style element */
  const styleElement = document.createElement('style');
  styleElement.appendChild(document.createTextNode(textarea.innerHTML));
  document.getElementsByTagName('head')[0].appendChild(styleElement);

  // Generate demo elements
  let demo = document.getElementById('result-demo');
  demo.innerHTML = '';
  images.forEach((img) => {
    demo.insertAdjacentHTML(
      'beforeend',
      `<div id="building-${img}" data-type="${img}" class='building-${img} building-container'>
        <div class='building-border'></div>
        <div class='building-inner'></div>
        <div class='building-crosshairs'></div>
      </div>`
    );
    let o = document.getElementById(`building-${img}`);
    o.addEventListener('click', () => {
      o.dataset.rotation = (parseInt(o.dataset.rotation ?? 0) + 1) % 6;
    });
  });

  // Generate scripts for UI and backend
  let resultScript = document.getElementById('script-result');
  resultScript.innerHTML = `const ENCLOSURES_OFFSETS = ${JSON.stringify(offsets)};
  
  <?php
  const ENCLOSURES = [
  `;
  images.forEach((img) => {
    resultScript.innerHTML += ` '${img}' => [`;
    let refHex = imagesHex[img][0].split('_');
    let refX = parseInt(refHex[0]),
      refY = parseInt(refHex[1]);
    imagesHex[img].forEach((h) => {
      let t = h.split('_');
      let x = parseInt(t[0]),
        y = parseInt(t[1]);
      resultScript.innerHTML += `[${x - refX}, ${y - refY}],`;
    });
    resultScript.innerHTML += `],
    `;
  });
  resultScript.innerHTML += '];';
}

function intersect(a, b) {
  var t;
  if (b.length > a.length) (t = b), (b = a), (a = t); // indexOf to loop over shorter
  return a.filter(function (e) {
    return b.indexOf(e) > -1;
  });
}
//////////////////////////////////////////////////////////////////
// ___                   _                    _ _
// |_ _|_ __ ___   __ _  | |    ___   __ _  __| (_)_ __   __ _
//  | || '_ ` _ \ / _` | | |   / _ \ / _` |/ _` | | '_ \ / _` |
//  | || | | | | | (_| | | |__| (_) | (_| | (_| | | | | | (_| |
// |___|_| |_| |_|\__, | |_____\___/ \__,_|\__,_|_|_| |_|\__, |
//                |___/                                  |___/
//////////////////////////////////////////////////////////////////

// Promise-based loacing images
function loadImage(name, path) {
  return new Promise(function (resolve) {
    let img = new Image();
    img.addEventListener('load', () => {
      resolve({ name, img });
    });

    img.src = path;
  });
}

let imagesDatas = null;
Promise.all(images.map((name) => loadImage(name, `./img/${name}.png`))).then((res) => {
  imagesDatas = {};
  res.forEach((data) => {
    imagesDatas[data.name] = data.img;
  });
  drawImages();
});
