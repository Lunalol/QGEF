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
// Reference to BGA game
//
			this.bgagame = bgagame;
			this.type = {[FIRST_GAME]: _('First game'), [MID]: _('MID'), [LATE]: _('LATE')};
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
					type: 'ground', reaction: _('Leningrad'),
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
						_('Gain 1 VP; then place the Scorched Earth marker on top of any Victory Star east of the 1939 line. That Victory Star is ignored for all purposes for the remainder of the game.')
					]
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
					type: 'ground', reaction: _('Sevastopol'),
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
						_('Eliminate an Axis infantry or tank adjacent to a Soviet infantry.')]
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
						_('On the next Axis turn, the Axis cannot use Advance! reactions and must skip the Second Movement step.')]
				},
				48: {
					type: 'command',
					text: [
						_('5 Year Plan'),
						_('May only be played if you have 3 or fewer cards in hand, including this one. Draw 3 cards; then take another action.')]
				},
//
// Late game (72-100)
//
				72: {
					type: 'command',
					text: [
						_('Operation Orator'),
						_('Deploy a Soviet infantry, tank, and airplane in Vologda.')]
				},
				73: {
					type: 'command',
					text: [
						_('Pripyat Partisans'),
						_('Gain 2 VPs unless the Axis either eliminates an Axis piece in Mogilev or discards 3 cards.')]
				},
				74: {
					type: 'tank',
					text: [
						_('Shock Armies'),
						_('Deploy a Soviet tank in a space containing a Soviet infantry; then you may attack with a force containing both pieces.')]
				},
				75: {
					type: 'command',
					text: [
						_('Katyusha Rocket Launcher'),
						_('Eliminate an Axis infantry adjacent to a Soviet force; then you may attack the space from which the infantry was eliminated.')]
				},
				76: {
					type: 'ground',
					text: [
						_('Studebaker Deuceand-a-Half Truck'),
						_('May only be played if it is not a Spring turn. Attack with a force containing a Soviet infantry; then, you may move that infantry into the attacked space.')]
				},
				77: {
					type: 'sea',
					text: [
						_('Road of Life'),
						_('Deploy a Soviet infantry in Petrozavodsk and a Soviet fleet in Lake Ladoga.')]
				},
				78: {
					type: 'command',
					text: [
						_('Warsaw Uprising'),
						_('Gain 2 VPs unless the Axis either eliminates an Axis piece in Warsaw or discards 3 cards.')]
				},
				79: {
					type: 'ground', reaction: _('Moscow'),
					text: [
						_('Counteroffensive'),
						_('Deploy a Soviet infantry in or adjacent to Moscow; then you may attack with a force containing that infantry.')]
				},
				80: {
					type: 'command',
					text: [
						_('Moscow Armistice'),
						_('May only be played if 4 or more Soviet pieces are in or adjacent to Finland. Gain 1 VP; the Axis must either eliminate a Pact piece in Finland or discard 2 cards , if possible.')
					]
				},
				81: {
					type: 'tank', reaction: _('Stalingrad'),
					text: [
						_('Volgograd Tractor Plant'),
						_('Deploy a Soviet tank in or adjacent to Stalingrad; then you may attack with a force containing that tank.')]
				},
				82: {
					type: 'air',
					text: [
						_('Airborne Corps'),
						_('Deploy a Soviet infantry in a space with a Soviet airplane; then attack with a force containing that infantry; then move that infantry into the attacked space.')]
				},
				83: {
					type: 'air',
					text: [
						_('Yak-3 Fighter'),
						_('Deploy a Soviet airplane east of the 1939 line; then you may eliminate an Axis airplane adjacent to the airplane you deployed.')]
				},
				84: {
					type: 'command',
					text: [
						_('Yalta Conference'),
						_('Draw 2 cards; then gain 1 VP if a Soviet piece is in or adjacent to Warsaw; then gain 1 VP if a Soviet piece is in or adjacent to Berlin.')]
				},
				85: {
					type: 'ground',
					text: [
						_('Guards Armies'),
						_('Deploy a Soviet infantry in a space with a Soviet tank; then you may attack with a force containing both pieces.')]
				},
				86: {
					type: 'command',
					text: [
						_('Industrial Evacuation'),
						_('Draw 1 card; then place the Gorki Victory Star marker on Gorki. (This marker counts as a Victory Star.)')]
				},
				87: {
					type: 'tank',
					text: [
						_('IS-2 Heavy Tank'),
						_('Eliminate an Axis tank adjacent to a Soviet tank; then, if it is not a Winter turn, you may attack the space from which the tank was eliminated.')]
				},
				88: {
					type: 'command',
					text: [
						_('King Michael Stages Coup'),
						_('If 2 or more Soviet pieces are in or adjacent to Romania: Gain 1 VP; the Axis must either eliminate a Pact piece in Romania or discard 2 cards, if possible.')]
				},
				89: {
					type: 'air',
					text: [
						_('Night Witches'),
						_('Deploy a Soviet airplane in a space east of the 1939 line; then you may attack with a Soviet force from that space.')]
				},
				90: {
					type: 'ground',
					text: [
						_('Diversionist Groups'),
						_('Gain 1 VP; then recruit a Soviet infantry in an unoccupied Axis controlled space east of the 1941 line. That infantry is supplied until the start of your next turn.')]
				},
				91: {
					type: 'command',
					text: [
						_('Tito Leads Yugoslav Resistance'),
						_('Gain 2 VPs unless the Axis either eliminates an Axis piece in Yugoslavia or discards 3 cards.')]
				},
				92: {
					type: 'command',
					text: [
						_('Breakthrough Artillery Divisions'),
						_('Attack with a Soviet force; then take another action.')]
				},
				93: {
					type: 'tank',
					text: [
						_('Bagration Strategic Offensive Operation'),
						_('Deploy a Soviet tank in or adjacent to Smolensk; then attack with a force containing that tank.')]
				},
				94: {
					type: 'air',
					text: [
						_('IL-2 Shturmovik'),
						_('Deploy a Soviet airplane in each of 2 different Victory Star spaces east of the 1939 line.')]
				},
				95: {
					type: 'ground',
					text: [
						_('Massed Attack'),
						_('Attack with a force containing 2 Soviet infantry; then you may attack a different space with that force if it still contains 2 infantry.')]
				},
				96: {
					type: 'command', reaction: _('Defending tank'),
					text: [
						_('Kursk Strategic Defensive Operation'),
						_('May only be played during your Second Action step. Deploy a Soviet infantry and 2 tanks in the same space, in or adjacent to Kursk.')]
				},
				97: {
					type: 'command',
					text: [
						_('Teheran Conference'),
						_('Draw 2 cards; then take another action.')]
				},
				98: {
					type: 'command',
					text: [
						_('Command Economy'),
						_('Draw 3 cards; then discard 1 card and deploy a Soviet piece in a land space east of the 1939 line.')]
				},
				99: {
					type: 'ground',
					text: [
						_('Forward!'),
						_('One at a time, move 1 or 2 different Soviet infantry; then take another action.')]
				},
				100: {
					type: 'ground',
					text: [
						_('Hero of the Soviet Union'),
						_('Eliminate a Soviet infantry in order to eliminate an adjacent Axis infantry or tank; then take another action.')]
				},
			};
		},
		place: function (card, location = 'QGEFhand-allies')
		{
			if (!(card.type_arg in this.bgagame.gamedatas.CARDS.allies)) return null;
//
			const parent = $(location);
//
			const node = dojo.place(this.card(card), parent);
			dojo.connect(node, 'click', this, 'click');
			this.bgagame.addTooltip(node.id,
					'<H2>' + _('Allies card') + '</H2>' + '<B>' + _(this.cards[card.type_arg].text[0]) + '</B>' + '<BR>',
					_(this.cards[card.type_arg].text[1]), 2000);
//
			Array.from(dojo.query('.QGEFcardContainer', parent)).sort((a, b) => {
				return a.dataset.type_arg - b.dataset.type_arg;
			}).forEach((child) => parent.appendChild(child));
//
			return node;
		},
		card: function (card, clone = false)
		{
			const reactionSVG = `<img draggable='false' class='QGEFreactionSVG' src='${g_gamethemeurl}img/svg/${this.bgagame.gamedatas.CARDS.allies[card.type_arg].reaction}.svg'>`;
			let reaction = `<div>${this.bgagame.REACTIONS[this.bgagame.gamedatas.CARDS.allies[card.type_arg].reaction]}</div>`;
			if ('reaction' in this.cards[card.type_arg]) reaction += `<div class='QGEFreactionText'>${this.cards[card.type_arg].reaction}</div>`;
			if (this.bgagame.gamedatas.CARDS.allies[card.type_arg].reaction === 'Advance') reaction += `<div class='QGEFreactionText'>${_('No Spring Turns')}</div>`;
			if (this.bgagame.gamedatas.CARDS.allies[card.type_arg].reaction === 'SustainAttack') reaction += `<div class='QGEFreactionText'>${_('No Winter Turns')}</div>`;
//
			return this.bgagame.format_block('QGEFcard', {
				id: (clone ? 'clone-' : '') + card.id,
				FACTION: this.bgagame.gamedatas.CARDS.allies[card.type_arg].faction,
				faction: this.FACTIONS[this.bgagame.gamedatas.CARDS.allies[card.type_arg].faction],
				type: this.cards[card.type_arg].type, type_arg: card.type_arg,
				title: this.cards[card.type_arg].text[0],
				text: this.cards[card.type_arg].text[1], FONT: 8 - this.cards[card.type_arg].text[1].length / 100,
				reactionSVG: reactionSVG, reaction: reaction,
				TYPE: this.type[card.type], COLOR: {[FIRST_GAME]: '#b88e2e', [MID]: '#b88e2e', [LATE]: '#c92442'}[card.type]
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
					if ('discard' in this.bgagame.gamedatas.gamestate.args)
					{
						if (dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', 'QGEFhand-allies').length >= this.bgagame.gamedatas.gamestate.args.discard) return;
					}
					else
					{
						if (dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', 'QGEFhand-allies').length >= 2) return;
						if (this.bgagame.gamedatas.gamestate.name !== 'actionStep') dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', 'QGEFhand-allies').removeClass('QGEFselected');
						dojo.query('.QGEFcontingencyHolder>.QGEFcardContainer.QGEFselectable.QGEFselected').removeClass('QGEFselected');
					}
				}
				dojo.toggleClass(node, 'QGEFselected');
//
				this.bgagame.updateActionButtons();
			}
		}
	}
	);
});
