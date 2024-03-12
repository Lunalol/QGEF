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
					type: 'ground', [FIRST_GAME]: [
						_('Army Group South'),
						_('Deploy a German infantry in or adjacent to Lwow; then move that infantry or attack with a force containing that infantry.')]
				},
				2: {
					type: 'ground', [FIRST_GAME]: [
						_('Army Group Center'),
						_('Deploy a German infantry in or adjacent to Brest; then move that infantry or attack with a force containing that infantry.')]
				},
				3: {
					type: 'ground', [FIRST_GAME]: [
						_('Army Group North'),
						_('Deploy a German infantry in or adjacent to East Prussia; then move that infantry or attack with a force containing that infantry.')]
				},
				4: {
					type: 'air', [FIRST_GAME]: [
						_('Airbase Surprise Attack'),
						_('Deploy a German airplane west of the 1941 line; then you may eliminate an Allies airplane within 2 spaces of the airplane you deployed.')]
				},
				5: {
					type: 'command', [FIRST_GAME]: [
						_('Barbarossa'),
						_('Attack with a German force from a space west of the 1939 line; then take another action.')]
				},
				6: {
					type: 'command', [FIRST_GAME]: [
						_('OKH'),
						_('Draw 2 cards; then take another action.')]
				},
				7: {
					type: 'tank', [FIRST_GAME]: [
						_('Romanian Cavalry Corps'),
						_('Move a Pact tank; then attack with a force containing that tank.')]
				},
//
// Mid game (15-33)
//
				15: {
					type: 'command',
					[MID]: [_('Panzergrenadiers'), _('')]
				},
				16: {
					type: 'ground',
					[MID]: [_('Motorized Corps'), _('')]
				},
				17: {
					type: 'tank',
					[MID]: [_('Leichter Panzerspähwagen'), _('')]
				},
				18: {
					type: 'command',
					[MID]: [_('Directive 21'), _('')]
				},
				19: {
					type: 'ground', text: _('Defending infantry'),
					[MID]: [_('Engineers'), _('')]
				},
				20: {
					type: 'sea',
					[MID]: [_('Marine Infantry'), _('')]
				},
				21: {
					type: 'air',
					[MID]: [_('Messerschmitts'), _('')]
				},
				22: {
					type: 'command',
					[MID]: [_('Sonderkraftfahrzeug'), _('')]
				},
				23: {
					type: 'ground',
					[MID]: [_('Flamethrowers'), _('')]
				},
				24: {
					type: 'command',
					[MID]: [_('Nebelwerfer Rocket Launcher'), _('')]
				},
				25: {
					type: 'command',
					[MID]: [_('Gyorshadtest “Rapid Corps”'), _('')]
				},
				26: {
					type: 'ground',
					[MID]: [_('The Continuation War'), _('')]
				},
				27: {
					type: 'command',
					[MID]: [_('Corpo di Spedizione Italiano in Russia'), _('')]
				},
				28: {
					type: 'ground',
					[MID]: [_('Finnish Ski Troops'), _('')]
				},
				29: {
					type: 'air',
					[MID]: [_('Corpo Aereo Spedizione in Russia'), _('')]
				},
				30: {
					type: 'command',
					[MID]: [_('Romanian 3rd Army'), _('')]
				},
				31: {
					type: 'ground',
					[MID]: [_('Romanian 4th Army'), _('')]
				},
				32: {
					type: 'ground',
					[MID]: [_('Hungarian 2nd Army'), _('')]
				},
				33: {
					type: 'ground',
					[MID]: [_('Armata Italiana in Russia'), _('')]
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
			this.bgagame.addTooltip(node.id, this.cards[card.type_arg][card.type][0], this.cards[card.type_arg][card.type][1], 1000);

			return node;
		},
		card: function (card)
		{
			const reactionSVG = `<img draggable='false' class='QGEFreactionSVG' src='${g_gamethemeurl}img/svg/${this.bgagame.gamedatas.CARDS.axis[card.type_arg].reaction}.svg'>`;
			let reaction = `<div>${this.bgagame.REACTIONS[this.bgagame.gamedatas.CARDS.axis[card.type_arg].reaction]}</div>`;
			if ('text' in this.cards[card.type_arg]) reaction += `<div class='QGEFreactionText'>${this.cards[card.type_arg].text}</div>`;
			if (this.bgagame.gamedatas.CARDS.axis[card.type_arg].reaction === 'Advance') reaction += `<div class='QGEFreactionText'>${_('No Spring Turns')}</div>`;
			if (this.bgagame.gamedatas.CARDS.axis[card.type_arg].reaction === 'SustainAttack') reaction += `<div class='QGEFreactionText'>${_('No Winter Turns')}</div>`;
//
			return this.bgagame.format_block('QGEFcard', {id: card.id,
				FACTION: this.bgagame.gamedatas.CARDS.axis[card.type_arg].faction,
				faction: this.FACTIONS[this.bgagame.gamedatas.CARDS.axis[card.type_arg].faction],
				type: this.cards[card.type_arg].type, type_arg: card.type_arg,
				title: this.cards[card.type_arg][card.type][0],
				text: this.cards[card.type_arg][card.type][1],
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
			if (dojo.hasClass(node, 'QGEFselectable') && this.bgagame.isCurrentPlayerActive())
			{
				dojo.stopEvent(event);
//
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
