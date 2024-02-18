define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", "ebg/counter",
	g_gamethemeurl + "modules/constants.js",
	g_gamethemeurl + "modules/JavaScript/board.js",
	g_gamethemeurl + "modules/JavaScript/track.js",
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
		},
		setLoader(value, max)
		{
			this.inherited(arguments);
		},
		setup: function (gamedatas)
		{
			console.log("Starting game setup");
//
			dojo.connect(dojo.byId('QGEFplayArea'), 'click', () => this.restoreServerGameState());
//
// Animations Speed
//
			DELAY = 500 /*DELAYS[this.prefs[100].value]*/;
			document.documentElement.style.setProperty('--DELAY', DELAY);
			dojo.query('.preference_control').connect('onchange', this, 'updatePreference');
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
			if ('move' in state.args) for (let piece in state.args.move) dojo.addClass(`QGEFpiece-${piece}`, 'QGEFselectable');
//
			switch (stateName)
			{
//
				case 'firstActionStep':
				case 'secondActionStep':
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
							this.gamedatas.gamestate.possibleactions = ['move'];
							break;
						case 'desperateAttack':
							this.gamedatas.gamestate.descriptionmyturn = _('Discard 2 cards and then attack a land space');
							break;
					}
					this.updatePageTitle();
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
			dojo.query('.QGEFhandHolder .QGEFselectable').removeClass('QGEFselectable');
//
			switch (stateName)
			{
				case 'action':
					dojo.query('.QGEFhandHolder .QGEFselected').removeClass('QGEFselected');
					break;
			}
		},
		onUpdateActionButtons: function (stateName, args)
		{
			console.log('onUpdateActionButtons: ' + stateName);
//
			if (this.isCurrentPlayerActive())
			{
				const FACTION = args.FACTION;
//
				switch (stateName)
				{
//
					case 'firstMovementStep':
					case 'secondMovementStep':
//
						this.addActionButton('QGEFpass', _('Pass'), (event) => {
							dojo.stopEvent(event);
							this.confirm(_('Do nothing'), 'pass', {});
						});
//
						break;
//
					case 'firstActionStep':
					case 'secondActionStep':
//
						this.addActionButton('QGEFplay', _('Play'), (event) => {
							dojo.stopEvent(event);
//
							const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
							this.confirm(event, _('Play a card to resolve its play text, discarding it afterwards'), 'play', {cards: JSON.stringify(cards)});
						});
						this.addActionButton('QGEFconscription', _('Conscription'), (event) => {
							dojo.stopEvent(event);
//
							const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
							this.confirm(_('Discard 1 card to deploy an infantry, or 2 cards to deploy a tank, airplane, or fleet'), 'conscription', {cards: JSON.stringify(cards)});
						});
						this.addActionButton('QGEforcedMarch', _('Forced March'), (event) => {
							dojo.stopEvent(event);
//
							const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
							this.confirm(_('Discard 1 card to move 1 piece'), 'forcedMarch', {cards: JSON.stringify(cards)});
						});
						this.addActionButton('QGEFdesperateAttack', _('Desperate Attack'), (event) => {
							dojo.stopEvent(event);
//
							const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
							this.confirm(_('Discard 2 cards and then attack a land space'), 'desperateAttack', {cards: JSON.stringify(cards)});
						});
						this.addActionButton('QGEFproductionInitiative', _('Production Initiative'), (event) => {
							dojo.stopEvent(event);
//
							const cards = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFhand-${FACTION}`).reduce((L, node) => [...L, +node.dataset.id], []);
							this.confirm(_('Draw 1 card'), 'productionInitiative', {});
						});
						this.addActionButton('QGEFcontingency', _('Contingency'), (event) => {
							dojo.stopEvent(event);
							this.confirm(_('Use one of your 5 Contingency cards'), 'contingency', {});
						});
						this.addActionButton('QGEFpass', _('Pass'), (event) => {
							dojo.stopEvent(event);
							this.confirm(_('Do nothing'), 'pass', {});
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
							this.action('cancel', {});
							dojo.query('.QGEFhandHolder .QGEFselected').removeClass('QGEFselected');
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
			dojo.subscribe('alliesDeck', (notif) => this.alliesDeck.place(notif.args.card));
			dojo.subscribe('axisDeck', (notif) => this.axisDeck.place(notif.args.card));
			dojo.subscribe('alliesDiscard', (notif) => this.alliesDeck.discard(notif.args.FACTION, notif.args.card));
			dojo.subscribe('axisDiscard', (notif) => this.axisDeck.discard(notif.args.FACTION, notif.args.card));
//
			this.setSynchronous();
		},
		setSynchronous()
		{
			this.notifqueue.setSynchronous('placePiece', DELAY);
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
				dojo.toggleClass('QGEFplay', 'disabled', cards !== 1);
				dojo.style('QGEFconscription', 'display', (cards === 1 || cards === 2) ? '' : 'none');
				dojo.style('QGEforcedMarch', 'display', (cards === 1) ? '' : 'none');
				dojo.style('QGEFdesperateAttack', 'display', (cards === 2) ? '' : 'none');
//
				const contingency = dojo.query('.QGEFcardContainer.QGEFselected', `QGEFcontingency-${FACTION}`).length;
				dojo.style('QGEFcontingency', 'display', (contingency === 1) ? '' : 'none');
//
				dojo.toggleClass('QGEFpass', 'disabled', cards + contingency !== 0);
				dojo.style('QGEFproductionInitiative', 'display', (cards + contingency === 0) ? '' : 'none');
			}
		},
		QGEFmovement: function (location)
		{
			this.board.clearCanvas();
//
			const pieces = dojo.query('.QGEFpiece.QGEFselected', 'QGEFboard');
			for (let piece of pieces) this.board.arrow(+piece.dataset.location, location, '#00FF0080');
//
			if (pieces.length > 0 && this.isCurrentPlayerActive()) this.action('move', {location: location, pieces: JSON.stringify(pieces.reduce((L, node) => [...L, +node.dataset.id], []))});
		},
		QGEFdeploy: function (location)
		{
			const node = $('generalactions').querySelector('.QGEFpieceContainer.QGEFselected>.QGEFpiece');
			if (node && this.isCurrentPlayerActive()) this.action('deploy', {location: location, faction: node.dataset.faction, type: node.dataset.type});
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
				if ('faction' in args) args.faction = `<div class='QGEFfaction' faction='${args.faction}'></div>`;
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
