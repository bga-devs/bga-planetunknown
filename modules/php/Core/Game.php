<?php
namespace PU\Core;
use planetunknown;

/*
 * Game: a wrapper over table object to allow more generic modules
 */
class Game
{
  public static function get()
  {
    return planetunknown::get();
  }
}
