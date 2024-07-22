<?php
require_once( APP_BASE_PATH . "view/common/game.view.php" );

class view_quartermastergeneraleastfront_quartermastergeneraleastfront extends game_view
{
	protected function getGameName()
	{
		return "quartermastergeneraleastfront";
	}
	function build_page($viewArgs)
	{
		$this->game->loadPlayersBasicInfos();
//
	}
}
