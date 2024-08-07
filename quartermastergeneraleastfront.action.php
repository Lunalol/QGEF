<?php

/**
 *
 * @author Lunalol - PERRIN Jean-Luc
 *
 */
class action_quartermastergeneraleastfront extends APP_GameAction
{
	public function __default()
	{
		if (self::isArg('notifwindow'))
		{
			$this->view = "common_notifwindow";
			$this->viewArgs['table'] = self::getArg("table", AT_posint, true);
		}
		else
		{
			$this->view = "quartermastergeneraleastfront_quartermastergeneraleastfront";
			self::trace("Complete reinitialization of board game");
		}
	}
	public function mulligan()
	{
		self::setAjaxMode();
		$this->game->acMulligan(self::getArg("mulligan", AT_bool, true));
		self::ajaxResponse("");
	}
	public function pass()
	{
		self::setAjaxMode();
		$this->game->acPass(self::getArg("FACTION", AT_alphanum, true));
		self::ajaxResponse("");
	}
	public function cancel()
	{
		self::setAjaxMode();
		$this->game->acCancel(self::getArg("FACTION", AT_alphanum, true));
		self::ajaxResponse("");
	}
	public function remove()
	{
		self::setAjaxMode();
		$this->game->acRemove(self::getArg("FACTION", AT_alphanum, true), self::getArg("piece", AT_int, true));
		self::ajaxResponse("");
	}
	public function discard()
	{
		self::setAjaxMode();
		$this->game->acDiscard(self::getArg("FACTION", AT_alphanum, true), self::getArg("cards", AT_json, true));
		self::ajaxResponse("");
	}
	public function VP()
	{
		self::setAjaxMode();
		$this->game->acVP(self::getArg("FACTION", AT_alphanum, true));
		self::ajaxResponse("");
	}
	public function play()
	{
		self::setAjaxMode();
		$this->game->acPlay(self::getArg("FACTION", AT_alphanum, true), self::getArg("card", AT_int, true));
		self::ajaxResponse("");
	}
	public function contingency()
	{
		self::setAjaxMode();
		$this->game->acContingency(self::getArg("FACTION", AT_alphanum, true), self::getArg("card", AT_int, true));
		self::ajaxResponse("");
	}
	public function conscription()
	{
		self::setAjaxMode();
		$this->game->acConscription(self::getArg("FACTION", AT_alphanum, true), self::getArg("cards", AT_json, true));
		self::ajaxResponse("");
	}
	public function forcedMarch()
	{
		self::setAjaxMode();
		$this->game->acForcedMarch(self::getArg("FACTION", AT_alphanum, true), self::getArg("cards", AT_json, true));
		self::ajaxResponse("");
	}
	public function desperateAttack()
	{
		self::setAjaxMode();
		$this->game->acDesperateAttack(self::getArg("FACTION", AT_alphanum, true), self::getArg("cards", AT_json, true));
		self::ajaxResponse("");
	}
	public function productionInitiative()
	{
		self::setAjaxMode();
		$this->game->acProductionInitiative(self::getArg("FACTION", AT_alphanum, true));
		self::ajaxResponse("");
	}
	public function move()
	{
		self::setAjaxMode();
		$this->game->acMove(self::getArg("FACTION", AT_alphanum, true), self::getArg("location", AT_int, true), self::getArg("pieces", AT_json, true));
		self::ajaxResponse("");
	}
	public function deploy()
	{
		self::setAjaxMode();
		$this->game->acDeploy(self::getArg("FACTION", AT_alphanum, true), self::getArg("location", AT_int, true), self::getArg("faction", AT_alphanum, true), self::getArg("type", AT_alphanum, true));
		self::ajaxResponse("");
	}
	public function recruit()
	{
		self::setAjaxMode();
		$this->game->acRecruit(self::getArg("FACTION", AT_alphanum, true), self::getArg("location", AT_int, true), self::getArg("faction", AT_alphanum, true), self::getArg("type", AT_alphanum, true));
		self::ajaxResponse("");
	}
	public function attack()
	{
		self::setAjaxMode();
		$this->game->acAttack(self::getArg("FACTION", AT_alphanum, true), self::getArg("location", AT_int, true), self::getArg("pieces", AT_json, true));
		self::ajaxResponse("");
	}
	public function removePiece()
	{
		self::setAjaxMode();
		$this->game->acRemovePiece(self::getArg("FACTION", AT_alphanum, true), self::getArg("piece", AT_int, true));
		self::ajaxResponse("");
	}
	public function retreat()
	{
		self::setAjaxMode();
		$this->game->acReaction(self::getArg("FACTION", AT_alphanum, true), self::getArg("card", AT_int, true), self::getArg("piece", AT_int, false), self::getArg("location", AT_int, false));
		self::ajaxResponse("");
	}
	public function reaction()
	{
		self::setAjaxMode();
		$this->game->acReaction(self::getArg("FACTION", AT_alphanum, true), self::getArg("card", AT_int, true), self::getArg("piece", AT_int, false), self::getArg("location", AT_int, false));
		self::ajaxResponse("");
	}
	public function scorched()
	{
		self::setAjaxMode();
		$this->game->acScorched(self::getArg("FACTION", AT_alphanum, true), self::getArg("location", AT_int, true));
		self::ajaxResponse("");
	}
}
