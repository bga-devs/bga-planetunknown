@charset "UTF-8";
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Planet Unknown implementation : © Timothée Pecatte <tim.pecatte@gmail.com>, Emmanuel Albisser <emmanuel.albisser@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * planetunknown.scss
 *
 * Planet Unknown stylesheet
 *
 */
/*! nouislider - 14.6.0 - 6/27/2020 */
@import url(https://fonts.googleapis.com/css?family=Permanent+Marker);
@import url(https://fonts.googleapis.com/css?family=Caveat);
.noUi-target, .noUi-target * {
  -webkit-touch-callout: none;
  -webkit-tap-highlight-color: transparent;
  -webkit-user-select: none;
  -ms-touch-action: none;
  touch-action: none;
  -ms-user-select: none;
  -moz-user-select: none;
  user-select: none;
  -moz-box-sizing: border-box;
  box-sizing: border-box; }

.noUi-target {
  position: relative; }

.noUi-base, .noUi-connects {
  width: 100%;
  height: 100%;
  position: relative;
  z-index: 1; }

.noUi-connects {
  overflow: hidden;
  z-index: 0; }

.noUi-connect, .noUi-origin {
  will-change: transform;
  position: absolute;
  z-index: 1;
  top: 0;
  right: 0;
  -ms-transform-origin: 0 0;
  -webkit-transform-origin: 0 0;
  -webkit-transform-style: preserve-3d;
  transform-origin: 0 0;
  transform-style: flat; }

.noUi-connect {
  height: 100%;
  width: 100%; }

.noUi-origin {
  height: 10%;
  width: 10%; }

.noUi-txt-dir-rtl.noUi-horizontal .noUi-origin {
  left: 0;
  right: auto; }

.noUi-vertical .noUi-origin {
  width: 0; }

.noUi-horizontal .noUi-origin {
  height: 0; }

.noUi-handle {
  -webkit-backface-visibility: hidden;
  backface-visibility: hidden;
  position: absolute; }

.noUi-touch-area {
  height: 100%;
  width: 100%; }

.noUi-state-tap .noUi-connect, .noUi-state-tap .noUi-origin {
  -webkit-transition: transform .3s;
  transition: transform .3s; }

.noUi-state-drag * {
  cursor: inherit !important; }

.noUi-horizontal {
  height: 18px; }

.noUi-horizontal .noUi-handle {
  width: 34px;
  height: 28px;
  right: -17px;
  top: -6px; }

.noUi-vertical {
  width: 18px; }

.noUi-vertical .noUi-handle {
  width: 28px;
  height: 34px;
  right: -6px;
  top: -17px; }

.noUi-txt-dir-rtl.noUi-horizontal .noUi-handle {
  left: -17px;
  right: auto; }

.noUi-target {
  background: #FAFAFA;
  border-radius: 4px;
  border: 1px solid #D3D3D3;
  box-shadow: inset 0 1px 1px #F0F0F0,0 3px 6px -5px #BBB; }

.noUi-connects {
  border-radius: 3px; }

.noUi-connect {
  background: #3FB8AF; }

.noUi-draggable {
  cursor: ew-resize; }

.noUi-vertical .noUi-draggable {
  cursor: ns-resize; }

.noUi-handle {
  border: 1px solid #D9D9D9;
  border-radius: 3px;
  background: #FFF;
  cursor: default;
  box-shadow: inset 0 0 1px #FFF,inset 0 1px 7px #EBEBEB,0 3px 6px -3px #BBB; }

.noUi-active {
  box-shadow: inset 0 0 1px #FFF,inset 0 1px 7px #DDD,0 3px 6px -3px #BBB; }

.noUi-handle:after, .noUi-handle:before {
  content: "";
  display: block;
  position: absolute;
  height: 14px;
  width: 1px;
  background: #E8E7E6;
  left: 14px;
  top: 6px; }

.noUi-handle:after {
  left: 17px; }

.noUi-vertical .noUi-handle:after, .noUi-vertical .noUi-handle:before {
  width: 14px;
  height: 1px;
  left: 6px;
  top: 14px; }

.noUi-vertical .noUi-handle:after {
  top: 17px; }

[disabled] .noUi-connect {
  background: #B8B8B8; }

[disabled] .noUi-handle, [disabled].noUi-handle, [disabled].noUi-target {
  cursor: not-allowed; }

.noUi-pips, .noUi-pips * {
  -moz-box-sizing: border-box;
  box-sizing: border-box; }

.noUi-pips {
  position: absolute;
  color: #999; }

.noUi-value {
  position: absolute;
  white-space: nowrap;
  text-align: center; }

.noUi-value-sub {
  color: #ccc;
  font-size: 10px; }

.noUi-marker {
  position: absolute;
  background: #CCC; }

.noUi-marker-sub {
  background: #AAA; }

.noUi-marker-large {
  background: #AAA; }

.noUi-pips-horizontal {
  padding: 10px 0;
  height: 80px;
  top: 100%;
  left: 0;
  width: 100%; }

.noUi-value-horizontal {
  -webkit-transform: translate(-50%, 50%);
  transform: translate(-50%, 50%); }

.noUi-rtl .noUi-value-horizontal {
  -webkit-transform: translate(50%, 50%);
  transform: translate(50%, 50%); }

.noUi-marker-horizontal.noUi-marker {
  margin-left: -1px;
  width: 2px;
  height: 5px; }

.noUi-marker-horizontal.noUi-marker-sub {
  height: 10px; }

.noUi-marker-horizontal.noUi-marker-large {
  height: 15px; }

.noUi-pips-vertical {
  padding: 0 10px;
  height: 100%;
  top: 0;
  left: 100%; }

.noUi-value-vertical {
  -webkit-transform: translate(0, -50%);
  transform: translate(0, -50%);
  padding-left: 25px; }

.noUi-rtl .noUi-value-vertical {
  -webkit-transform: translate(0, 50%);
  transform: translate(0, 50%); }

.noUi-marker-vertical.noUi-marker {
  width: 5px;
  height: 2px;
  margin-top: -1px; }

.noUi-marker-vertical.noUi-marker-sub {
  width: 10px; }

.noUi-marker-vertical.noUi-marker-large {
  width: 15px; }

.noUi-tooltip {
  display: block;
  position: absolute;
  border: 1px solid #D9D9D9;
  border-radius: 3px;
  background: #fff;
  color: #000;
  padding: 5px;
  text-align: center;
  white-space: nowrap; }

.noUi-horizontal .noUi-tooltip {
  -webkit-transform: translate(-50%, 0);
  transform: translate(-50%, 0);
  left: 50%;
  bottom: 120%; }

.noUi-vertical .noUi-tooltip {
  -webkit-transform: translate(0, -50%);
  transform: translate(0, -50%);
  top: 50%;
  right: 120%; }

.noUi-horizontal .noUi-origin > .noUi-tooltip {
  -webkit-transform: translate(50%, 0);
  transform: translate(50%, 0);
  left: auto;
  bottom: 10px; }

.noUi-vertical .noUi-origin > .noUi-tooltip {
  -webkit-transform: translate(0, -18px);
  transform: translate(0, -18px);
  top: auto;
  right: 28px; }

:root {
  --planetUnknownBoardScale: 1;
  --tileScale: 0.577; }

:root {
  --cardScale: 0.6; }

.planetunknown-card {
  box-sizing: border-box; }
  .planetunknown-card.selectable {
    cursor: pointer; }
    .planetunknown-card.selectable:hover {
      transform: scale(1.05); }
  .planetunknown-card.selected {
    outline: 4px solid #1caf1c;
    border-radius: 7px;
    box-shadow: 2px 2px 6px -1px black; }
  .planetunknown-card .card-inner {
    transform: scale(var(--cardScale));
    transform-origin: top left;
    border-radius: 7px;
    box-sizing: border-box; }

.planetunknown-card[data-type="civCard"] {
  width: calc(var(--cardScale) * 316px);
  height: calc(var(--cardScale) * 208px); }

.planetunknown-card[data-type="civCard"] .card-inner {
  width: 316px;
  height: 208px;
  background-image: url("img/civ.jpg");
  background-size: 500% 800%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="1"] {
    background-position-x: 0%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="2"] {
    background-position-x: 25%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="3"] {
    background-position-x: 50%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="4"] {
    background-position-x: 75%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="5"] {
    background-position-x: 100%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="6"] {
    background-position-x: 0%;
    background-position-y: 14.2857142857%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="7"] {
    background-position-x: 25%;
    background-position-y: 14.2857142857%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="8"] {
    background-position-x: 50%;
    background-position-y: 14.2857142857%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="9"] {
    background-position-x: 75%;
    background-position-y: 14.2857142857%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="10"] {
    background-position-x: 100%;
    background-position-y: 14.2857142857%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="11"] {
    background-position-x: 0%;
    background-position-y: 28.5714285714%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="12"] {
    background-position-x: 25%;
    background-position-y: 28.5714285714%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="13"] {
    background-position-x: 50%;
    background-position-y: 28.5714285714%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="14"] {
    background-position-x: 75%;
    background-position-y: 28.5714285714%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="15"] {
    background-position-x: 100%;
    background-position-y: 28.5714285714%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="16"] {
    background-position-x: 0%;
    background-position-y: 42.8571428571%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="17"] {
    background-position-x: 25%;
    background-position-y: 42.8571428571%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="18"] {
    background-position-x: 50%;
    background-position-y: 42.8571428571%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="19"] {
    background-position-x: 75%;
    background-position-y: 42.8571428571%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="20"] {
    background-position-x: 100%;
    background-position-y: 42.8571428571%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="21"] {
    background-position-x: 0%;
    background-position-y: 57.1428571429%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="22"] {
    background-position-x: 25%;
    background-position-y: 57.1428571429%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="23"] {
    background-position-x: 50%;
    background-position-y: 57.1428571429%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="24"] {
    background-position-x: 75%;
    background-position-y: 57.1428571429%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="25"] {
    background-position-x: 100%;
    background-position-y: 57.1428571429%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="26"] {
    background-position-x: 0%;
    background-position-y: 71.4285714286%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="27"] {
    background-position-x: 25%;
    background-position-y: 71.4285714286%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="28"] {
    background-position-x: 50%;
    background-position-y: 71.4285714286%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="29"] {
    background-position-x: 75%;
    background-position-y: 71.4285714286%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="30"] {
    background-position-x: 100%;
    background-position-y: 71.4285714286%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="31"] {
    background-position-x: 0%;
    background-position-y: 85.7142857143%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="32"] {
    background-position-x: 25%;
    background-position-y: 85.7142857143%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="33"] {
    background-position-x: 50%;
    background-position-y: 85.7142857143%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="34"] {
    background-position-x: 75%;
    background-position-y: 85.7142857143%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="35"] {
    background-position-x: 100%;
    background-position-y: 85.7142857143%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="36"] {
    background-position-x: 0%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="37"] {
    background-position-x: 25%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="38"] {
    background-position-x: 50%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="39"] {
    background-position-x: 75%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="40"] {
    background-position-x: 100%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="-1"][data-level="1"] {
    background-position: 25% 100%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="-1"][data-level="2"] {
    background-position: 50% 100%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="-1"][data-level="3"] {
    background-position: 75% 100%; }
  .planetunknown-card[data-type="civCard"] .card-inner[data-id="-1"][data-level="4"] {
    background-position: 100% 100%; }

.nocard-wrapper {
  display: flex;
  border-left: 1px solid black;
  padding: 0px 5px;
  font-family: "HemiHead"; }
  .nocard-wrapper .nocard-indicator {
    padding: 0px 3px;
    margin-top: -3px; }
  .nocard-wrapper .nocard-indicator-value {
    font-weight: bold;
    font-size: 20px; }
  .nocard-wrapper .icon-medal {
    display: inline-block;
    width: 20px;
    height: 20px;
    color: white;
    margin-top: 7px;
    transform: translateY(11px);
    font-size: 15px;
    line-height: 19px;
    text-indent: 5px; }

.prev-objectives .nocard-wrapper {
  border-left: none;
  border-right: 1px solid black;
  flex-flow: row-reverse; }

.planetunknown-card[data-type="NOCard"] {
  width: calc(var(--cardScale) * 396px);
  height: calc(var(--cardScale) * 260px); }

.planetunknown-card[data-type="NOCard"] .card-inner {
  width: 396px;
  height: 260px;
  background-image: url("img/neighbour-objectives.jpg");
  background-size: 400% 700%;
  border-radius: 7px;
  box-shadow: 2px 2px 6px -1px black;
  box-sizing: border-box; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="37"] {
    background-position-x: 0%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="38"] {
    background-position-x: 33.3333333333%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="39"] {
    background-position-x: 66.6666666667%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="40"] {
    background-position-x: 100%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="41"] {
    background-position-x: 0%;
    background-position-y: 16.6666666667%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="42"] {
    background-position-x: 33.3333333333%;
    background-position-y: 16.6666666667%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="43"] {
    background-position-x: 66.6666666667%;
    background-position-y: 16.6666666667%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="44"] {
    background-position-x: 100%;
    background-position-y: 16.6666666667%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="45"] {
    background-position-x: 0%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="46"] {
    background-position-x: 33.3333333333%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="47"] {
    background-position-x: 66.6666666667%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="48"] {
    background-position-x: 100%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="49"] {
    background-position-x: 0%;
    background-position-y: 50%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="50"] {
    background-position-x: 33.3333333333%;
    background-position-y: 50%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="51"] {
    background-position-x: 66.6666666667%;
    background-position-y: 50%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="52"] {
    background-position-x: 100%;
    background-position-y: 50%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="53"] {
    background-position-x: 0%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="54"] {
    background-position-x: 33.3333333333%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="55"] {
    background-position-x: 66.6666666667%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="56"] {
    background-position-x: 100%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="57"] {
    background-position-x: 0%;
    background-position-y: 83.3333333333%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="58"] {
    background-position-x: 33.3333333333%;
    background-position-y: 83.3333333333%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="59"] {
    background-position-x: 66.6666666667%;
    background-position-y: 83.3333333333%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="60"] {
    background-position-x: 100%;
    background-position-y: 83.3333333333%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="61"] {
    background-position-x: 0%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="62"] {
    background-position-x: 33.3333333333%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="63"] {
    background-position-x: 66.6666666667%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="NOCard"] .card-inner[data-id="64"] {
    background-position-x: 100%;
    background-position-y: 100%; }

.planetunknown-card.icon-only[data-type="NOCard"] {
  width: calc(var(--cardScale) * 275px);
  height: calc(var(--cardScale) * 100px);
  overflow: hidden; }

.planetunknown-card.icon-only[data-type="NOCard"] .card-inner {
  margin-top: -53px;
  margin-left: -46px; }

.planetunknown-card[data-type="POCard"] {
  width: calc(var(--cardScale) * 260px);
  height: calc(var(--cardScale) * 396px); }

.planetunknown-card[data-type="POCard"] .card-inner {
  width: 260px;
  height: 396px;
  background-image: url("img/private-objectives.jpg");
  background-size: 700% 400%;
  border-radius: 7px;
  box-shadow: 2px 2px 6px -1px black;
  box-sizing: border-box; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="37"] {
    background-position-x: 0%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="38"] {
    background-position-x: 16.6666666667%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="39"] {
    background-position-x: 33.3333333333%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="40"] {
    background-position-x: 50%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="41"] {
    background-position-x: 66.6666666667%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="42"] {
    background-position-x: 83.3333333333%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="43"] {
    background-position-x: 100%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="44"] {
    background-position-x: 0%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="45"] {
    background-position-x: 16.6666666667%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="46"] {
    background-position-x: 33.3333333333%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="47"] {
    background-position-x: 50%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="48"] {
    background-position-x: 66.6666666667%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="49"] {
    background-position-x: 83.3333333333%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="50"] {
    background-position-x: 100%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="51"] {
    background-position-x: 0%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="52"] {
    background-position-x: 16.6666666667%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="53"] {
    background-position-x: 33.3333333333%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="54"] {
    background-position-x: 50%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="55"] {
    background-position-x: 66.6666666667%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="56"] {
    background-position-x: 83.3333333333%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="57"] {
    background-position-x: 100%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="58"] {
    background-position-x: 0%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="59"] {
    background-position-x: 16.6666666667%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="60"] {
    background-position-x: 33.3333333333%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="61"] {
    background-position-x: 50%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="62"] {
    background-position-x: 66.6666666667%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="63"] {
    background-position-x: 83.3333333333%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="POCard"] .card-inner[data-id="64"] {
    background-position-x: 100%;
    background-position-y: 100%; }

.pocard-wrapper.selectable {
  cursor: pointer; }
  .pocard-wrapper.selectable:hover {
    transform: scale(1.1); }
  .pocard-wrapper.selectable.selectedToDiscard .card-inner {
    filter: grayscale(1);
    -webkit-transform: translateZ(0) scale(var(--cardScale));
    -webkit-perspective: 1000;
    -webkit-backface-visibility: hidden;
    position: relative; }
    .pocard-wrapper.selectable.selectedToDiscard .card-inner::before {
      content: "\f1f8";
      position: absolute;
      left: 0;
      right: 0;
      top: 0;
      bottom: 0;
      background: #d1d1d1be;
      z-index: 10;
      font: normal normal normal 14px/1 FontAwesome;
      font-size: 140px;
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 7px; }

.pocard-indicator {
  display: none; }

.private-objectives {
  --cardScale: 0.3; }
  .private-objectives .pocard-wrapper {
    display: flex;
    border: 2px solid #2d2c2c;
    padding: 0px 5px;
    font-family: "HemiHead";
    border-radius: 9px;
    height: 36px;
    box-shadow: 2px 2px 3px #3c3b3b;
    background: #e4dacc; }
    .private-objectives .pocard-wrapper .pocard-indicator {
      display: block;
      padding: 0px 6px 0px 3px;
      margin-top: -3px; }
    .private-objectives .pocard-wrapper .pocard-indicator-value {
      font-weight: bold;
      font-size: 20px; }
      .private-objectives .pocard-wrapper .pocard-indicator-value::before {
        position: absolute;
        display: inline-block;
        font: normal normal normal 14px/1 FontAwesome;
        font-size: 16px;
        text-rendering: auto;
        text-stroke: 1px white;
        -webkit-text-stroke: 1px white;
        margin-top: 3px;
        margin-left: -4px;
        content: "\f00d";
        color: red; }
      .private-objectives .pocard-wrapper .pocard-indicator-value.ok::before {
        content: "\f00c";
        color: #00d900; }
    .private-objectives .pocard-wrapper .icon-medal {
      display: inline-block;
      width: 20px;
      height: 20px;
      color: white;
      margin-top: 7px;
      transform: translateY(11px);
      font-size: 15px;
      line-height: 19px;
      text-indent: 5px; }
  .private-objectives .planetunknown-card[data-type="POCard"] {
    width: calc(var(--cardScale) * 210px);
    height: 36px;
    overflow: hidden;
    border-top-right-radius: 2px;
    border-bottom-right-radius: 2px; }
  .private-objectives .planetunknown-card[data-type="POCard"] .card-inner {
    margin-top: -70px;
    margin-left: -12px; }

.next-objectives .private-objectives {
  display: flex; }
  .next-objectives .private-objectives .pocard-wrapper {
    display: flex;
    border: none;
    border-left: 1px solid black;
    border-radius: 0;
    height: 40px;
    box-shadow: none;
    background: none; }

#pending-cards {
  --cardScale: 0.6;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%; }
  #pending-cards .planetunknown-card {
    margin: 1px 5px 7px; }

.planetunknown-card[data-type="EventCard"] {
  width: calc(var(--cardScale) * 260px);
  height: calc(var(--cardScale) * 396px);
  border-radius: 4px;
  box-shadow: 2px 2px 2px -1px black; }

.planetunknown-card[data-type="EventCard"] .card-inner {
  width: 260px;
  height: 396px;
  background-image: url("img/events.jpg");
  background-size: 900% 700%;
  border-radius: 15px;
  box-sizing: border-box; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="65"] {
    background-position-x: 0%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="66"] {
    background-position-x: 12.5%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="67"] {
    background-position-x: 25%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="68"] {
    background-position-x: 37.5%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="69"] {
    background-position-x: 50%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="70"] {
    background-position-x: 62.5%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="71"] {
    background-position-x: 75%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="72"] {
    background-position-x: 87.5%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="73"] {
    background-position-x: 100%;
    background-position-y: 0%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="74"] {
    background-position-x: 0%;
    background-position-y: 16.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="75"] {
    background-position-x: 12.5%;
    background-position-y: 16.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="76"] {
    background-position-x: 25%;
    background-position-y: 16.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="77"] {
    background-position-x: 37.5%;
    background-position-y: 16.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="78"] {
    background-position-x: 50%;
    background-position-y: 16.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="79"] {
    background-position-x: 62.5%;
    background-position-y: 16.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="80"] {
    background-position-x: 75%;
    background-position-y: 16.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="81"] {
    background-position-x: 87.5%;
    background-position-y: 16.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="82"] {
    background-position-x: 100%;
    background-position-y: 16.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="83"] {
    background-position-x: 0%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="84"] {
    background-position-x: 12.5%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="85"] {
    background-position-x: 25%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="86"] {
    background-position-x: 37.5%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="87"] {
    background-position-x: 50%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="88"] {
    background-position-x: 62.5%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="89"] {
    background-position-x: 75%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="90"] {
    background-position-x: 87.5%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="91"] {
    background-position-x: 100%;
    background-position-y: 33.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="92"] {
    background-position-x: 0%;
    background-position-y: 50%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="93"] {
    background-position-x: 12.5%;
    background-position-y: 50%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="94"] {
    background-position-x: 25%;
    background-position-y: 50%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="95"] {
    background-position-x: 37.5%;
    background-position-y: 50%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="96"] {
    background-position-x: 50%;
    background-position-y: 50%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="97"] {
    background-position-x: 62.5%;
    background-position-y: 50%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="98"] {
    background-position-x: 75%;
    background-position-y: 50%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="99"] {
    background-position-x: 87.5%;
    background-position-y: 50%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="100"] {
    background-position-x: 100%;
    background-position-y: 50%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="101"] {
    background-position-x: 0%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="102"] {
    background-position-x: 12.5%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="103"] {
    background-position-x: 25%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="104"] {
    background-position-x: 37.5%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="105"] {
    background-position-x: 50%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="106"] {
    background-position-x: 62.5%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="107"] {
    background-position-x: 75%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="108"] {
    background-position-x: 87.5%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="109"] {
    background-position-x: 100%;
    background-position-y: 66.6666666667%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="110"] {
    background-position-x: 0%;
    background-position-y: 83.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="111"] {
    background-position-x: 12.5%;
    background-position-y: 83.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="112"] {
    background-position-x: 25%;
    background-position-y: 83.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="113"] {
    background-position-x: 37.5%;
    background-position-y: 83.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="114"] {
    background-position-x: 50%;
    background-position-y: 83.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="115"] {
    background-position-x: 62.5%;
    background-position-y: 83.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="116"] {
    background-position-x: 75%;
    background-position-y: 83.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="117"] {
    background-position-x: 87.5%;
    background-position-y: 83.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="118"] {
    background-position-x: 100%;
    background-position-y: 83.3333333333%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="119"] {
    background-position-x: 0%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="120"] {
    background-position-x: 12.5%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="121"] {
    background-position-x: 25%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="122"] {
    background-position-x: 37.5%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="123"] {
    background-position-x: 50%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="124"] {
    background-position-x: 62.5%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="125"] {
    background-position-x: 75%;
    background-position-y: 100%; }
  .planetunknown-card[data-type="EventCard"] .card-inner[data-id="back"] {
    background-position-x: 75%;
    background-position-y: 100%; }

#decks-info {
  display: flex;
  justify-content: space-between;
  font-family: "HemiHead"; }
  #decks-info .civ-deck-counter-wrapper {
    display: flex;
    justify-content: space-around;
    align-items: center;
    color: white;
    background: #353534;
    border: 1px solid white;
    border-radius: 9px;
    padding: 4px 6px;
    box-shadow: 1px 1px 1px black;
    position: relative; }
    #decks-info .civ-deck-counter-wrapper span {
      margin-right: 5px; }
    #decks-info .civ-deck-counter-wrapper .icon-civ {
      position: relative; }
      #decks-info .civ-deck-counter-wrapper .icon-civ span {
        position: absolute;
        display: inline-block;
        background: #d5d5d5;
        top: -2px;
        right: -6px;
        color: #2b2b2b;
        width: 10px;
        border: 1px solid white;
        font-size: 11px;
        text-align: center;
        border-radius: 3px; }
  #decks-info #events-info {
    --cardScale: 0.1;
    display: flex;
    border-left: 1px solid black;
    margin: -5px 0px;
    padding: 4px 0px 4px 7px;
    cursor: zoom-in; }
    #decks-info #events-info #event-deck {
      margin-right: 4px;
      position: relative; }
      #decks-info #events-info #event-deck .planetunknown-card {
        position: absolute; }
      #decks-info #events-info #event-deck span {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        color: black;
        text-stroke: 1px white;
        -webkit-text-stroke: 1px white;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        font-size: 20px;
        text-shadow: 0px 0px 10px white, 0px 0px 10px white, 0px 0px 10px white, 0px 0px 10px white; }

#card-overlay {
  opacity: 0;
  position: fixed;
  width: 100%;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
  top: 0;
  left: 0;
  z-index: 4000;
  pointer-events: none;
  transition: opacity 600ms;
  display: flex;
  align-items: center;
  justify-content: center;
  --cardScale: 1; }
  #card-overlay.active {
    opacity: 1;
    pointer-events: all; }

