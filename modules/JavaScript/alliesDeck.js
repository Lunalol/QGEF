define(["dojo", "dojo/_base/declare"], function (dojo, declare)
{
	return declare("alliesDeck", null,
	{
//
		constructor: function (bgagame)
		{
			console.log('AlliesDeck constructor');
//
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
			this.FACTIONS = {sovietUnion: _('Soviet Union')};
//
			this.cards = {
//
// First game (8-14)
//
				8: {
					type: 'command',
					text: [
						_('Stavka'),
						_('Draw 1 card; move a Soviet piece; then take another action.')]
				},
				9: {
					type: 'air',
					text: [
						_('Women Volunteers'),
						_('Deploy a Soviet airplane in Moscow and an infantry in a Victory Star space east of the 1939 line.')]
				},
				10: {
					type: 'ground',
					text: [
						_('Molotov Cocktails'),
						_('Deploy a Soviet infantry in a space east of the 1939 line; then you may attack with a force containing that infantry.')]
				},
				11: {
					type: 'ground', reaction: _('Defending infantry'),
					text: [
						_('Dig in!'),
						_('Deploy a Soviet infantry in each of 2 different spaces that already contain a Soviet piece.')]
				},
				12: {
					type: 'sea',
					text: [
						_('Black Sea Fleet'),
						_('Deploy a Soviet fleet in Sea of Azov.')]
				},
				13: {
					type: 'ground',
					text: [
						_('Border Guards'),
						_('Deploy a Soviet infantry in each of 2 different spaces west of the 1939 line.')]
				},
				14: {
					type: 'ground',
					text: [
						_('Mosin–Nagant M1891/30 Rifle'),
						_('Deploy a Soviet infantry in a space east of the 1939 line; then take another action.')]
				},
//
// Mid game (34-48)
//
				34: {
					type: 'ground',
					text: [_('Trans-Siberian Railway'), _('')]
				},
				35: {
					type: 'ground', reation: _('Leningrad'),
					text: [_('People’s Militia Army'), _('')]
				},
				36: {
					type: 'sea',
					text: [_('Red Banner Baltic Fleet'), _('')]
				},
				37: {
					type: 'command',
					text: [_('Scorched Earth'), _('')]
				},
				38: {
					type: 'command',
					text: [_('General Winter'), _('')]
				},
				39: {
					type: 'command',
					text: [_('Operation Countenance'), _('')]
				},
				40: {
					type: 'ground',
					text: [_('Militsiya'), _('')]
				},
				41: {
					type: 'ground',
					text: [_('Tank Desant'), _('')]
				},
				42: {
					type: 'ground', reation: _('Sevastopol'),
					text: [_('The Stronghold of Sevastopol'), _('')]
				},
				43: {
					type: 'ground',
					text: [_('Soviet Cavalry Corps'), _('')]
				},
				44: {
					type: 'ground',
					text: [_('Snipers'), _('')]
				},
				45: {
					type: 'tank',
					text: [_('T-34 Medium Tank'), _('')]
				},
				46: {
					type: 'ground',
					text: [_('Brothers and Sisters!'), _('')]
				},
				47: {
					type: 'command',
					text: [_('General Mud'), _('')]
				},
				48: {
					type: 'command',
					text: [_('5 Year Plan'), _('')]
				}
//
// Late game (72-100)
//
			};
		},
		place: function (card, location = 'QGEFhand-allies')
		{
			const node = dojo.place(this.card(card), location);
			dojo.connect(node, 'click', this, 'click');
//
			this.bgagame.addTooltip(node.id, this.cards[card.type_arg].text[0], this.cards[card.type_arg].text[1], 1000);
			return node;
		},
		card: function (card)
		{
			const reactionSVG = `<img draggable='false' class='QGEFreactionSVG' src='${g_gamethemeurl}img/svg/${this.bgagame.gamedatas.CARDS.allies[card.type_arg].reaction}.svg'>`;
			let reaction = `<div>${this.bgagame.REACTIONS[this.bgagame.gamedatas.CARDS.allies[card.type_arg].reaction]}</div>`;
			if ('reaction' in this.cards[card.type_arg]) reaction += `<div class='QGEFreactionText'>${this.cards[card.type_arg].reaction}</div>`;
			if (this.bgagame.gamedatas.CARDS.allies[card.type_arg].reaction === 'Advance') reaction += `<div class='QGEFreactionText'>${_('No Spring Turns')}</div>`;
			if (this.bgagame.gamedatas.CARDS.allies[card.type_arg].reaction === 'SustainAttack') reaction += `<div class='QGEFreactionText'>${_('No Winter Turns')}</div>`;
//
			return this.bgagame.format_block('QGEFcard', {id: card.id,
				FACTION: this.bgagame.gamedatas.CARDS.allies[card.type_arg].faction,
				faction: this.FACTIONS[this.bgagame.gamedatas.CARDS.allies[card.type_arg].faction],
				type: this.cards[card.type_arg].type, type_arg: card.type_arg,
				title: this.cards[card.type_arg].text[0],
				text: this.cards[card.type_arg].text[1],
				reactionSVG: reactionSVG, reaction: reaction
			});
		},
		play: function (card)
		{
			dojo.query(`.QGEFcardContainer[data-id='${card.id}']`).forEach((node) =>
			{
				dojo.addClass(node, 'QGEFselected');
				this.bgagame.slideToObjectAndDestroy(node, `QGEFplayer-allies-${node.dataset.faction}`, DELAY);
			});
		},
		discard: function (card)
		{
			dojo.query(`.QGEFcardContainer[data-id='${card.id}']`).forEach((node) =>
			{
				dojo.addClass(node, 'QGEFselected');
//				dojo.style(node, 'transition', 'none');
//				dojo.style(node, 'display', '');
				this.bgagame.slideToObjectAndDestroy(node, 'QGEFplayArea', DELAY);
			});
		},
		click: function (event)
		{
			const node = event.currentTarget;
//
			dojo.stopEvent(event);
			if (dojo.hasClass(node, 'QGEFselectable') && this.bgagame.isCurrentPlayerActive())
			{
				if (!dojo.hasClass(node, 'QGEFselected'))
				{
					if (dojo.query('.QGEFhandHolder>.QGEFcardContainer.QGEFselected').length >= 2) return;
					if (this.bgagame.gamedatas.gamestate.name !== 'actionStep') dojo.query('.QGEFcardContainer.QGEFselected').removeClass('QGEFselected');
					dojo.query('.QGEFcontingencyHolder>.QGEFcardContainer.QGEFselected').removeClass('QGEFselected');
				}
				dojo.toggleClass(node, 'QGEFselected');
//
				this.bgagame.updateActionButtons();
			}
		}
	}
	);
});
