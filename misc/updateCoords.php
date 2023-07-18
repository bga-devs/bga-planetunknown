<?php

$fileList = glob('Maps/*.php');

foreach ($fileList as $f) {
  $content = file_get_contents($f);
  $content = preg_replace_callback(
    '|([0-9]+)\_([0-9]+)|',
    function ($matches) {
      $y = (int) $matches[2];
      $x = (int) 2 * $matches[1] + ($y % 2 == 0 ? 1 : 0);
      return $x . '_' . $y;
    },
    $content
  );

  $fp = fopen($f, 'w');
  fwrite($fp, $content);
  fclose($fp);
}