.planetunknown-icon {
  background: url("img/icons.png");
  background-repeat: no-repeat; }
  .planetunknown-icon.icon-biomass {
    background-position: 68.83745963401508% 34.77218225419664%;
    background-size: 1077.8947368421052%; }
  .planetunknown-icon.icon-biomass-patch {
    background-position: 57.87506673785371% 35.1089588377724%;
    background-size: 1170.2857142857142%; }
  .planetunknown-icon.icon-civ {
    background-position: 61.386138613861384% 2.518891687657431%;
    background-size: 890.4347826086956%; }
  .planetunknown-icon.icon-energy {
    background-position: 47.052280311457174% 2.5839793281653747%;
    background-size: 819.2%; }
  .planetunknown-icon.icon-first-player {
    background-position: 85.9203693017888% 33.295063145809415%;
    background-size: 650.1587301587301%; }
  .planetunknown-icon.icon-flux {
    background-position: 12.570260602963721% 60.342979635584136%;
    background-size: 2250.5494505494507%; }
  .planetunknown-icon.icon-lifepod {
    background-position: 72.0844327176781% 2.484472049689441%;
    background-size: 1338.562091503268%; }
  .planetunknown-icon.icon-medal {
    background-position: 1.0230179028132993% 60.537634408602145%;
    background-size: 2202.150537634409%; }
  .planetunknown-icon.icon-medal-nb {
    background-position: 6.803069053708439% 60.537634408602145%;
    background-size: 2202.150537634409%; }
  .planetunknown-icon.icon-meteor {
    background-position: 94.50777202072538% 32.25806451612903%;
    background-size: 1735.593220338983%; }
  .planetunknown-icon.icon-rover {
    background-position: 46.22950819672131% 35.98014888337469%;
    background-size: 939.4495412844037%; }
  .planetunknown-icon.icon-rover-meeple {
    background-position: 1.610305958132045% 3.992015968063872%;
    background-size: 254.09429280397023%; }
  .planetunknown-icon.icon-tech {
    background-position: 97.21159103335157% 2.484472049689441%;
    background-size: 935.1598173515981%; }
  .planetunknown-icon.icon-tracker-biomass {
    background-position: 98.1% 29.713114754098363%;
    background-size: 4266.666666666666%; }
  .planetunknown-icon.icon-tracker-civ {
    background-position: 98.1% 36.68032786885246%;
    background-size: 4266.666666666666%; }
  .planetunknown-icon.icon-tracker-rover {
    background-position: 17.849999999999998% 57.684426229508205%;
    background-size: 4266.666666666666%; }
  .planetunknown-icon.icon-tracker-tech {
    background-position: 21.25% 57.684426229508205%;
    background-size: 4266.666666666666%; }
  .planetunknown-icon.icon-tracker-water {
    background-position: 24.65% 57.684426229508205%;
    background-size: 4266.666666666666%; }
  .planetunknown-icon.icon-water {
    background-position: 84.14434117003827% 2.484472049689441%;
    background-size: 935.1598173515981%; }

.icon-container {
  display: inline-block;
  vertical-align: middle; }
  .icon-container .icon-water,
  .icon-container .icon-tech,
  .icon-container .icon-rover,
  .icon-container .icon-civ,
  .icon-container .icon-biomass,
  .icon-container .icon-energy {
    width: 1.9em;
    height: 1.9em;
    border-radius: 15%; }
  .icon-container .icon-flux {
    width: 1.9em;
    height: 1.9em; }

.action-button .icon-container-water,
.action-button .icon-container-tech,
.action-button .icon-container-rover,
.action-button .icon-container-civ,
.action-button .icon-container-biomass,
.action-button .icon-container-energy,
.action-button .icon-container-flux {
  margin: -5px 0px -5px; }
.action-button#btnciv, .action-button#btntech, .action-button#btnwater, .action-button#btnrover, .action-button#btnbiomass {
  padding: 9px 7px;
  margin: -10px 0px -10px 15px !important; }

