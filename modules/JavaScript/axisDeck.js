/* global g_gamethemeurl */

define(["dojo", "dojo/_base/declare"], function (dojo, declare)
{
	return declare("axisDeck", null,
	{
//
		constructor: function (bgagame)
		{
			console.log('AxisDeck constructor');
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
			this.FACTIONS = {germany: _('Germany'), pact: _('Pact')};
//
			this.cards = {
//
// First game (1-7)
//
				1: {
					type: 'ground',
					text: [
						_('Army Group South'),
						_('Deploy a German infantry in or adjacent to Lwow; then move that infantry or attack with a force containing that infantry.')]
				},
				2: {
					type: 'ground',
					text: [
						_('Army Group Center'),
						_('Deploy a German infantry in or adjacent to Brest; then move that infantry or attack with a force containing that infantry.')]
				},
				3: {
					type: 'ground',
					text: [
						_('Army Group North'),
						_('Deploy a German infantry in or adjacent to East Prussia; then move that infantry or attack with a force containing that infantry.')]
				},
				4: {
					type: 'air',
					text: [
						_('Airbase Surprise Attack'),
						_('Deploy a German airplane west of the 1941 line; then you may eliminate an Allies airplane within 2 spaces of the airplane you deployed.')]
				},
				5: {
					type: 'command',
					text: [
						_('Barbarossa'),
						_('Attack with a German force from a space west of the 1939 line; then take another action.')]
				},
				6: {
					type: 'command',
					text: [
						_('OKH'),
						_('Draw 2 cards; then take another action.')]
				},
				7: {
					type: 'tank',
					text: [
						_('Romanian Cavalry Corps'),
						_('Move a Pact tank; then attack with a force containing that tank.')]
				},
//
// Mid game (15-33)
//
				15: {
					type: 'command',
					text: [
						_('Panzergrenadiers'),
						_('Attack with a German force containing both an infantry and a tank; then, if it is not a Spring turn, you may move that tank and/or infantry into the attacked space.')]
				},
				16: {
					type: 'ground',
					text: [
						_('Motorized Corps'),
						_('One at a time, move up to 3 different German infantry.')]
				},
				17: {
					type: 'tank',
					text: [
						_('Leichter Panzerspähwagen'),
						_('May only be played if it is not a Spring turn. Move a German tank 1 or 2 times; then take another action.')]
				},
				18: {
					type: 'command',
					text: [
						_('Directive 21'),
						_('Attack with a German force from a space east of the 1941 line; then take another action.')]
				},
				19: {
					type: 'ground', reaction: _('Defending infantry'),
					text: [
						_('Engineers'),
						_('Attack with a German force containing an infantry; then take another action.')]
				},
				20: {
					type: 'sea',
					text: [
						_('Marine Infantry'),
						_('Recruit a German infantry adjacent to a German fleet; then move that infantry or attack with a force containing that infantry.')]
				},
				21: {
					type: 'air',
					text: [
						_('Messerschmitts'),
						_('Deploy a German airplane in each of 2 different spaces west of the 1941 line.')]
				},
				22: {
					type: 'command',
					text: [
						_('Sonderkraftfahrzeug'),
						_('May only be played if it is not a Spring turn. Move both a German infantry and a German tank together; then you may attack with a force containing both pieces.')]
				},
				23: {
					type: 'ground',
					text: [
						_('Flamethrowers'),
						_('Eliminate an Allies infantry or tank adjacent to a German infantry.')]
				},
				24: {
					type: 'command',
					text: [
						_('Nebelwerfer Rocket Launcher'),
						_('Eliminate an Allies infantry adjacent to a German force; then you may attack the space from which the infantry was eliminated.')]
				},
				25: {
					type: 'command',
					text: [
						_('Gyorshadtest “Rapid Corps”'),
						_('Attack with a Pact force; then, if it is not a Spring turn, you may move 1 piece from the attacking force into the attacked space.')]
				},
				26: {
					type: 'ground',
					text: [
						_('The Continuation War'),
						_('Attack into Karelia or Petrozavodsk with a force containing a Pact infantry; then you may move that infantry into the attacked space')]
				},
				27: {
					type: 'command',
					text: [
						_('Corpo di Spedizione Italiano in Russia'),
						_('May only be played if it is not a Spring turn. Move a Pact tank or infantry; then attack with a force containing that piece.')]
				},
				28: {
					type: 'ground',
					text: [
						_('Finnish Ski Troops'),
						_('Eliminate a Soviet infantry or tank in Finland or Karelia; then recruit a Pact infantry in Finland.')]
				},
				29: {
					type: 'air',
					text: [
						_('Corpo Aereo Spedizione in Russia'),
						_('Deploy a Pact airplane east of the 1941 line; then take another action.')]
				},
				30: {
					type: 'command',
					text: [
						_('Romanian 3rd Army'),
						_('Deploy a Pact infantry in or adjacent to Romania; then move that infantry or attack with a force containing that infantry.')]
				},
				31: {
					type: 'ground',
					text: [
						_('Romanian 4th Army'),
						_('Deploy a Pact infantry in or adjacent to Romania; then move that infantry or attack with a force containing that infantry.')]
				},
				32: {
					type: 'ground',
					text: [
						_('Hungarian 2nd Army'),
						_('Deploy a Pact infantry in or adjacent to Hungary; then move that infantry or attack with a force containing that infantry.')]
				},
				33: {
					type: 'ground',
					text: [
						_('Armata Italiana in Russia'),
						_('Deploy a Pact infantry east of the 1941 line; then take another action.')]
				}
//
// Late game (49-71)
//
			};
		},
		place: function (card, location = 'QGEFhand-axis')
		{
			const parent = $(location);
//
			const node = dojo.place(this.card(card), parent);
			dojo.connect(node, 'click', this, 'click');
			this.bgagame.addTooltip(node.id,
					'<H2>' + _('Axis card') + '</H2>' + '<B>' + this.cards[card.type_arg].text[0] + '</B>' + '<BR>',
					this.cards[card.type_arg].text[1], 2000);
//
			Array.from(dojo.query('.QGEFcardContainer', parent)).sort((a, b) => {
				return a.dataset.type_arg - b.dataset.type_arg;
			}).forEach((child) => parent.appendChild(child));
//
			return node;
		},
		card: function (card, clone = false)
		{
			const reactionSVG = `<img draggable='false' class='QGEFreactionSVG' src='${g_gamethemeurl}img/svg/${this.bgagame.gamedatas.CARDS.axis[card.type_arg].reaction}.svg'>`;
			let reaction = `<div>${this.bgagame.REACTIONS[this.bgagame.gamedatas.CARDS.axis[card.type_arg].reaction]}</div>`;
			if ('reaction' in this.cards[card.type_arg]) reaction += `<div class='QGEFreactionText'>${this.cards[card.type_arg].reaction}</div>`;
			if (this.bgagame.gamedatas.CARDS.axis[card.type_arg].reaction === 'Advance') reaction += `<div class='QGEFreactionText'>${_('No Spring Turns')}</div>`;
			if (this.bgagame.gamedatas.CARDS.axis[card.type_arg].reaction === 'SustainAttack') reaction += `<div class='QGEFreactionText'>${_('No Winter Turns')}</div>`;
//
			return this.bgagame.format_block('QGEFcard', {
				id: (clone ? 'clone-' : '') + card.id,
				FACTION: this.bgagame.gamedatas.CARDS.axis[card.type_arg].faction,
				faction: this.FACTIONS[this.bgagame.gamedatas.CARDS.axis[card.type_arg].faction],
				type: this.cards[card.type_arg].type, type_arg: card.type_arg,
				title: this.cards[card.type_arg].text[0],
				text: this.cards[card.type_arg].text[1], FONT: 100 - this.cards[card.type_arg].text[1].length / 5,
				reactionSVG: reactionSVG, reaction: reaction
			});
		},
		play: function (card)
		{
			dojo.query(`.QGEFcardContainer[data-id='${card.id}']`).forEach((node) =>
			{
				dojo.addClass(node, 'QGEFselected');
				this.bgagame.slideToObjectAndDestroy(node, `QGEFplayer-axis-${node.dataset.faction}`, DELAY);
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
					if (dojo.query('.QGEFhandHolder>.QGEFcardContainer.QGEFselectable.QGEFselected').length >= 2) return;
					if (this.bgagame.gamedatas.gamestate.name !== 'actionStep') dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected').removeClass('QGEFselected');
					dojo.query('.QGEFcontingencyHolder>.QGEFcardContainer.QGEFselectable.QGEFselected').removeClass('QGEFselected');
				}
				dojo.toggleClass(node, 'QGEFselected');
//
				this.bgagame.updateActionButtons();
			}
		}
	}
	);
});
