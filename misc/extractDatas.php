<?php

function clienttranslate($str)
{
  return $str;
}
class APP_DbObject
{
}

include_once '../modules/php/constants.inc.php';
$swdNamespaceAutoload = function ($class) {
  $classParts = explode('\\', $class);
  if ($classParts[0] == 'PU') {
    array_shift($classParts);
    $file = dirname(__FILE__) . '/../modules/php/' . implode(DIRECTORY_SEPARATOR, $classParts) . '.php';
    if (file_exists($file)) {
      require_once $file;
    } else {
      var_dump('Cannot find file : ' . $file);
    }
  }
};
spl_autoload_register($swdNamespaceAutoload, true, true);

/*
function getCardInstance($id, $data = null)
{
  $t = explode('_', $id);
  // First part before _ specify the type and the numbering
  $prefixes = [
    'A' => 'Animals',
    'S' => 'Sponsors',
    'P' => 'Projects',
    'F' => 'FinalScoring',
  ];
  $prefix = $prefixes[$t[0][0]];

  require_once "../modules/php/Cards/$prefix/$id.php";
  $className = "\ARK\Cards\\$prefix\\$id";
  return new $className($data);
}

include_once '../modules/php/Cards/list.inc.php';

$cards = [];
foreach ($cardIds as $cardId) {
  $card = getCardInstance($cardId);
  $cards[$cardId] = $card->getStaticData();
}
*/

$planets = [];
foreach (ALL_PLANETS as $planetId) {
  require_once "../modules/php/Models/Planets/Planet$planetId.php";
  $className = '\PU\Models\Planets\Planet' . $planetId;
  $planet = new $className(null);
  $planets[$planetId] = $planet->getUiData();
}

$corpos = [];
foreach (ALL_CORPORATIONS as $corpoId) {
  require_once "../modules/php/Models/Corporations/Corporation$corpoId.php";
  $className = '\PU\Models\Corporations\Corporation' . $corpoId;
  $corpo = new $className(null);
  $corpos[$corpoId] = $corpo->getUiData();
}

$cards = [];
for ($i = 1; $i <= 124; $i++) {
  if ($i > 0 && $i <= 36) {
    require_once "../modules/php/Models/Cards/CivCard$i.php";
    $className = '\PU\Models\Cards\CivCard' . $i;
  } elseif ($i > 36 && $i <= 64) {
    // require_once "../modules/php/Models/Corporations/Corporation$corpoId.php";
    // $className = '\PU\Models\Cards\OCard';
    continue;
  } elseif ($i > 64 && $i <= 124) {
    require_once "../modules/php/Models/Cards/EventCard$i.php";
    $className = '\PU\Models\Cards\EventCard' . $i;
  }

  $card = new $className(null);
  $cards[$i] = $card->getStaticData();
}
$fp = fopen('../modules/js/data.js', 'w');
// fwrite($fp, 'const CARDS_DATA = ' . json_encode($cards) . ';');
fwrite($fp, 'const PLANETS_DATA = ' . json_encode($planets) . ';');
fwrite($fp, 'const CORPOS_DATA = ' . json_encode($corpos) . ';');
fwrite($fp, 'const CARDS_DATA = ' . json_encode($cards) . ';');
fclose($fp);
