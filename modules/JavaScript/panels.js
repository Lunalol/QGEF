/* global g_gamethemeurl */

define(["dojo", "dojo/_base/declare"], function (dojo, declare)
{
	return declare("Panels", null,
	{
//
		constructor: function (bgagame, players)
		{
			console.log('Panels constructor');
//
// Reference to BGA game
//
			this.bgagame = bgagame;
//
			for (let player_id in players) dojo.place(`<div id='QGEFplayer-${player_id}' class='QGEFcontainer'></div>`, `player_board_${player_id}`);
//
			dojo.connect(dojo.byId('player_boards'), 'mouseenter', () =>
				dojo.query('.QGEFregion', 'QGEFboard').forEach((node) => {
					const region = node.dataset.location;
					const allies = this.bgagame.gamedatas.factions.allies.control.includes(region);
					const axis = this.bgagame.gamedatas.factions.axis.control.includes(region);
					if (!axis && allies) node.setAttribute('fill', '#be1e1e80');
					if (axis && !allies) node.setAttribute('fill', '#4d514d80');
				})
			);
			dojo.connect(dojo.byId('player_boards'), 'mouseleave', () => dojo.query('.QGEFregion', 'QGEFboard').forEach((node) => node.setAttribute('fill', 'transparent')));
//
		},
		place: function (FACTION, factions, player_id)
		{
			const player = `QGEFplayer-${player_id}`;
//
			for (let faction of factions)
			{
				const container = dojo.place(`<div id='QGEFplayer-${FACTION}-${faction}' class='QGEFplayer' style='display:grid;grid-template-columns: 30px repeat(4, 40px);'></div>`, player);
				const flag = dojo.place(`<img style='width:25px;' src='${g_gamethemeurl}img/flag_${faction}.jpg'>`, container);
//
				dojo.connect(flag, 'click', () => this.bgagame.board.supplyLines(this.bgagame.gamedatas.factions[FACTION].supply[faction], '#ffffff40'));
//
				for (let type of ['infantry', 'tank', 'airplane', 'fleet'])
				{
					let pieceContainer = dojo.place(`<div id='QGEFpiece-${FACTION}-${faction}-${type}' class='QGEFpieceContainer'></div>`, container);
					let piece = dojo.place(`<div class='QGEFpiece' data-faction='${faction}' data-type='${type}'></div>`, pieceContainer);
					dojo.setStyle(piece, {transform: `scale(${20 / piece.clientHeight})`, 'transform-origin': 'left top', transition: ''});
					dojo.setStyle(pieceContainer, 'width', (piece.clientWidth * 20 / piece.clientHeight) + 'px');
					dojo.setStyle(pieceContainer, 'justify-self', 'center');
//
				}
				dojo.place(`<div></div>`, container);
				for (let type of ['infantry', 'tank', 'airplane', 'fleet'])
					dojo.place(`<div style='font-size:small;font-weight:bold;font-family:GaramondPremrPro;text-align:center;'><span id='QGEFpieces-${FACTION}-${faction}-${type}'>?</span>/${this.bgagame.gamedatas.PIECES[faction][type]}</div>`, container);
//
			}
		},
		update: function (FACTION, faction)
		{
			for (let type of ['infantry', 'tank', 'airplane', 'fleet'])
			{
				const pieces = dojo.query(`.QGEFpiece[data-faction='${faction}'][data-type='${type}']`, 'QGEFboard').length;
				dojo.toggleClass(`QGEFpiece-${FACTION}-${faction}-${type}`, 'QGEFdisabled', pieces === this.bgagame.gamedatas.PIECES[faction][type]);
				$(`QGEFpieces-${FACTION}-${faction}-${type}`).innerHTML = pieces;
			}
		}
	}
	);
});
