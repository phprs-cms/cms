/* phpRS 3.0 - drobnosti administrace. Bez knihoven, bez build kroku. */

(function () {
	'use strict';

	// Lišta nástrojů nad textovým polem: <div data-nastroje="id-textarea">
	var TLACITKA = [
		['B', '<strong>', '</strong>', 'Tučně'],
		['I', '<em>', '</em>', 'Kurzíva'],
		['odstavec', '<p>', '</p>', 'Odstavec'],
		['nadpis', '<h2>', '</h2>', 'Mezititulek'],
		['odkaz', null, '</a>', 'Odkaz'],
		['seznam', '<ul>\n<li>', '</li>\n</ul>', 'Odrážkový seznam'],
		['citace', '<blockquote>', '</blockquote>', 'Citace'],
		['obrázek', null, '', 'Obrázek']
	];

	function obal(pole, pred, za) {
		var od = pole.selectionStart, po = pole.selectionEnd;
		var vyber = pole.value.slice(od, po);
		pole.setRangeText(pred + vyber + za, od, po, 'select');
		pole.focus();
		if (vyber === '') {
			pole.selectionStart = pole.selectionEnd = od + pred.length;
		}
	}

	document.querySelectorAll('[data-nastroje]').forEach(function (lista) {
		var pole = document.getElementById(lista.getAttribute('data-nastroje'));
		if (!pole) { return; }
		lista.className = 'nastroje';
		TLACITKA.forEach(function (t) {
			var b = document.createElement('button');
			b.type = 'button';
			b.textContent = t[0];
			b.title = t[3];
			b.addEventListener('click', function () {
				if (t[0] === 'odkaz') {
					var url = window.prompt('Adresa odkazu:', 'https://');
					if (url) { obal(pole, '<a href="' + url.replace(/"/g, '&quot;') + '">', '</a>'); }
				} else if (t[0] === 'obrázek') {
					var src = window.prompt('Adresa obrázku:', '');
					if (src) { obal(pole, '<img src="' + src.replace(/"/g, '&quot;') + '" alt="', '">'); }
				} else {
					obal(pole, t[1], t[2]);
				}
			});
			lista.appendChild(b);
		});
	});

	// Varování před opuštěním rozepsaného formuláře
	document.querySelectorAll('form.formular').forEach(function (form) {
		var zmeneno = false;
		form.addEventListener('input', function () { zmeneno = true; });
		form.addEventListener('submit', function () { zmeneno = false; });
		window.addEventListener('beforeunload', function (e) {
			if (zmeneno) { e.preventDefault(); e.returnValue = ''; }
		});
	});
})();
