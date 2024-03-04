define(["dojo", "dojo/_base/declare"], function (dojo, declare)
{
	return declare("Counters", null,
	{
		constructor: function (bgagame)
		{
			console.log('Counters constructor');
//
// Reference to BGA game
//
			this.bgagame = bgagame;
			this.board = bgagame.board;
		},
		place: function (counter)
		{

		}
	}
	);
});
