<?php
namespace PU\Managers;

/* Class to manage all the Buildings for ArkNova */

class Buildings extends \PU\Helpers\Pieces
{
  protected static $table = 'buildings';
  protected static $prefix = 'building_';
  protected static $customFields = ['type', 'player_id', 'x', 'y', 'rotation'];

  protected static function cast($building)
  {
    return [
      'id' => (int) $building['id'],
      'location' => $building['location'],
      'state' => $building['state'],
      'pId' => (int) $building['player_id'],
      'type' => $building['type'],
      'x' => (int) $building['x'],
      'y' => (int) $building['y'],
      'rotation' => (int) $building['rotation'],
    ];
  }

  public static function setupNextGame()
  {
    // deletion of all
    self::DB()
      ->delete()
      ->run();
  }

  public static function getUiData()
  {
    return self::getAll()->toArray();
  }

  public static function getOfPlayer($pId)
  {
    return self::getSelectQuery()
      ->wherePlayer($pId)
      ->get();
  }

  public static function add($pId, $type, $pos, $rotation)
  {
    return self::singleCreate([
      'location' => 'board',
      'player_id' => $pId,
      'type' => $type,
      'x' => $pos['x'],
      'y' => $pos['y'],
      'rotation' => $rotation,
    ]);
  }
}
