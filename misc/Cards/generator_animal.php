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

function startsWith($haystack, $needle)
{
  $length = strlen($needle);
  return substr($haystack, 0, $length) === $needle;
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

//mkdir('Cards/Animals/');

$o = 0;
$i = 0;

$nAbilities = [
  'Sprint', //N
  'Hunter', //N
  'Inventive', // N + WEIRD STUFF 459
  'Jumping', // N
  'Sunbathing', // N
  'Pouch', // N
  'Flock Animal', // N
  'Digging', // N
  'Venom', // N
  'Pilfering', // N
  'Snapping', // N
  'Hypnosis', // N
  'Scavenging', // N
  'Posturing', // N
  'Perception', // N
];
$abilities = array_merge($nAbilities, [
  'Pack',
  'Clever',
  'Boost', // ACTION,
  'Action', // ACTION
  'Multiplier', // ACTION
  'Full-throated',
  'Iconic Animal', // CONTINENT,
  'Resistance',
  'Assertion',
  'Sponsor Magnet',
  'Constriction',
  'Determination',
  'Peacocking',
  'Petting Zoo Animal',
  'Dominance',
]);

if (($handle = fopen('animals.csv', 'r')) !== false) {
  while (($row = fgetcsv($handle, 1000, ',')) !== false) {
    $parentClass = 'Sponsor';
    $number = $row[0];
    $id = $number;
    $name = ucwords(strtolower($row[1]));
    $slug = slugify($name);
    $className = 'A' . $number . '_' . $slug;

    $latin = $row[2];

    $rawEnclosure = explode('(', $row[3]);
    $normalEnclosure = $rawEnclosure[0];
    $specialEnclosure = $rawEnclosure[1] ?? null;
    if ($normalEnclosure[0] == 'P') {
      $specialEnclosure = $normalEnclosure;
      $normalEnclosure = null;
    }

    $w = 0; // Water
    $r = 0; // Rock
    if (!is_null($normalEnclosure)) {
      $w = substr_count($normalEnclosure, 'W');
      $r = substr_count($normalEnclosure, 'R');
      $size = (int) $normalEnclosure[0];
    }

    if (!is_null($specialEnclosure)) {
      $t = explode(' ', $specialEnclosure);
      $speType = $t[0];
      $speCube = (int) $t[1][0];
    }

    $cost = (int) $row[4];

    $rawCategories = explode('/', $row[5]);

    $rawContinents = $row[6] == '' ? [] : explode('/', $row[6]);

    $rawPrerequisite = $row[7] == '' ? [] : explode("\n", $row[7]);

    $rawAbilities = $row[8] == '' ? [] : explode("\n", $row[8]);

    $rawBonuses = explode('/', $row[9]);
    $appeal = (int) $rawBonuses[0];
    $conservation = (int) $rawBonuses[1];
    $reputation = (int) $rawBonuses[2];

    $fp = fopen('Cards/Animals/' . $className . '.php', 'w');
    fwrite(
      $fp,
      "<?php
namespace PU\Cards\Animals;

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
    \$this->latin = clienttranslate(\"" .
        $latin .
        "\");
    \$this->cost = " .
        $cost .
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

    if (!is_null($normalEnclosure)) {
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

    fwrite($fp, "     \$this->categories = [");
    foreach ($rawCategories as $cat) {
      fwrite($fp, strtoupper($cat) . ', ');
    }
    fwrite($fp, "]; \n");

    fwrite($fp, "     \$this->continents = [");
    foreach ($rawContinents as $cat) {
      fwrite($fp, strtoupper($cat) . ', ');
    }
    fwrite($fp, "]; \n");

    if (!empty($rawPrerequisite)) {
      fwrite($fp, "     \$this->prerequisites = [\n");
      foreach ($rawPrerequisite as $pre) {
        $t = explode(' ', $pre);
        $mapping = [
          'Partner zoo' => 'PARTNER_ZOO',
          'Animals II' => 'UPGRADED_ANIMALS_CARD',
        ];
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
        } elseif (isset($mapping[$pre])) {
          fwrite($fp, '     ' . $mapping[$pre] . " => 1,\n");
        } else {
          fwrite($fp, "     '" . $pre . "',\n");
        }
      }
      fwrite($fp, "]; \n");
    }

    if (!empty($rawAbilities)) {
      fwrite($fp, "     \$this->ability = [");
      foreach ($rawAbilities as $ability) {
        $found = false;
        foreach ($abilities as $ab) {
          if (startsWith($ability, $ab)) {
            $found = true;
            $rest = substr($ability, strlen($ab));
            if (in_array($ab, $nAbilities)) {
              $n = (int) $rest;
              if ($ab == 'Flock Animal') {
                $ab = 'Flock_Animal';
              }

              fwrite($fp, strtoupper($ab) . ' => ' . $n . ', ');
            } elseif (in_array($ab, ['Boost', 'Action', 'Multiplier', 'Iconic Animal'])) {
              $rest = explode(': ', $rest)[1];
              if ($ab == 'Iconic Animal') {
                $ab = 'Iconic_Animal';
              }
              fwrite($fp, strtoupper($ab) . ' => ' . strtoupper($rest) . ', ');
            } else {
              if ($ab == 'Full-throated') {
                $ab = 'Full_throated';
              }
              if ($ab == 'Petting Zoo Animal') {
                $ab = 'Petting_Zoo_Animal';
              }
              if ($ab == 'Sponsor Magnet') {
                $ab = 'Sponsor_Magnet';
              }
              fwrite($fp, strtoupper($ab) . ' => null, ');
            }
          }
        }
        if (!$found) {
          die('test');
        }
      }
      fwrite($fp, "]; \n");
    }

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
