define(["dojo", "dojo/_base/declare"], function (dojo, declare)
{
	return declare("Contingency", null,
	{
//
		constructor: function (bgagame)
		{
			console.log('Contingency constructor');
//
			const INITIAL_SIDE = 3;
			const SECOND_SIDE = 4;
//
// Reference to BGA game
//
			this.bgagame = bgagame;
//
// Translate
//
			this.FACTIONS = {sovietUnion: _('Soviet Union'), germany: _('GERMANY'), pact: _('Pact')}
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
		place: function (faction, card)
		{
			const node = dojo.place(this.bgagame.format_block('QGEFcard', {
				id: card.id,
				FACTION: this.cards[card.type_arg].faction,
				faction: this.FACTIONS[this.cards[card.type_arg].faction],
				type: this.cards[card.type_arg].type, type_arg: card.type_arg,
				title: this.cards[card.type_arg][card.type][0],
				text: this.cards[card.type_arg][card.type][1]
			}), `QGEFcontingency-${faction}`);
//
			dojo.connect(node, 'click', this, 'click');
//
			this.bgagame.addTooltip(node.id, this.cards[card.type_arg][card.type][0], this.cards[card.type_arg][card.type][1], 1000);
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
//
				this.bgagame.updateActionButtons();
			}
		}
	}
	);
});
