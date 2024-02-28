<?php
/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
define('STUDIO', strpos($_SERVER['HTTP_HOST'], "studio.boardgamearena.com") !== false);
#
# Game Preferences
#
define('SPEED', 100);
define('SLOW', 0);
define('NORMAL', 1);
define('FAST', 2);
#
# Game options
#
define('DUEL', 100);
define('FACTIONSCHOICE', 101);
define('FIRSTGAME', 102);
#
# Globals
#
define('ROUND', 10);
define('ACTION', 11);
#
# Card types
#
define('FIRST_GAME', 0);
define('MID', 1);
define('LATE', 2);
define('INITIAL_SIDE', 3); # contingency
define('SECOND_SIDE', 4);  # contingency
#
# Regions (44)
#
define('LAND', 1);
define('WATER', 2);
#
# Regions (44)
#
define('BULGARIA', 1);
define('BOSPORUS', 2);
define('ADRIATICSEA', 3);
define('YUGOSLAVIA', 4);
define('BLACKSEA', 5);
define('TRIESTE', 6);
define('ROMANIA', 7);
define('SEVASTOPOL', 8);
define('HUNGARY', 9);
define('BESSARABIA', 10);
define('SEAOFAZOV', 11);
define('VIENNA', 12);
define('CAUCASUS', 13);
define('DNIEPRRIVER', 14);
define('KIEV', 15);
define('LWOW', 16);
define('VOLGARIVER', 17);
define('STALINGRAD', 18);
define('ROSTOV', 19);
define('KHARKOV', 20);
define('MOGILEV', 21);
define('WARSAW', 22);
define('BERLIN', 23);
define('EASTPRUSSIA', 24);
define('BREST', 25);
define('KURSK', 26);
define('WESTBALTICSEA', 27);
define('VORONEZH', 28);
define('SMOLENSK', 29);
define('LAKEPEIPUS', 30);
define('BALTICSTATES', 31);
define('MOSCOW', 32);
define('RYBINSKSEA', 33);
define('GULFOFFINLAND', 34);
define('NOVGOROD', 35);
define('GORKI', 36);
define('LENINGRAD', 37);
define('LAKELADOGA', 38);
define('PETROZAVODSK', 39);
define('VOLOGDA', 40);
define('LAKEONEGA', 41);
define('KARELIA', 42);
define('FINLAND', 43);
define('BALTICSEA', 44);
#