.planetunknown-meeple {
  display: inline-block;
  vertical-align: middle;
  z-index: 10;
  position: relative; }
  .planetunknown-meeple.icon-lifepod {
    width: 2.7945em;
    height: 4em; }
    .planetunknown-meeple.icon-lifepod.selectable {
      border-radius: 10px;
      box-shadow: 0px 0px 12px 6px white;
      cursor: pointer; }
      .planetunknown-meeple.icon-lifepod.selectable:hover {
        background-color: rgba(255, 255, 255, 0.473); }
    .planetunknown-meeple.icon-lifepod.selected, .planetunknown-meeple.icon-lifepod.selectable.selected {
      box-shadow: 0px 0px 12px 6px green;
      border-radius: 10px;
      background-color: rgba(0, 128, 0, 0.548); }
  .planetunknown-meeple.icon-meteor {
    width: 4em;
    height: 4em; }
    .planetunknown-meeple.icon-meteor.selected {
      filter: hue-rotate(60deg); }
  .planetunknown-meeple.icon-rover-meeple {
    width: 4em;
    height: 2.3257em;
    border-top-right-radius: 16px;
    border: 3px solid transparent;
    margin-top: -3px;
    margin-left: -3px;
    background-color: white;
    border-color: #575757; }
    .planetunknown-meeple.icon-rover-meeple.selectable {
      background-color: #fdb4b4;
      border-color: #885c5c;
      cursor: pointer; }
    .planetunknown-meeple.icon-rover-meeple.selected {
      background-color: #7bf27b;
      border-color: #1f8a1f; }
  .planetunknown-meeple.icon-tracker-civ, .planetunknown-meeple.icon-tracker-water, .planetunknown-meeple.icon-tracker-biomass, .planetunknown-meeple.icon-tracker-rover, .planetunknown-meeple.icon-tracker-tech {
    width: 2.4em;
    height: 2.4em;
    transform: translateX(0);
    transition: transform 0.5s;
    box-shadow: 0px 0px 10px 0px yellow;
    background-color: #ffff0070; }
  .planetunknown-meeple.icon-first-player {
    width: 5em;
    height: 2.4em; }
  .planetunknown-meeple.icon-flux {
    width: 2.2em;
    height: 2.2em; }

.action-button .icon-container ~ .planetunknown-meeple {
  margin-left: 0.5em; }

#page-title .icon-lifepod {
  font-size: 0.5em;
  position: absolute; }

#tile-selector {
  display: flex;
  --tileScale: 0.4;
  justify-content: center;
  align-items: center; }
  #tile-selector .tile-container {
    margin: 0 20px; }

#susan-container {
  width: 340px;
  height: 340px;
  position: relative;
  --tileScale: 0.17;
  margin: 10px 0px; }
  #susan-container #susan-indicators {
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    transform-origin: center center; }
    #susan-container #susan-indicators .susan-indicator-slot {
      position: absolute;
      top: 0;
      left: 25%;
      right: 25%;
      bottom: 50%;
      transform-origin: bottom center;
      display: flex;
      justify-content: center; }
      #susan-container #susan-indicators .susan-indicator-slot#indicator-0 {
        transform: rotate(0deg); }
      #susan-container #susan-indicators .susan-indicator-slot#indicator-1 {
        transform: rotate(60deg); }
      #susan-container #susan-indicators .susan-indicator-slot#indicator-2 {
        transform: rotate(120deg); }
      #susan-container #susan-indicators .susan-indicator-slot#indicator-3 {
        transform: rotate(180deg); }
      #susan-container #susan-indicators .susan-indicator-slot#indicator-4 {
        transform: rotate(240deg); }
      #susan-container #susan-indicators .susan-indicator-slot#indicator-5 {
        transform: rotate(300deg); }
      #susan-container #susan-indicators .susan-indicator-slot .susan-indicator {
        margin-top: -10px;
        border-right: 15px solid transparent;
        border-left: 15px solid transparent;
        border-top-width: 15px;
        border-top-style: solid;
        width: 0; }
  #susan-container #susan-exterior {
    position: absolute;
    top: 10px;
    left: 10px;
    bottom: 10px;
    right: 10px;
    transform-origin: center center;
    background: #f4f4f4;
    border-radius: 50%;
    border: 1px solid black;
    transform: rotate(0deg);
    transition: transform 0.5s; }
    #susan-container #susan-exterior .susan-space {
      padding-bottom: 90px;
      padding-top: 5px; }
      #susan-container #susan-exterior .susan-space#top-exterior-0 {
        transform: rotate(0deg); }
      #susan-container #susan-exterior .susan-space#top-exterior-1 {
        transform: rotate(60deg); }
      #susan-container #susan-exterior .susan-space#top-exterior-2 {
        transform: rotate(120deg); }
      #susan-container #susan-exterior .susan-space#top-exterior-3 {
        transform: rotate(180deg); }
      #susan-container #susan-exterior .susan-space#top-exterior-4 {
        transform: rotate(240deg); }
      #susan-container #susan-exterior .susan-space#top-exterior-5 {
        transform: rotate(300deg); }
  #susan-container #susan-interior {
    position: absolute;
    top: 70px;
    left: 70px;
    bottom: 70px;
    right: 70px;
    transform-origin: center center;
    background: #cecdcd;
    border-radius: 50%;
    border: 1px solid black; }
    #susan-container #susan-interior[data-shift="0"] {
      transform: rotate(0deg); }
    #susan-container #susan-interior[data-shift="1"] {
      transform: rotate(-60deg); }
    #susan-container #susan-interior[data-shift="2"] {
      transform: rotate(-120deg); }
    #susan-container #susan-interior[data-shift="3"] {
      transform: rotate(-180deg); }
    #susan-container #susan-interior[data-shift="4"] {
      transform: rotate(-240deg); }
    #susan-container #susan-interior[data-shift="5"] {
      transform: rotate(-300deg); }
    #susan-container #susan-interior .susan-space {
      padding-bottom: 27px;
      padding-top: 3px;
      clip-path: polygon(0px 0px, 100% 0%, 70% 75%, 30% 75%); }
      #susan-container #susan-interior .susan-space#top-interior-0 {
        transform: rotate(0deg); }
      #susan-container #susan-interior .susan-space#top-interior-1 {
        transform: rotate(60deg); }
      #susan-container #susan-interior .susan-space#top-interior-2 {
        transform: rotate(120deg); }
      #susan-container #susan-interior .susan-space#top-interior-3 {
        transform: rotate(180deg); }
      #susan-container #susan-interior .susan-space#top-interior-4 {
        transform: rotate(240deg); }
      #susan-container #susan-interior .susan-space#top-interior-5 {
        transform: rotate(300deg); }
  #susan-container .susan-space {
    position: absolute;
    top: 0;
    left: 25%;
    right: 25%;
    bottom: 50%;
    transform-origin: bottom center;
    display: flex;
    justify-content: center;
    align-items: center; }
    #susan-container .susan-space .tile-container {
      margin: 0; }
    #susan-container .susan-space .susan-counter {
      position: absolute;
      top: 0;
      width: 20px;
      height: 20px;
      background: white;
      border-radius: 50%;
      border: 1px solid black;
      text-align: center; }

/*
#susan-container {
    width: 100%;
    background: white;
    border-radius: 7px;
    border: 1px solid black;
    --tileScale: 0.4;

    #susan-exterior,
    #susan-interior {
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 7px 0px;

        .tile-container {
            margin: 0;
            transform: scale(0.9);
            transform-origin: center center;

            &.selectable {
                cursor: pointer;
                .tile-crosshairs {
                    visibility: visible;
                }

                &:hover {
                    transform: scale(0.95);
                }

                .tile-border {
                    background: black;
                }
            }

            &.selected {
                transform: scale(0.95);
                .tile-border {
                    background: green;
                }
            }

            &.unplacable {
                cursor: not-allowed;
                filter: grayscale(1);
                -webkit-transform: translateZ(0);
                -webkit-perspective: 1000;
                -webkit-backface-visibility: hidden;
                opacity: 0.7;

                &:hover {
                    transform: scale(0.9);
                }
            }
        }
    }

    #susan-exterior {
        border-bottom: 1px solid black;
    }
}
*/
#planetunknown-main-container {
  display: flex;
  flex-wrap: wrap;
  position: relative; }
  #planetunknown-main-container > .planetunknown-card {
    position: absolute; }

[data-player-boards-layout="0"] #planetunknown-main-container .pu-player-board-wrapper:not(.active) {
  display: none; }

