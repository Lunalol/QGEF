define(["dojo", "dojo/_base/declare"], function (dojo, declare)
{
	return declare("Panels", null,
	{
//
		constructor: function (bgagame)
		{
//
// Reference to BGA game
//
			this.bgagame = bgagame;
//
// Translate
//

		},
		place: function (FACTION, factions, player_id)
		{
			dojo.place(`<HR>`, `player_board_${player_id}`);
			for (let faction of factions)
			{
				const container = dojo.place(`<div id='QGEFplayer-${FACTION}-${faction}' class='QGEFplayer' style='display:grid;grid-template-columns: 30px repeat(4, 40px);'></div>`, `player_board_${player_id}`);
				dojo.place(`<img style='width:25px;' src='${g_gamethemeurl}img/flag_${faction}.jpg'>`, container);
//
				for (let type of ['infantry', 'tank', 'airplane', 'fleet'])
				{
					let pieceContainer = dojo.place(`<div class='QGEFpieceContainer'></div>`, container);
					let piece = dojo.place(`<div class='QGEFpiece' data-faction='${faction}' data-type='${type}'></div>`, pieceContainer);
					dojo.setStyle(piece, {transform: `scale(${20 / piece.clientHeight})`, 'transform-origin': 'left top', transition: ''});
					dojo.setStyle(pieceContainer, 'width', (piece.clientWidth * 20 / piece.clientHeight) + 'px');
					dojo.setStyle(pieceContainer, 'justify-self', 'center');
//
				}
				dojo.place(`<div></div>`, container);
				for (let type of ['infantry', 'tank', 'airplane', 'fleet'])
					dojo.place(`<div style='font-size:small;font-weight:bold;font-family:GaramondPremrPro;text-align:center;'><span id='QGEF-${FACTION}-${faction}-${type}'>?</span>/${this.bgagame.gamedatas.PIECES[faction][type]}</div>`, container);
//
			}
			dojo.place(`<HR>`, `player_board_${player_id}`);
		},
		update: function (FACTION, faction)
		{
			for (let type of ['infantry', 'tank', 'airplane', 'fleet'])
				$(`QGEF-${FACTION}-${faction}-${type}`).innerHTML = dojo.query(`.QGEFpiece[data-faction='${faction}'][data-type='${type}']`, 'QGEFboard').length;
		}
	}
	);
});
