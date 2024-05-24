/* global g_gamethemeurl */

define(["dojo", "dojo/_base/declare"], function (dojo, declare)
{
	return declare("Tracks", null,
	{
//
		constructor: function (bgagame)
		{
			const SIZE = 75;
//
// Reference to BGA game
//
			this.bgagame = bgagame;
//
// Translate
//
			this.translate = {
				spring: _('SPRING'),
				summer: _('SUMMER'),
				fall: _('FALL'),
				winter: _('WINTER'),
				scoring: _('SCORING'),
				late: _('+ LATE CARDS')
			};
//
// VP Track
//
			const QGEFvpTrack = dojo.place(`<div id='QGEFvpTrack' class='QGEFvpTrack'>`, 'QGEFboard');
			for (let VP = 0; VP <= 50; VP++)
			{
				const node = dojo.place(`<div id='QGEFvp-${VP}' class='QGEFvpTrackElement' VP='${VP}'>`, QGEFvpTrack);
				dojo.setStyle(node, {
					left: (Math.min(VP, 25) * SIZE) + 'px',
					top: (Math.max(VP - 25, 0) * SIZE) + 'px',
					width: SIZE + 'px', 'line-height': SIZE + 'px'
				});
			}
//
// Round Track
//
			const QGEFroundTrackContainer = dojo.place(`<div id='QGEFroundTrackContainer' class='QGEFroundTrackContainer'>`, 'QGEF');
			dojo.place("<div class='QGEFroundTrackElement QGEFroundTrackElement-gray' style='position:absolute;margin:10px;z-index:1;'></div>", QGEFroundTrackContainer);
//
			const QGEFroundTrack = dojo.place(`<div id='QGEFroundTrack' class='QGEFroundTrack'>`, QGEFroundTrackContainer);
			dojo.place(this.bgagame.format_block('QGEFroundTrackGRAY'), QGEFroundTrack);
			dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 1, season: this.translate.summer, year: 1941}), QGEFroundTrack);
			dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 2, season: this.translate.fall, year: 1941}), QGEFroundTrack);
			const winter1941 = dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 3, season: this.translate.winter, year: 1941}), QGEFroundTrack);
			dojo.place(`<img draggable='false' style='position:absolute;top:12px;left:-6px;width:26px;' src='${g_gamethemeurl}img/svg/SustainAttack.svg'>`, winter1941);
			dojo.place(`<img draggable='false' style='position:absolute;top:10px;left:-10px;width:30px;' src='${g_gamethemeurl}img/svg/forbidden.svg'>`, winter1941);
			dojo.place(this.bgagame.format_block('QGEFroundTrackYELLOW', {text: this.translate.scoring}), QGEFroundTrack);
			dojo.place(this.bgagame.format_block('QGEFroundTrackRED', {text: this.translate.late}), QGEFroundTrack);
			const spring1942 = dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 4, season: this.translate.spring, year: 1942}), QGEFroundTrack);
			dojo.place(`<img draggable='false' style='position:absolute;top:12px;left:-6px;width:26px;' src='${g_gamethemeurl}img/svg/Advance.svg'>`, spring1942);
			dojo.place(`<img draggable='false' style='position:absolute;top:10px;left:-10px;width:30px;' src='${g_gamethemeurl}img/svg/forbidden.svg'>`, spring1942);
			dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 5, season: this.translate.summer, year: 1942}), QGEFroundTrack);
			dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 6, season: this.translate.fall, year: 1942}), QGEFroundTrack);
			const winter1942 = dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 7, season: this.translate.winter, year: 1942}), QGEFroundTrack);
			dojo.place(`<img draggable='false' style='position:absolute;top:12px;left:-6px;width:26px;' src='${g_gamethemeurl}img/svg/SustainAttack.svg'>`, winter1942);
			dojo.place(`<img draggable='false' style='position:absolute;top:10px;left:-10px;width:30px;' src='${g_gamethemeurl}img/svg/forbidden.svg'>`, winter1942);
			dojo.place(this.bgagame.format_block('QGEFroundTrackYELLOW', {text: this.translate.scoring}), QGEFroundTrack);
			const spring1943 = dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 8, season: this.translate.spring, year: 1943}), QGEFroundTrack);
			dojo.place(`<img draggable='false' style='position:absolute;top:12px;left:-6px;width:26px;' src='${g_gamethemeurl}img/svg/Advance.svg'>`, spring1943);
			dojo.place(`<img draggable='false' style='position:absolute;top:10px;left:-10px;width:30px;' src='${g_gamethemeurl}img/svg/forbidden.svg'>`, spring1943);
			dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 9, season: this.translate.summer, year: 1943}), QGEFroundTrack);
			dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 10, season: this.translate.fall, year: 1943}), QGEFroundTrack);
			const winter1943 = dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 11, season: this.translate.winter, year: 1943}), QGEFroundTrack);
			dojo.place(`<img draggable='false' style='position:absolute;top:12px;left:-6px;width:26px;' src='${g_gamethemeurl}img/svg/SustainAttack.svg'>`, winter1943);
			dojo.place(`<img draggable='false' style='position:absolute;top:10px;left:-10px;width:30px;' src='${g_gamethemeurl}img/svg/forbidden.svg'>`, winter1943);
			dojo.place(this.bgagame.format_block('QGEFroundTrackYELLOW', {text: this.translate.scoring}), QGEFroundTrack);
			const spring1944 = dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 12, season: this.translate.spring, year: 1944}), QGEFroundTrack);
			dojo.place(`<img draggable='false' style='position:absolute;top:12px;left:-6px;width:26px;' src='${g_gamethemeurl}img/svg/Advance.svg'>`, spring1944);
			dojo.place(`<img draggable='false' style='position:absolute;top:10px;left:-10px;width:30px;' src='${g_gamethemeurl}img/svg/forbidden.svg'>`, spring1944);
			dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 13, season: this.translate.summer, year: 1944}), QGEFroundTrack);
			dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 14, season: this.translate.fall, year: 1944}), QGEFroundTrack);
			const winter1944 = dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 15, season: this.translate.winter, year: 1944}), QGEFroundTrack);
			dojo.place(`<img draggable='false' style='position:absolute;top:12px;left:-6px;width:26px;' src='${g_gamethemeurl}img/svg/SustainAttack.svg'>`, winter1944);
			dojo.place(`<img draggable='false' style='position:absolute;top:10px;left:-10px;width:30px;' src='${g_gamethemeurl}img/svg/forbidden.svg'>`, winter1944);
			const spring1945 = dojo.place(this.bgagame.format_block('QGEFroundTrackBLACK', {round: 16, season: this.translate.spring, year: 1945}), QGEFroundTrack);
			dojo.place(`<img draggable='false' style='position:absolute;top:12px;left:-6px;width:26px;' src='${g_gamethemeurl}img/svg/Advance.svg'>`, spring1945);
			dojo.place(`<img draggable='false' style='position:absolute;top:10px;left:-10px;width:30px;' src='${g_gamethemeurl}img/svg/forbidden.svg'>`, spring1945);
			dojo.place(this.bgagame.format_block('QGEFroundTrackYELLOW', {text: this.translate.scoring}), QGEFroundTrack);
			dojo.place(this.bgagame.format_block('QGEFroundTrackGRAY'), QGEFroundTrack);
			dojo.place(`<div style='flex:0 0 100vh;'></div>`, QGEFroundTrack);
		},
		round: function (steps)
		{
			$('QGEFroundTrack').scrollTo({top: steps * 45, behavior: 'smooth'});
		}
	}
	);
});