.pu-player-board-wrapper {
  background-color: #f9e5cf;
  border-radius: 8px;
  box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.7);
  border-width: 3px;
  border-style: solid;
  margin-bottom: 15px; }
  .pu-player-board-wrapper .pu-player-board-top {
    display: flex;
    justify-content: center;
    height: 40px;
    overflow: hidden;
    z-index: 10;
    position: relative;
    overflow: visible; }
    .pu-player-board-wrapper .pu-player-board-top .player-board-name {
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 20px;
      flex-grow: 1; }
      .pu-player-board-wrapper .pu-player-board-top .player-board-name .prev-player-board,
      .pu-player-board-wrapper .pu-player-board-top .player-board-name .next-player-board {
        padding: 0px 10px;
        cursor: pointer;
        font-size: 25px; }
    .pu-player-board-wrapper .pu-player-board-top .prev-objectives,
    .pu-player-board-wrapper .pu-player-board-top .next-objectives {
      --cardScale: 0.4;
      display: flex; }
      .pu-player-board-wrapper .pu-player-board-top .prev-objectives .planetunknown-card,
      .pu-player-board-wrapper .pu-player-board-top .next-objectives .planetunknown-card {
        margin-left: 5px; }
  .pu-player-board-wrapper .pu-player-board-resizable {
    position: relative;
    width: calc(var(--planetUnknownBoardScale) * 1510px);
    height: calc(var(--planetUnknownBoardScale) * 1000px); }
    .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size {
      width: 1510px;
      height: 1000px;
      transform: scale(var(--planetUnknownBoardScale));
      transform-origin: top left;
      display: flex; }
      .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet {
        width: 1000px;
        height: 1000px; }
        .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet {
          width: 1000px;
          height: 1000px;
          box-sizing: border-box;
          background-size: 100% 100%;
          background-image: url("img/Planets/0.jpg");
          padding-top: 29px;
          padding-left: 15px; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet[data-id="0"] {
            background-image: url("img/Planets/0.jpg"); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet[data-id="1"] {
            background-image: url("img/Planets/1.jpg"); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet[data-id="2"] {
            background-image: url("img/Planets/2.jpg"); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet[data-id="3"] {
            background-image: url("img/Planets/3.jpg"); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet[data-id="4"] {
            background-image: url("img/Planets/4.jpg"); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet[data-id="5"] {
            background-image: url("img/Planets/5.jpg"); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet[data-id="6"] {
            background-image: url("img/Planets/6.jpg"); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet[data-id="7"] {
            background-image: url("img/Planets/7.jpg"); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet[data-id="8"] {
            background-image: url("img/Planets/8.jpg"); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet[data-id="9"] {
            background-image: url("img/Planets/9.jpg"); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet[data-id="10"] {
            background-image: url("img/Planets/10.jpg"); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet[data-id="11"] {
            background-image: url("img/Planets/11.jpg"); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet[data-id="12"] {
            background-image: url("img/Planets/12.jpg"); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .chiasm-right {
            transform: translateX(7px); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .pending-tiles {
            position: absolute;
            top: 5px;
            left: 5px;
            --tileScale: 0.25;
            background: white;
            padding: 2px;
            border-radius: 2px; }
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .pending-tiles:empty {
              display: none; }
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .pending-tiles .tile-container {
              margin: 0; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid {
            width: 923px;
            height: 923px;
            display: grid;
            grid-template-columns: repeat(13, 71px);
            grid-template-rows: repeat(13, 71px); }
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .tile-container {
              z-index: 2; }
              .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .tile-container .tile-inner {
                clip-path: none !important; }
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell-overlay {
              z-index: 1;
              position: relative;
              background: #ffffff60; }
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell {
              z-index: 3;
              position: relative;
              border: 2px solid transparent; }
              .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell.selectable:not(.unselectable) {
                border: 2px dashed white;
                background: #f7b1b190;
                cursor: pointer; }
                .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell.selectable:not(.unselectable):hover {
                  border: 2px solid white;
                  background: #f7b1b1d1; }
              .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell.selected:not(.unselectable), .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell.selected:not(.unselectable):hover {
                border: 2px solid #0f5308;
                background: #7dfc83a1; }
              .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell.ok::before, .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell.nok::before {
                display: inline-block;
                font: normal normal normal 14px/1 FontAwesome;
                font-size: 13px;
                text-rendering: auto;
                position: absolute;
                text-stroke: 1px white;
                -webkit-text-stroke: 1px #040404; }
              .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell.ok::before {
                content: "\f00c";
                color: #00d900; }
              .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell.nok::before {
                content: "\f00d";
                color: red; }
              .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell[data-y="-1"]::before {
                left: 42px;
                top: 2px; }
              .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell[data-x="-1"]::before {
                left: 29px;
                top: 19px; }
              .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell .planetunknown-meeple {
                position: absolute; }
                .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell .planetunknown-meeple.icon-rover-meeple {
                  top: 10px;
                  left: 2px; }
                .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell .planetunknown-meeple.icon-lifepod {
                  top: 2px;
                  left: 12px; }
                .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-planet .planet .planet-grid .planet-grid-cell .planetunknown-meeple.icon-meteor {
                  top: 3px;
                  left: 1px; }
      .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation {
        width: 510px;
        height: 1000px; }
        .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation {
          width: 510px;
          height: 1000px;
          box-sizing: border-box;
          background-size: 100% 100%;
          position: relative;
          background-image: url("img/corporations.jpg");
          background-size: 500% 200%; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation[data-id="0"] {
            background-position-x: 0%;
            background-position-y: 0%; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation[data-id="1"] {
            background-position-x: 25%;
            background-position-y: 0%; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation[data-id="2"] {
            background-position-x: 50%;
            background-position-y: 0%; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation[data-id="3"] {
            background-position-x: 75%;
            background-position-y: 0%; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation[data-id="4"] {
            background-position-x: 100%;
            background-position-y: 0%; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation[data-id="5"] {
            background-position-x: 0%;
            background-position-y: 100%; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation[data-id="6"] {
            background-position-x: 25%;
            background-position-y: 100%; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation[data-id="7"] {
            background-position-x: 50%;
            background-position-y: 100%; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation[data-id="8"] {
            background-position-x: 75%;
            background-position-y: 100%; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation[data-id="9"] {
            background-position-x: 100%;
            background-position-y: 100%; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns {
            width: 295px;
            height: 608px;
            position: absolute;
            top: 167px;
            left: 33px;
            column-gap: 25px;
            display: flex; }
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns:hover .icon-tracker-civ,
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns:hover .icon-tracker-water,
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns:hover .icon-tracker-biomass,
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns:hover .icon-tracker-rover,
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns:hover .icon-tracker-tech {
              transform: translateX(-75%); }
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns:hover .icon-lifepod {
              transform: translateX(-100%); }
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns:hover .icon-flux {
              transform: translateX(-75%); }
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns .corporation-column {
              width: 38px;
              height: 608px;
              position: relative; }
              .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns .corporation-column .icon-flux {
                position: absolute;
                top: -28px;
                left: 2px;
                transition: transform 0.5s; }
              .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns .corporation-column .corpo-cell {
                width: 38px;
                height: 38px;
                text-align: center; }
                .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns .corporation-column .corpo-cell.selectable {
                  box-shadow: 0px 0px 8px 4px white;
                  cursor: pointer; }
                  .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns .corporation-column .corpo-cell.selectable:hover {
                    background: rgba(255, 255, 255, 0.473); }
                .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns .corporation-column .corpo-cell.selected, .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns .corporation-column .corpo-cell.selectable.selected {
                  box-shadow: 0px 0px 8px 4px green;
                  background: rgba(0, 128, 0, 0.548); }
                .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .corporation-columns .corporation-column .corpo-cell .icon-lifepod {
                  font-size: 11px;
                  transition: transform 0.5s;
                  margin-top: -8px; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .rover-reserve {
            position: absolute;
            bottom: 10px;
            left: 10px; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .meteor-reserve {
            position: absolute;
            right: 35px;
            top: 35px;
            width: 240px;
            height: 113px;
            font-size: 8px;
            display: flex;
            flex-wrap: wrap; }
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .meteor-reserve .icon-meteor {
              margin: 5px; }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .lifepod-reserve {
            position: absolute;
            left: 25px;
            top: 23px;
            width: 205px;
            height: 138px;
            font-size: 12px;
            display: flex;
            flex-wrap: wrap; }
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .lifepod-reserve .icon-lifepod {
              margin: 10px 17px; }
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .lifepod-reserve.selectable {
              box-shadow: 0px 0px 8px 4px white;
              cursor: pointer; }
              .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .lifepod-reserve.selectable:hover {
                background: rgba(255, 255, 255, 0.473); }
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .lifepod-reserve.selected, .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .lifepod-reserve.selectable.selected {
              box-shadow: 0px 0px 8px 4px green;
              background: rgba(0, 128, 0, 0.548); }
          .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .biomass-patch-holder {
            position: absolute;
            top: 5px;
            left: 5px;
            --tileScale: 0.2;
            background: white;
            padding: 3px;
            border-radius: 2px; }
            .pu-player-board-wrapper .pu-player-board-resizable .pu-player-board-fixed-size .pu-player-board-corporation .corporation .biomass-patch-holder:empty {
              display: none; }

.player-name svg {
  width: 25px;
  height: 21px;
  cursor: pointer;
  vertical-align: bottom;
  margin-left: 3px; }

.player-info {
  display: flex;
  justify-content: space-between; }
  .player-info .civ-hand {
    width: 100px;
    height: 35px;
    display: flex;
    justify-content: space-around;
    align-items: center;
    color: white;
    background: #353534;
    border: 2px solid white;
    border-radius: 9px;
    font-family: "HemiHead";
    padding-left: 4px;
    box-shadow: 2px 2px 3px black;
    cursor: pointer;
    position: relative; }
    .player-info .civ-hand .planetunknown-card {
      position: absolute; }

.planetunknown-first-player-holder {
  position: absolute;
  height: 40px;
  right: 49px;
  top: 7px;
  width: 80px; }

#tile-controls {
  display: flex;
  justify-content: center;
  align-items: center;
  pointer-events: none;
  z-index: 12; }
  #tile-controls.inactive, [data-rotation-arrows="1"] #tile-controls, [data-rotation-arrows="2"] #tile-controls {
    display: none; }
  #tile-controls #tile-controls-circle {
    width: 0px;
    height: 0px;
    flex-shrink: 0;
    border-radius: 50%;
    border: 4px dotted black;
    z-index: 4;
    pointer-events: none;
    background: #7d7d7d1f;
    position: relative; }
    #tile-controls #tile-controls-circle #tile-rotate-clockwise,
    #tile-controls #tile-controls-circle #tile-rotate-cclockwise,
    #tile-controls #tile-controls-circle #tile-move-up,
    #tile-controls #tile-controls-circle #tile-move-down,
    #tile-controls #tile-controls-circle #tile-move-right,
    #tile-controls #tile-controls-circle #tile-move-left,
    #tile-controls #tile-controls-circle #tile-flip,
    #tile-controls #tile-controls-circle #tile-confirm-btn {
      position: absolute;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: white;
      border: 1px solid black;
      display: flex;
      justify-content: center;
      align-items: center;
      pointer-events: all;
      cursor: pointer;
      font-size: 30px; }
    #tile-controls #tile-controls-circle #tile-rotate-clockwise {
      top: calc(15% - 20px);
      left: calc(87% - 20px); }
      #tile-controls #tile-controls-circle #tile-rotate-clockwise svg {
        width: 26px;
        height: 25px; }
    #tile-controls #tile-controls-circle #tile-rotate-cclockwise {
      top: calc(15% - 20px);
      right: calc(87% - 20px); }
      #tile-controls #tile-controls-circle #tile-rotate-cclockwise svg {
        width: 26px;
        height: 25px; }
    #tile-controls #tile-controls-circle #tile-flip {
      top: calc(85% - 15px);
      left: calc(15% - 15px); }
      #tile-controls #tile-controls-circle #tile-flip svg {
        width: 36px; }
    #tile-controls #tile-controls-circle #tile-move-up {
      top: -21px;
      left: calc(50% - 20px); }
    #tile-controls #tile-controls-circle #tile-move-down {
      bottom: -21px;
      left: calc(50% - 20px); }
    #tile-controls #tile-controls-circle #tile-move-right {
      top: calc(50% - 20px);
      right: -21px; }
    #tile-controls #tile-controls-circle #tile-move-left {
      top: calc(50% - 20px);
      left: -21px; }
    #tile-controls #tile-controls-circle #tile-confirm-btn {
      top: calc(85% - 15px);
      left: calc(85% - 15px);
      color: white;
      background: #4871b6;
      padding: 0px;
      margin: 0px;
      font-size: 25px; }
      #tile-controls #tile-controls-circle #tile-confirm-btn.disabled {
        opacity: 1;
        background: linear-gradient(rgba(189, 189, 189, 0.8), rgba(166, 165, 165, 0.9));
        cursor: not-allowed;
        pointer-events: all; }
    #tile-controls #tile-controls-circle.bottom #tile-confirm-btn {
      top: calc(15% - 15px); }
  #tile-controls.hovering #tile-controls-circle #tile-confirm-btn {
    display: none; }
  #tile-controls[data-type="biomass_patch"] #tile-controls-circle #tile-flip,
  #tile-controls[data-type="biomass_patch"] #tile-controls-circle #tile-rotate-clockwise,
  #tile-controls[data-type="biomass_patch"] #tile-controls-circle #tile-rotate-cclockwise {
    display: none; }

#susan-container .tile-container.selectable .tile-border {
  background: none; }
#susan-container .tile-container.selectable .tile-crosshairs {
  visibility: hidden; }

.tile-container {
  position: relative;
  flex-shrink: 0; }
  .tile-container.phantom {
    visibility: hidden; }
  .tile-container.selectable {
    cursor: pointer; }
    .tile-container.selectable .tile-crosshairs {
      visibility: visible; }
    .tile-container.selectable:hover {
      transform: scale(0.95); }
    .tile-container.selectable .tile-border {
      background: black; }
  .tile-container.selected {
    transform: scale(0.95); }
    .tile-container.selected .tile-border {
      background: green; }
  .tile-container.unplacable {
    cursor: not-allowed;
    filter: grayscale(1);
    -webkit-transform: translateZ(0);
    -webkit-perspective: 1000;
    -webkit-backface-visibility: hidden;
    opacity: 0.7; }
    .tile-container.unplacable:hover {
      transform: scale(0.9); }
  .tile-container[data-rotation="0"][data-flipped="0"] {
    transform: rotate(0deg) scaleX(1); }
  .tile-container[data-rotation="0"][data-flipped="1"] {
    transform: rotate(0deg) scaleX(-1); }
  .tile-container[data-rotation="1"][data-flipped="0"] {
    transform: rotate(90deg) scaleX(1); }
  .tile-container[data-rotation="1"][data-flipped="1"] {
    transform: rotate(90deg) scaleX(-1); }
  .tile-container[data-rotation="2"][data-flipped="0"] {
    transform: rotate(180deg) scaleX(1); }
  .tile-container[data-rotation="2"][data-flipped="1"] {
    transform: rotate(180deg) scaleX(-1); }
  .tile-container[data-rotation="3"][data-flipped="0"] {
    transform: rotate(270deg) scaleX(1); }
  .tile-container[data-rotation="3"][data-flipped="1"] {
    transform: rotate(270deg) scaleX(-1); }
  [data-tiles-borders] .tile-container.selectable {
    cursor: pointer; }
    [data-tiles-borders] .tile-container.selectable .tile-border {
      background: black; }
  [data-tiles-borders] .tile-container.selected .tile-border {
    background: #004cff; }
  .tile-container .tile-border,
  .tile-container .tile-inner {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%; }
  [data-tiles-borders="1"] .tile-container .tile-border {
    background: #a68731; }
  .tile-container .tile-inner {
    background-size: calc(1722px * var(--tileScale));
    background-repeat: no-repeat; }
    .tile-container .tile-inner .planetunknown-meeple {
      position: absolute; }
  .tile-container .tile-crosshairs {
    visibility: hidden;
    pointer-events: none;
    position: absolute;
    width: calc(50px * var(--tileScale));
    height: calc(50px * var(--tileScale));
    margin-left: calc(-25px * var(--tileScale));
    margin-top: calc(-25px * var(--tileScale));
    fill: white;
    stroke: black;
    background: #000000c2;
    box-shadow: 0px 0px 7px 3px black;
    border-radius: 50%; }
    .tile-container .tile-crosshairs svg {
      width: 100%;
      height: 100%; }
  .tile-container#tile-hover {
    transition: transform 0.4s;
    pointer-events: none;
    z-index: 6; }
    .tile-container#tile-hover .tile-border {
      background: #085f08; }
    .tile-container#tile-hover .tile-crosshairs {
      visibility: visible;
      fill: #085f08;
      box-shadow: 0px 0px 9px 4px white;
      background: #ffffffdd; }
      .tile-container#tile-hover .tile-crosshairs::before {
        display: none; }
    .tile-container#tile-hover.hovering .tile-border {
      background: black; }
    .tile-container#tile-hover.hovering .tile-crosshairs {
      fill: black; }
    .tile-container#tile-hover.invalid .tile-border {
      background: red; }
    .tile-container#tile-hover.invalid .tile-crosshairs {
      fill: red; }
    [data-rotation-arrows="0"] .tile-container#tile-hover .tile-crosshairs {
      visibility: visible; }
      [data-rotation-arrows="0"] .tile-container#tile-hover .tile-crosshairs #tile-rotate-clockwise-on-tile,
      [data-rotation-arrows="0"] .tile-container#tile-hover .tile-crosshairs #tile-rotate-cclockwise-on-tile {
        visibility: hidden; }
    [data-rotation-arrows="1"] .tile-container#tile-hover .tile-crosshairs, [data-rotation-arrows="2"] .tile-container#tile-hover .tile-crosshairs {
      visibility: visible;
      transition: transform 0.4s; }
      [data-rotation-arrows="1"] .tile-container#tile-hover .tile-crosshairs #tile-rotate-clockwise-on-tile,
      [data-rotation-arrows="1"] .tile-container#tile-hover .tile-crosshairs #tile-rotate-cclockwise-on-tile, [data-rotation-arrows="2"] .tile-container#tile-hover .tile-crosshairs #tile-rotate-clockwise-on-tile,
      [data-rotation-arrows="2"] .tile-container#tile-hover .tile-crosshairs #tile-rotate-cclockwise-on-tile {
        pointer-events: all;
        position: absolute;
        left: -27px;
        top: -23px;
        width: 33px;
        height: 73px; }
        [data-rotation-arrows="1"] .tile-container#tile-hover .tile-crosshairs #tile-rotate-clockwise-on-tile svg,
        [data-rotation-arrows="1"] .tile-container#tile-hover .tile-crosshairs #tile-rotate-cclockwise-on-tile svg, [data-rotation-arrows="2"] .tile-container#tile-hover .tile-crosshairs #tile-rotate-clockwise-on-tile svg,
        [data-rotation-arrows="2"] .tile-container#tile-hover .tile-crosshairs #tile-rotate-cclockwise-on-tile svg {
          width: 50%;
          margin-left: 11px; }
      [data-rotation-arrows="1"] .tile-container#tile-hover .tile-crosshairs #tile-rotate-clockwise-on-tile, [data-rotation-arrows="2"] .tile-container#tile-hover .tile-crosshairs #tile-rotate-clockwise-on-tile {
        left: 25px; }
        [data-rotation-arrows="1"] .tile-container#tile-hover .tile-crosshairs #tile-rotate-clockwise-on-tile svg, [data-rotation-arrows="2"] .tile-container#tile-hover .tile-crosshairs #tile-rotate-clockwise-on-tile svg {
          margin-left: 3px; }
  .tile-container:not([data-type]), .tile-container[data-type=""] {
    display: none; }

.pu-player-board-wrapper
.pu-player-board-resizable
.pu-player-board-fixed-size
.pu-player-board-planet
.planet
.tile-container.chiasm-right[data-rotation="0"][data-flipped="0"] {
  transform: translateX(7px) rotate(0deg) scaleX(1); }
.pu-player-board-wrapper
.pu-player-board-resizable
.pu-player-board-fixed-size
.pu-player-board-planet
.planet
.tile-container.chiasm-right[data-rotation="0"][data-flipped="1"] {
  transform: translateX(7px) rotate(0deg) scaleX(-1); }
.pu-player-board-wrapper
.pu-player-board-resizable
.pu-player-board-fixed-size
.pu-player-board-planet
.planet
.tile-container.chiasm-right[data-rotation="1"][data-flipped="0"] {
  transform: translateX(7px) rotate(90deg) scaleX(1); }
.pu-player-board-wrapper
.pu-player-board-resizable
.pu-player-board-fixed-size
.pu-player-board-planet
.planet
.tile-container.chiasm-right[data-rotation="1"][data-flipped="1"] {
  transform: translateX(7px) rotate(90deg) scaleX(-1); }
.pu-player-board-wrapper
.pu-player-board-resizable
.pu-player-board-fixed-size
.pu-player-board-planet
.planet
.tile-container.chiasm-right[data-rotation="2"][data-flipped="0"] {
  transform: translateX(7px) rotate(180deg) scaleX(1); }
.pu-player-board-wrapper
.pu-player-board-resizable
.pu-player-board-fixed-size
.pu-player-board-planet
.planet
.tile-container.chiasm-right[data-rotation="2"][data-flipped="1"] {
  transform: translateX(7px) rotate(180deg) scaleX(-1); }
.pu-player-board-wrapper
.pu-player-board-resizable
.pu-player-board-fixed-size
.pu-player-board-planet
.planet
.tile-container.chiasm-right[data-rotation="3"][data-flipped="0"] {
  transform: translateX(7px) rotate(270deg) scaleX(1); }
.pu-player-board-wrapper
.pu-player-board-resizable
.pu-player-board-fixed-size
.pu-player-board-planet
.planet
.tile-container.chiasm-right[data-rotation="3"][data-flipped="1"] {
  transform: translateX(7px) rotate(270deg) scaleX(-1); }

.tile-container .tile-inner {
  background-image: url("img/tiles-0.png");
  background-repeat: no-repeat; }
.tile-container[data-sprite="1"] .tile-inner {
  background-image: url("img/tiles-1.png"); }
.tile-container[data-sprite="2"] .tile-inner {
  background-image: url("img/tiles-2.png"); }
.tile-container[data-type="biomass_patch"] {
  width: calc(123px * var(--tileScale));
  height: calc(123px * var(--tileScale));
  margin-left: 0%;
  margin-top: 0%;
  clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%, 0% 0%);
  transform-origin: 50% 50%; }
  .tile-container[data-type="biomass_patch"] .tile-inner {
    background-image: url("img/tiles-1.png"); }
  .tile-container[data-type="biomass_patch"] .tile-inner {
    background-position: 77% 100%;
    clip-path: polygon(2.5% 2.5%, 97.5% 2.5%, 97.5% 97.5%, 2.5% 97.5%, 2.5% 2.5%); }
  .tile-container[data-type="biomass_patch"] .tile-border {
    clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%, 0% 0%, 6.25% 6.25%, 6.25% 93.75%, 93.75% 93.75%, 93.75% 6.25%, 6.25% 6.25%); }
  .tile-container[data-type="biomass_patch"] .tile-crosshairs {
    left: 50%;
    top: 50%; }
