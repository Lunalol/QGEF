const INITIAL_SIDE = 3;
const SECOND_SIDE = 4;
//
define(["dojo", "dojo/_base/declare"], function (dojo, declare)
{
	return declare("Contingency", null,
	{
//
		constructor: function (bgagame)
		{
			console.log('Contingency constructor');
//
//
// Reference to BGA game
//
			this.bgagame = bgagame;
//
			dojo.connect($('QGEFcontingency-allies-arrow'), 'click', (event) => {
				dojo.stopEvent(event);
				dojo.toggleClass('QGEFcontingency-allies', 'dock');
				this.bgagame.board.resize();
			});
			dojo.connect($('QGEFcontingency-axis-arrow'), 'click', (event) => {
				dojo.stopEvent(event);
				dojo.toggleClass('QGEFcontingency-axis', 'dock');
				this.bgagame.board.resize();
			});
//
// Translate
//
			this.FACTIONS = {sovietUnion: _('Soviet Union'), germany: _('GERMANY'), pact: _('Pact')};
//
			this.cards = {
//
// Axis
//
				101: {
					faction: 'germany', type: 'command',
					[INITIAL_SIDE]: [_('Industrial Realignment'), _('Deploy a German tank or airplane in a German Supply Flag space.')],
					[SECOND_SIDE]: [_('Civilian Industry Dismantled'), _('The Allies gain 1 VP. Deploy 2 German infantry in Berlin.')]
				},
				102: {
					faction: 'germany', type: 'command',
					[INITIAL_SIDE]: [_('Supplies Diverted East'), _('Attack with a German force into a space east of the 1941 line.')],
					[SECOND_SIDE]: [_('Werwolf'), _('The Allies must eliminate an Allies piece in or adjacent to Berlin.')]
				},
				103: {
					faction: 'germany', type: 'sea',
					[INITIAL_SIDE]: [_('Naval Projects Completed'), _('Discard 1 card to deploy a German fleet in West Baltic Sea, or flip to the secondary side with no other effect.')],
					[SECOND_SIDE]: [_('Barents Sea Debacle'), _('Deploy a German infantry adjacent to West Baltic Sea.')]
				},
				104: {
					faction: 'germany', type: 'command',
					[INITIAL_SIDE]: [_('Forced Labor'), _('The Allies gain 1 VP. Draw 3 cards.')],
					[SECOND_SIDE]: [_('Pervitin'), _('The Allies gain 1 VP. Move a German infantry; then take another action.')]
				},
				105: {
					faction: 'pact', type: 'ground',
					[INITIAL_SIDE]: [_('Hardliners Take Control'), _('Deploy 2 Pact infantry in the same Pact Supply Flag space.')],
					[SECOND_SIDE]: [_('Germany Arms Fascist Loyalists'), _('Deploy a Pact infantry in a Pact Supply Flag space.')]
				},
//
// Allies
//
				106: {
					faction: 'sovietUnion', type: 'command',
					[INITIAL_SIDE]: [_('Increased Aid from the West'), _('Deploy a Soviet tank or airplane.')],
					[SECOND_SIDE]: [_('Pacific Lend-Lease Shipments'), _('The Axis gain 1 VP. Deploy a Soviet tank or airplane adjacent to Gorki.')]
				},
				107: {
					faction: 'sovietUnion', type: 'command',
					[INITIAL_SIDE]: [_('Pre-War Doctrine'), _('Attack with a Soviet force.')],
					[SECOND_SIDE]: [_('Unit Reorganization'), _('Eliminate a Soviet infantry and deploy a Soviet tank in the same space.')]
				},
				108: {
					faction: 'sovietUnion', type: 'ground',
					[INITIAL_SIDE]: [_('Shtrafbats'), _('Deploy a Soviet infantry in a Soviet Supply Flag space; then attack with a force containing that infantry.')],
					[SECOND_SIDE]: [_('Human Wave'), _('Eliminate 1 Soviet infantry from a space containing 2 Soviet infantry; then attack with a force containing the other infantry.')]
				},
				109: {
					faction: 'sovietUnion', type: 'command',
					[INITIAL_SIDE]: [_('Gulag Economy'), _('The Axis gain 1 VP. Draw 3 cards.')],
					[SECOND_SIDE]: [_('Anti-Soviet Deportees'), _('Draw 2 cards; discard 1 card; and then take another action')]
				},
				110: {
					faction: 'sovietUnion', type: 'sea',
					[INITIAL_SIDE]: [_('River Flotillas'), _('Deploy a Soviet fleet in a water space that is not adjacent to another water space.')],
					[SECOND_SIDE]: [_('Danube River Flotilla'), _('Eliminate a Soviet fleet from any space to attack Romania or Bessarabia with a Soviet force.')]
				}
			};
		},
		card: function (card, clone = false)
		{
			return this.bgagame.format_block('QGEFcontingency', {
				id: (clone ? 'clone-' : '') + card.id,
				FACTION: this.cards[card.type_arg].faction,
				faction: this.FACTIONS[this.cards[card.type_arg].faction],
				type: this.cards[card.type_arg].type, type_arg: card.type_arg,
				title: this.cards[card.type_arg][INITIAL_SIDE][0],
				text: this.cards[card.type_arg][INITIAL_SIDE][1], FONT: 8 - this.cards[card.type_arg][INITIAL_SIDE][1].length / 100,
				back_title: this.cards[card.type_arg][SECOND_SIDE][0],
				back_text: this.cards[card.type_arg][SECOND_SIDE][1], back_FONT: 8 - this.cards[card.type_arg][SECOND_SIDE][1].length / 100,
				side: +card.type,
				flip: +card.type === SECOND_SIDE ? 'QGEFflip' : ''
			});
		},
		place: function (faction, card)
		{
			const parent = $(`QGEFcontingency-${faction}`);
//
			const node = dojo.place(this.card(card), parent);
			dojo.connect(node, 'click', this, 'click');
			dojo.connect(node, 'mouseleave', this, () => {
				dojo.removeClass(node, 'QGEFlookBack')
			});
			dojo.connect(node.firstElementChild.firstElementChild, 'click', (event) =>
			{
				dojo.stopEvent(event);
				dojo.toggleClass(node, 'QGEFlookBack');
			});
			this.bgagame.addTooltip(node.id,
					'<H2>' + _('Contingency card') + '</H2>'
					+ _('FIRST SIDE: ') + '<B>' + _(this.cards[card.type_arg][INITIAL_SIDE][0]) + '</B><BR>'
					+ _('SECOND SIDE: ') + '<B>' + _(this.cards[card.type_arg][SECOND_SIDE][0]) + '</B>',
					'<BR>' + '<BR>'
					+ _('FIRST SIDE: ') + _(this.cards[card.type_arg][INITIAL_SIDE][1]) + '<BR>'
					+ _('SECOND SIDE: ') + _(this.cards[card.type_arg][SECOND_SIDE][1]),
					2000);
//
			Array.from(dojo.query('.QGEFcardContainer', parent)).sort((a, b) => {
				return a.dataset.type_arg - b.dataset.type_arg;
			}).forEach((child) => parent.appendChild(child));
		},
		flip: function (card)
		{
			dojo.addClass(`QGEFcardContainer-${card.id}`, 'QGEFflip');
			dojo.setAttr(`QGEFcardContainer-${card.id}`, 'side', SECOND_SIDE);
		},
		discard: function (card)
		{
			dojo.query(`.QGEFcardContainer[data-id='${card.id}']`).forEach((node) =>
			{
				dojo.addClass(node, 'QGEFselected');
				this.bgagame.slideToObjectAndDestroy(node, 'QGEFplayArea', DELAY);
			});
		},
		click: function (event)
		{
			const node = event.currentTarget;
//
			if (dojo.hasClass(node, 'QGEFselectable') && this.bgagame.isCurrentPlayerActive())
			{
				dojo.stopEvent(event);
//
				if (!dojo.hasClass(node, 'QGEFselected')) dojo.query('.QGEFcardContainer.QGEFselected').removeClass('QGEFselected');
				dojo.toggleClass(node, 'QGEFselected');
				dojo.removeClass(node, 'QGEFlookBack');
//
				this.bgagame.updateActionButtons();
			}
		}
	}
	);
});
