/* global g_gamethemeurl, ebg */

define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", "ebg/counter",
	g_gamethemeurl + "modules/constants.js",
	g_gamethemeurl + "modules/JavaScript/board.js",
	g_gamethemeurl + "modules/JavaScript/tracks.js",
	g_gamethemeurl + "modules/JavaScript/panels.js",
	g_gamethemeurl + "modules/JavaScript/contingency.js",
	g_gamethemeurl + "modules/JavaScript/alliesDeck.js",
	g_gamethemeurl + "modules/JavaScript/axisDeck.js",
	g_gamethemeurl + "modules/JavaScript/markers.js",
	g_gamethemeurl + "modules/JavaScript/pieces.js"
], function (dojo, declare)
{
	return declare("bgagame.quartermastergeneraleastfront", ebg.core.gamegui,
	{
		constructor: function ()
		{
			console.log('quartermastergeneraleastfront constructor');
//
			this.default_viewport = 'initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,width=device-width,user-scalable=no';
//
		},
		setLoader(value, max)
		{
			this.inherited(arguments);
		},
		setup: function (gamedatas)
		{
			console.log("Starting game setup", gamedatas);
			dojo.destroy('debug_output');
//
			this.REACTIONS = {
				StandFast: _('Stand Fast'),
				SustainAttack: _('Sustain Attack'),
				Retreat: _('Retreat'),
				Exchange: _('Exchange'),
				AntiAir: _('Anti-Air'),
				NavalCombat: _('Naval Combat'),
				Advance: _('Advance!')
			};
//
			dojo.connect(dojo.byId('QGEFplayArea'), 'click', () => this.restoreServerGameState());
			dojo.connect(dojo.byId('game_play_area'), 'click', () => this.restoreServerGameState());
//
// Animations Speed
//
			DELAY = DELAYS[this.getGameUserPreference(SPEED)];
			document.documentElement.style.setProperty('--DELAY', DELAY);
//
// Setup Player Panels
//
			this.panels = new Panels(this, gamedatas.players);
			for (let FACTION in gamedatas.factions)
			{
				this.panels.place(FACTION, gamedatas.FACTIONS[FACTION], gamedatas.factions[FACTION].player_id);
				$(`QGEFplayerHand-${FACTION}-value`).innerHTML = gamedatas.hands[FACTION];
				$(`QGEFplayerDeck-${FACTION}-value`).innerHTML = gamedatas.decks[FACTION];
			}
//
// Setup Game Board
//
			this.board = new Board(this);
//
// Setup Turn Track
//
			this.tracks = new Tracks(this);
			this.tracks.round(gamedatas.steps)
//
// Place Markers
//
			this.markers = new Markers(this);
			for (let marker of gamedatas.markers) this.markers.place(marker);
//
// Place Pieces
//
			this.pieces = new Pieces(this);
			for (let piece of gamedatas.pieces) this.pieces.place(piece);
//
// Setup Contingency Cards
//
			this.contingency = new Contingency(this);
			for (let contigency of Object.values(gamedatas.contingency.allies)) this.contingency.place('allies', contigency);
			for (let contigency of Object.values(gamedatas.contingency.axis)) this.contingency.place('axis', contigency);
//
// Setup Decks
//
			this.alliesDeck = new alliesDeck(this);
			this.axisDeck = new axisDeck(this);
//
			if ('private' in gamedatas)
			{
				if ('allies' in gamedatas.private)
				{
					dojo.place(`<div id='QGEFhand-allies' class='QGEFhandHolder'></div>`, 'QGEFplayArea', 'after');
					for (let card of Object.values(gamedatas.private.allies)) this.alliesDeck.place(card);
				}
				if ('axis' in gamedatas.private)
				{
					dojo.place(`<div id='QGEFhand-axis' class='QGEFhandHolder'></div>`, 'QGEFplayArea', 'after');
					for (let card of Object.values(gamedatas.private.axis)) this.axisDeck.place(card);
				}
			}
//
			this.setupNotifications();
//
			console.log("Ending game setup");
		},
		onEnteringState: function (stateName, state)
		{
			console.log('Entering state: ' + stateName, state.args);
//
			if (state.args && 'FACTION' in state.args)
			{
				if (state.args.FACTION === 'axis' && $('QGEFhand-allies')) $('QGEFflex').appendChild($('QGEFhand-allies'));
				if (state.args.FACTION === 'allies' && $('QGEFhand-axis')) $('QGEFflex').appendChild($('QGEFhand-axis'));
			}
//
			switch (stateName)
			{
//
				case 'mulligan':
//
					if (this.isCurrentPlayerActive()) dojo.query('.QGEFhandHolder>.QGEFcardContainer').addClass('QGEFselectable');
//
					break;
//
				case 'remove':
//
					dojo.query(`.QGEFpiece[data-player='${state.args.FACTION}']`, 'QGEFboard').addClass('QGEFselectable');
//
					break;
//
				case 'actionStep':
//
					dojo.query('.QGEFhandHolder .QGEFselected').removeClass('QGEFselected');
					dojo.query('.QGEFcontingencyHolder .QGEFselected').removeClass('QGEFselected');
//
					if (this.isCurrentPlayerActive())
					{
						dojo.query('.QGEFcardContainer', `QGEFhand-${state.args.FACTION}`).addClass('QGEFselectable');
						dojo.query('.QGEFcardContainer', `QGEFcontingency-${state.args.FACTION}`).addClass('QGEFselectable');
					}
//
					break;
//
				case 'action':
//
					switch (state.args.action.name)
					{
						case 'scorched':
							this.gamedatas.gamestate.descriptionmyturn = _('${you} have to place Scorched Earth marker');
							this.gamedatas.gamestate.possibleactions = ['cancel', 'scorched'];
							break;
						case 'discard':
							this.gamedatas.gamestate.descriptionmyturn = _('${you} have to discard some cards');
							this.gamedatas.gamestate.possibleactions = ['cancel', 'discard'];
							break;
						case 'deploy':
							this.gamedatas.gamestate.descriptionmyturn = _('${you} have to deploy an unit');
							this.gamedatas.gamestate.possibleactions = ['cancel', 'pass', 'deploy', 'remove'];
							break;
						case 'recruit':
							this.gamedatas.gamestate.descriptionmyturn = _('${you} have to recruit an unit');
							this.gamedatas.gamestate.possibleactions = ['cancel', 'pass', 'recruit', 'remove'];
							break;
						case 'move/attack':
							this.gamedatas.gamestate.descriptionmyturn = _('${you} have to move deployed unit or attack with a force containing that unit');
							this.gamedatas.gamestate.possibleactions = ['cancel', 'pass', 'move', 'attack'];
							break;
						case 'attack':
							this.gamedatas.gamestate.descriptionmyturn = _('${you} have to attack a land space');
							this.gamedatas.gamestate.possibleactions = ['cancel', 'pass', 'attack'];
							break;
						case 'move':
							this.gamedatas.gamestate.descriptionmyturn = _('${you} have to move an unit');
							this.gamedatas.gamestate.possibleactions = ['cancel', 'pass', 'move'];
							break;
						case 'eliminate':
						case 'eliminateVS':
							this.gamedatas.gamestate.descriptionmyturn = _('${you} have to eliminate an unit');
							this.gamedatas.gamestate.possibleactions = ['cancel', 'pass', 'removePiece', 'discard', 'VP'];
							break;
						case 'conscription':
							this.gamedatas.gamestate.descriptionmyturn = {1: _('${you} have to discard 1 card to deploy an infantry'), 2: _('${you} have to discard 2 cards to deploy a tank, airplane, or fleet')}[state.args.action.cards.length];
							this.gamedatas.gamestate.possibleactions = ['cancel', 'deploy', 'remove'];
							break;
						case 'forcedMarch':
							this.gamedatas.gamestate.descriptionmyturn = _('${you} have to discard 1 card to move 1 piece');
							this.gamedatas.gamestate.possibleactions = ['cancel', 'move'];
							break;
						case 'desperateAttack':
							this.gamedatas.gamestate.descriptionmyturn = _('${you} have to discard 2 cards and then attack a land space');
							this.gamedatas.gamestate.possibleactions = ['cancel', 'attack'];
							break;
					}
					this.updatePageTitle();
//
					break;
//
				case 'attackRoundDefender':
//
					for (let card of state.args.action.cards) dojo.query(`.QGEFcardContainer[data-id='${card}']`, 'QGEF').addClass('QGEFselected QGEFused');
					$(`QGEFregion-${state.args.location}`).setAttribute('class', 'QGEFregion QGEFselectable');
//
					for (let piece of state.args.attacker)
					{
						const node = $(`QGEFpiece-${piece}`);
						if (node)
						{
							dojo.addClass(node, 'QGEFselected');
							this.board.arrow(+node.dataset.location, state.args.location);
						}
					}
					for (let piece of state.args.defender)
					{
						const node = $(`QGEFpiece-${piece}`);
						if (node)
						{
							dojo.addClass(node, 'QGEFselected');
							if (this.isCurrentPlayerActive()) dojo.addClass(node, 'QGEFselectable');
						}

					}
					if (this.isCurrentPlayerActive() && '_private' in state.args && 'reactions' in state.args._private)
					{
						for (let card of state.args._private.reactions) if (card) dojo.addClass(`QGEFcardContainer-${card}`, 'QGEFselectable');
					}
//
					break;
//
				case 'attackRoundAttacker':
				case 'attackRoundExchange':
//
					for (let card of state.args.action.cards) dojo.query(`.QGEFcardContainer[data-id='${card}']`, 'QGEF').addClass('QGEFselected QGEFused');
					$(`QGEFregion-${state.args.location}`).setAttribute('class', 'QGEFregion QGEFselectable');
//
					for (let piece of state.args.attacker)
					{
						const node = $(`QGEFpiece-${piece}`);
						if (node)
						{
							dojo.addClass(node, 'QGEFselected');
							if (this.isCurrentPlayerActive()) dojo.addClass(node, 'QGEFselectable');
							this.board.arrow(+node.dataset.location, state.args.location);
						}
					}
					for (let piece of state.args.defender)
					{
						const node = $(`QGEFpiece-${piece}`);
						if (node) dojo.addClass(node, 'QGEFselected');
					}
					if (this.isCurrentPlayerActive() && '_private' in state.args && 'reactions' in state.args._private)
					{
						for (let card of state.args._private.reactions) if (card) dojo.addClass(`QGEFcardContainer-${card}`, 'QGEFselectable');
					}
//
					break;
//
				case 'attackRoundSpecial':
//
					for (let card of state.args.action.cards) dojo.query(`.QGEFcardContainer[data-id='${card}']`, 'QGEF').addClass('QGEFselected QGEFused');
					$(`QGEFregion-${state.args.location}`).setAttribute('class', 'QGEFregion QGEFselectable');
//
					for (let piece of state.args.attacker)
					{
						const node = $(`QGEFpiece-${piece}`);
						if (node)
						{
							dojo.addClass(node, 'QGEFselected');
							this.board.arrow(+node.dataset.location, state.args.location);
						}
					}
					for (let piece of state.args.defender)
					{
						const node = $(`QGEFpiece-${piece}`);
						if (node) dojo.addClass(node, 'QGEFselected');
					}
					if (this.isCurrentPlayerActive() && '_private' in state.args && 'reactions' in state.args._private)
					{
						for (let card of state.args._private.reactions) if (card) dojo.addClass(`QGEFcardContainer-${card}`, 'QGEFselectable');
					}
//
					break;
//
				case 'retreat':
//
					for (let card of state.args.action.cards) dojo.query(`.QGEFcardContainer[data-id='${card}']`, 'QGEF').addClass('QGEFselected QGEFused');
//
					const piece = $(`QGEFpiece-${state.args.piece}`);
					dojo.addClass(piece, 'QGEFselected');
//
					dojo.query('.QGEFregion', 'QGEFboard').forEach((node) => {
//
						const possible = state.args._private.retreat[piece.dataset.id].includes(+node.dataset.location);
						node.setAttribute('class', possible ? 'QGEFregion QGEFselectable' : 'QGEFregion');
						if (possible) this.board.arrow(+piece.dataset.location, +node.dataset.location, '#FF660080');
					});
//
					break;
//
				case 'attackRoundAdvance':
//
					for (let card of state.args.action.cards) dojo.query(`.QGEFcardContainer[data-id='${card}']`, 'QGEF').addClass('QGEFselected QGEFused');
					$(`QGEFregion-${state.args.location}`).setAttribute('class', 'QGEFregion QGEFselectable');
//
					for (let piece of state.args.attacker)
					{
						const node = $(`QGEFpiece-${piece}`);
						if (node)
						{
							if (node.dataset.type === 'infantry' || node.dataset.type === 'tank')
							{
								if (this.isCurrentPlayerActive())
								{
									if (!state.args._private.reactions.includes(0) || state.args._private.free.includes(piece)) dojo.addClass(node, 'QGEFselected');
									if (state.args._private.pieces.includes(piece)) dojo.addClass(node, 'QGEFselectable');
								}
								this.board.arrow(+node.dataset.location, state.args.location);
							}
						}
					}
					if (this.isCurrentPlayerActive() && '_private' in state.args && 'reactions' in state.args._private)
					{
						for (let card of state.args._private.reactions) if (card) dojo.addClass(`QGEFcardContainer-${card}`, 'QGEFselectable');
						if (state.args._private.reactions.includes(0))
						{
							this.gamedatas.gamestate.descriptionmyturn = _('${you} can advance after combat for free');
							this.updatePageTitle();
						}
					}
//
					break;
//
			}
//
			if (this.isCurrentPlayerActive() && state.args && 'scorched' in state.args)
			{
				for (let region of state.args.scorched) $(`QGEFregion-${region}`).setAttribute('class', 'QGEFregion QGEFselectable');
			}
//
			if (this.isCurrentPlayerActive() && state.args && 'move' in state.args)
			{
				const pieces = Object.entries(state.args.move);
				for (let [piece, locations] of pieces)
				{
					dojo.addClass(`QGEFpiece-${piece}`, 'QGEFselectable');
				}
				if (pieces.length === 1) $(`QGEFpiece-${pieces[0][0]}`).click();
			}
//
			if (this.isCurrentPlayerActive() && state.args && 'attack' in state.args)
			{
				const pieces = Object.entries(state.args.attack);
				for (let [piece, locations] of pieces)
				{
					const node = $(`QGEFpiece-${piece}`);
					dojo.addClass(node, 'QGEFselectable');
					for (let location of locations) this.board.arrow(+node.dataset.location, location);
				}
				if (pieces.length === 1) $(`QGEFpiece-${pieces[0][0]}`).click();
			}
//
			if (this.isCurrentPlayerActive() && state.args && 'eliminate' in state.args)
			{
				for (let piece of state.args.eliminate)
				{
					const node = $(`QGEFpiece-${piece}`);
					dojo.addClass(node, 'QGEFselectable');
					$(`QGEFregion-${node.dataset.location}`).setAttribute('class', 'QGEFregion QGEFselectable');
				}
			}
//
		},
		onLeavingState: function (stateName)
		{
			console.log('Leaving state: ' + stateName);
//
			this.board.clearCanvas();
//
			dojo.query('.QGEFlookBack').removeClass('QGEFlookBack');
			dojo.query('.QGEFselected', 'QGEFboard').removeClass('QGEFselected');
			dojo.query('.QGEFselectable', 'QGEFboard').removeClass('QGEFselectable');
//
			dojo.query('.QGEFregion', 'QGEFboard').forEach((node) => node.setAttribute('class', 'QGEFregion'));
//
			dojo.query('.QGEFcardContainer.QGEFselectable').removeClass('QGEFselectable');
			dojo.query('.QGEFcardContainer.QGEFselected').removeClass('QGEFselected');
		},
		onUpdateActionButtons: function (stateName, args)
		{
			console.log('onUpdateActionButtons: ' + stateName);
//
			if (!this.isCurrentPlayerActive()) return;
//
			if (args && 'FACTION' in args) this.FACTION = args.FACTION;
			if (args && 'cancel' in args && args.cancel)
			{
				this.addActionButton('QGEFcancel', _('Cancel'), (event) => {
					dojo.stopEvent(event);
					this.bgaPerformAction('cancel', {FACTION: this.FACTION});
				});
			}
//
			if (this.gamedatas.gamestate.possibleactions.includes('remove'))
			{
				if (this.on_client_state)
				{
					this.addActionButton('QGEFcancel', _('Cancel'), (event) => {
						dojo.stopEvent(event);
						this.restoreServerGameState();
					});
				}
				else this.addActionButton('QGEFremove', `<div class='fa fa-trash'></div>`, (event) => {
						dojo.stopEvent(event);
						this.setClientState('remove', {args: {FACTION: args.FACTION}, possibleactions: ['remove'], descriptionmyturn: _('${you} may remove one of your piece')});
					});
			}
//
			switch (stateName)
			{
//
				case 'mulligan':
//
					this.addActionButton('QGEFmulligan', _('Mulligan'), (event) => {
						dojo.stopEvent(event);
						this.confirm(_('Reshuffle your hand into your Mid War cards and draw a new hand'), 'mulligan', {mulligan: true});
					}, null, false, 'red');
					this.addActionButton('QGEFpass', _('Keep current hand'), (event) => {
						dojo.stopEvent(event);
						this.confirm(_('Keep current hand'), 'mulligan', {mulligan: false});
					}, null, false, 'red');
//
					break;
//
				case 'firstMovementStep':
				case 'secondMovementStep':
//
					this.addActionButton('QGEFpass', _('Pass'), (event) => {
						dojo.stopEvent(event);
						this.confirm(_('Do nothing'), 'pass', {FACTION: this.FACTION});
					}, null, false, 'red');
//
					break;
//
				case 'actionStep':
//
					this.addActionButton('QGEFplay', _('Play'), (event) => {
						dojo.stopEvent(event);
						const cards = dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', `QGEFhand-${this.FACTION}`);
						if (cards.length === 1)
							this.confirm(_(this[this.FACTION + 'Deck'].cards[cards[0].dataset.type_arg].text[0]), 'play', {FACTION: this.FACTION, card: cards[0].dataset.id});
					});
					this.addActionButton('QGEFconscription', _('Conscription'), (event) => {
						dojo.stopEvent(event);
						const cards = dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', `QGEFhand-${this.FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
						if (cards.length === 1 || cards.length === 2)
							this.confirm(_('Discard 1 card to deploy an infantry, or 2 cards to deploy a tank, airplane, or fleet'), 'conscription', {FACTION: this.FACTION, cards: JSON.stringify(cards)});
					});
					this.addActionButton('QGEforcedMarch', _('Forced March'), (event) => {
						dojo.stopEvent(event);
						const cards = dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', `QGEFhand-${this.FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
						if (cards.length === 1)
							this.confirm(_('Discard 1 card to move 1 piece'), 'forcedMarch', {FACTION: this.FACTION, cards: JSON.stringify(cards)});
					});
					this.addActionButton('QGEFdesperateAttack', _('Desperate Attack'), (event) => {
						dojo.stopEvent(event);
						const cards = dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', `QGEFhand-${this.FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
						if (cards.length === 2)
							this.confirm(_('Discard 2 cards and then attack a land space'), 'desperateAttack', {FACTION: this.FACTION, cards: JSON.stringify(cards)});
					});
					this.addActionButton('QGEFproductionInitiative', _('Production Initiative'), (event) => {
						dojo.stopEvent(event);
						this.confirm(_('Draw 1 card'), 'productionInitiative', {FACTION: this.FACTION});
					});
					this.addActionButton('QGEFcontingency', _('Contingency'), (event) => {
						dojo.stopEvent(event);
						const cards = dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', `QGEFcontingency-${this.FACTION}`);
						this.confirm(_('Use one of your 5 Contingency cards'), 'contingency', {FACTION: this.FACTION, card: cards[0].dataset.id});
					});
					this.addActionButton('QGEFpass', _('Pass'), (event) => {
						dojo.stopEvent(event);
						this.confirm(_('Do nothing'), 'pass', {FACTION: this.FACTION});
					}, null, false, 'red');
//
					this.updateActionButtons();
//
					this.addTooltip('QGEFplay', _('Play'), _('Play a card to resolve its play text, discarding it afterwards'), 1000);
					this.addTooltip('QGEFconscription', _('Conscription'), _('Discard 1 card to deploy an infantry, or 2 cards to deploy a tank, airplane, or fleet'), 1000);
					this.addTooltip('QGEforcedMarch', _('Forced March'), _('Discard 1 card to move 1 piece.'), 1000);
					this.addTooltip('QGEFdesperateAttack', _('Desperate Attack'), _('Discard 2 cards and then attack a land space.'), 1000);
					this.addTooltip('QGEFproductionInitiative', _('Production Initiative'), _('Draw 1 card'), 1000);
					this.addTooltip('QGEFcontingency', _('Contingency'), _('Use one of your 5 Contingency cards'), 1000);
//
					break;
//
				case 'action':
//
					for (let card of args.action.cards) dojo.query(`.QGEFcardContainer[data-id='${card}']`, 'QGEF').addClass('QGEFselected');
//
					switch (args.action.name)
					{
//
						case 'discard':
//
							dojo.query('.QGEFhandHolder>.QGEFcardContainer').addClass('QGEFselectable');
//
							this.addActionButton('QGEFdiscard', dojo.string.substitute(_('Discard ${N} card(s)'), {N: args.discard}), (event) => {
								dojo.stopEvent(event);
								const cards = dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', `QGEFhand-${this.FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
								if (cards.length === args.discard) this.confirm(dojo.string.substitute(_('Discard ${N} card(s)'), {N: args.discard}), 'discard', {FACTION: this.FACTION, cards: JSON.stringify(cards)});
							}, null, false, 'red');
//
							this.updateActionButtons();
//
							break;
//
						case 'move':
//
							if (!('mandatory' in args.action))
							{
								this.addActionButton('QGEFpass', _('No movement'), (event) => {
									dojo.stopEvent(event);
									this.confirm(_('Do nothing'), 'pass', {FACTION: this.FACTION});
								}, null, false, 'red');
							}
//
							break;
//
						case 'attack':
//
							if (!('mandatory' in args.action))
							{
								this.addActionButton('QGEFpass', _('No attack'), (event) => {
									dojo.stopEvent(event);
									this.confirm(_('Do nothing'), 'pass', {FACTION: this.FACTION});
								}, null, false, 'red');
							}
//
							break;
//
						case 'eliminate':
//
							if (!('mandatory' in args.action))
							{
								this.addActionButton('QGEFpass', _('No elimination'), (event) => {
									dojo.stopEvent(event);
									this.confirm(_('Do nothing'), 'pass', {FACTION: this.FACTION});
								}, null, false, 'red');
							}
//
							break;
//
						case 'eliminateVS':
//
							if ('discard' in args.action)
							{
								dojo.query('.QGEFhandHolder>.QGEFcardContainer').addClass('QGEFselectable');
//
								this.addActionButton('QGEFdiscard', dojo.string.substitute(_('Discard ${N} card(s)'), {N: args.discard}), (event) => {
									dojo.stopEvent(event);
									const cards = dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', `QGEFhand-${this.FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
									if (cards.length === args.discard) this.confirm(dojo.string.substitute(_('Discard ${N} card(s)'), {N: args.discard}), 'discard', {FACTION: this.FACTION, cards: JSON.stringify(cards)});
								}, null, false, 'red');
//
								this.updateActionButtons();
							}
//
							if ('VP' in args.action)
							{
								this.addActionButton('QGEFVP', dojo.string.substitute(_('${VP} VP(s) to opponent'), {VP: args.VP}), (event) => {
									dojo.stopEvent(event);
									this.confirm(dojo.string.substitute(_('${VP} VP(s) to opponent'), {VP: args.VP}), 'VP', {FACTION: this.FACTION});
								}, null, false, 'red');
//
								this.updateActionButtons();
							}
//
							break;
//
						case 'recruit':
						case 'deploy':
//
							if (!('mandatory' in args.action))
							{
								this.addActionButton('QGEFpass', _('No deployment/recruitment'), (event) => {
									dojo.stopEvent(event);
									this.confirm(_('Do nothing'), 'pass', {FACTION: this.FACTION});
								}, null, false, 'red');
							}
//
						case 'conscription':
//
							let container = dojo.place(`<div style='display:inline-flex;vertical-align:middle;margin:0px 25px 0px 25px;'></div>`, 'generalactions');
							for (let faction in args.deploy)
							{
								for (let type in args.deploy[faction])
								{
									let pieceContainer = dojo.place(`<div class='QGEFpieceContainer'></div>`, container);
									let piece = dojo.place(`<div class='QGEFpiece' data-faction='${faction}' data-type='${type}'></div>`, pieceContainer);
									dojo.setStyle(piece, {transform: `scale(${50 / piece.clientHeight})`, 'transform-origin': 'left top', transition: ''});
									dojo.setStyle(pieceContainer, 'aspect-ratio', piece.clientWidth / piece.clientHeight);
//
									dojo.connect(pieceContainer, 'click', (event) => {
										dojo.stopEvent(event);
										dojo.query('.QGEFpieceContainer', container).removeClass('QGEFselected');
										dojo.addClass(event.currentTarget, 'QGEFselected');
//
										dojo.query('.QGEFregion', 'QGEFboard').forEach((node) => {
											node.setAttribute('class', args.deploy[faction][type].includes(+node.dataset.location) ? 'QGEFregion QGEFselectable' : 'QGEFregion');
										});
									});
								}
							}
							if (container.childElementCount === 1) container.children[0].click();
//
							break;
//
					}
//
					break;
//
				case 'attackRoundDefender':
//
					for (let card of args.action.cards) dojo.query(`.QGEFcardContainer[data-id='${card}']`, 'QGEF').addClass('QGEFselected');
//
					this.board.centerMap(args.location);
//
					this.addActionButton('QGEFreaction', _('Reaction'), (event) => {
						dojo.stopEvent(event);
//
						const cards = dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', `QGEFhand-${this.FACTION}`);
						if (cards.length === 1)
						{
							const card = cards[0];
							const reaction = this.gamedatas.CARDS[args.FACTION][card.dataset.type_arg].reaction;
							if (reaction === 'Exchange') return this.showMessage(_('Click on a piece to remove'), 'info');
							if (reaction === 'Retreat') return this.showMessage(_('Click on a piece to retreat'), 'info');
							this.confirm(dojo.string.substitute(_('Play a card for reaction: <B>${reaction}</B>'), {reaction: this.REACTIONS[reaction].toUpperCase()}), 'reaction', {FACTION: this.FACTION, card: card.dataset.id});
						}
					});
//
					this.updateActionButtons();
//
					break;
//
				case 'attackRoundSpecial':
//
					for (let card of args.action.cards) dojo.query(`.QGEFcardContainer[data-id='${card}']`, 'QGEF').addClass('QGEFselected');
//
					this.board.centerMap(args.location);
//
					this.addActionButton('QGEFreaction', _('Reaction'), (event) => {
						dojo.stopEvent(event);
//
						const cards = dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', `QGEFhand-${this.FACTION}`);
						if (cards.length === 1)
						{
							const card = cards[0];
							const reaction = this.gamedatas.CARDS[args.FACTION][card.dataset.type_arg].reaction;
							if (reaction === 'Exchange') return this.showMessage(_('Click on a piece to remove'), 'info');
							this.confirm(dojo.string.substitute(_('Play a card for reaction: <B>${reaction}</B>'), {reaction: this.REACTIONS[reaction].toUpperCase()}), 'reaction', {FACTION: this.FACTION, card: card.dataset.id});
						}
					});
//
					this.updateActionButtons();
//
					this.addActionButton('QGEFpass', _('Pass'), (event) => {
						dojo.stopEvent(event);
						this.confirm(_('Do nothing'), 'pass', {FACTION: this.FACTION});
					}, null, false, 'red');
//
					break;
//
				case 'attackRoundAttacker':
//
					this.board.centerMap(args.location);
//
					this.addActionButton('QGEFreaction', _('Reaction'), (event) => {
						dojo.stopEvent(event);
//
						const cards = dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', `QGEFhand-${this.FACTION}`);
						if (cards.length === 1)
						{
							const card = cards[0];
							const reaction = this.REACTIONS[this.gamedatas.CARDS[args.FACTION][card.dataset.type_arg].reaction];
							this.confirm(dojo.string.substitute(_('Play a card for reaction: <B>${reaction}</B>'), {reaction: reaction.toUpperCase()}), 'reaction', {FACTION: this.FACTION, card: card.dataset.id});
						}
					});
//
					this.updateActionButtons();
//
					this.addActionButton('QGEFpass', _('Pass'), (event) => {
						dojo.stopEvent(event);
						this.confirm(_('Do nothing'), 'pass', {FACTION: this.FACTION});
					}, null, false, 'red');
//
					break;
//
				case 'retreat':
//
					for (let card of args.action.cards) dojo.query(`.QGEFcardContainer[data-id='${card}']`, 'QGEF').addClass('QGEFselected');
//
					dojo.addClass(`QGEFcardContainer-${args.card}`, 'QGEFselected');
//
					break;
//
				case 'attackRoundAdvance':
//
					for (let card of args.action.cards) dojo.query(`.QGEFcardContainer[data-id='${card}']`, 'QGEF').addClass('QGEFselected');
//
					this.board.centerMap(args.location);
//
					this.addActionButton('QGEFpass', _('Pass'), (event) => {
						dojo.stopEvent(event);
						this.confirm(_('Do nothing'), 'pass', {FACTION: this.FACTION});
					}, null, false, 'red');
//
					break;
//
			}
		},
		setupNotifications: function ()
		{
			console.log('notifications subscriptions setup');
//
			dojo.subscribe('updateScore', (notif) => this.scoreCtrl[notif.args.player_id].setValue(notif.args.VP));
			dojo.subscribe('updateRound', (notif) => this.tracks.round(notif.args.steps));
			dojo.subscribe('updateControl', (notif) => {
				this.gamedatas.factions.allies.control = notif.args.allies;
				this.gamedatas.factions.axis.control = notif.args.axis;
				if (+this.getGameUserPreference(CONTROL) === 0) this.panels.control();

			});
			dojo.subscribe('updateSupply', (notif) => {
				this.gamedatas.factions.allies.supply = notif.args.allies;
				this.gamedatas.factions.axis.supply = notif.args.axis;
			});
//
			dojo.subscribe('updateDeck', (notif) => {
				$(`QGEFplayerDeck-${notif.args.FACTION}-value`).innerHTML = notif.args.deck;
				$(`QGEFplayerHand-${notif.args.FACTION}-value`).innerHTML = notif.args.hand;
			});
//
			dojo.subscribe('placeMarker', (notif) => this.markers.place(notif.args.marker));
			dojo.subscribe('placePiece', (notif) => this.pieces.place(notif.args.piece));
			dojo.subscribe('removePiece', (notif) => this.pieces.remove(notif.args.piece));
//
			dojo.subscribe('flip', (notif) => this.contingency.flip(notif.args.card));
			dojo.subscribe('discard', (notif) => this.contingency.discard(notif.args.card));
//
			dojo.subscribe('alliesDeck', (notif) => this.alliesDeck.place(notif.args.card));
			dojo.subscribe('alliesPlay', (notif) => this.alliesDeck.play(notif.args.card));
			dojo.subscribe('alliesDiscard', (notif) => this.alliesDeck.discard(notif.args.card));
//
			dojo.subscribe('axisDeck', (notif) => this.axisDeck.place(notif.args.card));
			dojo.subscribe('axisPlay', (notif) => this.axisDeck.play(notif.args.card));
			dojo.subscribe('axisDiscard', (notif) => this.axisDeck.discard(notif.args.card));
//
			this.setSynchronous();
		},
		setSynchronous()
		{
			this.notifqueue.setSynchronous('placeMarker', DELAY);
			this.notifqueue.setSynchronous('placePiece', DELAY);
			this.notifqueue.setSynchronous('removePiece', DELAY);
//
			this.notifqueue.setSynchronous('discard', DELAY);
//
			this.notifqueue.setSynchronous('alliesDeck', DELAY);
			this.notifqueue.setSynchronous('alliesDiscard', DELAY);
//
			this.notifqueue.setSynchronous('axisDeck', DELAY);
			this.notifqueue.setSynchronous('axisDiscard', DELAY);
		},
		updateActionButtons: function ()
		{
			if (this.gamedatas.gamestate.args && 'FACTION' in this.gamedatas.gamestate.args)
			{
				const FACTION = this.gamedatas.gamestate.args.FACTION;
//
				const cards = dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', `QGEFhand-${FACTION}`).length;
				if ($('QGEFreaction')) dojo.toggleClass('QGEFreaction', 'disabled', cards !== 1);
				if ($('QGEFplay')) dojo.toggleClass('QGEFplay', 'disabled', cards !== 1);
				if ($('QGEFconscription')) dojo.style('QGEFconscription', 'display', (cards === 1 || cards === 2) ? '' : 'none');
				if ($('QGEforcedMarch')) dojo.style('QGEforcedMarch', 'display', (cards === 1) ? '' : 'none');
				if ($('QGEFdesperateAttack')) dojo.style('QGEFdesperateAttack', 'display', (cards === 2) ? '' : 'none');
//
				const contingency = dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', `QGEFcontingency-${FACTION}`).length;
				if ($('QGEFcontingency')) dojo.style('QGEFcontingency', 'display', (contingency === 1) ? '' : 'none');
//
				if ($('QGEFpass')) dojo.toggleClass('QGEFpass', 'disabled', cards + contingency !== 0);
				if ($('QGEFproductionInitiative')) dojo.style('QGEFproductionInitiative', 'display', (cards + contingency === 0) ? '' : 'none');
//
				if ($('QGEFdiscard')) dojo.toggleClass('QGEFdiscard', 'disabled', cards !== this.gamedatas.gamestate.args.discard);
			}
		},
		QGEFremove: function (piece)
		{
			const node = $(`QGEFpiece-${piece}`);
//
			if (node && this.isCurrentPlayerActive())
			{
				this.confirm(dojo.string.substitute(_('Remove from <B>${REGION}<B>'), {REGION: _(this.gamedatas.REGIONS[node.dataset.location])}) + node.outerHTML, 'remove', {FACTION: this.FACTION, piece: piece});
			}
		},
		QGEFscorched: function (location)
		{
			if (this.isCurrentPlayerActive()) this.bgaPerformAction('scorched', {FACTION: this.FACTION, location: location});
		},
		QGEFdeploy: function (location)
		{
			const node = $('generalactions').querySelector('.QGEFpieceContainer.QGEFselected>.QGEFpiece');
			if (node && this.isCurrentPlayerActive()) this.bgaPerformAction('deploy', {FACTION: this.FACTION, location: location, faction: node.dataset.faction, type: node.dataset.type});
		},
		QGEFrecruit: function (location)
		{
			const node = $('generalactions').querySelector('.QGEFpieceContainer.QGEFselected>.QGEFpiece');
			if (node && this.isCurrentPlayerActive()) this.bgaPerformAction('recruit', {FACTION: this.FACTION, location: location, faction: node.dataset.faction, type: node.dataset.type});
		},
		QGEFmoveAttack: function (location)
		{
			const pieces = dojo.query('.QGEFpiece.QGEFselected', 'QGEFboard');
			if (pieces.length > 0 && this.isCurrentPlayerActive())
			{
				if ('move' in this.gamedatas.gamestate.args && pieces[0].dataset.id in this.gamedatas.gamestate.args.move && this.gamedatas.gamestate.args.move[pieces[0].dataset.id].includes(location))
				{
					this.board.clearCanvas();
					for (let piece of pieces) this.board.arrow(+piece.dataset.location, location, '#00FF0080');
					this.bgaPerformAction('move', {FACTION: this.FACTION, location: location, pieces: JSON.stringify(pieces.reduce((L, node) => [...L, +node.dataset.id], []))});
				}
				if ('attack' in this.gamedatas.gamestate.args && pieces[0].dataset.id in this.gamedatas.gamestate.args.attack && this.gamedatas.gamestate.args.attack[pieces[0].dataset.id].includes(location))
				{
					this.board.clearCanvas();
					for (let piece of pieces) this.board.arrow(+piece.dataset.location, location, '#FF000080');
					this.bgaPerformAction('attack', {FACTION: this.FACTION, location: location, pieces: JSON.stringify(pieces.reduce((L, node) => [...L, +node.dataset.id], []))});
				}
			}
		},
		QGEFretreat: function (location)
		{
			const node = $(`QGEFpiece-${this.gamedatas.gamestate.args.piece}`);
			if (node && this.isCurrentPlayerActive())
				this.confirm(dojo.string.substitute(_('Retreat to <B>${REGION}<B>'), {REGION: _(this.gamedatas.REGIONS[location])}) + node.outerHTML, 'reaction', {FACTION: this.FACTION, card: this.gamedatas.gamestate.args.card, piece: this.gamedatas.gamestate.args.piece, location: location});
		},
		QGEFreaction: function (piece)
		{
			const node = $(`QGEFpiece-${piece}`);
//
			if (node && this.isCurrentPlayerActive())
			{
				if (this.gamedatas.gamestate.name === 'attackRoundDefender')
				{
					const cards = dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', `QGEFhand-${this.FACTION}`);
					if (cards.length === 1)
					{
						const card = cards[0];
						const reaction = this.gamedatas.CARDS[this.FACTION][card.dataset.type_arg].reaction;
						switch (reaction)
						{
							case 'Exchange':
								return this.confirm(dojo.string.substitute(_('Play a card for reaction: <B>${reaction}</B>'), {reaction: this.REACTIONS[reaction].toUpperCase()}) + '<BR>' + dojo.string.substitute(_('Remove from <B>${REGION}<B>'), {REGION: _(this.gamedatas.REGIONS[node.dataset.location])}) + node.outerHTML, 'reaction', {FACTION: this.FACTION, card: card.dataset.id, piece: piece});
							case 'Retreat':
								if (piece in this.gamedatas.gamestate.args._private.retreat)
								{
									this.gamedatas.gamestate.args['card'] = card.dataset.id;
									this.gamedatas.gamestate.args['piece'] = piece;
									return this.setClientState('retreat', {possibleactions: ['retreat'], descriptionmyturn: _('${you} may move one piece out of the attacked space')});
								}
						}
						return this.showMessage(_('Deselect reaction first'), 'info');
					}
				}
				else if (this.gamedatas.gamestate.name === 'attackRoundAdvance')
				{
					const cards = dojo.query('.QGEFcardContainer.QGEFselectable.QGEFselected', `QGEFhand-${this.FACTION}`);
					if (cards.length === 0 && this.gamedatas.gamestate.args._private.reactions.includes(0) && this.gamedatas.gamestate.args._private.free.includes(+piece))
						return this.confirm(_('Advance after combat') + node.outerHTML, 'reaction', {FACTION: this.FACTION, card: 0, piece: piece});
					else if (cards.length === 1)
					{
						const card = cards[0];
						const reaction = this.gamedatas.CARDS[this.FACTION][card.dataset.type_arg].reaction;
						if (reaction === 'Advance')
							return this.confirm(dojo.string.substitute(_('Play a card for reaction: <B>${reaction}</B>'), {reaction: this.REACTIONS[reaction].toUpperCase()}) + '<BR>' + dojo.string.substitute(_('Advance from <B>${old}<B> to <B>${new}</B>'), {old: _(this.gamedatas.REGIONS[node.dataset.location]), new : _(this.gamedatas.REGIONS[this.gamedatas.gamestate.args.location])}) + node.outerHTML, 'reaction', {FACTION: this.FACTION, card: card.dataset.id, piece: piece});
					}
					else return this.showMessage(_('Advance reaction only'), 'info');
				}
				this.confirm(dojo.string.substitute(_('Remove from <B>${REGION}<B>'), {REGION: _(this.gamedatas.REGIONS[node.dataset.location])}) + node.outerHTML, 'removePiece', {FACTION: this.FACTION, piece: piece});
			}
		},
		onGameUserPreferenceChanged: function (pref, value)
		{
			switch (pref)
			{
				case SPEED:
					DELAY = DELAYS[+value];
					document.documentElement.style.setProperty('--DELAY', DELAY);
					this.setSynchronous();
					break;
				case CONTROL:
					if (+value === 0) this.panels.control();
			}
		},
		format_string_recursive: function (log, args)
		{
			if (log && args && !args.processed)
			{
				args.processed = true;
//
				if ('round' in args) args.round = $(`QGEFround-${args.round}`).outerHTML;
//
				if ('you' in args && 'FACTION' in args) args.you = `<div class='QGEFfaction' faction='${args.FACTION}'></div><span> </span>` + args.you;
				if ('FACTION' in args) args.FACTION = `<div class='QGEFfaction' faction='${args.FACTION}'></div>`;
				if ('faction' in args) args.faction = `<img style='width:20px;vertical-align:middle;' src='${g_gamethemeurl}img/flag_${args.faction}.jpg'>`;
//
				if ('CARD' in args) args.CARD = this[args.CARD.FACTION + 'Deck'].card(args.CARD.card, true);
				if ('CONTINGENCY' in args) args.CONTINGENCY = this.contingency.card(args.CONTINGENCY.card, true);
			}
			return this.inherited(arguments);
		},
		confirm: function (text, ...args)
		{
			if (+this.getGameUserPreference(CONFIRM)) this.confirmationDialog(text, () => this.bgaPerformAction(...args));
			else this.bgaPerformAction(...args);
		}
	}
	);
});