.tile-container[data-shape="0"] {
  width: calc(492px * var(--tileScale));
  height: calc(246px * var(--tileScale));
  margin-left: -200%;
  margin-top: -100%;
  clip-path: polygon(75% 50%, 75% 100%, 50% 100%, 25% 100%, 0% 100%, 0% 50%, 25% 50%, 50% 50%, 50% 0%, 75% 0%, 100% 0%, 100% 50%, 75% 50%);
  transform-origin: 62.5% 75%; }
  .tile-container[data-shape="0"] .tile-inner {
    background-position: 0% 0%;
    clip-path: polygon(73.75% 47.5%, 73.75% 97.5%, 50% 97.5%, 25% 97.5%, 1.25% 97.5%, 1.25% 52.5%, 25% 52.5%, 51.25% 52.5%, 51.25% 2.5%, 75% 2.5%, 98.75% 2.5%, 98.75% 47.5%, 73.75% 47.5%); }
  .tile-container[data-shape="0"] .tile-border {
    clip-path: polygon(75% 50%, 75% 100%, 50% 100%, 25% 100%, 0% 100%, 0% 50%, 25% 50%, 50% 50%, 50% 0%, 75% 0%, 100% 0%, 100% 50%, 75% 50%, 75% 50%, 71.875% 43.75%, 71.875% 43.75%, 96.875% 43.75%, 96.875% 6.25%, 75% 6.25%, 53.125% 6.25%, 53.125% 56.25%, 25% 56.25%, 3.125% 56.25%, 3.125% 93.75%, 25% 93.75%, 50% 93.75%, 71.875% 93.75%, 71.875% 43.75%); }
  .tile-container[data-shape="0"] .tile-crosshairs {
    left: 62.5%;
    top: 75%; }
.tile-container[data-shape="1"] {
  width: calc(369px * var(--tileScale));
  height: calc(369px * var(--tileScale));
  margin-left: -100%;
  margin-top: -100%;
  clip-path: polygon(33.3333333333% 33.3333333333%, 66.6666666667% 33.3333333333%, 66.6666666667% 0%, 100% 0%, 100% 33.3333333333%, 100% 66.6666666667%, 66.6666666667% 66.6666666667%, 66.6666666667% 100%, 33.3333333333% 100%, 33.3333333333% 66.6666666667%, 0% 66.6666666667%, 0% 33.3333333333%, 33.3333333333% 33.3333333333%);
  transform-origin: 50% 50%; }
  .tile-container[data-shape="1"] .tile-inner {
    background-position: 9.090909090909092% 100%;
    clip-path: polygon(33.3333333333% 35%, 68.3333333333% 35%, 68.3333333333% 1.6666666667%, 98.3333333333% 1.6666666667%, 98.3333333333% 33.3333333333%, 98.3333333333% 65%, 65% 65%, 65% 98.3333333333%, 35% 98.3333333333%, 35% 65%, 1.6666666667% 65%, 1.6666666667% 35%, 33.3333333333% 35%); }
  .tile-container[data-shape="1"] .tile-border {
    clip-path: polygon(33.3333333333% 33.3333333333%, 66.6666666667% 33.3333333333%, 66.6666666667% 0%, 100% 0%, 100% 33.3333333333%, 100% 66.6666666667%, 66.6666666667% 66.6666666667%, 66.6666666667% 100%, 33.3333333333% 100%, 33.3333333333% 66.6666666667%, 0% 66.6666666667%, 0% 33.3333333333%, 33.3333333333% 33.3333333333%, 33.3333333333% 33.3333333333%, 33.3333333333% 37.5%, 33.3333333333% 37.5%, 4.1666666667% 37.5%, 4.1666666667% 62.5%, 37.5% 62.5%, 37.5% 95.8333333333%, 62.5% 95.8333333333%, 62.5% 62.5%, 95.8333333333% 62.5%, 95.8333333333% 33.3333333333%, 95.8333333333% 4.1666666667%, 70.8333333333% 4.1666666667%, 70.8333333333% 37.5%, 33.3333333333% 37.5%); }
  .tile-container[data-shape="1"] .tile-crosshairs {
    left: 50%;
    top: 50%; }
.tile-container[data-shape="2"] {
  width: calc(123px * var(--tileScale));
  height: calc(369px * var(--tileScale));
  margin-left: 0%;
  margin-top: -100%;
  clip-path: polygon(100% 33.3333333333%, 100% 66.6666666667%, 100% 100%, 0% 100%, 0% 66.6666666667%, 0% 33.3333333333%, 0% 0%, 100% 0%, 100% 33.3333333333%);
  transform-origin: 50% 50%; }
  .tile-container[data-shape="2"] .tile-inner {
    background-position: 30.76923076923077% 0%;
    clip-path: polygon(95% 33.3333333333%, 95% 66.6666666667%, 95% 98.3333333333%, 5% 98.3333333333%, 5% 66.6666666667%, 5% 33.3333333333%, 5% 1.6666666667%, 95% 1.6666666667%, 95% 33.3333333333%); }
  .tile-container[data-shape="2"] .tile-border {
    clip-path: polygon(100% 33.3333333333%, 100% 66.6666666667%, 100% 100%, 0% 100%, 0% 66.6666666667%, 0% 33.3333333333%, 0% 0%, 100% 0%, 100% 33.3333333333%, 100% 33.3333333333%, 87.5% 33.3333333333%, 87.5% 33.3333333333%, 87.5% 4.1666666667%, 12.5% 4.1666666667%, 12.5% 33.3333333333%, 12.5% 66.6666666667%, 12.5% 95.8333333333%, 87.5% 95.8333333333%, 87.5% 66.6666666667%, 87.5% 33.3333333333%); }
  .tile-container[data-shape="2"] .tile-crosshairs {
    left: 50%;
    top: 50%; }
.tile-container[data-shape="3"] {
  width: calc(246px * var(--tileScale));
  height: calc(246px * var(--tileScale));
  margin-left: -100%;
  margin-top: 0%;
  clip-path: polygon(50% 0%, 100% 0%, 100% 50%, 100% 100%, 50% 100%, 50% 50%, 0% 50%, 0% 0%, 50% 0%);
  transform-origin: 75% 25%; }
  .tile-container[data-shape="3"] .tile-inner {
    background-position: 100% 50%;
    clip-path: polygon(50% 2.5%, 97.5% 2.5%, 97.5% 50%, 97.5% 97.5%, 52.5% 97.5%, 52.5% 47.5%, 2.5% 47.5%, 2.5% 2.5%, 50% 2.5%); }
  .tile-container[data-shape="3"] .tile-border {
    clip-path: polygon(50% 0%, 100% 0%, 100% 50%, 100% 100%, 50% 100%, 50% 50%, 0% 50%, 0% 0%, 50% 0%, 50% 0%, 50% 6.25%, 50% 6.25%, 6.25% 6.25%, 6.25% 43.75%, 56.25% 43.75%, 56.25% 93.75%, 93.75% 93.75%, 93.75% 50%, 93.75% 6.25%, 50% 6.25%); }
  .tile-container[data-shape="3"] .tile-crosshairs {
    left: 75%;
    top: 25%; }
.tile-container[data-shape="4"] {
  width: calc(369px * var(--tileScale));
  height: calc(246px * var(--tileScale));
  margin-left: -100%;
  margin-top: 0%;
  clip-path: polygon(33.3333333333% 0%, 66.6666666667% 0%, 100% 0%, 100% 50%, 66.6666666667% 50%, 66.6666666667% 100%, 33.3333333333% 100%, 33.3333333333% 50%, 0% 50%, 0% 0%, 33.3333333333% 0%);
  transform-origin: 50% 25%; }
  .tile-container[data-shape="4"] .tile-inner {
    background-position: 45.45454545454545% 0%;
    clip-path: polygon(33.3333333333% 2.5%, 66.6666666667% 2.5%, 98.3333333333% 2.5%, 98.3333333333% 47.5%, 65% 47.5%, 65% 97.5%, 35% 97.5%, 35% 47.5%, 1.6666666667% 47.5%, 1.6666666667% 2.5%, 33.3333333333% 2.5%); }
  .tile-container[data-shape="4"] .tile-border {
    clip-path: polygon(33.3333333333% 0%, 66.6666666667% 0%, 100% 0%, 100% 50%, 66.6666666667% 50%, 66.6666666667% 100%, 33.3333333333% 100%, 33.3333333333% 50%, 0% 50%, 0% 0%, 33.3333333333% 0%, 33.3333333333% 0%, 33.3333333333% 6.25%, 33.3333333333% 6.25%, 4.1666666667% 6.25%, 4.1666666667% 43.75%, 37.5% 43.75%, 37.5% 93.75%, 62.5% 93.75%, 62.5% 43.75%, 95.8333333333% 43.75%, 95.8333333333% 6.25%, 66.6666666667% 6.25%, 33.3333333333% 6.25%); }
  .tile-container[data-shape="4"] .tile-crosshairs {
    left: 50%;
    top: 25%; }
.tile-container[data-shape="5"] {
  width: calc(369px * var(--tileScale));
  height: calc(246px * var(--tileScale));
  margin-left: -100%;
  margin-top: 0%;
  clip-path: polygon(33.3333333333% 0%, 66.6666666667% 0%, 100% 0%, 100% 50%, 66.6666666667% 50%, 66.6666666667% 100%, 33.3333333333% 100%, 0% 100%, 0% 50%, 33.3333333333% 50%, 33.3333333333% 0%);
  transform-origin: 50% 25%; }
  .tile-container[data-shape="5"] .tile-inner {
    background-position: 54.54545454545454% 50%;
    clip-path: polygon(35% 2.5%, 66.6666666667% 2.5%, 98.3333333333% 2.5%, 98.3333333333% 47.5%, 65% 47.5%, 65% 97.5%, 33.3333333333% 97.5%, 1.6666666667% 97.5%, 1.6666666667% 52.5%, 35% 52.5%, 35% 2.5%); }
  .tile-container[data-shape="5"] .tile-border {
    clip-path: polygon(33.3333333333% 0%, 66.6666666667% 0%, 100% 0%, 100% 50%, 66.6666666667% 50%, 66.6666666667% 100%, 33.3333333333% 100%, 0% 100%, 0% 50%, 33.3333333333% 50%, 33.3333333333% 0%, 33.3333333333% 0%, 37.5% 6.25%, 37.5% 6.25%, 37.5% 56.25%, 4.1666666667% 56.25%, 4.1666666667% 93.75%, 33.3333333333% 93.75%, 62.5% 93.75%, 62.5% 43.75%, 95.8333333333% 43.75%, 95.8333333333% 6.25%, 66.6666666667% 6.25%, 37.5% 6.25%); }
  .tile-container[data-shape="5"] .tile-crosshairs {
    left: 50%;
    top: 25%; }
.tile-container[data-shape="6"] {
  width: calc(246px * var(--tileScale));
  height: calc(369px * var(--tileScale));
  margin-left: -100%;
  margin-top: -100%;
  clip-path: polygon(100% 33.3333333333%, 100% 66.6666666667%, 100% 100%, 50% 100%, 0% 100%, 0% 66.6666666667%, 50% 66.6666666667%, 50% 33.3333333333%, 50% 0%, 100% 0%, 100% 33.3333333333%);
  transform-origin: 75% 50%; }
  .tile-container[data-shape="6"] .tile-inner {
    background-position: 33.33333333333333% 100%;
    clip-path: polygon(97.5% 33.3333333333%, 97.5% 66.6666666667%, 97.5% 98.3333333333%, 50% 98.3333333333%, 2.5% 98.3333333333%, 2.5% 68.3333333333%, 52.5% 68.3333333333%, 52.5% 33.3333333333%, 52.5% 1.6666666667%, 97.5% 1.6666666667%, 97.5% 33.3333333333%); }
  .tile-container[data-shape="6"] .tile-border {
    clip-path: polygon(100% 33.3333333333%, 100% 66.6666666667%, 100% 100%, 50% 100%, 0% 100%, 0% 66.6666666667%, 50% 66.6666666667%, 50% 33.3333333333%, 50% 0%, 100% 0%, 100% 33.3333333333%, 100% 33.3333333333%, 93.75% 33.3333333333%, 93.75% 33.3333333333%, 93.75% 4.1666666667%, 56.25% 4.1666666667%, 56.25% 33.3333333333%, 56.25% 70.8333333333%, 6.25% 70.8333333333%, 6.25% 95.8333333333%, 50% 95.8333333333%, 93.75% 95.8333333333%, 93.75% 66.6666666667%, 93.75% 33.3333333333%); }
  .tile-container[data-shape="6"] .tile-crosshairs {
    left: 75%;
    top: 50%; }
