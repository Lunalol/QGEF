define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", "ebg/counter",
	g_gamethemeurl + "modules/constants.js",
	g_gamethemeurl + "modules/JavaScript/board.js",
	g_gamethemeurl + "modules/JavaScript/track.js",
	g_gamethemeurl + "modules/JavaScript/panels.js",
	g_gamethemeurl + "modules/JavaScript/contingency.js",
	g_gamethemeurl + "modules/JavaScript/alliesDeck.js",
	g_gamethemeurl + "modules/JavaScript/axisDeck.js",
	g_gamethemeurl + "modules/JavaScript/pieces.js",
	g_gamethemeurl + "modules/JavaScript/counters.js"
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
			this.dontPreloadImage('background.jpg');
//
		},
		setLoader(value, max)
		{
			this.inherited(arguments);
		},
		setup: function (gamedatas)
		{
			console.log("Starting game setup", gamedatas);
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
			DELAY = 500 /*DELAYS[this.prefs[100].value]*/;
			document.documentElement.style.setProperty('--DELAY', DELAY);
			dojo.query('.preference_control').connect('onchange', this, 'updatePreference');
//
// Setup Player Panels
//
			this.panels = new Panels(this);
			for (let FACTION in gamedatas.factions) this.panels.place(FACTION, gamedatas.FACTIONS[FACTION], gamedatas.factions[FACTION].player_id);
//
// Setup Game Board
//
			this.board = new Board(this);
//
// Setup Turn Track
//
			this.track = new Track(this);
//
// Place Pieces
//
			this.pieces = new Pieces(this);
			for (let piece of gamedatas.pieces) this.pieces.place(piece);
//
// Place Counters
//
			this.counters = new Counters(this);
			for (let counter of gamedatas.counters) this.counters.place(counter);
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
			if (!(state.args)) return;
//
			if ('move' in state.args) for (let piece in state.args.move) dojo.addClass(`QGEFpiece-${piece}`, 'QGEFselectable');
			if ('attack' in state.args) {
				for (let piece in state.args.attack)
				{
					const node = $(`QGEFpiece-${piece}`);
					dojo.addClass(node, 'QGEFselectable');
					for (let location of state.args.attack[piece]) this.board.arrow(+node.dataset.location, location);
				}
			}
//
			if ('FACTION' in state.args)
			{
				if (state.args.FACTION === 'axis' && $('QGEFhand-allies')) $('QGEFflex').appendChild($('QGEFhand-allies'));
				if (state.args.FACTION === 'allies' && $('QGEFhand-axis')) $('QGEFflex').appendChild($('QGEFhand-axis'));
			}
//
			switch (stateName)
			{
//
				case 'actionStep':
//
					dojo.query('.QGEFhandHolder .QGEFselected').removeClass('QGEFselected');
					dojo.query('.QGEFcontingencyHolder .QGEFselected').removeClass('QGEFselected');
//
					dojo.query('.QGEFcardContainer', `QGEFhand-${state.args.FACTION}`).addClass('QGEFselectable');
					dojo.query('.QGEFcardContainer', `QGEFcontingency-${state.args.FACTION}`).addClass('QGEFselectable');
//
					break;
//
				case 'action':
//
					switch (state.args.action.name)
					{
						case 'conscription':
							this.gamedatas.gamestate.descriptionmyturn = {1: _('Discard 1 card to deploy an infantry'), 2: _('Discard 2 cards to deploy a tank, airplane, or fleet')}[state.args.action.cards.length];
							this.gamedatas.gamestate.possibleactions = ['deploy'];
							break;
						case 'forcedMarch':
							this.gamedatas.gamestate.descriptionmyturn = _('Discard 1 card to move 1 piece');
							this.gamedatas.gamestate.possibleactions = ['forcedMarch'];
							break;
						case 'desperateAttack':
							this.gamedatas.gamestate.descriptionmyturn = _('Discard 2 cards and then attack a land space');
							this.gamedatas.gamestate.possibleactions = ['desperateAttack'];
							break;
					}
					this.updatePageTitle();
//
					break;
//
				case 'attackRoundDefender':
//
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
						if (node) dojo.addClass(node, 'QGEFselectable QGEFselected');
					}
					if ('_private' in state.args && 'reactions' in state.args._private)
					{
						for (let card of state.args._private.reactions) dojo.addClass(`QGEFcardContainer-${card}`, 'QGEFselectable');
					}
//
					break;
//
				case 'attackRoundAttacker':
				case 'attackRoundExchange':
//
					$(`QGEFregion-${state.args.location}`).setAttribute('class', 'QGEFregion QGEFselectable');
//
					for (let piece of state.args.attacker)
					{
						const node = $(`QGEFpiece-${piece}`);
						if (node)
						{
							dojo.addClass(node, 'QGEFselectable QGEFselected');
							this.board.arrow(+node.dataset.location, state.args.location);
						}
					}
					for (let piece of state.args.defender)
					{
						const node = $(`QGEFpiece-${piece}`);
						if (node) dojo.addClass(node, 'QGEFselected');
					}
					if ('_private' in state.args && 'reactions' in state.args._private)
					{
						for (let card of state.args._private.reactions) dojo.addClass(`QGEFcardContainer-${card}`, 'QGEFselectable');
					}
//
					break;
//
				case 'attackRoundSpecial':
//
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
					if ('_private' in state.args && 'reactions' in state.args._private)
					{
						for (let card of state.args._private.reactions) dojo.addClass(`QGEFcardContainer-${card}`, 'QGEFselectable');
					}
//
					break;
//
				case 'retreat':
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
					$(`QGEFregion-${state.args.location}`).setAttribute('class', 'QGEFregion QGEFselectable');
//
					for (let piece of state.args.attacker)
					{
						const node = $(`QGEFpiece-${piece}`);
						if (node)
						{
							if (node.dataset.type === 'infantry' || node.dataset.type === 'tank')
							{
								dojo.addClass(node, 'QGEFselected QGEFselectable');
								this.board.arrow(+node.dataset.location, state.args.location);
							}
						}
					}
					if ('_private' in state.args && 'reactions' in state.args._private)
					{
						for (let card of state.args._private.reactions) dojo.addClass(`QGEFcardContainer-${card}`, 'QGEFselectable');
					}
//
					break;
//
			}
		},
		onLeavingState: function (stateName)
		{
			console.log('Leaving state: ' + stateName);
//
			this.board.clearCanvas();
//
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
			if (this.isCurrentPlayerActive())
			{
				this.FACTION = args.FACTION;
//
				switch (stateName)
				{
//
					case 'firstMovementStep':
					case 'secondMovementStep':
//
						this.addActionButton('QGEFpass', _('Pass'), (event) => {
							dojo.stopEvent(event);
							this.confirm(_('Do nothing'), 'pass', {FACTION: this.FACTION});
						});
//
						break;
//
					case 'actionStep':
//
						this.addActionButton('QGEFplay', _('Play'), (event) => {
							dojo.stopEvent(event);
//
							const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${this.FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
							this.confirm(event, _('Play a card to resolve its play text, discarding it afterwards'), 'play', {FACTION: this.FACTION, cards: JSON.stringify(cards)});
						});
						this.addActionButton('QGEFconscription', _('Conscription'), (event) => {
							dojo.stopEvent(event);
//
							const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${this.FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
							this.confirm(_('Discard 1 card to deploy an infantry, or 2 cards to deploy a tank, airplane, or fleet'), 'conscription', {FACTION: this.FACTION, cards: JSON.stringify(cards)});
						});
						this.addActionButton('QGEforcedMarch', _('Forced March'), (event) => {
							dojo.stopEvent(event);
//
							const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${this.FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
							this.confirm(_('Discard 1 card to move 1 piece'), 'forcedMarch', {FACTION: this.FACTION, cards: JSON.stringify(cards)});
						});
						this.addActionButton('QGEFdesperateAttack', _('Desperate Attack'), (event) => {
							dojo.stopEvent(event);
//
							const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${this.FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
							this.confirm(_('Discard 2 cards and then attack a land space'), 'desperateAttack', {FACTION: this.FACTION, cards: JSON.stringify(cards)});
						});
						this.addActionButton('QGEFproductionInitiative', _('Production Initiative'), (event) => {
							dojo.stopEvent(event);
//
							const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${this.FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
							this.confirm(_('Draw 1 card'), 'productionInitiative', {FACTION: this.FACTION});
						});
						this.addActionButton('QGEFcontingency', _('Contingency'), (event) => {
							dojo.stopEvent(event);
							this.confirm(_('Use one of your 5 Contingency cards'), 'contingency', {FACTION: this.FACTION});
						});
						this.addActionButton('QGEFpass', _('Pass'), (event) => {
							dojo.stopEvent(event);
							this.confirm(_('Do nothing'), 'pass', {FACTION: this.FACTION});
						});
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
						for (let card of args.action.cards) dojo.query(`.QGEFcardContainer[data-id='${card}']`, `QGEFhand-${args.FACTION}`).addClass('QGEFselected');
//
						switch (args.action.name)
						{
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
//
								break;
//
						}
//
						this.addActionButton('QGEFcancel', _('Cancel'), (event) => {
							dojo.stopEvent(event);
							this.action('cancel', {FACTION: this.FACTION});
							dojo.query('.QGEFhandHolder .QGEFselected').removeClass('QGEFselected');
						});
//
						break;
//
					case 'attackRoundDefender':
//
						this.board.centerMap(args.location);
//
						this.addActionButton('QGEFreaction', _('Reaction'), (event) => {
							dojo.stopEvent(event);
//
							const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${this.FACTION}`);
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
						break;
//
					case 'attackRoundSpecial':
//
						this.board.centerMap(args.location);
//
						this.addActionButton('QGEFreaction', _('Reaction'), (event) => {
							dojo.stopEvent(event);
//
							const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${this.FACTION}`);
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
						});
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
							const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${this.FACTION}`);
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
						});
//
						break;
//
					case 'retreat':
//
						dojo.addClass(`QGEFcardContainer-${args.card}`, 'QGEFselected');
//
						break;
//
					case 'attackRoundAdvance':
//
						this.board.centerMap(args.location);
//
						this.addActionButton('QGEFpass', _('Pass'), (event) => {
							dojo.stopEvent(event);
							this.confirm(_('Do nothing'), 'pass', {FACTION: this.FACTION});
						});
//
						break;
//
				}
			}
		},
		setupNotifications: function ()
		{
			console.log('notifications subscriptions setup');
//
			dojo.subscribe('placePiece', (notif) => this.pieces.place(notif.args.piece));
			dojo.subscribe('removePiece', (notif) => this.pieces.remove(notif.args.piece));
			dojo.subscribe('alliesDeck', (notif) => this.alliesDeck.place(notif.args.card));
			dojo.subscribe('axisDeck', (notif) => this.axisDeck.place(notif.args.card));
			dojo.subscribe('alliesPlay', (notif) => this.alliesDeck.play(notif.args.card));
			dojo.subscribe('alliesDiscard', (notif) => this.alliesDeck.discard(notif.args.card));
			dojo.subscribe('axisPlay', (notif) => this.axisDeck.play(notif.args.card));
			dojo.subscribe('axisDiscard', (notif) => this.axisDeck.discard(notif.args.card));
//
			this.setSynchronous();
		},
		setSynchronous()
		{
			this.notifqueue.setSynchronous('placePiece', DELAY);
			this.notifqueue.setSynchronous('removePiece', DELAY);
			this.notifqueue.setSynchronous('alliesDeck', DELAY);
			this.notifqueue.setSynchronous('axisDeck', DELAY);
			this.notifqueue.setSynchronous('alliesDiscard', DELAY);
			this.notifqueue.setSynchronous('axisDiscard', DELAY);
		},
		updateActionButtons: function ()
		{
			if ('FACTION' in this.gamedatas.gamestate.args)
			{
				const FACTION = this.gamedatas.gamestate.args.FACTION;
//
				const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${FACTION}`).length;
				if ($('QGEFreaction')) dojo.toggleClass('QGEFreaction', 'disabled', cards !== 1);
				if ($('QGEFplay')) dojo.toggleClass('QGEFplay', 'disabled', cards !== 1);
				if ($('QGEFconscription')) dojo.style('QGEFconscription', 'display', (cards === 1 || cards === 2) ? '' : 'none');
				if ($('QGEforcedMarch')) dojo.style('QGEforcedMarch', 'display', (cards === 1) ? '' : 'none');
				if ($('QGEFdesperateAttack')) dojo.style('QGEFdesperateAttack', 'display', (cards === 2) ? '' : 'none');
//
				const contingency = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFcontingency-${FACTION}`).length;
				if ($('QGEFcontingency')) dojo.style('QGEFcontingency', 'display', (contingency === 1) ? '' : 'none');
//
				if ($('QGEFpass')) dojo.toggleClass('QGEFpass', 'disabled', cards + contingency !== 0);
				if ($('QGEFproductionInitiative')) dojo.style('QGEFproductionInitiative', 'display', (cards + contingency === 0) ? '' : 'none');
			}
		},
		QGEFdeploy: function (location)
		{
			const node = $('generalactions').querySelector('.QGEFpieceContainer.QGEFselected>.QGEFpiece');
			if (node && this.isCurrentPlayerActive()) this.action('deploy', {FACTION: this.FACTION, location: location, faction: node.dataset.faction, type: node.dataset.type});
		},
		QGEFmovement: function (location)
		{
			this.board.clearCanvas();
//
			const pieces = dojo.query('.QGEFpiece.QGEFselected', 'QGEFboard');
			for (let piece of pieces) this.board.arrow(+piece.dataset.location, location, '#00FF0080');
//
			if (pieces.length > 0 && this.isCurrentPlayerActive())
				this.action('move', {FACTION: this.FACTION, location: location, pieces: JSON.stringify(pieces.reduce((L, node) => [...L, +node.dataset.id], []))});
		},
		QGEFattack: function (location)
		{
			this.board.clearCanvas();
//
			const pieces = dojo.query('.QGEFpiece.QGEFselected', 'QGEFboard');
			for (let piece of pieces) this.board.arrow(+piece.dataset.location, location, '#00FF0080');
//
			if (pieces.length > 0 && this.isCurrentPlayerActive())
				this.action('attack', {FACTION: this.FACTION, location: location, pieces: JSON.stringify(pieces.reduce((L, node) => [...L, +node.dataset.id], []))});
		},
		QGEFretreat: function (location)
		{
			const node = $(`QGEFpiece-${this.gamedatas.gamestate.args.piece}`);
			this.confirm(`Retreat to <B>${_(this.gamedatas.REGIONS[location])}<B>: ${node.outerHTML}`, 'reaction', {FACTION: this.FACTION, card: this.gamedatas.gamestate.args.card, piece: this.gamedatas.gamestate.args.piece, location: location});
		},
		QGEFreaction: function (piece)
		{
			const node = $(`QGEFpiece-${piece}`);
//
			if (this.gamedatas.gamestate.name === 'attackRoundDefender')
			{
				const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${this.FACTION}`);
				if (cards.length === 1)
				{
					const card = cards[0];
					const reaction = this.gamedatas.CARDS[this.FACTION][card.dataset.type_arg].reaction;
					switch (reaction)
					{
						case 'Exchange':
							return this.confirm(dojo.string.substitute(_(`Play a card for reaction: <B>${reaction}</B>${node.outerHTML}`), {reaction: this.REACTIONS[reaction].toUpperCase()}), 'reaction', {FACTION: this.FACTION, card: card.dataset.id, piece: piece});
						case 'Retreat':
							this.gamedatas.gamestate.args['card'] = card.dataset.id;
							this.gamedatas.gamestate.args['piece'] = piece;
							return this.setClientState('retreat', {possibleactions: ['retreat'], descriptionmyturn: _('${you} may move one piece out of the attacked space')});
						default:
							return this.showMessage(_('Deselect reaction first'), 'info');
					}
				}
			}
			if (this.gamedatas.gamestate.name === 'attackRoundAdvance')
			{
				const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${this.FACTION}`);
				if (cards.length === 1)
				{
					const card = cards[0];
					const reaction = this.gamedatas.CARDS[this.FACTION][card.dataset.type_arg].reaction;
					if (reaction === 'Advance')
						return this.confirm(dojo.string.substitute(_(`Play a card for reaction: <B>${reaction}</B>${node.outerHTML}`), {reaction: this.REACTIONS[reaction].toUpperCase()}), 'reaction', {FACTION: this.FACTION, card: card.dataset.id, piece: piece});
				}
				return this.showMessage(_('Advance reaction only'), 'info');
			}
			this.confirm(`Remove from <B>${_(this.gamedatas.REGIONS[node.dataset.location])}<B>:${node.outerHTML}`, 'removePiece', {FACTION: this.FACTION, piece: piece});
		},
		updatePreference: function (event)
		{
			const match = event.target.id.match(/^preference_[cf]ontrol_(\d+)$/);
//
			if (match)
			{
				let pref = +match[1];
				let value = +event.target.value;
				this.prefs[pref].value = value;
				switch (pref)
				{
					case SPEED:
						DELAY = DELAYS[value];
						document.documentElement.style.setProperty('--DELAY', DELAY);
						this.setSynchronous();
						break;
				}
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
				if ('FACTION' in args) args.FACTION = `<div class='QGEFfaction' faction='${args.FACTION}'></div>`;
				if ('faction' in args) args.faction = `<img style='width:20px;vertical-align:middle;' src='${g_gamethemeurl}img/flag_${args.faction}.jpg'>`;
//
				if ('CARD' in args) args.CARD = this[args.CARD.FACTION + 'Deck'].card(args.CARD.card);
			}
			return this.inherited(arguments);
		},
		confirm: function (text, ...args)
		{
			this.confirmationDialog(text, () => this.action(...args));
		},
		action: function (action, args =
		{}, success = () => {}, fail = undefined)
		{
			args.lock = true;
			this.ajaxcall(`/quartermastergeneraleastfront/quartermastergeneraleastfront/${action}.html`, args, this, success, fail);
		}
	}
	);
});
