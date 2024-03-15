define(["dojo", "dojo/_base/declare"], function (dojo, declare)
{
	return declare("Pieces", null,
	{
		constructor: function (bgagame)
		{
			console.log('Pieces constructor');
//
// Reference to BGA game
//
			this.bgagame = bgagame;
			this.board = bgagame.board.board;
//
			this.decals = {infantry: 0, tank: -0.5, airplane: 0.5, fleet: -0.5};
		},
		place: function (piece)
		{
			let location = null;
//
			let node = $(`QGEFpiece-${piece.id}`);
			if (!node)
			{
				node = dojo.place(`<div id='QGEFpiece-${piece.id}' class='QGEFpiece' data-id='${piece.id}' data-faction='${piece.faction}' data-type='${piece.type}' data-location='${piece.location}'></div>`, this.board);
				dojo.connect(node, 'click', this, 'click');
			}
			else location = +node.dataset.location;
//
			node.dataset.location = piece.location;
			dojo.removeClass(node, 'QGEFselected QGEFselectable');
//
			this.arrange(piece.location, piece.type);
			if (location && location !== piece.location) this.arrange(location, piece.type);
//
			this.bgagame.panels.update(piece.player, piece.faction, piece.type);
		},
		remove: function (piece)
		{
			dojo.query(`.QGEFpiece[data-id='${piece.id}']`, 'QGEFboard').remove();
			this.arrange(piece.location, piece.type);
//
			this.bgagame.panels.update(piece.player, piece.faction, piece.type);
		},
		arrange: function (location, type)
		{
			const decal = {infantry: 0, tank: -0.5, airplane: 0.5, fleet: -0.5}[type];
			const nodes = dojo.query(`.QGEFpiece[data-type='${type}'][data-location='${location}']`, 'QGEFboard');
			for (let i = 0; i < nodes.length; i++)
			{
				dojo.setStyle(nodes[i], {
					left: (REGIONS[location].x - nodes[i].offsetWidth / 2 + this.decals[type] * REGIONS[location].W - ((i - (nodes.length - 1) / 2) * .25) * REGIONS[location].H) + 'px',
					top: (REGIONS[location].y - nodes[i].offsetHeight / 2 + this.decals[type] * REGIONS[location].H + ((i - (nodes.length - 1) / 2) * .25) * REGIONS[location].W) + 'px'
				});
			}
		},
		click: function (event)
		{
			const node = event.currentTarget;
//
			if (dojo.hasClass(node, 'QGEFselectable') && this.bgagame.isCurrentPlayerActive())
			{
				dojo.stopEvent(event);
//
				if (this.bgagame.gamedatas.gamestate.possibleactions.includes('removePiece')) return this.bgagame.QGEFreaction(node.dataset.id);
				if (this.bgagame.gamedatas.gamestate.possibleactions.includes('reaction')) return this.bgagame.QGEFreaction(node.dataset.id);
//
				if (this.bgagame.gamedatas.gamestate.possibleactions.includes('forcedMarch'))
				{
					if (!dojo.hasClass(node, 'QGEFselected')) dojo.query(`.QGEFpiece.QGEFselectable`, 'QGEFboard').removeClass('QGEFselected');
					dojo.toggleClass(node, 'QGEFselected');
				}
				else if (this.bgagame.gamedatas.gamestate.possibleactions.includes('desperateAttack'))
				{
					dojo.query(`.QGEFpiece.QGEFselectable`, 'QGEFboard').removeClass('QGEFselected');
					dojo.query(`.QGEFpiece.QGEFselectable[data-faction='${node.dataset.faction}'][data-location='${node.dataset.location}']`, 'QGEFboard').toggleClass('QGEFselected');
				}
				else
				{
					if (event.detail === 1) dojo.toggleClass(node, 'QGEFselected');
					else
						dojo.query(`.QGEFpiece.QGEFselectable[data-type='${node.dataset.type}'][data-location='${node.dataset.location}']`, 'QGEFboard').toggleClass('QGEFselected', dojo.hasClass(node, 'QGEFselected'));
				}
//
				if (this.bgagame.gamedatas.gamestate.possibleactions.includes('move') || this.bgagame.gamedatas.gamestate.possibleactions.includes('forcedMarch'))
				{
					this.bgagame.board.clearCanvas();
//
					const pieces = dojo.query('.QGEFpiece.QGEFselected', 'QGEFboard');
					dojo.query('.QGEFregion', 'QGEFboard').forEach((node) => {
//
						let possible = pieces.length > 0;
						for (let piece of pieces)
						{
							if (!this.bgagame.gamedatas.gamestate.args.move[piece.dataset.id].includes(+node.dataset.location))
							{
								possible = false;
								break;
							}
						}
						node.setAttribute('class', possible ? 'QGEFregion QGEFselectable' : 'QGEFregion');
						if (possible) for (let piece of pieces) this.bgagame.board.arrow(+piece.dataset.location, +node.dataset.location, '#00FF0080');
					});
				}
//
				if (this.bgagame.gamedatas.gamestate.possibleactions.includes('attack') || this.bgagame.gamedatas.gamestate.possibleactions.includes('desperateAttack'))
				{
					this.bgagame.board.clearCanvas();
//
					const pieces = dojo.query('.QGEFpiece.QGEFselected', 'QGEFboard');
					dojo.query('.QGEFregion', 'QGEFboard').forEach((node) => {
//
						let possible = pieces.length > 0;
						for (let piece of pieces)
						{
							if (!this.bgagame.gamedatas.gamestate.args.attack[piece.dataset.id].includes(+node.dataset.location))
							{
								possible = false;
								break;
							}
						}
						node.setAttribute('class', possible ? 'QGEFregion QGEFselectable' : 'QGEFregion');
						if (possible) for (let piece of pieces) this.bgagame.board.arrow(+piece.dataset.location, +node.dataset.location, '#FF000080');
					});
				}
			}
			else $(`QGEFregion-${node.dataset.location}`).dispatchEvent(new MouseEvent(event.type, event));
		}
	}
	);
});
