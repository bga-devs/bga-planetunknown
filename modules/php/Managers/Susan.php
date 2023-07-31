<?php

namespace PU\Managers;

use PU\Core\Game;
use PU\Core\Engine;
use PU\Managers\Players;
use PU\Core\Globals;
use PU\Core\Notifications;

/* Class to manage all the stuff around Susan Space station */

class Susan
{
	public static function getDepotOfPlayer($player)
	{
		return static::getDepot($player->getPosition());
	}

	public static function getDepot($position)
	{
		$exterior = ($position + Globals::getSusanRotation()) % 6;
		$interior = ($exterior + Globals::getSusanShift()) % 6;
		return [
			'interior' => $interior,
			'exterior' => $exterior
		];
	}

	public static function getDepots()
	{
		$depots = [];
		for ($i = 0; $i < 6; $i++) {
			$depots[$i] = static::getDepot($i);
		}
		return $depots;
	}

	public static function rotate($nb, $player = null)
	{
		$rotation = Globals::getSusanRotation();
		$rotation = ($rotation + $nb) % 6;
		Globals::setSusanRotation($rotation);
		Notifications::newRotation($rotation, $player);
	}

	public static function getUiData()
	{
		return [
			'shift' => Globals::getSusanShift(),
			'rotation' => Globals::getSusanRotation()
		];
	}

	/*
   * Setup new game
   */
	public static function setupNewGame($players, $options)
	{
		Globals::setSusanShift(bga_rand(0, 5));
		Globals::setSusanRotation(bga_rand(0, 5));
	}
}
