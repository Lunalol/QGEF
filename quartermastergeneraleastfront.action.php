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
	public function pass()
	{
		self::setAjaxMode();
		$this->game->acPass();
		self::ajaxResponse("");
	}
	public function move()
	{
		self::setAjaxMode();
		$this->game->acMove(self::getArg("location", AT_json, true), self::getArg("pieces", AT_json, true));
		self::ajaxResponse("");
	}
	public function productionInitiative()
	{
		self::setAjaxMode();
		$this->game->acProductionInitiative();
		self::ajaxResponse("");
	}
}
