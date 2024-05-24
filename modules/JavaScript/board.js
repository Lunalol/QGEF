define(["dojo", "dojo/_base/declare"], function (dojo, declare)
{
	return declare("Board", null,
	{
		constructor: function (bgagame)
		{
			console.log('Board constructor');
//
// Reference to BGA game
//
			this.bgagame = bgagame;
//
// Getting playarea & board container and map dimensions
//
			this.boardWidth = boardWidth;
			this.boardHeight = boardHeight;
//
			this.playarea = dojo.byId('QGEFscrollArea');
			this.board = dojo.byId('QGEFboard');
//
			this.canvas = dojo.byId('QGEFcanvas');
			dojo.setAttr(this.canvas, 'width', this.boardWidth);
			dojo.setAttr(this.canvas, 'height', this.boardHeight);
//
			dojo.style('QGEFbackground', 'width', this.boardWidth + 'px');
			dojo.style('QGEFbackground', 'height', this.boardHeight + 'px');
//
			dojo.query('.QGEFregion', this.board).connect('click', this, 'click');
//
			this.zoomLevel = dojo.byId('QGEFzoomLevel');
			this.bgagame.onScreenWidthChange = this.resize.bind(this);
//
// Flag to follow drag gestures
//
			this.dragging = false;
//
			dojo.connect(document, 'oncontextmenu', (event) => dojo.stopEvent(event));
//
// Event listeners for drag gestures
//
			dojo.connect(this.playarea, 'mousedown', this, 'begin_drag');
			dojo.connect(this.playarea, 'mousemove', this, 'drag');
			dojo.connect(this.playarea, 'mouseup', this, 'end_drag');
			dojo.connect(this.playarea, 'mouseleave', this, 'end_drag');
//
// Event listeners for scaling
//
			dojo.connect(this.playarea, 'scroll', this, 'scroll');
			dojo.connect(this.playarea, 'wheel', this, 'wheel');
			dojo.connect(this.zoomLevel, 'oninput', this, () => {
				dojo.stopEvent(event);
				this.setZoom(Math.pow(10., event.target.value / 100), this.playarea.clientWidth / 2, this.playarea.clientHeight / 2);
			});
			dojo.connect(dojo.byId('QGEFzoomMinus'), 'click', () => {
				dojo.stopEvent(event);
				this.setZoom(Math.pow(10., (parseInt(this.zoomLevel.value) - 10) / 100), this.playarea.clientWidth / 2, this.playarea.clientHeight / 2);
			});
			dojo.connect(dojo.byId('QGEFzoomPlus'), 'click', () => {
				dojo.stopEvent(event);
				this.setZoom(Math.pow(10., (parseInt(this.zoomLevel.value) + 10) / 100), this.playarea.clientWidth / 2, this.playarea.clientHeight / 2);
			});
//
			this.ratio = parseFloat(localStorage.getItem(`${this.bgagame.game_id}.ratio`));
			if (isNaN(this.ratio)) this.ratio = 1;
			dojo.style('QGEFscrollArea', 'aspect-ratio', `${this.ratio}`);
//
			dojo.connect(dojo.byId('QGEFsmaller'), 'click', (event) => {
				dojo.stopEvent(event);
				localStorage.setItem(`${this.bgagame.game_id}.ratio`, this.ratio = this.ratio = Math.min(Math.max(.5, this.ratio - .25), 2.5));
				dojo.style('QGEFscrollArea', 'aspect-ratio', `${this.ratio}`);
				this.resize();
			});
			dojo.connect(dojo.byId('QGEFbigger'), 'click', (event) => {
				dojo.stopEvent(event);
				localStorage.setItem(`${this.bgagame.game_id}.ratio`, this.ratio = this.ratio = Math.min(Math.max(.5, this.ratio + .25), 2.5));
				dojo.style('QGEFscrollArea', 'aspect-ratio', `${this.ratio}`);
				this.resize();
			});
//
			dojo.connect(this.playarea, 'gesturestart', this, () => this.zooming = this.board.scale);
			dojo.connect(this.playarea, 'gestureend', this, () => this.zooming = null);
			dojo.connect(this.playarea, 'gesturechange', this, (event) =>
			{
				event.preventDefault();
//
				if (this.zooming !== null)
				{
					const rect = this.playarea.getBoundingClientRect();
					this.setZoom(this.zooming * event.scale, event.clientX - rect.left, event.clientY - rect.top);
				}
			});
//
// Event listeners for hiding units/markers
//
			document.addEventListener('keydown', (event) => {
				if (event.key === 'Shift') dojo.addClass(this.board, 'QGEFhidePieces');
				if (event.key === 'Control') dojo.addClass(this.board, 'QGEFhideMarkers');
			});
			document.addEventListener('keyup', (event) => {
				if (event.key === 'Shift') dojo.removeClass(this.board, 'QGEFhidePieces');
				if (event.key === 'Control') dojo.removeClass(this.board, 'QGEFhideMarkers');
			});
			window.onblur = () => {
				dojo.removeClass(this.board, 'QGEFhidePieces');
				dojo.removeClass(this.board, 'QGEFhideMarkers');
			};
//
// Initial zoom to cover the whole map or stored in session
//
			const scale = parseFloat(localStorage.getItem(`${this.bgagame.game_id}.${this.bgagame.table_id}.zoomLevel`));
			const sX = parseFloat(localStorage.getItem(`${this.bgagame.game_id}.${this.bgagame.table_id}.sX`));
			const sY = parseFloat(localStorage.getItem(`${this.bgagame.game_id}.${this.bgagame.table_id}.sY`));
//
			this.setZoom(Math.max(this.playarea.clientWidth / this.boardWidth, this.playarea.clientHeight / this.boardHeight, isNaN(scale) ? 0 : scale), this.playarea.clientWidth / 2, this.playarea.clientHeight / 2);
//
			const zoom = parseFloat(this.board.scale);
			this.playarea.scrollLeft = isNaN(scale) ? (this.boardWidth * zoom - this.playarea.clientWidth) / 2 : sX;
			this.playarea.scrollTop = isNaN(scale) ? (this.boardHeight * zoom - this.playarea.clientHeight) / 2 : sY;
		},
		resize: function ()
		{
			const zoomLevelMin = Math.floor(Math.log10(Math.max(this.playarea.clientWidth / this.boardWidth, this.playarea.clientHeight / this.boardHeight)) * 100.);
			this.zoomLevel.min = zoomLevelMin;
			this.zoomLevel.max = 100 + zoomLevelMin;
			this.zoomLevel.value = this.zoomLevel.min;
			this.setZoom(Math.pow(10., this.zoomLevel.value / 100), this.playarea.clientWidth / 2, this.playarea.clientHeight / 2);
		},
		setZoom: function (scale, x, y)
		{
//
// Calc scale and store in session
//
			scale = Math.max(this.playarea.clientWidth / this.boardWidth, this.playarea.clientHeight / this.boardHeight, scale);
			localStorage.setItem(`${this.bgagame.game_id}.${this.bgagame.table_id}.zoomLevel`, scale);
//
// Update range value
//
			this.zoomLevel.value = Math.round(Math.log10(scale) * 100.);
//
// Get scroll positions and scale before scaling
//
			let sX = this.playarea.scrollLeft;
			let sY = this.playarea.scrollTop;
//
// Board scaling
//
			const oldScale = this.board.scale;
			this.board.scale = scale;
			this.board.style.transform = `scale(${scale})`;
//			this.board.style.width = `${this.boardWidth * Math.min(1.0, scale)}px`;
//			this.board.style.height = `${this.boardHeight * Math.min(1.0, scale)}px`;
//
// Set scroll positions after scaling
//
			this.playarea.scrollTo(Math.round((x + sX) * (scale / oldScale) - x), Math.round((y + sY) * (scale / oldScale) - y));
		},
		wheel: function (event)
		{
			if (event.ctrlKey /*|| true*/)
			{
//
// Ctrl + Wheel
//
				dojo.stopEvent(event);
//
// Update scale only when zoom factor is updated
//
				const oldZoom = parseInt(this.zoomLevel.value);
				const newZoom = Math.min(Math.max(this.zoomLevel.min, oldZoom - 10 * Math.sign(event.deltaY)), this.zoomLevel.max);
				if (oldZoom !== newZoom)
				{
					const rect = this.playarea.getBoundingClientRect();
					this.setZoom(Math.pow(10., newZoom / 100.), event.clientX - rect.left, event.clientY - rect.top);
				}
			}
		},
		centerMap: function (location)
		{
			const zoom = this.board.scale;
			this.playarea.scrollTo({left: REGIONS[location].x * zoom - this.playarea.clientWidth / 2, top: REGIONS[location].y * zoom - this.playarea.clientHeight / 2, behavior: 'smooth'});
		},
		scroll: function ()
		{
			localStorage.setItem(`${this.bgagame.game_id}.${this.bgagame.table_id}.sX`, this.playarea.scrollLeft);
			localStorage.setItem(`${this.bgagame.game_id}.${this.bgagame.table_id}.sY`, this.playarea.scrollTop);
		},
		begin_drag: function (event)
		{
			this.dragging = true;
//
			this.startX = event.clientX;
			this.startY = event.clientY;
		},
		drag: function (event)
		{
			if (this.dragging)
			{
				this.playarea.scrollLeft -= (event.clientX - this.startX);
				this.playarea.scrollTop -= (event.clientY - this.startY);
//
				this.startX = event.clientX;
				this.startY = event.clientY;
			}
		},
		end_drag: function ()
		{
			this.dragging = false;
		},
		click: function (event)
		{
			const node = event.currentTarget;
//
			if (node.getAttribute('class').indexOf('QGEFselectable') >= 0 && this.bgagame.isCurrentPlayerActive())
			{
				dojo.stopEvent(event);
//
				if (this.bgagame.gamedatas.gamestate.possibleactions.includes('scorched')) return this.bgagame.QGEFscorched(+node.dataset.location);
				if (this.bgagame.gamedatas.gamestate.possibleactions.includes('move')) return this.bgagame.QGEFmoveAttack(+node.dataset.location);
				if (this.bgagame.gamedatas.gamestate.possibleactions.includes('attack')) return this.bgagame.QGEFmoveAttack(+node.dataset.location);
				if (this.bgagame.gamedatas.gamestate.possibleactions.includes('desperateAttack')) return this.bgagame.QGEFmoveAttack(+node.dataset.location);
				if (this.bgagame.gamedatas.gamestate.possibleactions.includes('deploy')) return this.bgagame.QGEFdeploy(+node.dataset.location);
				if (this.bgagame.gamedatas.gamestate.possibleactions.includes('recruit')) return this.bgagame.QGEFrecruit(+node.dataset.location);
				if (this.bgagame.gamedatas.gamestate.possibleactions.includes('retreat')) return this.bgagame.QGEFretreat(+node.dataset.location);
				if (this.bgagame.gamedatas.gamestate.possibleactions.includes('forcedMarch')) return this.bgagame.QGEFmoveAttack(+node.dataset.location);
			}
		},
		clearCanvas()
		{
			const ctx = this.canvas.getContext('2d');
			ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
		},
		arrow: function (start, end, color = '#FF000080')
		{
			if (start === end) return;
//
			const ctx = this.canvas.getContext('2d');
//
			ctx.save();
//
			ctx.strokeStyle = '#00000020';
			ctx.fillStyle = color;
//
			const dx = REGIONS[end].x - REGIONS[start].x;
			const dy = REGIONS[end].y - REGIONS[start].y;
//
			if (dx > 0) angle = Math.atan(dy / dx);
			else if (dx < 0) angle = Math.PI + Math.atan(dy / dx);
			else angle = Math.PI / 2 * Math.sign(dy);
//
			ctx.translate(REGIONS[start].x, REGIONS[start].y);
			ctx.scale(Math.sqrt(dx * dx + dy * dy) / 100., Math.sqrt(dx * dx + dy * dy) / 100.);
			ctx.rotate(angle);
//
			ctx.beginPath();
			ctx.moveTo(0, 0);
			ctx.lineTo(-5, 10);
			ctx.lineTo(80, 5);
			ctx.lineTo(80, 10);
			ctx.lineTo(100, 00);
			ctx.lineTo(80, -10);
			ctx.lineTo(80, -5);
			ctx.lineTo(0, -10);
			ctx.lineTo(-5, -10);
			ctx.closePath();
//
			ctx.fill();
			ctx.stroke();
//
			ctx.restore();
		},
		supplyLines(lines, color)
		{
			console.log(lines);
			const ctx = this.canvas.getContext('2d');
//
			ctx.save();
//
			ctx.strokeStyle = color;
			ctx.lineWidth = 20;
			ctx.lineCap = 'round';
//
			for (let from = 0; from < lines.length; from++)
			{
				for (let to = from + 1; to < lines.length; to++)
				{
					if (this.bgagame.gamedatas.ADJACENCY[lines[from]].includes(lines[to]))
					{
						ctx.beginPath();
						ctx.moveTo(REGIONS[lines[from]].x, REGIONS[lines[from]].y);
						ctx.lineTo(REGIONS[lines[to]].x, REGIONS[lines[to]].y);
						ctx.stroke();
					}
				}
			}
//
			ctx.restore();
		}
	}
	);
});