.tile-container[data-shape="7"] {
  width: calc(369px * var(--tileScale));
  height: calc(246px * var(--tileScale));
  margin-left: -100%;
  margin-top: -100%;
  clip-path: polygon(33.3333333333% 50%, 66.6666666667% 50%, 66.6666666667% 0%, 100% 0%, 100% 50%, 100% 100%, 66.6666666667% 100%, 33.3333333333% 100%, 0% 100%, 0% 50%, 0% 0%, 33.3333333333% 0%, 33.3333333333% 50%);
  transform-origin: 50% 75%; }
  .tile-container[data-shape="7"] .tile-inner {
    background-position: 90.9090909090909% 100%;
    clip-path: polygon(31.6666666667% 52.5%, 68.3333333333% 52.5%, 68.3333333333% 2.5%, 98.3333333333% 2.5%, 98.3333333333% 50%, 98.3333333333% 97.5%, 66.6666666667% 97.5%, 33.3333333333% 97.5%, 1.6666666667% 97.5%, 1.6666666667% 50%, 1.6666666667% 2.5%, 31.6666666667% 2.5%, 31.6666666667% 52.5%); }
  .tile-container[data-shape="7"] .tile-border {
    clip-path: polygon(33.3333333333% 50%, 66.6666666667% 50%, 66.6666666667% 0%, 100% 0%, 100% 50%, 100% 100%, 66.6666666667% 100%, 33.3333333333% 100%, 0% 100%, 0% 50%, 0% 0%, 33.3333333333% 0%, 33.3333333333% 50%, 33.3333333333% 50%, 29.1666666667% 56.25%, 29.1666666667% 56.25%, 29.1666666667% 6.25%, 4.1666666667% 6.25%, 4.1666666667% 50%, 4.1666666667% 93.75%, 33.3333333333% 93.75%, 66.6666666667% 93.75%, 95.8333333333% 93.75%, 95.8333333333% 50%, 95.8333333333% 6.25%, 70.8333333333% 6.25%, 70.8333333333% 56.25%, 29.1666666667% 56.25%); }
  .tile-container[data-shape="7"] .tile-crosshairs {
    left: 50%;
    top: 75%; }
.tile-container[data-shape="8"] {
  width: calc(123px * var(--tileScale));
  height: calc(246px * var(--tileScale));
  margin-left: 0%;
  margin-top: 0%;
  clip-path: polygon(0% 0%, 100% 0%, 100% 50%, 100% 100%, 0% 100%, 0% 50%, 0% 0%);
  transform-origin: 50% 25%; }
  .tile-container[data-shape="8"] .tile-inner {
    background-position: 0% 100%;
    clip-path: polygon(5% 2.5%, 95% 2.5%, 95% 50%, 95% 97.5%, 5% 97.5%, 5% 50%, 5% 2.5%); }
  .tile-container[data-shape="8"] .tile-border {
    clip-path: polygon(0% 0%, 100% 0%, 100% 50%, 100% 100%, 0% 100%, 0% 50%, 0% 0%, 0% 0%, 12.5% 6.25%, 12.5% 6.25%, 12.5% 50%, 12.5% 93.75%, 87.5% 93.75%, 87.5% 50%, 87.5% 6.25%, 12.5% 6.25%); }
  .tile-container[data-shape="8"] .tile-crosshairs {
    left: 50%;
    top: 25%; }
.tile-container[data-shape="9"] {
  width: calc(492px * var(--tileScale));
  height: calc(123px * var(--tileScale));
  margin-left: -100%;
  margin-top: 0%;
  clip-path: polygon(25% 0%, 50% 0%, 75% 0%, 100% 0%, 100% 100%, 75% 100%, 50% 100%, 25% 100%, 0% 100%, 0% 0%, 25% 0%);
  transform-origin: 37.5% 50%; }
  .tile-container[data-shape="9"] .tile-inner {
    background-position: 100% 0%;
    clip-path: polygon(25% 5%, 50% 5%, 75% 5%, 98.75% 5%, 98.75% 95%, 75% 95%, 50% 95%, 25% 95%, 1.25% 95%, 1.25% 5%, 25% 5%); }
  .tile-container[data-shape="9"] .tile-border {
    clip-path: polygon(25% 0%, 50% 0%, 75% 0%, 100% 0%, 100% 100%, 75% 100%, 50% 100%, 25% 100%, 0% 100%, 0% 0%, 25% 0%, 25% 0%, 25% 12.5%, 25% 12.5%, 3.125% 12.5%, 3.125% 87.5%, 25% 87.5%, 50% 87.5%, 75% 87.5%, 96.875% 87.5%, 96.875% 12.5%, 75% 12.5%, 50% 12.5%, 25% 12.5%); }
  .tile-container[data-shape="9"] .tile-crosshairs {
    left: 37.5%;
    top: 50%; }
.tile-container[data-shape="10"] {
  width: calc(246px * var(--tileScale));
  height: calc(246px * var(--tileScale));
  margin-left: 0%;
  margin-top: 0%;
  clip-path: polygon(0% 0%, 50% 0%, 100% 0%, 100% 50%, 100% 100%, 50% 100%, 0% 100%, 0% 50%, 0% 0%);
  transform-origin: 25% 25%; }
  .tile-container[data-shape="10"] .tile-inner {
    background-position: 66.66666666666666% 100%;
    clip-path: polygon(2.5% 2.5%, 50% 2.5%, 97.5% 2.5%, 97.5% 50%, 97.5% 97.5%, 50% 97.5%, 2.5% 97.5%, 2.5% 50%, 2.5% 2.5%); }
  .tile-container[data-shape="10"] .tile-border {
    clip-path: polygon(0% 0%, 50% 0%, 100% 0%, 100% 50%, 100% 100%, 50% 100%, 0% 100%, 0% 50%, 0% 0%, 0% 0%, 6.25% 6.25%, 6.25% 6.25%, 6.25% 50%, 6.25% 93.75%, 50% 93.75%, 93.75% 93.75%, 93.75% 50%, 93.75% 6.25%, 50% 6.25%, 6.25% 6.25%); }
  .tile-container[data-shape="10"] .tile-crosshairs {
    left: 25%;
    top: 25%; }
.tile-container[data-shape="11"] {
  width: calc(369px * var(--tileScale));
  height: calc(369px * var(--tileScale));
  margin-left: -100%;
  margin-top: -100%;
  clip-path: polygon(33.3333333333% 33.3333333333%, 66.6666666667% 33.3333333333%, 100% 33.3333333333%, 100% 66.6666666667%, 100% 100%, 66.6666666667% 100%, 66.6666666667% 66.6666666667%, 33.3333333333% 66.6666666667%, 0% 66.6666666667%, 0% 33.3333333333%, 0% 0%, 33.3333333333% 0%, 33.3333333333% 33.3333333333%);
  transform-origin: 50% 50%; }
  .tile-container[data-shape="11"] .tile-inner {
    background-position: 81.81818181818183% 0%;
    clip-path: polygon(31.6666666667% 35%, 66.6666666667% 35%, 98.3333333333% 35%, 98.3333333333% 66.6666666667%, 98.3333333333% 98.3333333333%, 68.3333333333% 98.3333333333%, 68.3333333333% 65%, 33.3333333333% 65%, 1.6666666667% 65%, 1.6666666667% 33.3333333333%, 1.6666666667% 1.6666666667%, 31.6666666667% 1.6666666667%, 31.6666666667% 35%); }
  .tile-container[data-shape="11"] .tile-border {
    clip-path: polygon(33.3333333333% 33.3333333333%, 66.6666666667% 33.3333333333%, 100% 33.3333333333%, 100% 66.6666666667%, 100% 100%, 66.6666666667% 100%, 66.6666666667% 66.6666666667%, 33.3333333333% 66.6666666667%, 0% 66.6666666667%, 0% 33.3333333333%, 0% 0%, 33.3333333333% 0%, 33.3333333333% 33.3333333333%, 33.3333333333% 33.3333333333%, 29.1666666667% 37.5%, 29.1666666667% 37.5%, 29.1666666667% 4.1666666667%, 4.1666666667% 4.1666666667%, 4.1666666667% 33.3333333333%, 4.1666666667% 62.5%, 33.3333333333% 62.5%, 70.8333333333% 62.5%, 70.8333333333% 95.8333333333%, 95.8333333333% 95.8333333333%, 95.8333333333% 66.6666666667%, 95.8333333333% 37.5%, 66.6666666667% 37.5%, 29.1666666667% 37.5%); }
  .tile-container[data-shape="11"] .tile-crosshairs {
    left: 50%;
    top: 50%; }
.tile-container[data-type="0"] .tile-inner {
  background-position: 0% 0%; }
.tile-container[data-type="1"] .tile-inner {
  background-position: 9.090909090909092% 7.6923076923076925%; }
.tile-container[data-type="2"] .tile-inner {
  background-position: 30.76923076923077% 0%; }
.tile-container[data-type="3"] .tile-inner {
  background-position: 100% 7.142857142857142%; }
.tile-container[data-type="4"] .tile-inner {
  background-position: 45.45454545454545% 0%; }
.tile-container[data-type="5"] .tile-inner {
  background-position: 54.54545454545454% 7.142857142857142%; }
.tile-container[data-type="6"] .tile-inner {
  background-position: 33.33333333333333% 7.6923076923076925%; }
.tile-container[data-type="7"] .tile-inner {
  background-position: 90.9090909090909% 14.285714285714285%; }
.tile-container[data-type="8"] .tile-inner {
  background-position: 0% 14.285714285714285%; }
.tile-container[data-type="9"] .tile-inner {
  background-position: 100% 0%; }
.tile-container[data-type="10"] .tile-inner {
  background-position: 66.66666666666666% 14.285714285714285%; }
.tile-container[data-type="11"] .tile-inner {
  background-position: 81.81818181818183% 0%; }
.tile-container[data-type="12"] .tile-inner {
  background-position: 0% 28.57142857142857%; }
.tile-container[data-type="13"] .tile-inner {
  background-position: 9.090909090909092% 38.46153846153847%; }
.tile-container[data-type="14"] .tile-inner {
  background-position: 30.76923076923077% 30.76923076923077%; }
.tile-container[data-type="15"] .tile-inner {
  background-position: 100% 35.714285714285715%; }
.tile-container[data-type="16"] .tile-inner {
  background-position: 45.45454545454545% 28.57142857142857%; }
.tile-container[data-type="17"] .tile-inner {
  background-position: 54.54545454545454% 35.714285714285715%; }
.tile-container[data-type="18"] .tile-inner {
  background-position: 33.33333333333333% 38.46153846153847%; }
.tile-container[data-type="19"] .tile-inner {
  background-position: 90.9090909090909% 42.857142857142854%; }
.tile-container[data-type="20"] .tile-inner {
  background-position: 0% 42.857142857142854%; }
.tile-container[data-type="21"] .tile-inner {
  background-position: 100% 26.666666666666668%; }
.tile-container[data-type="22"] .tile-inner {
  background-position: 66.66666666666666% 42.857142857142854%; }
.tile-container[data-type="23"] .tile-inner {
  background-position: 81.81818181818183% 30.76923076923077%; }
.tile-container[data-type="24"] .tile-inner {
  background-position: 0% 57.14285714285714%; }
.tile-container[data-type="25"] .tile-inner {
  background-position: 9.090909090909092% 69.23076923076923%; }
.tile-container[data-type="26"] .tile-inner {
  background-position: 30.76923076923077% 61.53846153846154%; }
.tile-container[data-type="27"] .tile-inner {
  background-position: 100% 64.28571428571429%; }
.tile-container[data-type="28"] .tile-inner {
  background-position: 45.45454545454545% 57.14285714285714%; }
.tile-container[data-type="29"] .tile-inner {
  background-position: 54.54545454545454% 64.28571428571429%; }
.tile-container[data-type="30"] .tile-inner {
  background-position: 33.33333333333333% 69.23076923076923%; }
.tile-container[data-type="31"] .tile-inner {
  background-position: 90.9090909090909% 71.42857142857143%; }
.tile-container[data-type="32"] .tile-inner {
  background-position: 0% 71.42857142857143%; }
.tile-container[data-type="33"] .tile-inner {
  background-position: 100% 53.333333333333336%; }
.tile-container[data-type="34"] .tile-inner {
  background-position: 66.66666666666666% 71.42857142857143%; }
.tile-container[data-type="35"] .tile-inner {
  background-position: 81.81818181818183% 61.53846153846154%; }
.tile-container[data-type="36"] .tile-inner {
  background-position: 0% 85.71428571428571%; }
.tile-container[data-type="37"] .tile-inner {
  background-position: 9.090909090909092% 100%; }
.tile-container[data-type="38"] .tile-inner {
  background-position: 30.76923076923077% 92.3076923076923%; }
.tile-container[data-type="39"] .tile-inner {
  background-position: 100% 92.85714285714286%; }
.tile-container[data-type="40"] .tile-inner {
  background-position: 45.45454545454545% 85.71428571428571%; }
.tile-container[data-type="41"] .tile-inner {
  background-position: 54.54545454545454% 92.85714285714286%; }
.tile-container[data-type="42"] .tile-inner {
  background-position: 33.33333333333333% 100%; }
.tile-container[data-type="43"] .tile-inner {
  background-position: 90.9090909090909% 100%; }
.tile-container[data-type="44"] .tile-inner {
  background-position: 0% 100%; }
.tile-container[data-type="45"] .tile-inner {
  background-position: 100% 80%; }
.tile-container[data-type="46"] .tile-inner {
  background-position: 66.66666666666666% 100%; }
.tile-container[data-type="47"] .tile-inner {
  background-position: 81.81818181818183% 92.3076923076923%; }
.tile-container[data-type="48"] .tile-inner {
  background-position: 0% 0%; }
.tile-container[data-type="49"] .tile-inner {
  background-position: 9.090909090909092% 7.6923076923076925%; }
.tile-container[data-type="50"] .tile-inner {
  background-position: 30.76923076923077% 0%; }
.tile-container[data-type="51"] .tile-inner {
  background-position: 100% 7.142857142857142%; }
.tile-container[data-type="52"] .tile-inner {
  background-position: 45.45454545454545% 0%; }
.tile-container[data-type="53"] .tile-inner {
  background-position: 54.54545454545454% 7.142857142857142%; }
.tile-container[data-type="54"] .tile-inner {
  background-position: 33.33333333333333% 7.6923076923076925%; }
.tile-container[data-type="55"] .tile-inner {
  background-position: 90.9090909090909% 14.285714285714285%; }
.tile-container[data-type="56"] .tile-inner {
  background-position: 0% 14.285714285714285%; }
