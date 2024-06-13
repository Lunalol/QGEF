CREATE TABLE IF NOT EXISTS `decks` (
	`card_id` INT(3) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`card_type` INT(1) NOT NULL, `card_type_arg` INT(3) NOT NULL,
	`card_location` VARCHAR(15) NOT NULL, `card_location_arg` VARCHAR(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `pieces` (
	`id` INT(2) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`player` ENUM ('allies', 'axis'),
	`faction` ENUM ('sovietUnion', 'germany', 'pact'),
	`type` ENUM('infantry', 'tank', 'airplane', 'fleet'),
	`location` INT(2), `status` JSON
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `markers` (
	`type` ENUM('allies', 'axis', 'Gorki', 'scorchedEarth') PRIMARY KEY, `location` INT(2)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `control` (
	`location` INT(2) PRIMARY KEY, `startOfTurn` ENUM('both','allies', 'axis'),`startOfStep` ENUM('both','allies', 'axis'), `current` ENUM('both','allies', 'axis'), `terrain` ENUM('land', 'water')
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `actions` (
	`id` INT(2) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY, `status` ENUM ('undo', 'done', 'pending'), `data` JSON
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `factions` (
	`faction` ENUM ('allies', 'axis'), `player_id` INT, `activation` ENUM ('no', 'yes', 'done'),`VP` INT DEFAULT 0, `status` JSON
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
