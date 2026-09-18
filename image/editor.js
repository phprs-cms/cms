/* phpRS 3.0 - editor článků a práce s obrázky. Bez knihoven, bez build kroku.
 *
 *   <textarea data-editor>            WYSIWYG editor (data-editor="maly" = zkrácená lišta)
 *   <input data-obrazek>              pole s adresou obrázku + tlačítko "Vybrat z galerie" a náhled
 *   <form data-nahravani>             nahrávání přetažením souborů
 *
 * Do formuláře se vždy odesílá obsah původní <textarea> - bez JavaScriptu zůstane obyčejným polem pro HTML.
 */

(function () {
	'use strict';

	var ADMIN = document.querySelector('script[data-admin-url]').getAttribute('data-admin-url');
	var CSRF = (document.querySelector('input[name="_csrf"]') || {}).value || '';
	var GALERIE = ADMIN + '?modul=intergal';
	var ID_CLANKU = parseInt((document.querySelector('form[data-koncept] input[name="idc"]') || {}).value || '0', 10);

	/* ---------- čištění HTML (vkládání z Wordu a webu) ---------- */

	var POVOLENE = { P: [], H2: [], H3: [], H4: [], STRONG: [], EM: [], B: [], I: [], U: [], S: [], SUB: [], SUP: [], BR: [], HR: [],
		A: ['href', 'title', 'target', 'rel'], UL: [], OL: [], LI: [], BLOCKQUOTE: [], CODE: [], PRE: [],
		FIGURE: ['class'], FIGCAPTION: [], IMG: ['src', 'alt', 'width', 'height', 'loading', 'data-id'],
		TABLE: [], THEAD: [], TBODY: [], TR: [], TH: ['colspan', 'rowspan'], TD: ['colspan', 'rowspan'], IFRAME: ['src', 'width', 'height', 'allowfullscreen', 'title'] };
	var PREVOD = { DIV: 'P', H1: 'H2', H5: 'H4', H6: 'H4' };

	function vycisti(uzel) {
		Array.prototype.slice.call(uzel.childNodes).forEach(function (n) {
			if (n.nodeType === 8) { n.remove(); return; }
			if (n.nodeType !== 1) { return; }
			var tag = n.tagName;
			if (/^(SCRIPT|STYLE|META|LINK|TITLE|HEAD|O:P|XML)$/.test(tag)) { n.remove(); return; }
			vycisti(n);
			if (PREVOD[tag]) {
				var novy = document.createElement(PREVOD[tag]);
				while (n.firstChild) { novy.appendChild(n.firstChild); }
				n.replaceWith(novy);
				return;
			}
			if (!POVOLENE[tag]) {
				while (n.firstChild) { n.parentNode.insertBefore(n.firstChild, n); }
				n.remove();
				return;
			}
			Array.prototype.slice.call(n.attributes).forEach(function (a) {
				if (POVOLENE[tag].indexOf(a.name) === -1 || /^\s*javascript:/i.test(a.value)) { n.removeAttribute(a.name); }
			});
		});
	}

	function cisteHtml(html) {
		var box = document.createElement('div');
		box.innerHTML = html;
		vycisti(box);
		return box.innerHTML.replace(/<p>(\s|&nbsp;|<br>)*<\/p>/g, '').replace(/&nbsp;/g, ' ').trim();
	}

	/* ---------- nahrávání ---------- */

	function nahraj(soubory) {
		var data = new FormData();
		var slozka = document.querySelector('.galerie-okno[open] select');
		data.append('_csrf', CSRF);
		data.append('sekce', slozka && /^\d+$/.test(slozka.value) ? slozka.value : '0');
		Array.prototype.forEach.call(soubory, function (s) { data.append('soubory[]', s); });
		return fetch(GALERIE + '&akce=nahraj&format=json', { method: 'POST', body: data, credentials: 'same-origin' })
			.then(function (r) { return r.json(); })
			.then(function (j) {
				if (j.chyby && j.chyby.length) { window.alert(j.chyby.join('\n')); }
				return j.obrazky || [];
			})
			.catch(function () { window.alert('Nahrání se nezdařilo. Zkontrolujte připojení a zkuste to znovu.'); return []; });
	}

	function jsouObrazky(prenos) {
		return prenos && prenos.files && prenos.files.length && Array.prototype.every.call(prenos.files, function (f) { return /^image\//.test(f.type); });
	}

	/* ---------- okno galerie ---------- */

	var okno = null;

	function vyberObrazek(zpetne) {
		if (!okno) {
			okno = document.createElement('dialog');
			okno.className = 'galerie-okno';
			okno.innerHTML = '<div class="galerie-okno-hlava"><strong>Média</strong>'
				+ '<label class="tl">Nahrát nový<input type="file" accept="image/*" multiple hidden></label>'
				+ '<button type="button" class="navigace" data-zavri>Zavřít</button></div>'
				+ '<div class="galerie-okno-filtr"><select aria-label="Složka"></select></div>'
				+ '<p class="napoveda">Klepnutím obrázek vložíte. Soubory sem můžete i přetáhnout - nahrají se do zvolené složky.</p><div class="galerie-mrizka"></div>';
			document.body.appendChild(okno);
			okno.querySelector('[data-zavri]').addEventListener('click', function () { okno.close(); });
			okno.querySelector('select').addEventListener('change', function () { nacti(this.value); });
			okno.querySelector('input[type=file]').addEventListener('change', function () {
				nahraj(this.files).then(function (nove) { nove.reverse().forEach(function (o) { pridej(o, true); }); });
				this.value = '';
			});
			okno.addEventListener('dragover', function (e) { e.preventDefault(); });
			okno.addEventListener('drop', function (e) {
				e.preventDefault();
				if (jsouObrazky(e.dataTransfer)) { nahraj(e.dataTransfer.files).then(function (nove) { nove.reverse().forEach(function (o) { pridej(o, true); }); }); }
			});
		}
		var mrizka = okno.querySelector('.galerie-mrizka');
		function pridej(o, nahoru) {
			var b = document.createElement('button');
			b.type = 'button';
			b.className = 'galerie-polozka';
			b.innerHTML = '<img loading="lazy" alt=""><span></span>';
			b.firstChild.src = o.nahled;
			b.lastChild.textContent = o.nazev || 'bez názvu';
			b.addEventListener('click', function () { okno.close(); okno.zpetne(o); });
			if (nahoru) { mrizka.prepend(b); } else { mrizka.appendChild(b); }
		}
		// filtr: "" = vše, "clanek" = obrázky tohoto článku, číslo = složka (0 = nezařazené)
		function nacti(filtr) {
			var dotaz = filtr === 'clanek' ? '&clanek=' + ID_CLANKU : (filtr !== '' ? '&sekce=' + filtr : '');
			mrizka.textContent = 'Načítám…';
			fetch(GALERIE + '&akce=seznam' + dotaz, { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (j) {
				var vyber = okno.querySelector('select');
				vyber.textContent = '';
				[['', 'Všechna média']].concat(ID_CLANKU ? [['clanek', 'V tomto článku']] : [], [['0', 'Nezařazené']], j.slozky.map(function (s) { return [String(s.id), 'Složka: ' + s.nazev]; })).forEach(function (v) {
					var o = document.createElement('option');
					o.value = v[0]; o.textContent = v[1]; o.selected = v[0] === filtr;
					vyber.appendChild(o);
				});
				mrizka.textContent = j.obrazky.length ? '' : 'Tady zatím žádné obrázky nejsou.';
				j.obrazky.forEach(function (o) { pridej(o, false); });
			});
		}
		okno.zpetne = zpetne;
		okno.showModal();
		nacti(okno.querySelector('select').value || '');
	}

	function htmlObrazku(o) {
		var e = function (t) { return String(t).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;'); };
		return '<figure><img src="' + e(o.url) + '" alt="' + e(o.nazev) + '" width="' + o.sirka + '" height="' + o.vyska + '" loading="lazy" data-id="' + o.id + '">'
			+ (o.popis ? '<figcaption>' + e(o.popis) + '</figcaption>' : '') + '</figure><p><br></p>';
	}

	/* ---------- editor ---------- */

	var TLACITKA = [
		['¶', 'Odstavec', function () { prikaz('formatBlock', 'P'); }],
		['H2', 'Mezititulek', function () { prikaz('formatBlock', 'H2'); }, 'velky'],
		['H3', 'Menší mezititulek', function () { prikaz('formatBlock', 'H3'); }, 'velky'],
		['B', 'Tučně (Ctrl+B)', function () { prikaz('bold'); }],
		['I', 'Kurzíva (Ctrl+I)', function () { prikaz('italic'); }],
		['odkaz', 'Vložit odkaz (Ctrl+K)', odkaz],
		['• seznam', 'Odrážkový seznam', function () { prikaz('insertUnorderedList'); }],
		['1. seznam', 'Číslovaný seznam', function () { prikaz('insertOrderedList'); }, 'velky'],
		['„citace“', 'Citace', function () { prikaz('formatBlock', 'BLOCKQUOTE'); }, 'velky'],
		['obrázek', 'Vložit obrázek z galerie', null, 'velky'],
		['—', 'Oddělovací čára', function () { prikaz('insertHorizontalRule'); }, 'velky'],
		['Tx', 'Odstranit formátování', function () { prikaz('removeFormat'); prikaz('unlink'); }]
	];

	function prikaz(nazev, hodnota) { document.execCommand(nazev, false, hodnota || null); }

	function odkaz() {
		var vyber = window.getSelection();
		var kotva = vyber.anchorNode && vyber.anchorNode.parentElement && vyber.anchorNode.parentElement.closest('a');
		var url = window.prompt('Adresa odkazu (prázdné = odkaz zrušit):', kotva ? kotva.getAttribute('href') : 'https://');
		if (url === null) { return; }
		if (url === '') { prikaz('unlink'); return; }
		if (vyber.isCollapsed && !kotva) { prikaz('insertHTML', '<a href="' + url.replace(/"/g, '&quot;') + '">' + url.replace(/</g, '&lt;') + '</a>'); } else { prikaz('createLink', url); }
	}

	function vytvorEditor(pole) {
		var maly = pole.getAttribute('data-editor') === 'maly';
		var obal = document.createElement('div');
		obal.className = 'editor' + (maly ? ' editor-maly' : '');
		var lista = document.createElement('div');
		lista.className = 'editor-nastroje';
		lista.setAttribute('role', 'toolbar');
		var plocha = document.createElement('div');
		plocha.className = 'editor-plocha';
		plocha.contentEditable = 'true';
		plocha.setAttribute('role', 'textbox');
		plocha.setAttribute('aria-multiline', 'true');
		plocha.setAttribute('aria-label', (pole.labels && pole.labels[0] ? pole.labels[0].textContent : 'Text'));
		var stav = document.createElement('div');
		stav.className = 'editor-stav';
		var zdroj = false;

		function doPole() { if (!zdroj) { pole.value = cisteHtml(plocha.innerHTML); } pocitej(); pole.dispatchEvent(new Event('input', { bubbles: true })); }
		function zPole() { plocha.innerHTML = pole.value.trim() || '<p><br></p>'; }
		function pocitej() {
			var slov = (plocha.innerText.trim().match(/\S+/g) || []).length;
			stav.firstChild.textContent = slov + ' slov' + (maly ? '' : ' · čtení asi ' + Math.max(1, Math.round(slov / 200)) + ' min');
		}
		function vlozObrazek() {
			var rozsah = window.getSelection().rangeCount ? window.getSelection().getRangeAt(0).cloneRange() : null;
			vyberObrazek(function (o) {
				plocha.focus();
				if (rozsah && plocha.contains(rozsah.startContainer)) { window.getSelection().removeAllRanges(); window.getSelection().addRange(rozsah); }
				prikaz('insertHTML', htmlObrazku(o));
				doPole();
			});
		}

		TLACITKA.forEach(function (t) {
			if (maly && t[3] === 'velky') { return; }
			var b = document.createElement('button');
			b.type = 'button';
			b.textContent = t[0];
			b.title = t[1];
			if (t[0] === 'B') { b.style.fontWeight = 'bold'; }
			if (t[0] === 'I') { b.style.fontStyle = 'italic'; }
			b.addEventListener('mousedown', function (e) { e.preventDefault(); });
			b.addEventListener('click', function () { if (zdroj) { return; } plocha.focus(); (t[2] || vlozObrazek)(); doPole(); });
			lista.appendChild(b);
		});
		var html = document.createElement('button');
		html.type = 'button';
		html.textContent = 'HTML';
		html.title = 'Přepnout na zdrojový kód';
		html.className = 'editor-html';
		html.setAttribute('aria-pressed', 'false');
		html.addEventListener('click', function () {
			zdroj = !zdroj;
			if (zdroj) { pole.value = cisteHtml(plocha.innerHTML).replace(/<\/(p|h2|h3|h4|ul|ol|li|blockquote|figure)>/g, '</$1>\n'); } else { zPole(); }
			obal.classList.toggle('editor-zdroj', zdroj);
			html.setAttribute('aria-pressed', zdroj ? 'true' : 'false');
			(zdroj ? pole : plocha).focus();
		});
		lista.appendChild(html);
		stav.appendChild(document.createElement('span'));
		stav.appendChild(document.createElement('span'));

		pole.parentNode.insertBefore(obal, pole);
		obal.appendChild(lista);
		obal.appendChild(plocha);
		obal.appendChild(pole);
		obal.appendChild(stav);
		pole.classList.add('editor-pole');
		zPole();
		prikaz('defaultParagraphSeparator', 'p');

		plocha.addEventListener('input', doPole);
		plocha.addEventListener('blur', doPole);
		plocha.addEventListener('keydown', function (e) {
			if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); odkaz(); doPole(); }
		});
		plocha.addEventListener('paste', function (e) {
			var prenos = e.clipboardData;
			if (jsouObrazky(prenos)) {
				e.preventDefault();
				nahraj(prenos.files).then(function (nove) { nove.forEach(function (o) { prikaz('insertHTML', htmlObrazku(o)); }); doPole(); });
				return;
			}
			var vlozene = prenos.getData('text/html');
			if (vlozene) { e.preventDefault(); prikaz('insertHTML', cisteHtml(vlozene)); doPole(); }
		});
		plocha.addEventListener('dragover', function (e) { if (jsouObrazky(e.dataTransfer) || (e.dataTransfer.types || []).indexOf('Files') !== -1) { e.preventDefault(); obal.classList.add('editor-pretazeni'); } });
		plocha.addEventListener('dragleave', function () { obal.classList.remove('editor-pretazeni'); });
		plocha.addEventListener('drop', function (e) {
			obal.classList.remove('editor-pretazeni');
			if (!jsouObrazky(e.dataTransfer)) { return; }
			e.preventDefault();
			nahraj(e.dataTransfer.files).then(function (nove) { plocha.focus(); nove.forEach(function (o) { prikaz('insertHTML', htmlObrazku(o)); }); doPole(); });
		});
		if (pole.form) { pole.form.addEventListener('submit', function () { if (!zdroj) { pole.value = cisteHtml(plocha.innerHTML); } }); }
		pocitej();
		return { obnov: zPole, stav: stav.lastChild };
	}

	/* ---------- automatické ukládání rozepsaného článku do prohlížeče ---------- */

	function autoUkladani(form, editory) {
		var klic = 'phprs3-koncept:' + form.getAttribute('data-koncept');
		var pole = Array.prototype.filter.call(form.elements, function (p) { return p.name && p.name !== '_csrf' && p.type !== 'password' && p.type !== 'file' && p.type !== 'submit'; });
		var casovac = null;

		function uloz() {
			var data = { cas: Date.now(), pole: {} };
			pole.forEach(function (p) { if (p.type === 'checkbox' || p.type === 'radio') { if (p.checked) { data.pole[p.name] = p.value; } else if (p.type === 'checkbox') { data.pole[p.name] = null; } } else { data.pole[p.name] = p.value; } });
			try { localStorage.setItem(klic, JSON.stringify(data)); } catch (e) { return; }
			editory.forEach(function (ed) { ed.stav.textContent = 'rozepsaný text uložen v prohlížeči ' + new Date().toLocaleTimeString('cs-CZ', { hour: '2-digit', minute: '2-digit' }); });
		}
		form.addEventListener('input', function () { clearTimeout(casovac); casovac = setTimeout(uloz, 1500); });
		form.addEventListener('submit', function () { clearTimeout(casovac); try { localStorage.removeItem(klic); } catch (e) { /* nic */ } });

		var ulozene = null;
		try { ulozene = JSON.parse(localStorage.getItem(klic) || 'null'); } catch (e) { /* nic */ }
		if (!ulozene || !ulozene.pole || Date.now() - ulozene.cas > 14 * 86400000) { return; }
		var lisiSe = pole.some(function (p) { return (p.tagName === 'TEXTAREA' || p.type === 'text') && ulozene.pole[p.name] !== undefined && ulozene.pole[p.name] !== p.value; });
		if (!lisiSe) { return; }
		var lista = document.createElement('p');
		lista.className = 'hlaska';
		lista.innerHTML = 'V prohlížeči je neuložená rozepsaná verze z ' + new Date(ulozene.cas).toLocaleString('cs-CZ') + '. <button type="button" class="navigace">Obnovit ji</button> <button type="button" class="navigace">Zahodit</button>';
		form.parentNode.insertBefore(lista, form);
		lista.children[0].addEventListener('click', function () {
			pole.forEach(function (p) {
				if (!(p.name in ulozene.pole)) { return; }
				if (p.type === 'checkbox') { p.checked = ulozene.pole[p.name] !== null; } else if (p.type === 'radio') { p.checked = p.value === ulozene.pole[p.name]; } else { p.value = ulozene.pole[p.name]; }
			});
			editory.forEach(function (ed) { ed.obnov(); });
			document.querySelectorAll('[data-obrazek]').forEach(function (p) { p.dispatchEvent(new Event('change')); });
			lista.remove();
		});
		lista.children[1].addEventListener('click', function () { try { localStorage.removeItem(klic); } catch (e) { /* nic */ } lista.remove(); });
	}

	/* ---------- pole "Hlavní obrázek" ---------- */

	document.querySelectorAll('[data-obrazek]').forEach(function (pole) {
		var tl = document.createElement('button');
		tl.type = 'button';
		tl.className = 'navigace';
		tl.textContent = 'Vybrat z médií';
		var nahled = document.createElement('img');
		nahled.className = 'obrazek-nahled';
		nahled.alt = '';
		function ukaz() { nahled.hidden = pole.value.trim() === ''; if (!nahled.hidden) { nahled.src = pole.value; } }
		pole.after(tl, nahled);
		tl.addEventListener('click', function () { vyberObrazek(function (o) { pole.value = o.url; ukaz(); pole.dispatchEvent(new Event('input', { bubbles: true })); }); });
		pole.addEventListener('change', ukaz);
		nahled.addEventListener('error', function () { nahled.hidden = true; });
		ukaz();
	});

	/* ---------- nahrávání přetažením na stránce galerie ---------- */

	document.querySelectorAll('[data-nahravani]').forEach(function (form) {
		var vstup = form.querySelector('input[type=file]');
		form.addEventListener('dragover', function (e) { e.preventDefault(); form.classList.add('nahravani-aktivni'); });
		form.addEventListener('dragleave', function () { form.classList.remove('nahravani-aktivni'); });
		form.addEventListener('drop', function (e) {
			e.preventDefault();
			form.classList.remove('nahravani-aktivni');
			if (e.dataTransfer.files.length) { vstup.files = e.dataTransfer.files; form.submit(); }
		});
	});

	window.phprsVytvorEditor = vytvorEditor; // vizuální editor bloků si editor vytváří sám nad dynamickým polem

	var editory = Array.prototype.map.call(document.querySelectorAll('textarea[data-editor]'), vytvorEditor);
	var formKoncept = document.querySelector('form[data-koncept]');
	if (formKoncept) { autoUkladani(formKoncept, editory); }
})();