.tile-container[data-type="57"] .tile-inner {
  background-position: 100% 0%; }
.tile-container[data-type="58"] .tile-inner {
  background-position: 66.66666666666666% 14.285714285714285%; }
.tile-container[data-type="59"] .tile-inner {
  background-position: 81.81818181818183% 0%; }
.tile-container[data-type="60"] .tile-inner {
  background-position: 0% 28.57142857142857%; }
.tile-container[data-type="61"] .tile-inner {
  background-position: 9.090909090909092% 38.46153846153847%; }
.tile-container[data-type="62"] .tile-inner {
  background-position: 30.76923076923077% 30.76923076923077%; }
.tile-container[data-type="63"] .tile-inner {
  background-position: 100% 35.714285714285715%; }
.tile-container[data-type="64"] .tile-inner {
  background-position: 45.45454545454545% 28.57142857142857%; }
.tile-container[data-type="65"] .tile-inner {
  background-position: 54.54545454545454% 35.714285714285715%; }
.tile-container[data-type="66"] .tile-inner {
  background-position: 33.33333333333333% 38.46153846153847%; }
.tile-container[data-type="67"] .tile-inner {
  background-position: 90.9090909090909% 42.857142857142854%; }
.tile-container[data-type="68"] .tile-inner {
  background-position: 0% 42.857142857142854%; }
.tile-container[data-type="69"] .tile-inner {
  background-position: 100% 26.666666666666668%; }
.tile-container[data-type="70"] .tile-inner {
  background-position: 66.66666666666666% 42.857142857142854%; }
.tile-container[data-type="71"] .tile-inner {
  background-position: 81.81818181818183% 30.76923076923077%; }
.tile-container[data-type="72"] .tile-inner {
  background-position: 0% 57.14285714285714%; }
.tile-container[data-type="73"] .tile-inner {
  background-position: 9.090909090909092% 69.23076923076923%; }
.tile-container[data-type="74"] .tile-inner {
  background-position: 30.76923076923077% 61.53846153846154%; }
.tile-container[data-type="75"] .tile-inner {
  background-position: 100% 64.28571428571429%; }
.tile-container[data-type="76"] .tile-inner {
  background-position: 45.45454545454545% 57.14285714285714%; }
.tile-container[data-type="77"] .tile-inner {
  background-position: 54.54545454545454% 64.28571428571429%; }
.tile-container[data-type="78"] .tile-inner {
  background-position: 33.33333333333333% 69.23076923076923%; }
.tile-container[data-type="79"] .tile-inner {
  background-position: 90.9090909090909% 71.42857142857143%; }
.tile-container[data-type="80"] .tile-inner {
  background-position: 0% 71.42857142857143%; }
.tile-container[data-type="81"] .tile-inner {
  background-position: 100% 53.333333333333336%; }
.tile-container[data-type="82"] .tile-inner {
  background-position: 66.66666666666666% 71.42857142857143%; }
.tile-container[data-type="83"] .tile-inner {
  background-position: 81.81818181818183% 61.53846153846154%; }
.tile-container[data-type="84"] .tile-inner {
  background-position: 0% 85.71428571428571%; }
.tile-container[data-type="85"] .tile-inner {
  background-position: 9.090909090909092% 100%; }
.tile-container[data-type="86"] .tile-inner {
  background-position: 30.76923076923077% 92.3076923076923%; }
.tile-container[data-type="87"] .tile-inner {
  background-position: 100% 92.85714285714286%; }
.tile-container[data-type="88"] .tile-inner {
  background-position: 45.45454545454545% 85.71428571428571%; }
.tile-container[data-type="89"] .tile-inner {
  background-position: 54.54545454545454% 92.85714285714286%; }
.tile-container[data-type="90"] .tile-inner {
  background-position: 33.33333333333333% 100%; }
.tile-container[data-type="91"] .tile-inner {
  background-position: 90.9090909090909% 100%; }
.tile-container[data-type="92"] .tile-inner {
  background-position: 0% 100%; }
.tile-container[data-type="93"] .tile-inner {
  background-position: 100% 80%; }
.tile-container[data-type="94"] .tile-inner {
  background-position: 66.66666666666666% 100%; }
.tile-container[data-type="95"] .tile-inner {
  background-position: 81.81818181818183% 92.3076923076923%; }
.tile-container[data-type="96"] .tile-inner {
  background-position: 0% 0%; }
.tile-container[data-type="97"] .tile-inner {
  background-position: 9.090909090909092% 7.6923076923076925%; }
.tile-container[data-type="98"] .tile-inner {
  background-position: 30.76923076923077% 0%; }
.tile-container[data-type="99"] .tile-inner {
  background-position: 100% 7.142857142857142%; }
.tile-container[data-type="100"] .tile-inner {
  background-position: 45.45454545454545% 0%; }
.tile-container[data-type="101"] .tile-inner {
  background-position: 54.54545454545454% 7.142857142857142%; }
.tile-container[data-type="102"] .tile-inner {
  background-position: 33.33333333333333% 7.6923076923076925%; }
.tile-container[data-type="103"] .tile-inner {
  background-position: 90.9090909090909% 14.285714285714285%; }
.tile-container[data-type="104"] .tile-inner {
  background-position: 0% 14.285714285714285%; }
.tile-container[data-type="105"] .tile-inner {
  background-position: 100% 0%; }
.tile-container[data-type="106"] .tile-inner {
  background-position: 66.66666666666666% 14.285714285714285%; }
.tile-container[data-type="107"] .tile-inner {
  background-position: 81.81818181818183% 0%; }
.tile-container[data-type="108"] .tile-inner {
  background-position: 0% 28.57142857142857%; }
.tile-container[data-type="109"] .tile-inner {
  background-position: 9.090909090909092% 38.46153846153847%; }
.tile-container[data-type="110"] .tile-inner {
  background-position: 30.76923076923077% 30.76923076923077%; }
.tile-container[data-type="111"] .tile-inner {
  background-position: 100% 35.714285714285715%; }
.tile-container[data-type="112"] .tile-inner {
  background-position: 45.45454545454545% 28.57142857142857%; }
.tile-container[data-type="113"] .tile-inner {
  background-position: 54.54545454545454% 35.714285714285715%; }
.tile-container[data-type="114"] .tile-inner {
  background-position: 33.33333333333333% 38.46153846153847%; }
.tile-container[data-type="115"] .tile-inner {
  background-position: 90.9090909090909% 42.857142857142854%; }
.tile-container[data-type="116"] .tile-inner {
  background-position: 0% 42.857142857142854%; }
.tile-container[data-type="117"] .tile-inner {
  background-position: 100% 26.666666666666668%; }
.tile-container[data-type="118"] .tile-inner {
  background-position: 66.66666666666666% 42.857142857142854%; }
.tile-container[data-type="119"] .tile-inner {
  background-position: 81.81818181818183% 30.76923076923077%; }
.tile-container[data-type="120"] .tile-inner {
  background-position: 0% 57.14285714285714%; }
.tile-container[data-type="121"] .tile-inner {
  background-position: 9.090909090909092% 69.23076923076923%; }
.tile-container[data-type="122"] .tile-inner {
  background-position: 30.76923076923077% 61.53846153846154%; }
.tile-container[data-type="123"] .tile-inner {
  background-position: 100% 64.28571428571429%; }
.tile-container[data-type="124"] .tile-inner {
  background-position: 45.45454545454545% 57.14285714285714%; }
.tile-container[data-type="125"] .tile-inner {
  background-position: 54.54545454545454% 64.28571428571429%; }
.tile-container[data-type="126"] .tile-inner {
  background-position: 33.33333333333333% 69.23076923076923%; }
.tile-container[data-type="127"] .tile-inner {
  background-position: 90.9090909090909% 71.42857142857143%; }
.tile-container[data-type="128"] .tile-inner {
  background-position: 0% 71.42857142857143%; }
.tile-container[data-type="129"] .tile-inner {
  background-position: 100% 53.333333333333336%; }
.tile-container[data-type="130"] .tile-inner {
  background-position: 66.66666666666666% 71.42857142857143%; }
.tile-container[data-type="131"] .tile-inner {
  background-position: 81.81818181818183% 61.53846153846154%; }
.tile-container[data-type="132"] .tile-inner {
  background-position: 0% 85.71428571428571%; }
.tile-container[data-type="133"] .tile-inner {
  background-position: 9.090909090909092% 100%; }
.tile-container[data-type="134"] .tile-inner {
  background-position: 30.76923076923077% 92.3076923076923%; }
.tile-container[data-type="135"] .tile-inner {
  background-position: 100% 92.85714285714286%; }
.tile-container[data-type="136"] .tile-inner {
  background-position: 45.45454545454545% 85.71428571428571%; }
.tile-container[data-type="137"] .tile-inner {
  background-position: 54.54545454545454% 92.85714285714286%; }
.tile-container[data-type="138"] .tile-inner {
  background-position: 33.33333333333333% 100%; }
.tile-container[data-type="139"] .tile-inner {
  background-position: 90.9090909090909% 100%; }
.tile-container[data-type="140"] .tile-inner {
  background-position: 0% 100%; }
.tile-container[data-type="141"] .tile-inner {
  background-position: 100% 80%; }
.tile-container[data-type="142"] .tile-inner {
  background-position: 66.66666666666666% 100%; }
.tile-container[data-type="143"] .tile-inner {
  background-position: 81.81818181818183% 92.3076923076923%; }

#player_board_config {
  position: relative;
  border-image: none; }

#player_config {
  padding-bottom: 8px; }
  #player_config .player_config_row {
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 5px 0px;
    border-bottom: 1px solid #80502e; }
    #player_config .player_config_row:last-child {
      border-bottom: none;
      padding-bottom: 0px; }
    #player_config .player_config_row#susan-holder {
      height: 355px;
      cursor: zoom-in; }
  #player_config #help-mode-switch .checkbox {
    display: none; }
  #player_config #help-mode-switch .label {
    background-color: #2c3037;
    border-radius: 50px;
    cursor: pointer;
    display: inline-block;
    position: relative;
    height: 26px;
    width: 50px;
    margin-right: 4px; }
  #player_config #help-mode-switch .label .ball {
    background-color: #fff;
    border-radius: 50%;
    position: absolute;
    top: 2px;
    left: 2px;
    height: 22px;
    width: 22px;
    transform: translateX(0px);
    transition: transform 0.2s linear, color 0.7s linear; }
  #player_config #help-mode-switch .checkbox:checked + .label .ball {
    transform: translateX(24px); }
  #player_config #help-mode-switch svg {
    width: 27px; }
  #player_config #show-scores {
    height: 40px;
    width: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer; }
    #player_config #show-scores:hover {
      color: grey; }
    #player_config #show-scores svg {
      width: auto;
      height: 40px; }
  #player_config #show-settings {
    height: 40px;
    width: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer; }
    #player_config #show-settings:hover {
      color: grey; }
    #player_config #show-settings svg {
      width: auto;
      height: 40px; }
  #player_config #show-settings .fa-primary {
    transform-origin: 216px 255px;
    transition: transform 1s; }
  #player_config #show-settings:hover .fa-primary {
    transform: rotate(180deg); }
  #player_config #open-scoreboard {
    cursor: pointer; }
    #player_config #open-scoreboard svg {
      width: 60px;
      height: 30px; }
      #player_config #open-scoreboard svg path {
        fill: #bcbdbd85;
        fill-opacity: 1;
        stroke: #000000;
        stroke-width: 5;
        stroke-linecap: round;
        stroke-linejoin: bevel;
        stroke-miterlimit: 4;
        stroke-dasharray: none;
        stroke-opacity: 1; }
    #player_config #open-scoreboard:hover svg {
      opacity: 0.5; }

#popin_showSettings_underlay {
  background-color: black !important;
  opacity: 0.6; }

/*
 * Controls in the top bar
 */
#settings-controls-container {
  position: relative;
  text-align: center;
  width: 550px; }
  #settings-controls-container #settings-controls-header {
    display: flex;
    align-items: stretch; }
    #settings-controls-container #settings-controls-header div {
      border-right: 1px solid black;
      border-bottom: 1px solid black;
      padding: 5px 8px;
      background: #cca172;
      flex-grow: 1;
      cursor: pointer; }
      #settings-controls-container #settings-controls-header div:last-child {
        border-right: none; }
      #settings-controls-container #settings-controls-header div.open {
        background: none;
        border-bottom: none; }
  #settings-controls-container #settings-controls-wrapper .settings-section {
    display: none; }
    #settings-controls-container #settings-controls-wrapper .settings-section.open {
      display: block; }
  #settings-controls-container .row-data {
    border: none;
    display: flex;
    flex-flow: row;
    justify-content: center;
    align-items: center;
    border-bottom: 1px solid gray; }
    #settings-controls-container .row-data .row-label {
      width: 100%;
      float: none;
      color: black;
      padding-bottom: 0px;
      text-overflow: initial;
      white-space: normal;
      padding: 2px 4px 0px; }
    #settings-controls-container .row-data .row-value {
      width: 85%;
      margin: 0; }
      #settings-controls-container .row-data .row-value.slider {
        width: calc(85% - 40px);
        padding-right: 20px;
        padding-left: 20px;
        box-sizing: content-box; }
    #settings-controls-container .row-data.row-data-switch .row-value {
      padding: 6px 0px; }
    #settings-controls-container .row-data label.switch {
      display: block; }
      #settings-controls-container .row-data label.switch input {
        display: none; }
      #settings-controls-container .row-data label.switch .slider {
        margin: auto;
        height: 27px;
        width: 55px;
        position: relative;
        background-color: #a6a6a6;
        cursor: pointer;
        -webkit-transition: 0.4s;
        transition: 0.4s;
        border-radius: 34px; }
        #settings-controls-container .row-data label.switch .slider::before {
          content: "";
          position: absolute;
          background-color: #fff;
          bottom: 4px;
          height: 19px;
          left: 4px;
          width: 19px;
          border-radius: 50%;
          transition: 0.4s; }
      #settings-controls-container .row-data label.switch input:checked + .slider {
        background-color: #2196f3; }
        #settings-controls-container .row-data label.switch input:checked + .slider::before {
          left: 30px; }
    [data-two-columns="1"] #settings-controls-container .row-data[data-id="columnSizes"] {
      display: none; }

