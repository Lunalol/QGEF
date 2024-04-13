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
			this.FACTIONS = {germany: _('GERMANY'), pact: _('Pact')};
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
					text: [_('Panzergrenadiers'), _('')]
				},
				16: {
					type: 'ground',
					text: [_('Motorized Corps'), _('')]
				},
				17: {
					type: 'tank',
					text: [_('Leichter Panzerspähwagen'), _('')]
				},
				18: {
					type: 'command',
					text: [_('Directive 21'), _('')]
				},
				19: {
					type: 'ground', reaction: _('Defending infantry'),
					text: [_('Engineers'), _('')]
				},
				20: {
					type: 'sea',
					text: [_('Marine Infantry'), _('')]
				},
				21: {
					type: 'air',
					text: [_('Messerschmitts'), _('')]
				},
				22: {
					type: 'command',
					text: [_('Sonderkraftfahrzeug'), _('')]
				},
				23: {
					type: 'ground',
					text: [_('Flamethrowers'), _('')]
				},
				24: {
					type: 'command',
					text: [_('Nebelwerfer Rocket Launcher'), _('')]
				},
				25: {
					type: 'command',
					text: [_('Gyorshadtest “Rapid Corps”'), _('')]
				},
				26: {
					type: 'ground',
					text: [_('The Continuation War'), _('')]
				},
				27: {
					type: 'command',
					text: [_('Corpo di Spedizione Italiano in Russia'), _('')]
				},
				28: {
					type: 'ground',
					text: [_('Finnish Ski Troops'), _('')]
				},
				29: {
					type: 'air',
					text: [_('Corpo Aereo Spedizione in Russia'), _('')]
				},
				30: {
					type: 'command',
					text: [_('Romanian 3rd Army'), _('')]
				},
				31: {
					type: 'ground',
					text: [_('Romanian 4th Army'), _('')]
				},
				32: {
					type: 'ground',
					text: [_('Hungarian 2nd Army'), _('')]
				},
				33: {
					type: 'ground',
					text: [_('Armata Italiana in Russia'), _('')]
				}
//
// Late game (49-71)
//
			};
		},
		place: function (card, location = 'QGEFhand-axis')
		{
			const node = dojo.place(this.card(card), location);
			dojo.connect(node, 'click', this, 'click');
//
			this.bgagame.addTooltip(node.id, this.cards[card.type_arg].text[0], this.cards[card.type_arg].text[1], 1000);
			return node;
		},
		card: function (card)
		{
			const reactionSVG = `<img draggable='false' class='QGEFreactionSVG' src='${g_gamethemeurl}img/svg/${this.bgagame.gamedatas.CARDS.axis[card.type_arg].reaction}.svg'>`;
			let reaction = `<div>${this.bgagame.REACTIONS[this.bgagame.gamedatas.CARDS.axis[card.type_arg].reaction]}</div>`;
			if ('reaction' in this.cards[card.type_arg]) reaction += `<div class='QGEFreactionText'>${this.cards[card.type_arg].reaction}</div>`;
			if (this.bgagame.gamedatas.CARDS.axis[card.type_arg].reaction === 'Advance') reaction += `<div class='QGEFreactionText'>${_('No Spring Turns')}</div>`;
			if (this.bgagame.gamedatas.CARDS.axis[card.type_arg].reaction === 'SustainAttack') reaction += `<div class='QGEFreactionText'>${_('No Winter Turns')}</div>`;
//
			return this.bgagame.format_block('QGEFcard', {id: card.id,
				FACTION: this.bgagame.gamedatas.CARDS.axis[card.type_arg].faction,
				faction: this.FACTIONS[this.bgagame.gamedatas.CARDS.axis[card.type_arg].faction],
				type: this.cards[card.type_arg].type, type_arg: card.type_arg,
				title: this.cards[card.type_arg].text[0],
				text: this.cards[card.type_arg].text[1],
				reactionSVG: reactionSVG, reaction: reaction
			});
		},
		play: function (card)
		{
			dojo.query(`.QGEFcardContainer[data-id='${card.id}']`, `QGEFhand-axis`).remove();
		},
		discard: function (card)
		{
			dojo.query(`.QGEFcardContainer[data-id='${card.id}']`, `QGEFhand-axis`).remove();
			dojo.query(`.QGEFcardContainer[data-id='${card.id}']`, `QGEFplayArea`).remove();
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
