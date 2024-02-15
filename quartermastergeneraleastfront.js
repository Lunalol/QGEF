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
			document.addEventListener('click', () => this.restoreServerGameState());
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
					dojo.query('.QGEFcardContainer', `QGEFhand-${state.args.FACTION}`).addClass('QGEFselectable');
					dojo.query('.QGEFcardContainer', `QGEFcontingency-${state.args.FACTION}`).addClass('QGEFselectable');
//
					break;
//
			}
		},
		onLeavingState: function (stateName)
		{
			console.log('Leaving state: ' + stateName);
//
			dojo.query('.QGEFselected').removeClass('QGEFselected');
			dojo.query('.QGEFselectable').removeClass('QGEFselectable');
//
			dojo.query('.QGEFregion', 'QGEFboard').forEach((node) => node.setAttribute('class', 'QGEFregion'));
//
		},
		onUpdateActionButtons: function (stateName, args)
		{
			console.log('onUpdateActionButtons: ' + stateName);
//
			if (this.isCurrentPlayerActive())
			{
				switch (stateName)
				{
//
					case 'firstMovementStep':
					case 'secondMovementStep':
//
						this.addActionButton('QGEFpass', _('Pass'), () => this.action('pass', {}));
//
						break;
//
					case 'firstActionStep':
					case 'secondActionStep':
//
						this.addActionButton('QGEFplay', _('Play'), () => this.action('pass', {}));
						this.addActionButton('QGEFconscription', _('Conscription'), () => this.action('conscription', {}));
						this.addActionButton('QGEforcedMarch', _('Forced March'), () => this.action('forcedMarch', {}));
						this.addActionButton('QGEFdesperateAttacks', _('Desperate Attack'), () => this.action('desperateAttacks', {}));
						this.addActionButton('QGEFproductionInitiative', _('Production Initiative'), () => this.action('productionInitiative', {}));
						this.addActionButton('QGEFcontingency', _('Contingency'), () => this.action('contingency', {}));
						this.addActionButton('QGEFpass', _('Pass'), () => this.action('pass', {}));
//
						this.updateActionButtons();
//
						this.addTooltip('QGEFplay', _('Play'), _('Play a card to resolve its play text, discarding it afterwards'), 1000);
						this.addTooltip('QGEFconscription', _('Conscription'), _('Discard 1 card to deploy an infantry, or 2 cards to deploy a tank, airplane, or fleet'), 1000);
						this.addTooltip('QGEforcedMarch', _('Forced March'), _('Discard 1 card to move 1 piece.'), 1000);
						this.addTooltip('QGEFdesperateAttacks', _('Desperate Attack'), _('Discard 2 cards and then attack a land space.'), 1000);
						this.addTooltip('QGEFproductionInitiative', _('Production Initiative'), _('Draw 1 card'), 1000);
						this.addTooltip('QGEFcontingency', _('Contingency'), _('Use one of your 5 Contingency cards'), 1000);
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
//
			this.setSynchronous();
		},
		setSynchronous()
		{
			this.notifqueue.setSynchronous('placePiece', DELAY);
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
				dojo.style('QGEFdesperateAttacks', 'display', (cards === 2) ? '' : 'none');
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
			const pieces = dojo.query('.QGEFpiece.QGEFselected', 'QGEFboard').reduce((L, node) => [...L, +node.dataset.id], []);
			if (pieces.length > 0 && this.isCurrentPlayerActive()) this.action('move', {location: location, pieces: JSON.stringify(pieces)});
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
		action: function (action, args
				=
		{}, success = () => {}, fail = undefined)
		{
			args.lock = true;
			this.ajaxcall(`/quartermastergeneraleastfront/quartermastergeneraleastfront/${action}.html`, args, this, success, fail);
		}
	}
	);
});
