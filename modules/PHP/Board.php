<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class Board extends APP_GameClass
{
//
// Regions (44)
//
	const ALL = [BULGARIA, BOSPORUS, ADRIATICSEA, YUGOSLAVIA, BLACKSEA, TRIESTE, ROMANIA, SEVASTOPOL, HUNGARY, BESSARABIA, SEAOFAZOV, VIENNA, CAUCASUS, DNIEPRRIVER, KIEV, LWOW, VOLGARIVER, STALINGRAD, ROSTOV, KHARKOV, MOGILEV, WARSAW, BERLIN, EASTPRUSSIA, BREST, KURSK, WESTBALTICSEA, VORONEZH, SMOLENSK, LAKEPEIPUS, BALTICSTATES, MOSCOW, RYBINSKSEA, GULFOFFINLAND, NOVGOROD, GORKI, LENINGRAD, LAKELADOGA, KARELIA, FINLAND, BALTICSEA];
	const W1939 = [BESSARABIA, LWOW, BREST, BALTICSTATES, KARELIA, BULGARIA, YUGOSLAVIA, TRIESTE, ROMANIA, HUNGARY, VIENNA, WARSAW, BERLIN, EASTPRUSSIA, FINLAND];
	const E1939 = [SEVASTOPOL, CAUCASUS, KIEV, ROSTOV, STALINGRAD, MOGILEV, KHARKOV, SMOLENSK, KURSK, VORONEZH, GORKI, NOVGOROD, MOSCOW, LENINGRAD, PETROZAVODSK, VOLOGDA];
	const W1941 = [BULGARIA, YUGOSLAVIA, TRIESTE, ROMANIA, HUNGARY, VIENNA, WARSAW, BERLIN, EASTPRUSSIA, FINLAND];
	const E1941 = [BESSARABIA, LWOW, BREST, BALTICSTATES, KARELIA, SEVASTOPOL, CAUCASUS, KIEV, ROSTOV, STALINGRAD, MOGILEV, KHARKOV, SMOLENSK, KURSK, VORONEZH, GORKI, NOVGOROD, MOSCOW, LENINGRAD, PETROZAVODSK, VOLOGDA];
//
	const REGIONS = [
		BULGARIA => ['type' => LAND],
		BOSPORUS => ['type' => WATER],
		ADRIATICSEA => ['type' => WATER],
		YUGOSLAVIA => ['type' => LAND, 'VP' => 1],
		BLACKSEA => ['type' => WATER],
		TRIESTE => ['type' => LAND],
		ROMANIA => ['type' => LAND, 'VP' => 1],
		SEVASTOPOL => ['type' => LAND, 'VP' => 1],
		HUNGARY => ['type' => LAND, 'VP' => 1],
		BESSARABIA => ['type' => LAND],
		SEAOFAZOV => ['type' => WATER],
		VIENNA => ['type' => LAND],
		CAUCASUS => ['type' => LAND, 'VP' => 1],
		DNIEPRRIVER => ['type' => WATER],
		KIEV => ['type' => LAND, 'VP' => 1],
		LWOW => ['type' => LAND],
		VOLGARIVER => ['type' => WATER],
		STALINGRAD => ['type' => LAND, 'VP' => 1],
		ROSTOV => ['type' => LAND],
		KHARKOV => ['type' => LAND],
		MOGILEV => ['type' => LAND],
		WARSAW => ['type' => LAND, 'VP' => 1],
		BERLIN => ['type' => LAND, 'VP' => 1],
		EASTPRUSSIA => ['type' => LAND, 'VP' => 1],
		BREST => ['type' => LAND],
		KURSK => ['type' => LAND],
		WESTBALTICSEA => ['type' => WATER],
		VORONEZH => ['type' => LAND, 'VP' => 1],
		SMOLENSK => ['type' => LAND, 'VP' => 1],
		LAKEPEIPUS => ['type' => WATER],
		BALTICSTATES => ['type' => LAND],
		MOSCOW => ['type' => LAND, 'VP' => 2],
		RYBINSKSEA => ['type' => WATER],
		GULFOFFINLAND => ['type' => WATER],
		NOVGOROD => ['type' => LAND],
		GORKI => ['type' => LAND],
		LENINGRAD => ['type' => LAND, 'VP' => 1],
		LAKELADOGA => ['type' => WATER],
		PETROZAVODSK => ['type' => LAND],
		VOLOGDA => ['type' => LAND, 'VP' => 1],
		LAKEONEGA => ['type' => WATER],
		KARELIA => ['type' => LAND],
		FINLAND => ['type' => LAND, 'VP' => 1],
		BALTICSEA => ['type' => WATER],
	];
//
// Adjacency  matrix
//
	const ADJACENCY = [
		BULGARIA => [YUGOSLAVIA, ROMANIA, BOSPORUS],
		BOSPORUS => [BULGARIA, ROMANIA, BLACKSEA],
		ADRIATICSEA => [TRIESTE, YUGOSLAVIA],
		YUGOSLAVIA => [ADRIATICSEA, TRIESTE, VIENNA, HUNGARY, ROMANIA, BULGARIA],
		BLACKSEA => [BOSPORUS, ROMANIA, BESSARABIA, SEVASTOPOL, SEAOFAZOV],
		TRIESTE => [ADRIATICSEA, YUGOSLAVIA, VIENNA],
		ROMANIA => [BULGARIA, YUGOSLAVIA, HUNGARY, LWOW, BESSARABIA, BLACKSEA, BOSPORUS],
		SEVASTOPOL => [BLACKSEA, SEAOFAZOV, ROSTOV, KIEV, BESSARABIA],
		HUNGARY => [YUGOSLAVIA, ROMANIA, LWOW, WARSAW, BERLIN, VIENNA],
		BESSARABIA => [ROMANIA, BLACKSEA, SEVASTOPOL, KIEV, LWOW],
		SEAOFAZOV => [CAUCASUS, ROSTOV, SEVASTOPOL, BLACKSEA],
		VIENNA => [TRIESTE, YUGOSLAVIA, HUNGARY, BERLIN],
		CAUCASUS => [STALINGRAD, ROSTOV, SEAOFAZOV],
		DNIEPRRIVER => [ROSTOV, KHARKOV, KIEV],
		KIEV => [SEVASTOPOL, ROSTOV, DNIEPRRIVER, KHARKOV, MOGILEV, LWOW, BESSARABIA],
		LWOW => [ROMANIA, BESSARABIA, KIEV, MOGILEV, BREST, WARSAW, HUNGARY],
		VOLGARIVER => [STALINGRAD, GORKI, ROSTOV],
		STALINGRAD => [CAUCASUS, GORKI, VOLGARIVER, ROSTOV],
		ROSTOV => [SEAOFAZOV, CAUCASUS, KIEV, STALINGRAD, VOLGARIVER, GORKI, VORONEZH, KHARKOV, DNIEPRRIVER, SEVASTOPOL],
		KHARKOV => [DNIEPRRIVER, ROSTOV, VORONEZH, KURSK, MOGILEV, KIEV],
		MOGILEV => [KIEV, KHARKOV, KURSK, SMOLENSK, BREST, LWOW],
		WARSAW => [HUNGARY, LWOW, BREST, EASTPRUSSIA, WESTBALTICSEA, BERLIN],
		BERLIN => [VIENNA, HUNGARY, WARSAW, WESTBALTICSEA],
		EASTPRUSSIA => [WARSAW, BREST, BALTICSTATES, BALTICSEA, WESTBALTICSEA],
		BREST => [LWOW, MOGILEV, SMOLENSK, BALTICSTATES, EASTPRUSSIA, WARSAW],
		KURSK => [KHARKOV, VORONEZH, MOSCOW, SMOLENSK, MOGILEV],
		WESTBALTICSEA => [BERLIN, WARSAW, EASTPRUSSIA, BALTICSEA],
		VORONEZH => [ROSTOV, GORKI, MOSCOW, KURSK, KHARKOV],
		SMOLENSK => [MOGILEV, KURSK, MOSCOW, NOVGOROD, LAKEPEIPUS, BALTICSTATES, BREST],
		LAKEPEIPUS => [BALTICSTATES, SMOLENSK, NOVGOROD, LENINGRAD],
		BALTICSTATES => [BREST, SMOLENSK, LAKEPEIPUS, LENINGRAD, GULFOFFINLAND, BALTICSEA, EASTPRUSSIA],
		MOSCOW => [KURSK, VORONEZH, GORKI, VOLOGDA, RYBINSKSEA, NOVGOROD, SMOLENSK],
		RYBINSKSEA => [MOSCOW, VOLOGDA, PETROZAVODSK, NOVGOROD],
		GULFOFFINLAND => [BALTICSTATES, LENINGRAD, KARELIA, FINLAND, BALTICSEA],
		NOVGOROD => [SMOLENSK, MOSCOW, RYBINSKSEA, PETROZAVODSK, LAKELADOGA, LENINGRAD, LAKEPEIPUS],
		GORKI => [VOLGARIVER, STALINGRAD, VOLOGDA, MOSCOW, VORONEZH, ROSTOV],
		LENINGRAD => [LAKEPEIPUS, NOVGOROD, LAKELADOGA, KARELIA, GULFOFFINLAND, BALTICSTATES],
		LAKELADOGA => [NOVGOROD, PETROZAVODSK, KARELIA, LENINGRAD],
		PETROZAVODSK => [NOVGOROD, RYBINSKSEA, VOLOGDA, LAKEONEGA, KARELIA, LAKELADOGA],
		VOLOGDA => [MOSCOW, GORKI, LAKEONEGA, PETROZAVODSK, RYBINSKSEA],
		LAKEONEGA => [PETROZAVODSK, VOLOGDA, KARELIA],
		KARELIA => [LAKELADOGA, PETROZAVODSK, LAKEONEGA, FINLAND, GULFOFFINLAND, LENINGRAD],
		FINLAND => [GULFOFFINLAND, KARELIA, BALTICSEA],
		BALTICSEA => [WESTBALTICSEA, EASTPRUSSIA, BALTICSTATES, GULFOFFINLAND, FINLAND],
	];
//
// Supply sources (4 + 2 + 5)
//
	const SUPPLY = [
		Factions::SOVIETUNION => [VOLOGDA, GORKI, STALINGRAD, CAUCASUS],
		Factions::GERMANY => [BERLIN, VIENNA],
		Factions::PACT => [FINLAND, BERLIN, TRIESTE, HUNGARY, ROMANIA],
	];
//
	static $table = null;
	static function updateControl(bool $startOfRound = false): void
	{
		self::$table->DbQuery("UPDATE control SET current = 'both' WHERE terrain = 'water'");
		foreach (array_keys(Factions::FACTIONS) as $FACTION) self::$table->DbQuery("UPDATE control SET current = '$FACTION' WHERE location IN (SELECT DISTINCT location FROM pieces WHERE player = '$FACTION')");
//
		if ($startOfRound) self::$table->DbQuery("UPDATE control SET startOfRound = current");
	}
	static function getControl(string $FACTION, bool $startOfRound = false): array
	{
		if ($startOfRound) return self::$table->getObjectListFromDB("SELECT location FROM control WHERE startOfRound IN ('both', '$FACTION')", true);
		return self::$table->getObjectListFromDB("SELECT location FROM control WHERE current IN ('both', '$FACTION')", true);
	}
	static function getSupplyLines($FACTION)
	{
		$ennemies = Pieces::getEnnemyControled($FACTION);
		$control = self::getControl($FACTION);
//
		foreach (Factions::FACTIONS[$FACTION] as $faction)
		{
			$visited = [];
			$supplyLines[$faction] = [];
//
			$locations = Board::SUPPLY[$faction];
			while ($locations)
			{
				$location = array_pop($locations);
				$visited[] = $location;
//
				$supplied = true;
//
// Can only include spaces controlled by that side (Germany and Pact share control)
//
				if (!in_array($location, $control)) $supplied = false;
				if (in_array($location, $ennemies)) $supplied = false;
//
// Can include unoccupied spaces, as long as they are not adjacent to an enemy piece that is able to interdict supply in that space
// This includes the space with the Supply Flag itself
//
				if ($supplied && !Pieces::getAtLocation($location))
				{
					foreach (Board::ADJACENCY[$location] as $next_location)
					{
						if (in_array($next_location, $ennemies))
						{
							foreach (Pieces::getAtLocation($next_location) as $piece)
							{
//
// Infantry, tanks, and airplanes can interdict supply in adjacent land spaces
//
								if (Board::REGIONS[$location]['type'] === LAND && in_array($piece['type'], [Pieces::INFANTRY, Pieces::TANK, Pieces::AIRPLANE])) $supplied = false;
//
// Fleets and airplanes can interdict supply in adjacent water spaces
//
								if (Board::REGIONS[$location]['type'] === WATER && in_array($piece['type'], [Pieces::AIRPLANE, Pieces::FLEET])) $supplied = false;
							}
						}
					}
				}
//
				if ($supplied)
				{
					if (!in_array($location, $supplyLines[$faction])) $supplyLines[$faction][] = $location;
					foreach (Board::ADJACENCY[$location] as $next_location) if (!in_array($next_location, $visited)) $locations[] = $next_location;
				}
			}
		}
		return $supplyLines;
	}
}
