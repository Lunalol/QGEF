define(["dojo", "dojo/_base/declare"], function (dojo, declare)
{
	return declare("alliesDeck", null,
	{
//
		constructor: function (bgagame)
		{
			const FIRST_GAME = 0;
			const MID = 1;
			const LATE = 2;
//
// Reference to BGA game
//
			this.bgagame = bgagame;
//
// Translate
//
			this.cards = {
//
// First game (8-14)
//
				8: {
					faction: 'sovietUnion', type: 'command',
					[FIRST_GAME]: [_('Stavka'), _('Draw 1 card; move a Soviet piece; then take another action.')]
				},
				9: {
					faction: 'sovietUnion', type: 'air',
					[FIRST_GAME]: [_('Women Volunteers'), _('Deploy a Soviet airplane in Moscow and an infantry in a Victory Star space east of the 1939 line.')]
				},
				10: {
					faction: 'sovietUnion', type: 'ground',
					[FIRST_GAME]: [_('Molotov Cocktails'), _('Deploy a Soviet infantry in a space east of the 1939 line; then you may attack with a force containing that infantry.')]
				},
				11: {
					faction: 'sovietUnion', type: 'ground',
					[FIRST_GAME]: [_('Dig in!'), _('Deploy a Soviet infantry in each of 2 different spaces that already contain a Soviet piece.')]
				},
				12: {
					faction: 'sovietUnion', type: 'sea',
					[FIRST_GAME]: [_('Black Sea Fleet'), _('Deploy a Soviet fleet in Sea of Azov.')]
				},
				13: {
					faction: 'sovietUnion', type: 'ground',
					[FIRST_GAME]: [_('Border Guards'), _('Deploy a Soviet infantry in each of 2 different spaces west of the 1939 line.')]
				},
				14: {
					faction: 'sovietUnion', type: 'ground',
					[[FIRST_GAME]]: [_('Mosin–Nagant M1891/30 Rifle'), _('Deploy a Soviet infantry in a space east of the 1939 line; then take another action.')]
				},
//
// Mid game (34-48)
//
				34: {
					faction: 'sovietUnion', type: 'ground',
					[MID]: [_('Trans-Siberian Railway'), _('')]
				},
				35: {
					faction: 'sovietUnion', type: 'ground',
					[MID]: [_('People’s Militia Army'), _('')]
				},
				36: {
					faction: 'sovietUnion', type: 'sea',
					[MID]: [_('Red Banner Baltic Fleet'), _('')]
				},
				37: {
					faction: 'sovietUnion', type: 'command',
					[MID]: [_('Scorched Earth'), _('')]
				},
				38: {
					faction: 'sovietUnion', type: 'command',
					[MID]: [_('General Winter'), _('')]
				},
				39: {
					faction: 'sovietUnion', type: 'command',
					[MID]: [_('Operation Countenance'), _('')]
				},
				40: {
					faction: 'sovietUnion', type: 'ground',
					[MID]: [_('Militsiya'), _('')]
				},
				41: {
					faction: 'sovietUnion', type: 'ground',
					[MID]: [_('Tank Desant'), _('')]
				},
				42: {
					faction: 'sovietUnion', type: 'ground',
					[MID]: [_('The Stronghold of Sevastopol'), _('')]
				},
				43: {
					faction: 'sovietUnion', type: 'ground',
					[MID]: [_('Soviet Cavalry Corps'), _('')]
				},
				44: {
					faction: 'sovietUnion', type: 'ground',
					[MID]: [_('Snipers'), _('')]
				},
				45: {
					faction: 'sovietUnion', type: 'tank',
					[MID]: [_('T-34 Medium Tank'), _('')]
				},
				46: {
					faction: 'sovietUnion', type: 'ground',
					[MID]: [_('Brothers and Sisters!'), _('')]
				},
				47: {
					faction: 'sovietUnion', type: 'command',
					[MID]: [_('General Mud'), _('')]
				},
				48: {
					faction: 'sovietUnion', type: 'command',
					[MID]: [_('5 Year Plan'), _('')]
				}
//
// Late game (72-100)
//
			};
		},
		place: function (card)
		{
			const node = dojo.place(this.bgagame.format_block('QGEFcard', {
				id: card.id, faction: this.cards[card.type_arg].faction,
				type: this.cards[card.type_arg].type,
				title: this.cards[card.type_arg][card.type][0],
				text: this.cards[card.type_arg][card.type][1]
			}), `QGEFhand-allies`);
//
			dojo.connect(node, 'click', this, 'click');
//
			this.bgagame.addTooltip(node.id, this.cards[card.type_arg][card.type][0], this.cards[card.type_arg][card.type][1], 1000);
		},
		click: function (event)
		{
			const node = event.currentTarget;
//
			if (dojo.hasClass(node, 'QGEFselectable'))
			{
				dojo.stopEvent(event);
//
				if (!dojo.hasClass(node, 'QGEFselected'))
				{
					if (dojo.query('.QGEFcardContainer.QGEFselected').length >= 2) return;
				}
				dojo.toggleClass(node, 'QGEFselected');
//
				this.bgagame.updateActionButtons();
			}
		}
	}
	);
});