#popin_showSettings,
#popin_showSusan,
#popin_showScores,
#popin_chooseCard,
.planetunknown_popin_cards {
  background-color: #ebd5bd;
  border-radius: 8px;
  box-shadow: 0 3px 8px black;
  position: relative; }
  #popin_showSettings .planetunknown_popin_closeicon,
  #popin_showSettings .planetunknown_popin_cards_closeicon,
  #popin_showSusan .planetunknown_popin_closeicon,
  #popin_showSusan .planetunknown_popin_cards_closeicon,
  #popin_showScores .planetunknown_popin_closeicon,
  #popin_showScores .planetunknown_popin_cards_closeicon,
  #popin_chooseCard .planetunknown_popin_closeicon,
  #popin_chooseCard .planetunknown_popin_cards_closeicon,
  .planetunknown_popin_cards .planetunknown_popin_closeicon,
  .planetunknown_popin_cards .planetunknown_popin_cards_closeicon {
    background-color: #a47a77;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: -18px;
    right: -18px;
    font-size: 90%;
    color: white !important;
    border: 1px solid #35302d;
    z-index: 2; }
    #popin_showSettings .planetunknown_popin_closeicon:hover,
    #popin_showSettings .planetunknown_popin_cards_closeicon:hover,
    #popin_showSusan .planetunknown_popin_closeicon:hover,
    #popin_showSusan .planetunknown_popin_cards_closeicon:hover,
    #popin_showScores .planetunknown_popin_closeicon:hover,
    #popin_showScores .planetunknown_popin_cards_closeicon:hover,
    #popin_chooseCard .planetunknown_popin_closeicon:hover,
    #popin_chooseCard .planetunknown_popin_cards_closeicon:hover,
    .planetunknown_popin_cards .planetunknown_popin_closeicon:hover,
    .planetunknown_popin_cards .planetunknown_popin_cards_closeicon:hover {
      text-decoration: none;
      color: #ccc !important;
      transform: scale(1.1); }
    #popin_showSettings .planetunknown_popin_closeicon i,
    #popin_showSettings .planetunknown_popin_cards_closeicon i,
    #popin_showSusan .planetunknown_popin_closeicon i,
    #popin_showSusan .planetunknown_popin_cards_closeicon i,
    #popin_showScores .planetunknown_popin_closeicon i,
    #popin_showScores .planetunknown_popin_cards_closeicon i,
    #popin_chooseCard .planetunknown_popin_closeicon i,
    #popin_chooseCard .planetunknown_popin_cards_closeicon i,
    .planetunknown_popin_cards .planetunknown_popin_closeicon i,
    .planetunknown_popin_cards .planetunknown_popin_cards_closeicon i {
      margin-top: -2px; }
  #popin_showSettings h2,
  #popin_showSusan h2,
  #popin_showScores h2,
  #popin_chooseCard h2,
  .planetunknown_popin_cards h2 {
    font-family: MyriadPro-Semibold;
    text-align: center;
    font-size: 25px;
    margin: 0px;
    background: #b79570;
    border-radius: 8px 8px 0px 0px;
    padding: 11px 0px 3px;
    border-bottom: 1px solid #734f2a; }

#popin_showScores_underlay {
  background: black !important; }

#popin_showScores {
  border-radius: 0px;
  background: #77bfb8; }
  #popin_showScores #popin_showScores_contents table {
    width: 100%;
    border-collapse: collapse;
    font-family: "HemiHead";
    font-size: 17px; }
    #popin_showScores #popin_showScores_contents table thead tr {
      border-bottom: 2px solid black;
      border-top: 2px solid black; }
      #popin_showScores #popin_showScores_contents table thead tr th {
        padding: 2px 15px;
        min-width: 50px;
        height: 50px;
        vertical-align: middle;
        text-align: center;
        border-left: 2px solid black;
        border-right: 2px solid black; }
      #popin_showScores #popin_showScores_contents table thead tr#scores-names {
        background-color: white; }
        #popin_showScores #popin_showScores_contents table thead tr#scores-names th:first-of-type {
          font-size: 20px; }
        #popin_showScores #popin_showScores_contents table thead tr#scores-names th {
          font-weight: bold; }
      #popin_showScores #popin_showScores_contents table thead tr#scores-planets {
        background: #ccd1d1; }
    #popin_showScores #popin_showScores_contents table tbody tr {
      height: 35px;
      border-bottom: 2px solid #828080; }
      #popin_showScores #popin_showScores_contents table tbody tr:nth-child(odd) {
        background-color: #ccd1d1; }
      #popin_showScores #popin_showScores_contents table tbody tr#scores-row-pastures, #popin_showScores #popin_showScores_contents table tbody tr#scores-row-vegetables, #popin_showScores #popin_showScores_contents table tbody tr#scores-row-cattles, #popin_showScores #popin_showScores_contents table tbody tr#scores-row-stables, #popin_showScores #popin_showScores_contents table tbody tr#scores-row-stoneRooms, #popin_showScores #popin_showScores_contents table tbody tr#scores-row-farmers {
        border-bottom: 1px solid black; }
      #popin_showScores #popin_showScores_contents table tbody tr td {
        vertical-align: top;
        text-align: center;
        word-wrap: anywhere;
        border-left: 2px solid black;
        border-right: 2px solid black;
        min-width: 100px; }
        #popin_showScores #popin_showScores_contents table tbody tr td.row-header {
          text-align: right;
          padding: 4px 12px 3px;
          vertical-align: top; }
        #popin_showScores #popin_showScores_contents table tbody tr td .scoring-entry,
        #popin_showScores #popin_showScores_contents table tbody tr td .scoring-subentry {
          display: flex;
          justify-content: flex-end;
          padding: 6px 8px; }
          #popin_showScores #popin_showScores_contents table tbody tr td .scoring-entry.scoring-subentry,
          #popin_showScores #popin_showScores_contents table tbody tr td .scoring-subentry.scoring-subentry {
            justify-content: space-between;
            padding: 0px 4px; }
            #popin_showScores #popin_showScores_contents table tbody tr td .scoring-entry.scoring-subentry > div,
            #popin_showScores #popin_showScores_contents table tbody tr td .scoring-subentry.scoring-subentry > div {
              padding: 2px 8px;
              line-height: 28px; }
            #popin_showScores #popin_showScores_contents table tbody tr td .scoring-entry.scoring-subentry > div:first-of-type,
            #popin_showScores #popin_showScores_contents table tbody tr td .scoring-subentry.scoring-subentry > div:first-of-type {
              border-right: 1px solid #7b7b7b91;
              margin-top: 0px;
              margin-bottom: 0px;
              min-width: 48px;
              height: 28px;
              text-align: left; }
          #popin_showScores #popin_showScores_contents table tbody tr td .scoring-entry i,
          #popin_showScores #popin_showScores_contents table tbody tr td .scoring-subentry i {
            color: #2222229e;
            font-size: 1.2em;
            margin-left: 4px;
            vertical-align: middle; }
        #popin_showScores #popin_showScores_contents table tbody tr td .scoring-subentries {
          border-top: 1px solid #8a8a8a59; }
          #popin_showScores #popin_showScores_contents table tbody tr td .scoring-subentries .scoring-subentry {
            font-size: 13px; }
            #popin_showScores #popin_showScores_contents table tbody tr td .scoring-subentries .scoring-subentry > div {
              padding: 0px 4px; }
            #popin_showScores #popin_showScores_contents table tbody tr td .scoring-subentries .scoring-subentry > div:first-of-type {
              border-right: none;
              height: auto; }
            #popin_showScores #popin_showScores_contents table tbody tr td .scoring-subentries .scoring-subentry i {
              margin-left: 1px; }
      #popin_showScores #popin_showScores_contents table tbody tr#scores-row-total {
        height: 40px;
        border-top: 2px solid black;
        font-size: 1.2em;
        background: white; }
        #popin_showScores #popin_showScores_contents table tbody tr#scores-row-total td:nth-of-type(1) {
          font-weight: bold;
          text-transform: uppercase; }

#popin_showSusan {
  width: 620px; }
  #popin_showSusan #susan-enlarge {
    width: 630px;
    height: 630px;
    padding: 20px 5px; }
    #popin_showSusan #susan-enlarge #susan-container {
      transform: scale(1.8);
      transform-origin: top left; }
  #popin_showSusan #susan-modal-footer {
    display: none;
    min-height: 50px;
    background-color: #e6e6e6d1;
    border-bottom: 1px solid #6a4b2f;
    justify-content: space-between;
    align-items: center; }
    #popin_showSusan #susan-modal-footer.active {
      display: flex; }
    #popin_showSusan #susan-modal-footer #susan-rotate-cclockwise,
    #popin_showSusan #susan-modal-footer #susan-rotate-clockwise {
      flex-basis: 33%;
      height: 48px;
      background: white;
      cursor: pointer;
      text-align: center;
      padding-top: 2px; }
      #popin_showSusan #susan-modal-footer #susan-rotate-cclockwise svg,
      #popin_showSusan #susan-modal-footer #susan-rotate-clockwise svg {
        height: 45px;
        width: 50px; }
    #popin_showSusan #susan-modal-footer #btnConfirmSusanRotation {
      display: block;
      flex-basis: 33%;
      margin: 0;
      border-radius: 0;
      height: 37px;
      display: flex;
      justify-content: center;
      align-items: center; }

#btnSusanRotateCclockwise svg,
#btnSusanRotateClockwise svg {
  height: 26px;
  width: 30px;
  margin: -4px 0px -4px; }

#popin_chooseCard {
  width: 750px;
  min-height: 300px;
  --cardScale: 1; }
  #popin_chooseCard #planetunknown-choose-card {
    padding: 10px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center; }
    #popin_chooseCard #planetunknown-choose-card .planetunknown-card {
      margin: 7px; }
  #popin_chooseCard #planetunknown-choose-card-footer {
    min-height: 30px;
    background-color: #e6e6e6d1;
    border-top: 1px solid #6a4b2f;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
    text-align: right;
    padding: 0px 10px; }

.planetunknown_popin_cards {
  width: 750px;
  min-height: 300px;
  --cardScale: 1; }
  .planetunknown_popin_cards .modal-cards-holder {
    padding: 10px;
    display: flex;
    flex-wrap: wrap;
    justify-content: center; }
    .planetunknown_popin_cards .modal-cards-holder .planetunknown-card {
      margin: 7px; }

@font-face {
  font-family: "HemiHead";
  src: url("img/fonts/HemiHeadRg-BoldItalic.woff2") format("woff2"), url("img/fonts/HemiHeadRg-BoldItalic.woff") format("woff"), url("img/fonts/HemiHeadRg-BoldItalic.ttf") format("truetype");
  font-weight: bold;
  font-style: italic;
  font-display: swap; }
#left-side {
  margin-right: 355px !important; }

.mobile_version #left-side {
  margin-right: 2px !important; }

#right-side {
  float: left;
  margin-left: -350px;
  margin-top: 5px;
  width: 350px; }

.logs_on_additional_column #right-side {
  margin-left: -600px;
  width: 600px; }
.logs_on_additional_column #right-side-first-part {
  float: left;
  width: 350px; }
.logs_on_additional_column #left-side {
  margin-right: 610px !important; }

.notransition {
  -webkit-transition: none !important;
  -moz-transition: none !important;
  -o-transition: none !important;
  transition: none !important; }

#logs .log.notif_newUndoableStep {
  margin-top: 0px; }
  #logs .log.notif_newUndoableStep .roundedbox {
    display: none;
    text-align: center;
    cursor: pointer;
    background-color: #c4c2c2;
    font-size: 12px;
    padding: 2px 5px; }
    #logs .log.notif_newUndoableStep .roundedbox::before, #logs .log.notif_newUndoableStep .roundedbox::after {
      content: "\f0e2";
      font: normal normal normal 12px/1 FontAwesome;
      margin: 0px 10px; }
  #logs .log.notif_newUndoableStep.selectable .roundedbox {
    display: block; }

.chatwindowlogs_zone .log.notif_newUndoableStep {
  margin-bottom: -4px;
  padding: 0px;
  display: none !important; }
  .chatwindowlogs_zone .log.notif_newUndoableStep.selectable {
    display: block !important; }
    .chatwindowlogs_zone .log.notif_newUndoableStep.selectable .roundedboxinner {
      text-align: center;
      cursor: pointer;
      background-color: #c4c2c2;
      font-size: 12px;
      padding: 2px 5px; }
      .chatwindowlogs_zone .log.notif_newUndoableStep.selectable .roundedboxinner::before, .chatwindowlogs_zone .log.notif_newUndoableStep.selectable .roundedboxinner::after {
        content: "\f0e2";
        font: normal normal normal 12px/1 FontAwesome;
        margin: 0px 10px; }
      .chatwindowlogs_zone .log.notif_newUndoableStep.selectable .roundedboxinner .msgtime {
        display: none; }

.phantom {
  visibility: hidden; }

#pagemaintitletext {
  position: relative; }

#customActions .separator {
  display: inline-block;
  margin-left: 15px; }

#restartAction {
  margin-left: 15px; }

#btnUndoLastStep {
  background: #d97050; }

[data-undobuttons="0"] #btnUndoLastStep {
  display: none; }

[data-undobuttons="2"] #btnRestartTurn {
  display: none; }

#pagesubtitle:not(:empty) {
  padding: 3px 0px; }

#last-round {
  background: #92d3e6;
  color: black;
  margin: 5px -5px -5px;
  text-align: center; }

#btnRotateClockwise,
#btnFlip,
#btnRotateCClockwise {
  color: white;
  font-size: 28px; }

#btnRotateClockwise {
  margin-right: 10px; }

#btnRotateCClockwise {
  margin: 0px 5px 0px 20px; }

#customActions .bgabutton.disabled {
  opacity: 0.5;
  background: linear-gradient(rgba(189, 189, 189, 0.59), rgba(166, 165, 165, 0.45));
  cursor: not-allowed;
  pointer-events: all; }

#customActions .bgabutton.selected {
  background: linear-gradient(rgba(89, 219, 78, 0.87), rgba(87, 149, 59, 0.89)); }

#page-title .pocard-wrapper {
  position: absolute; }

#flux-selection {
  display: inline-block;
  padding-left: 10px; }

#maintitlebar_content .action-button ~ .action-button {
  margin-left: 15px; }

#btnConfirmChoice {
  margin-left: 30px;
  background: #d7ab29; }

#ebd-body.help-mode .tooltipable {
  cursor: help; }

.help-marker {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 20px;
  height: 20px;
  z-index: 900;
  border: 1px solid black;
  border-radius: 50%; }
  .help-marker svg {
    width: 20px;
    height: 20px; }

#ebd-body:not(.help-mode) .help-marker {
  opacity: 0;
  pointer-events: none; }

#logs .log .timestamp {
  color: black; }

/* Cancelled notification messages */
.log.cancel {
  color: #c62828 !important;
  text-decoration: line-through; }

/* Desktop logs */
.log.cancel .roundedbox {
  background-color: rgba(240, 186, 117, 0.6) !important; }

/* Mobile logs */
.log.cancel.roundedbox {
  background-color: #ef9a9a; }

/* Hide the "You may note something for next time..." popup that would appear. */
#turnBasedNotesIncent {
  display: none; }

#customActions .action-button.bgabutton,
#customActions .bgabutton {
  text-overflow: initial; }

/*# sourceMappingURL=planetunknown.cs.map */
