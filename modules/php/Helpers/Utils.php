<?php
namespace PU\Helpers;
use PU\Managers\ZooCards;
use PU\Managers\ActionCards;

abstract class Utils extends \APP_DbObject
{
  // COMPATIBLE WITH TURKISH I
  public static function ucfirst($str)
  {
    $tmp = preg_split('//u', $str, 2, PREG_SPLIT_NO_EMPTY);
    return mb_convert_case(str_replace('i', 'İ', $tmp[0]), MB_CASE_TITLE, 'UTF-8') . $tmp[1];
  }

  public static function filterPrivateDatas($cards)
  {
    $t = [];
    foreach ($cards as $card) {
      $d = $card->jsonSerialize();
      $t[] = [
        'id' => -1,
        'pId' => $d['pId'],
        'location' => $d['location'],
        'type' => $d['type'],
        'level' => $d['level'] ?? 0,
      ];
    }

    return $t;
  }

  public static function filter(&$data, $filter)
  {
    $data = array_values(array_filter($data, $filter));
  }

  public function rand($array, $n = 1)
  {
    $keys = array_rand($array, $n);
    if ($n == 1) {
      $keys = [$keys];
    }
    $entries = [];
    foreach ($keys as $key) {
      $entries[] = $array[$key];
    }
    shuffle($entries);
    return $entries;
  }

  function getTypesDesc($types)
  {
    $names = [
      BIOMASS => \clienttranslate('Biomass'),
      TECH => \clienttranslate('Tech'),
      CIV => \clienttranslate('Civ'),
      WATER => \clienttranslate('Water'),
      ROVER => \clienttranslate('Rover'),
      ENERGY => \clienttranslate('Energy'),
    ];

    $args = [];
    $logs = [];
    foreach ($types as $i => $type) {
      $logs[] = '${type' . $i . '}';
      $args['type' . $i] = [
        'log' => '${type}${type_name}',
        'args' => [
          'type' => '',
          'type_name' => $names[$type],
          'i18n' => ['type_name'],
        ],
      ];
      $args['i18n'][] = 'type' . $i;
    }

    return [
      'log' => join(', ', $logs),
      'args' => $args,
    ];
  }

  function search($array, $test)
  {
    $found = false;
    $iterator = new \ArrayIterator($array);

    while ($found === false && $iterator->valid()) {
      if ($test($iterator->current())) {
        $found = $iterator->key();
      }
      $iterator->next();
    }

    return $found;
  }

  public static function topological_sort($nodeids, $edges)
  {
    $L = $S = $nodes = [];
    foreach ($nodeids as $id) {
      $nodes[$id] = ['in' => [], 'out' => []];
      foreach ($edges as $e) {
        if ($id == $e[0]) {
          $nodes[$id]['out'][] = $e[1];
        }
        if ($id == $e[1]) {
          $nodes[$id]['in'][] = $e[0];
        }
      }
    }
    foreach ($nodes as $id => $n) {
      if (empty($n['in'])) {
        $S[] = $id;
      }
    }
    while (!empty($S)) {
      $L[] = $id = array_shift($S);
      foreach ($nodes[$id]['out'] as $m) {
        $nodes[$m]['in'] = array_diff($nodes[$m]['in'], [$id]);
        if (empty($nodes[$m]['in'])) {
          $S[] = $m;
        }
      }
      $nodes[$id]['out'] = [];
    }
    foreach ($nodes as $n) {
      if (!empty($n['in']) or !empty($n['out'])) {
        return null; // not sortable as graph is cyclic
      }
    }
    return $L;
  }

  public static function die($args = null)
  {
    if (is_null($args)) {
      throw new \BgaVisibleSystemException(implode('<br>', self::$logmsg));
    }
    throw new \BgaVisibleSystemException(json_encode($args));
  }

  /**
   * Return a string corresponding to an assoc array of resources
   */
  public static function resourcesToStr($resources, $keepZero = false)
  {
    $descs = [];
    foreach ($resources as $resource => $amount) {
      if (in_array($resource, ['sources', 'sourcesDesc', 'cardId', 'cId', 'pId', 'income'])) {
        continue;
      }

      if ($amount == 0 && !$keepZero) {
        continue;
      }

      if (in_array($resource, [REPUTATION, CONSERVATION, APPEAL, MONEY])) {
        $descs[] = '<' . strtoupper($resource) . ':' . $amount . '>';
      } else {
        $descs[] = $amount . '<' . strtoupper($resource) . '>';
      }
    }
    return implode(',', $descs);
  }

  public static function tagTree($t, $tags)
  {
    foreach ($tags as $tag => $v) {
      $t[$tag] = $v;
    }

    if (isset($t['childs'])) {
      $t['childs'] = array_map(function ($child) use ($tags) {
        return self::tagTree($child, $tags);
      }, $t['childs']);
    }
    return $t;
  }

  public static function formatFee($cost)
  {
    return [
      'fees' => [$cost],
    ];
  }

  public static function reduceResources($meeples)
  {
    $allResources = [XTOKEN, REPUTATION, APPEAL, MONEY];
    $t = [];
    foreach ($allResources as $resource) {
      $t[$resource] = 0;
    }

    foreach ($meeples as $meeple) {
      if ($meeple['type'] == SCORE) {
        continue;
      }

      $t[$meeple['type']]++;
    }

    return $t;
  }

  public static function uniqueZones($arr1)
  {
    if (empty($arr1)) {
      return [];
    }
    return array_values(
      array_uunique($arr1, function ($a, $b) {
        return $a['x'] == $b['x'] ? $a['y'] - $b['y'] : $a['x'] - $b['x'];
      })
    );
  }

  /**
   * Intersect two arrays of obj with keys x,y
   */
  public static function intersectZones($arr1, $arr2)
  {
    return array_values(
      \array_uintersect($arr1, $arr2, function ($a, $b) {
        return $a['x'] == $b['x'] ? $a['y'] - $b['y'] : $a['x'] - $b['x'];
      })
    );
  }

  /**
   * Diff two arrays of obj with keys x,y
   */
  public static function diffZones($arr1, $arr2)
  {
    return array_values(
      array_udiff($arr1, $arr2, function ($a, $b) {
        return $a['x'] == $b['x'] ? $a['y'] - $b['y'] : $a['x'] - $b['x'];
      })
    );
  }

  public static function bonus_diff($array1, $array2)
  {
    $result = [];
    foreach ($array1 as $key => $val) {
      if (!in_array($val, $array2)) {
        $result[] = $val;
      }
    }

    return $result;
  }
}

function array_uunique($array, $comparator)
{
  $unique_array = [];
  do {
    $element = array_shift($array);
    $unique_array[] = $element;

    $array = array_udiff($array, [$element], $comparator);
  } while (count($array) > 0);

  return $unique_array;
}
