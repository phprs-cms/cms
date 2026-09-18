/* phpRS 3.0 - vizuální editor bloků. Běží přímo ve stránce webu (adresa s ?upravit=1), bez knihoven.
 *
 * Stránka v tomto režimu obsahuje značky: .rs-zona[data-zona] (zóna), .rs-blok[data-blok] (blok), .rs-pridat (tlačítko).
 * Vše se ukládá hned přes JSON akce modulu Bloky v administraci; po změně obsahu se stránka načte znovu,
 * takže je vždy vidět skutečný výsledek.
 */
(function () {
	'use strict';

	var N = JSON.parse(document.getElementById('rs-nastaveni').textContent);
	var MODUL = N.admin + '?modul=bloky';

	function el(tag, atributy, deti) {
		var e = document.createElement(tag);
		Object.keys(atributy || {}).forEach(function (k) {
			if (k === 'text') { e.textContent = atributy[k]; } else if (k === 'html') { e.innerHTML = atributy[k]; } else if (k.slice(0, 2) === 'on') { e.addEventListener(k.slice(2), atributy[k]); } else if (atributy[k] !== false && atributy[k] != null) { e.setAttribute(k, atributy[k] === true ? '' : atributy[k]); }
		});
		(deti || []).forEach(function (d) { if (d) { e.appendChild(typeof d === 'string' ? document.createTextNode(d) : d); } });
		return e;
	}
	function odesli(akce, data) {
		var fd = new FormData();
		fd.append('_csrf', N.csrf);
		Object.keys(data || {}).forEach(function (k) { fd.append(k, data[k]); });
		return fetch(MODUL + '&akce=' + akce, { method: 'POST', body: fd, credentials: 'same-origin' });
	}
	function znovu(otevritBlok) {
		sessionStorage.setItem('rs-posun', String(window.scrollY));
		if (otevritBlok) { sessionStorage.setItem('rs-otevrit', String(otevritBlok)); }
		location.reload();
	}
	function hlaska(text) {
		var h = el('div', { class: 'rs-hlaska', role: 'status', text: text });
		document.body.appendChild(h);
		setTimeout(function () { h.remove(); }, 2200);
	}

	/* ---------- horní lišta ---------- */

	var lista = el('div', { class: 'rs-lista rs-ui' }, [
		el('strong', { text: 'Úprava bloků' }),
		el('span', { class: 'rs-lista-napoveda', text: 'Bloky přetahujte myší. Najetím na blok se ukáže jeho ovládání.' }),
		el('span', { class: 'rs-lista-rozvrzeni' }, [el('span', { text: 'Rozvržení:' })].concat(Object.keys(N.rozvrzeniVolby).map(function (klic) {
			return el('button', { type: 'button', class: klic === N.rozvrzeni ? 'rs-aktivni' : '', title: N.rozvrzeniVolby[klic].popis, text: N.rozvrzeniVolby[klic].nazev,
				onclick: function () { if (klic !== N.rozvrzeni) { odesli('rozvrzeni', { rozvrzeni: klic }).then(function () { znovu(); }); } } });
		}))),
		el('a', { class: 'rs-hotovo', href: N.admin, text: 'Hotovo' })
	]);
	document.body.appendChild(lista);
	document.documentElement.classList.add('rs-upravy');

	// odkazy na webu zůstávají v režimu úprav - bloky se tak dají ladit i na stránce článku nebo rubriky
	document.addEventListener('click', function (e) {
		var a = e.target.closest && e.target.closest('a[href]');
		if (!a || a.closest('.rs-ui') || a.target === '_blank' || a.origin !== location.origin || a.pathname.indexOf('admin.php') !== -1) { return; }
		e.preventDefault();
		var u = new URL(a.href);
		u.searchParams.set('upravit', '1');
		location.href = u.toString();
	}, true);

	/* ---------- ovládání bloku ---------- */

	var tazeny = null;

	function ulozPoradi() {
		var poradi = {};
		document.querySelectorAll('.rs-zona').forEach(function (z) {
			poradi[z.getAttribute('data-zona')] = Array.prototype.map.call(z.querySelectorAll(':scope > .rs-blok'), function (b) { return b.getAttribute('data-blok'); });
		});
		odesli('poradi', { poradi: JSON.stringify(poradi) }).then(function (r) { return r.json(); }).then(function (j) { hlaska(j.ok ? 'Pořadí uloženo' : 'Pořadí se nepodařilo uložit'); });
	}
	function posun(blok, smer) {
		var soused = smer > 0 ? blok.nextElementSibling : blok.previousElementSibling;
		if (!soused || !soused.classList.contains('rs-blok')) { return; }
		blok.parentNode.insertBefore(blok, smer > 0 ? soused.nextElementSibling : soused);
		ulozPoradi();
	}

	document.querySelectorAll('.rs-blok').forEach(function (blok) {
		var id = blok.getAttribute('data-blok');
		blok.appendChild(el('div', { class: 'rs-nastroje rs-ui' }, [
			el('span', { class: 'rs-nazev', text: blok.getAttribute('data-nazev') }),
			el('button', { type: 'button', title: 'Posunout výš', 'aria-label': 'Posunout výš', text: '↑', onclick: function () { posun(blok, -1); } }),
			el('button', { type: 'button', title: 'Posunout níž', 'aria-label': 'Posunout níž', text: '↓', onclick: function () { posun(blok, 1); } }),
			el('button', { type: 'button', class: 'rs-hlavni', text: 'Nastavit', onclick: function () { otevriNastaveni(id); } }),
			el('button', { type: 'button', title: 'Smazat blok', 'aria-label': 'Smazat blok', text: '✕', onclick: function () {
				potvrd('Smazat blok „' + blok.getAttribute('data-nazev') + '“?', function () { odesli('smaz', { idb: id }).then(function () { blok.remove(); hlaska('Blok smazán'); }); });
			} })
		]));
		blok.addEventListener('dragstart', function (e) {
			if (e.target !== blok) { return; }
			tazeny = blok;
			e.dataTransfer.effectAllowed = 'move';
			e.dataTransfer.setData('text/plain', id);
			setTimeout(function () { blok.classList.add('rs-tazeny'); document.documentElement.classList.add('rs-tahne'); }, 0);
		});
		blok.addEventListener('dragend', function () {
			blok.classList.remove('rs-tazeny');
			document.documentElement.classList.remove('rs-tahne');
			document.querySelectorAll('.rs-cil').forEach(function (z) { z.classList.remove('rs-cil'); });
			if (tazeny) { tazeny = null; ulozPoradi(); }
		});
	});

	document.querySelectorAll('.rs-zona').forEach(function (zona) {
		zona.addEventListener('dragover', function (e) {
			if (!tazeny) { return; }
			e.preventDefault();
			document.querySelectorAll('.rs-cil').forEach(function (z) { if (z !== zona) { z.classList.remove('rs-cil'); } });
			zona.classList.add('rs-cil');
			var pred = null;
			Array.prototype.some.call(zona.querySelectorAll(':scope > .rs-blok'), function (b) {
				if (b === tazeny) { return false; }
				var r = b.getBoundingClientRect();
				if (e.clientY < r.top + r.height / 2 && e.clientX < r.right) { pred = b; return true; }
				return false;
			});
			zona.insertBefore(tazeny, pred || zona.querySelector(':scope > .rs-pridat'));
		});
		zona.addEventListener('drop', function (e) { if (tazeny) { e.preventDefault(); } });
	});

	/* ---------- dialogy ---------- */

	function okno(trida, obsah) {
		var d = el('dialog', { class: 'rs-okno rs-ui ' + trida }, obsah);
		document.body.appendChild(d);
		d.addEventListener('close', function () { d.remove(); });
		d.addEventListener('click', function (e) { if (e.target === d) { d.close(); } });
		d.showModal();
		return d;
	}
	function potvrd(text, ano) {
		var d = okno('rs-potvrzeni', [
			el('p', { text: text }),
			el('div', { class: 'rs-tlacitka' }, [
				el('button', { type: 'button', class: 'rs-hlavni', text: 'Ano, smazat', onclick: function () { d.close(); ano(); } }),
				el('button', { type: 'button', text: 'Zrušit', onclick: function () { d.close(); } })
			])
		]);
	}

	/* ---------- přidání bloku ---------- */

	document.querySelectorAll('.rs-pridat').forEach(function (tl) {
		tl.addEventListener('click', function () {
			var zona = tl.getAttribute('data-zona');
			var d = okno('rs-nabidka', [
				el('div', { class: 'rs-okno-hlava' }, [el('strong', { text: 'Co chcete přidat?' }), el('button', { type: 'button', 'aria-label': 'Zavřít', text: '✕', onclick: function () { d.close(); } })]),
				el('p', { class: 'rs-okno-popis', text: 'Blok se přidá do zóny „' + tl.closest('.rs-zona').getAttribute('data-nazev') + '“. Nastavení můžete kdykoli změnit.' })
			].concat(Object.keys(N.katalog).map(function (skupina) {
				return el('section', {}, [el('h3', { text: skupina }), el('div', { class: 'rs-karty' }, N.katalog[skupina].map(function (p) {
					return el('button', { type: 'button', class: 'rs-karta', onclick: function () {
						odesli('rychle_pridat', { zona: zona, typ: p.typ }).then(function (r) { return r.json(); }).then(function (j) { if (j.ok) { znovu(j.idb); } });
					} }, [el('i', { text: p.ikona, 'aria-hidden': 'true' }), el('strong', { text: p.nazev }), el('span', { text: p.popis })]);
				}))]);
			})));
		});
	});

	/* ---------- nastavení bloku ---------- */

	function pole(popisek, prvek, napoveda) {
		return el('label', { class: 'rs-pole' }, [el('span', { text: popisek }), prvek, napoveda ? el('small', { text: napoveda }) : null]);
	}
	function vyber(name, volby, hodnota) {
		return el('select', { name: name }, volby.map(function (v) { return el('option', { value: v[0], selected: String(v[0]) === String(hodnota), text: v[1] }); }));
	}

	function otevriNastaveni(id) {
		fetch(MODUL + '&akce=nastaveni_json&id=' + id, { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (j) {
			if (!j.ok) { return; }
			var b = j.blok, typ = b.sys_funkce, data = String(b.data_sys || '');
			var nazevTypu = 'Text';
			Object.keys(N.katalog).forEach(function (s) { N.katalog[s].forEach(function (p) { if (p.typ === typ) { nazevTypu = p.nazev; } }); });
			var vzhled = Number(b.typ) === 5 ? 1 : Number(b.typ);
			var form = el('form', { class: 'rs-formular' });
			var radky = [];

			radky.push(pole('Nadpis', el('input', { type: 'text', name: 'nazev', value: b.nazev, maxlength: '100', required: true })));
			radky.push(el('label', { class: 'rs-zaskrtnuti' }, [el('input', { type: 'checkbox', name: 'ukazat_nadpis', checked: Number(b.typ) !== 5 }), ' Zobrazit nadpis na webu']));

			if (typ === '') {
				var ta = el('textarea', { name: 'obsah', rows: '8', 'data-editor': 'maly' });
				ta.value = b.obsah;
				radky.push(pole('Obsah', ta, 'Text, obrázek z médií nebo vložený kód (přepněte na HTML).'));
			}
			if (typ === 'men') {
				var seznam = el('div', { class: 'rs-odkazy' });
				var pridejRadek = function (text, adresa) {
					var r = el('div', { class: 'rs-odkaz' }, [
						el('input', { type: 'text', placeholder: 'Text odkazu', value: text || '', 'aria-label': 'Text odkazu' }),
						el('input', { type: 'text', placeholder: '/o-nas nebo https://…', value: adresa || '', 'aria-label': 'Adresa' }),
						el('button', { type: 'button', 'aria-label': 'Odebrat odkaz', text: '✕', onclick: function () { r.remove(); } })
					]);
					seznam.appendChild(r);
				};
				String(b.obsah).split(/\r?\n/).forEach(function (l) { var c = l.split('|'); if (c.length > 1 && c[0].trim()) { pridejRadek(c[0].trim(), c.slice(1).join('|').trim()); } });
				if (!seznam.children.length) { pridejRadek('', ''); }
				radky.push(el('div', { class: 'rs-pole' }, [el('span', { text: 'Odkazy' }), seznam, el('button', { type: 'button', class: 'rs-pridat-radek', text: '+ další odkaz', onclick: function () { pridejRadek('', ''); } })]));
			}
			if (typ === 'cla') {
				radky.push(pole('Rubrika', vyber('blok_rubrika', [[0, 'Nejnovější ze všech rubrik']].concat(N.rubriky.map(function (r) { return [r.id, r.nazev]; })), data.split(':')[0])));
			}
			if (['cla', 'nej', 'sti', 'aut', 'arc'].indexOf(typ) !== -1) {
				radky.push(pole('Kolik položek', el('input', { type: 'number', name: 'blok_pocet', min: '1', max: '50', value: typ === 'cla' ? (data.split(':')[1] || 5) : (data || 5) })));
			}
			if (typ === 'rek') {
				radky.push(pole('Reklamní pozice', vyber('data_sys', Object.keys(N.pozice).map(function (k) { return [k, N.pozice[k]]; }), data), 'Bannery se spravují v sekci Reklama.'));
			}

			radky.push(el('div', { class: 'rs-pole' }, [el('span', { text: 'Vzhled' }), el('div', { class: 'rs-vzhledy' }, [[1, 'Běžný'], [2, 'Podbarvený'], [3, 'Zvýrazněný nadpis'], [4, 'V rámečku']].map(function (v) {
				return el('label', {}, [el('input', { type: 'radio', name: 'vzhled', value: v[0], checked: vzhled === v[0] }), el('span', { text: v[1] })]);
			}))]));

			radky.push(el('details', {}, [
				el('summary', { text: 'Kdy a kde blok zobrazit' }),
				pole('Stránky', vyber('zobrazit_kde', Object.keys(N.kde).map(function (k) { return [k, N.kde[k]]; }), b.zobrazit_kde)),
				pole('Jen v rubrice', vyber('jen_rubrika', [[0, 've všech']].concat(N.rubriky.map(function (r) { return [r.id, r.nazev]; })), b.jen_rubrika || 0)),
				pole('Zařízení', vyber('zarizeni', Object.keys(N.zarizeni).map(function (k) { return [k, N.zarizeni[k]]; }), b.zarizeni)),
				el('label', { class: 'rs-zaskrtnuti' }, [el('input', { type: 'checkbox', name: 'skryt', checked: !Number(b.zobrazit) }), ' Blok dočasně skrýt'])
			]));
			radky.push(el('div', { class: 'rs-tlacitka' }, [el('button', { type: 'submit', class: 'rs-hlavni', text: 'Uložit' }), el('button', { type: 'button', text: 'Zrušit', onclick: function () { d.close(); } })]));
			radky.forEach(function (r) { form.appendChild(r); });

			var d = okno('rs-panel', [
				el('div', { class: 'rs-okno-hlava' }, [el('strong', { text: nazevTypu }), el('button', { type: 'button', 'aria-label': 'Zavřít', text: '✕', onclick: function () { d.close(); } })]),
				form
			]);
			var editor = form.querySelector('textarea[data-editor]');
			if (editor && window.phprsVytvorEditor) { window.phprsVytvorEditor(editor); }

			form.addEventListener('submit', function (e) {
				e.preventDefault();
				var f = new FormData(form), odeslat = { idb: id, sys_funkce: typ, nazev: f.get('nazev'), zona: b.zona };
				odeslat.typ = f.get('ukazat_nadpis') ? (f.get('vzhled') || 1) : 5;
				odeslat.zobrazit_kde = f.get('zobrazit_kde'); odeslat.jen_rubrika = f.get('jen_rubrika'); odeslat.zarizeni = f.get('zarizeni');
				if (!f.get('skryt')) { odeslat.zobrazit = 1; }
				['obsah', 'blok_rubrika', 'blok_pocet', 'data_sys'].forEach(function (k) { if (f.get(k) !== null) { odeslat[k] = f.get(k); } });
				if (typ === 'men') {
					odeslat.obsah_menu = Array.prototype.map.call(form.querySelectorAll('.rs-odkaz'), function (r) {
						var v = r.querySelectorAll('input'); return v[0].value.trim() && v[1].value.trim() ? v[0].value.trim() + ' | ' + v[1].value.trim() : '';
					}).filter(Boolean).join('\n');
				}
				odesli('uloz_json', odeslat).then(function (r) { return r.json(); }).then(function (o) {
					if (o.ok) { znovu(); } else { hlaska(o.chyba || 'Uložení se nezdařilo'); }
				});
			});
		});
	}

	/* ---------- po znovunačtení: vrátit posun a případně otevřít nastavení nového bloku ---------- */

	var posunuti = sessionStorage.getItem('rs-posun');
	if (posunuti !== null) { sessionStorage.removeItem('rs-posun'); window.scrollTo(0, Number(posunuti)); }
	var otevrit = sessionStorage.getItem('rs-otevrit');
	if (otevrit) {
		sessionStorage.removeItem('rs-otevrit');
		var novy = document.querySelector('.rs-blok[data-blok="' + otevrit + '"]');
		if (novy) { novy.scrollIntoView({ block: 'center' }); novy.classList.add('rs-novy'); otevriNastaveni(otevrit); }
	}
})();
