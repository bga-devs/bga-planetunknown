<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function varexport($expression, $return = false)
{
  $export = var_export($expression, true);
  $patterns = [
    '/array \(/' => '[',
    '/^([ ]*)\)(,?)$/m' => '$1]$2',
    "/=>[ ]?\n[ ]+\[/" => '=> [',
    "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
  ];
  $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
  if ((bool) $return) {
    return $export;
  } else {
    echo $export;
  }
}

function slugify($text)
{
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '', $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, '');

  // lowercase
  //  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}

mkdir('Classes/Sponsors/');

$o = 0;
$i = 0;

if (($handle = fopen('sponsors.csv', 'r')) !== false) {
  while (($row = fgetcsv($handle, 1000, ':')) !== false) {
    $parentClass = 'Sponsor';
    $number = $row[0];
    $id = $number;
    $name = ucwords(strtolower($row[1]));
    $slug = slugify($name);
    $className = 'S' . $number . '_' . $slug;

    $lvl = $row[2];

    $rawEnclosure = explode('(', $row[3]);
    $normalEnclosure = $rawEnclosure[0];
    $specialEnclosure = $rawEnclosure[1] ?? null;
    if (($normalEnclosure[0] ?? null) == 'P') {
      $specialEnclosure = $normalEnclosure;
      $normalEnclosure = null;
    }

    $w = 0; // Water
    $r = 0; // Rock
    if (!is_null($normalEnclosure)) {
      $w = substr_count($normalEnclosure, 'W');
      $r = substr_count($normalEnclosure, 'R');
      $size = $normalEnclosure[0] ?? null;
    }

    if (!is_null($specialEnclosure)) {
      $t = explode(' ', $specialEnclosure);
      $speType = $t[0];
      $speCube = (int) $t[1][0];
    }

    $rawCategories = $row[4] == '' ? [] : explode('/', $row[4]);
    $rawContinents = $row[5] == '' ? [] : explode('/', $row[5]);
    $rawSciences = $row[6] == '' ? [] : explode('/', $row[6]);
    $rawPrerequisite = $row[7] == '' ? [] : explode("\n", $row[7]);

    $rawBonuses = explode('/', $row[8]);
    $appeal = (int) $rawBonuses[0];
    $conservation = (int) $rawBonuses[1];
    $reputation = (int) $rawBonuses[2];

    $fp = fopen('Classes/Sponsors/' . $className . '.php', 'w');
    fwrite(
      $fp,
      "<?php
namespace PU\Cards\Sponsors;

class " .
        $className .
        ' extends \PU\Models\\' .
        $parentClass .
        "
{
  public function __construct(\$row){
    parent::__construct(\$row);
    \$this->id    = '" .
        $className .
        "';
    \$this->number = " .
        $number .
        ";
    \$this->name  = clienttranslate(\"" .
        $name .
        "\");
    \$this->lvl = " .
        $lvl .
        ";
  "
    );

    if ($appeal > 0) {
      fwrite($fp, "     \$this->appeal = " . $appeal . "; \n");
    }

    if ($conservation > 0) {
      fwrite($fp, "     \$this->conservation = " . $conservation . "; \n");
    }

    if ($reputation > 0) {
      fwrite($fp, "     \$this->reputation = " . $reputation . "; \n");
    }

    if ($w + $r > 0) {
      fwrite($fp, "     \$this->enclosureRequirements = [\n");
      if ($w > 0) {
        fwrite($fp, '     WATER => ' . $w . ",\n");
      }
      if ($r > 0) {
        fwrite($fp, '     ROCK => ' . $r . ",\n");
      }
      fwrite($fp, "     ];\n");
    }

    if (!is_null($normalEnclosure) && !is_null($size)) {
      fwrite($fp, "     \$this->enclosureSize = " . $size . "; \n");
    }

    if (!is_null($specialEnclosure)) {
      $speNames = [
        'RH' => 'REPTILE_HOUSE',
        'LBA' => 'LARGE_BIRD_AVIARY',
        'PZ' => 'PETTING_ZOO',
      ];
      fwrite($fp, "     \$this->specialEnclosure = [\n");
      fwrite($fp, "     'type' => " . $speNames[$speType] . ",\n");
      fwrite($fp, "     'cubes' => " . $speCube . ",\n");
      fwrite($fp, "     ];\n");
    }

    if (!empty($rawCategories)) {
      fwrite($fp, "     \$this->categories = [");
      foreach ($rawCategories as $cat) {
        fwrite($fp, strtoupper($cat) . ', ');
      }
      fwrite($fp, "]; \n");
    }

    if (!empty($rawContinents)) {
      fwrite($fp, "     \$this->continents = [");
      foreach ($rawContinents as $cat) {
        fwrite($fp, strtoupper($cat) . ', ');
      }
      fwrite($fp, "]; \n");
    }

    if (!empty($rawSciences)) {
      fwrite($fp, "     \$this->sciences = [");
      foreach ($rawSciences as $cat) {
        fwrite($fp, strtoupper($cat) . ', ');
      }
      fwrite($fp, "]; \n");
    }

    if (!empty($rawPrerequisite)) {
      fwrite($fp, "     \$this->prerequisites = [\n");
      foreach ($rawPrerequisite as $pre) {
        $t = explode(' ', $pre);
        if (
          in_array($t[0], [
            'Europe',
            'Asia',
            'Africa',
            'Americas',
            'Australia',
            'Predator',
            'Science',
            'Bear',
            'Herbivore',
            'Primate',
            'Bird',
            'Reptile',
          ])
        ) {
          $n = (int) count($t) > 1 ? $t[1][1] : 1;
          fwrite($fp, '     ' . strtoupper($t[0]) . ' => ' . $n . ",\n");
        } else {
          fwrite($fp, "     '" . $pre . "',\n");
        }
      }
      fwrite($fp, "]; \n");
    }

    fwrite($fp, "			\$this->effects = [ \n");
    if ($row[9] == 'oui') {
      fwrite($fp, "				IMMEDIATE => [clienttranslate('')], \n");
    }
    if ($row[12] == 'oui') {
      fwrite($fp, "				PASSIVE => [ clienttranslate('')], \n");
    }
    if ($row[10] == 'oui') {
      fwrite($fp, "				INCOME => [clienttranslate('')], \n");
    }
    if ($row[11] == 'oui') {
      fwrite($fp, "				ENDGAME => [clienttranslate('')], \n");
    }
    fwrite($fp, "]; \n");

    fwrite($fp, "     \$this->implemented = false; \n");

    fwrite(
      $fp,
      "
  }
}
"
    );

    fclose($fp);
  }
  fclose($handle);
}
