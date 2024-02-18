<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
trait gameStateActions
{
	function acPass(): void
	{
		$this->checkAction('pass');
//
		Pieces::setStatus(Pieces::ALL, 'moved');
//
		$this->gamestate->nextState('next');
	}
	function acCancel(): void
	{
		$this->checkAction('cancel');
//
		$FACTION = Factions::getActive();
		Factions::setStatus($FACTION, 'action');
//
		$this->gamestate->nextState('cancel');
	}
	function acMove(string $location, array $pieces): void
	{
		$this->checkAction('move');
//
//		$FACTION = Factions::getActive();
//
// Check movement
//
		foreach ($pieces as $id)
		{
			if (!array_key_exists($id, $this->possible)) throw new BgaVisibleSystemException("Invalid piece: $id");
			if (!in_array($location, $this->possible[$id])) throw new BgaVisibleSystemException("Invalid location: $location");
		}
//
// Do movement
//
		foreach ($pieces as $id)
		{
			$piece = Pieces::get($id);
			if ($piece['location'] !== $location) Pieces::setStatus($id, 'moved');
//
			$piece['location'] = $location;
			Pieces::setLocation($id, $location);
//* -------------------------------------------------------------------------------------------------------- */
			self::notifyAllPlayers('placePiece', '', ['piece' => $piece]);
//* -------------------------------------------------------------------------------------------------------- */
		}
//
		$this->gamestate->nextState('continue');
	}
	function acConscription(array $cards): void
	{
		$this->checkAction('conscription');
//
		if (sizeof($cards) < 1 || sizeof($cards) > 2) throw new BgaVisibleSystemException("Invalid number of cards: " . json_encode($cards));
//
		$FACTION = Factions::getActive();
		Factions::setStatus($FACTION, 'action', ['name' => 'conscription', 'cards' => $cards]);
//
		$this->gamestate->nextState('action');
	}
	function acForcedMarch(array $cards): void
	{
		$this->checkAction('forcedMarch');
//
		if (sizeof($cards) !== 1) throw new BgaVisibleSystemException("Invalid number of cards: " . json_encode($cards));
//
		$FACTION = Factions::getActive();
		Factions::setStatus($FACTION, 'action', ['name' => 'forcedMarch', 'cards' => $cards]);
//
		$this->gamestate->nextState('action');
	}
	function acDesperateAttack(array $cards): void
	{
		$this->checkAction('desperateAttack');
//
		if (sizeof($cards) !== 2) throw new BgaVisibleSystemException("Invalid number of cards: " . json_encode($cards));
//
		$FACTION = Factions::getActive();
		Factions::setStatus($FACTION, 'action', ['name' => 'desperateAttack', 'cards' => $cards]);
//
		$this->gamestate->nextState('action');
	}
	function acProductionInitiative(): void
	{
		$this->checkAction('productionInitiative');
//
		$FACTION = Factions::getActive();
		$card = $this->{$FACTION . 'Deck'}->pickCard('deck', $FACTION);
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers($FACTION . 'Deck', '<B>Production Initiative</B>:<BR>${faction} Draw 1 card', ['card' => $card, 'faction' => $FACTION]);
//* -------------------------------------------------------------------------------------------------------- */
		$this->gamestate->nextState('next');
	}
	function acDeploy(string $location, string $faction, string $type): void
	{
		$this->checkAction('deploy');
//
		if (!array_key_exists($faction, $this->possible)) throw new BgaVisibleSystemException("Invalid faction: $faction");
		if (!array_key_exists($type, $this->possible[$faction])) throw new BgaVisibleSystemException("Invalid type: $type");
		if (!in_array($location, $this->possible[$faction][$type])) throw new BgaVisibleSystemException("Invalid location: $location");
//
		$FACTION = Factions::getActive();
		$action = Factions::getStatus($FACTION, 'action');
		Factions::setStatus($FACTION, 'action');
//* -------------------------------------------------------------------------------------------------------- */
		self::notifyAllPlayers('placePiece', '', ['piece' => Pieces::get(Pieces::create($FACTION, $faction, $type, $location))]);
//* -------------------------------------------------------------------------------------------------------- */
		foreach ($action['cards'] as $card)
		{
			$this->{$FACTION . 'Deck'}->moveCard($card, 'discard', $FACTION);
			self::notifyAllPlayers($FACTION . 'Discard', '${faction} Discard 1 card', ['card' => $card, 'FACTION' => $FACTION, 'faction' => $FACTION]);
		}
//
		$this->gamestate->nextState('next');
	}
}
