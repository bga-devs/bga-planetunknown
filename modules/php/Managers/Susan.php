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

	public static function getPlayableTilesForPlayer($player)
	{
		$tiles = [];
		$depot = Susan::getDepotOfPlayer($player);
		$tile = Tiles::getTopOf('top-interior-' . $depot['interior'])->first();
		if ($tile) {
			$tiles[] = $tile;
		}
		$tile2 = Tiles::getTopOf('top-exterior-' . $depot['exterior'])->first();
		if ($tile2) {
			$tiles[] = $tile2;
		}
		return $tiles;
	}

	public static function getDepots()
	{
		$depots = [];
		for ($i = 0; $i < 6; $i++) {
			$depots[$i] = static::getDepot($i);
		}
		return $depots;
	}

	public static function rotate($rotation, $player = null)
	{
		Globals::setSusanRotation($rotation);
		Notifications::newRotation($rotation, $player);
	}

	public static function refill()
	{
		for ($j = 0; $j < 6; $j++) {
			$tile = Tiles::getTopOf("top-interior-$j")->first();
			if (is_null($tile)) {
				$next_tile = Tiles::getTopOf("interior-$j")->first();
				if (!is_null($next_tile)) {
					Tiles::move($next_tile->getId(), "top-interior-$j");
				}
			}

			$tile = Tiles::getTopOf("top-exterior-$j")->first();
			if (is_null($tile)) {
				$next_tile = Tiles::getTopOf("exterior-$j")->first();
				if (!is_null($next_tile)) {
					Tiles::move($next_tile->getId(), "top-exterior-$j");
				}
			}
		}
	}

	public static function hasEmptyDepot()
	{
		$depots = static::getDepots();

		for ($i = 0; $i < 6; $i++) {
			if (!Tiles::getTopOf("top-interior-" . $depots[$i]['interior'])->first() && !Tiles::getTopOf("top-exterior-" . $depots[$i]['exterior'])->first()) {
				return true;
			}
		}
		return false;
	}

	public static function getUiData()
	{
		return [
			'shift' => Globals::getSusanShift(),
			'rotation' => Globals::getSusanRotation(),
			'decks' => static::getDecks()
		];
	}

	public static function getDecks()
	{
		$result = [];
		for ($i = 0; $i < 6; $i++) {
			foreach (['interior-', 'exterior-'] as $side) {
				$deck = $side . $i;
				$result[$deck] = Tiles::countInLocation($deck) + Tiles::countInLocation('top-' . $deck);
			}
		}
		return $result;
	}

	public static function getTilesNumberInMinDeck()
	{
		$min = 20;
		for ($i = 0; $i < 6; $i++) {
			$tileInDepot = Tiles::countInLocation('interior-' . $i) + Tiles::countInLocation('top-interior-' . $i) +
				Tiles::countInLocation('exterior-' . $i) + Tiles::countInLocation('top-exterior-' . $i);
			$min = min($min, $tileInDepot);
		}
		return $min;
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
