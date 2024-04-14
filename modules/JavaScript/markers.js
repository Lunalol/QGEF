const SIZE = 75;
//
define(["dojo", "dojo/_base/declare"], function (dojo, declare)
{
	return declare("Markers", null,
	{
		constructor: function (bgagame)
		{
			console.log('Markers constructor');
//
//
// Reference to BGA game
//
			this.bgagame = bgagame;
			this.board = bgagame.board.board;
		},
		place: function (marker)
		{
			switch (marker.type)
			{
//
				case 'axis':
				case 'allies':
//
					{
						let node = $(`QGEFmarker-${marker.type}`);
						if (!node) node = dojo.place(`<div id='QGEFmarker-${marker.type}' class='QGEFmarker' data-type='${marker.type}' data-location='${marker.location}'></div>`, this.board);
						dojo.setStyle(node, {
							left: (Math.min(marker.location, 25) * SIZE + 20) + 'px',
							top: (Math.max(marker.location - 25, 0) * SIZE + 20) + 'px'
						});
						node.dataset.location = marker.location;
					}
//
					const axis = $('QGEFmarker-axis');
					const allies = $('QGEFmarker-allies');
//
					if (axis && allies)
					{
						if (axis.dataset.location === allies.dataset.location)
						{
							dojo.style(axis, 'margin', '-10px');
							dojo.style(allies, 'margin', '10px');
						}
						else
						{
							dojo.style(axis, 'margin', '');
							dojo.style(allies, 'margin', '');
						}
					}
//
					break;
//
			}
		}
	}
	);
});
