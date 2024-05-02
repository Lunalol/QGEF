/* global g_gamethemeurl */

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
					text: [
						_('Trans-Siberian Railway'),
						_('Deploy 2 or 3 Soviet infantry in Gorki.')]
				},
				35: {
					type: 'ground', reation: _('Leningrad'),
					text: [
						_('People’s Militia Army'),
						_('Deploy 2 Soviet infantry in Leningrad.')]
				},
				36: {
					type: 'sea',
					text: [
						_('Red Banner Baltic Fleet'),
						_('Deploy a Soviet fleet in Gulf of Finland.')]
				},
				37: {
					type: 'command',
					text: [
						_('Scorched Earth'),
						_('')]
				},
				38: {
					type: 'command',
					text: [
						_('General Winter'),
						_('May only be played on a Winter turn. Draw 1 card; the Axis must eliminate a German piece and a Pact piece in land spaces east of the 1939 line.')]
				},
				39: {
					type: 'command',
					text: [
						_('Operation Countenance'),
						_('Deploy a Soviet infantry, tank, and airplane in Caucasus.')]
				},
				40: {
					type: 'ground',
					text: [
						_('Militsiya'),
						_('Deploy a Soviet infantry in each of 2 different spaces east of the 1939 line.')]
				},
				41: {
					type: 'ground',
					text: [
						_('Tank Desant'),
						_('Deploy a Soviet infantry in a space with a Soviet tank; then you may attack with a force containing both pieces.')]
				},
				42: {
					type: 'ground', reation: _('Sevastopol'),
					text: [
						_('The Stronghold of Sevastopol'),
						_('Deploy 2 Soviet infantry in Sevastopol.')]
				},
				43: {
					type: 'ground',
					text: [
						_('Soviet Cavalry Corps'),
						_('Move a Soviet infantry; then attack with a force containing that infantry.')]
				},
				44: {
					type: 'ground',
					text: [
						_('Snipers'),
						_('')]
				},
				45: {
					type: 'tank',
					text: [
						_('T-34 Medium Tank'),
						_('Deploy a Soviet tank in each of 2 different Victory Star spaces east of the 1939 line.')]
				},
				46: {
					type: 'ground',
					text: [
						_('Brothers and Sisters!'),
						_('Deploy a Soviet infantry in each of 3 different Victory Star spaces east of the 1939 line.')]
				},
				47: {
					type: 'command',
					text: [
						_('General Mud'),
						_('')]
				},
				48: {
					type: 'command',
					text: [
						_('5 Year Plan'),
						_('May only be played if you have 3 or fewer cards in hand, including this one. Draw 3 cards; then take another action.')]
				}
//
// Late game (72-100)
//
			};
		},
		place: function (card, location = 'QGEFhand-allies')
		{
			const parent = $(location);
//
			const node = dojo.place(this.card(card), parent);
			dojo.connect(node, 'click', this, 'click');
			this.bgagame.addTooltip(node.id, this.cards[card.type_arg].text[0], this.cards[card.type_arg].text[1], 1000);
//
			Array.from(dojo.query('.QGEFcardContainer', parent)).sort((a, b) => {
				return a.dataset.type_arg - b.dataset.type_arg;
			}).forEach((child) => parent.appendChild(child));
//
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
