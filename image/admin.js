/* phpRS 3.0 - drobnosti administrace. Bez knihoven, bez build kroku. */

(function () {
	'use strict';

	// Potvrzení nevratných akcí: data-potvrdit="text" na formuláři nebo tlačítku.
	// Vlastní dialog místo window.confirm(), který vestavěné prohlížeče (např. v aplikacích) potichu potlačují.
	var dialogPotvrzeni = null;
	document.addEventListener('submit', function (e) {
		var form = e.target, tlacitko = e.submitter;
		var text = (tlacitko && tlacitko.getAttribute('data-potvrdit')) || form.getAttribute('data-potvrdit');
		if (!text || form.potvrzeno) { return; }
		e.preventDefault();
		if (!dialogPotvrzeni) {
			dialogPotvrzeni = document.createElement('dialog');
			dialogPotvrzeni.className = 'potvrzeni';
			dialogPotvrzeni.innerHTML = '<p></p><div><button type="button" class="tl" data-ano>Ano, provést</button> <button type="button" class="navigace" data-ne>Zrušit</button></div>';
			document.body.appendChild(dialogPotvrzeni);
			dialogPotvrzeni.querySelector('[data-ne]').addEventListener('click', function () { dialogPotvrzeni.close(); });
		}
		dialogPotvrzeni.querySelector('p').textContent = text;
		dialogPotvrzeni.querySelector('[data-ano]').onclick = function () {
			dialogPotvrzeni.close();
			form.potvrzeno = true;
			if (form.requestSubmit) { form.requestSubmit(tlacitko || undefined); } else { form.submit(); }
			form.potvrzeno = false;
		};
		dialogPotvrzeni.showModal();
		dialogPotvrzeni.querySelector('[data-ne]').focus();
	});

	// Prostředí 2026: světlý / tmavý režim (výchozí podle systému, volba se pamatuje v prohlížeči)
	var temaTl = document.querySelector('[data-tema-prepinac]');
	if (temaTl) {
		temaTl.addEventListener('click', function () {
			var koren = document.documentElement;
			var tmavy = koren.getAttribute('data-tema') ? koren.getAttribute('data-tema') === 'tmavy' : window.matchMedia('(prefers-color-scheme: dark)').matches;
			koren.setAttribute('data-tema', tmavy ? 'svetly' : 'tmavy');
			try { localStorage.setItem('phprs3-tema', tmavy ? 'svetly' : 'tmavy'); } catch (e) { /* nic */ }
		});
	}

	// Prostředí 2026: rozbalení menu na mobilu
	var prepinac = document.querySelector('.menu-prepinac');
	if (prepinac) {
		prepinac.addEventListener('click', function () {
			var otevrene = document.body.classList.toggle('menu-otevrene');
			prepinac.setAttribute('aria-expanded', otevrene ? 'true' : 'false');
		});
	}

	// Úprava bloků: přetahování bloků mezi zónami a šipky nahoru/dolů; pořadí se ukládá samo
	var platno = document.querySelector('[data-platno]');
	if (platno) {
		var tazeny = null;
		var stavPoradi = document.querySelector('[data-stav-poradi]');
		var ulozPoradi = function () {
			var poradi = {};
			platno.querySelectorAll('[data-zona]').forEach(function (z) {
				poradi[z.getAttribute('data-zona')] = Array.prototype.map.call(z.querySelectorAll('[data-idb]'), function (k) { return k.getAttribute('data-idb'); });
			});
			var data = new FormData();
			data.append('_csrf', document.querySelector('input[name="_csrf"]').value);
			data.append('poradi', JSON.stringify(poradi));
			stavPoradi.textContent = 'Ukládám…';
			fetch(platno.getAttribute('data-url'), { method: 'POST', body: data, credentials: 'same-origin' })
				.then(function (r) { return r.json(); })
				.then(function (j) { stavPoradi.textContent = j.ok ? 'Pořadí uloženo.' : 'Pořadí se nepodařilo uložit.'; })
				.catch(function () { stavPoradi.textContent = 'Pořadí se nepodařilo uložit.'; });
		};
		// karta, před kterou se má tažený blok vložit (podle polohy kurzoru), nebo null = na konec
		var kartaZa = function (seznam, x, y) {
			var karty = Array.prototype.filter.call(seznam.querySelectorAll('[data-idb]'), function (k) { return k !== tazeny; });
			for (var i = 0; i < karty.length; i++) {
				var r = karty[i].getBoundingClientRect();
				if (y < r.top + r.height / 2 && (y < r.top || x < r.right) || (y >= r.top && y <= r.bottom && x < r.left + r.width / 2)) { return karty[i]; }
			}
			return null;
		};
		platno.addEventListener('dragstart', function (e) {
			tazeny = e.target.closest('[data-idb]');
			if (!tazeny) { return; }
			e.dataTransfer.effectAllowed = 'move';
			e.dataTransfer.setData('text/plain', tazeny.getAttribute('data-idb'));
			setTimeout(function () { tazeny.classList.add('blok-tazeny'); }, 0);
		});
		platno.addEventListener('dragover', function (e) {
			var zona = e.target.closest('[data-zona]');
			if (!tazeny || !zona) { return; }
			e.preventDefault();
			platno.querySelectorAll('.zona-cil').forEach(function (z) { if (z !== zona) { z.classList.remove('zona-cil'); } });
			zona.classList.add('zona-cil');
			var seznam = zona.querySelector('.zona-bloky');
			seznam.insertBefore(tazeny, kartaZa(seznam, e.clientX, e.clientY));
		});
		platno.addEventListener('drop', function (e) { if (tazeny) { e.preventDefault(); } });
		platno.addEventListener('dragend', function () {
			if (!tazeny) { return; }
			tazeny.classList.remove('blok-tazeny');
			platno.querySelectorAll('.zona-cil').forEach(function (z) { z.classList.remove('zona-cil'); });
			tazeny = null;
			ulozPoradi();
		});
		platno.addEventListener('click', function (e) {
			var tl = e.target.closest('[data-posun]');
			if (!tl) { return; }
			var karta = tl.closest('[data-idb]');
			var dolu = tl.getAttribute('data-posun') === '1';
			var soused = dolu ? karta.nextElementSibling : karta.previousElementSibling;
			if (soused) {
				karta.parentNode.insertBefore(karta, dolu ? soused.nextElementSibling : soused);
			} else {
				// na kraji zóny blok přeskočí do sousední zóny
				var zony = Array.prototype.slice.call(platno.querySelectorAll('[data-zona]'));
				var cil = zony[zony.indexOf(karta.closest('[data-zona]')) + (dolu ? 1 : -1)];
				if (!cil) { return; }
				var seznam = cil.querySelector('.zona-bloky');
				seznam.insertBefore(karta, dolu ? seznam.firstChild : null);
			}
			tl.focus();
			ulozPoradi();
		});
	}

	// Editor článku: každou minutu dá serveru vědět, že je článek stále otevřený (zámek proti souběžné úpravě)
	var formClanku = document.querySelector('form.formular-clanek');
	if (formClanku && formClanku.idc && formClanku.idc.value !== '0') {
		setInterval(function () {
			var data = new FormData();
			data.append('_csrf', formClanku._csrf.value);
			data.append('idc', formClanku.idc.value);
			fetch(formClanku.action.replace('akce=uloz', 'akce=zamek'), { method: 'POST', body: data, credentials: 'same-origin' });
		}, 60000);
	}

	// Obecné: formulář s data-prepinac="pole" ukazuje jen řádky, jejichž data-pro obsahuje zvolenou hodnotu pole
	document.querySelectorAll('form[data-prepinac]').forEach(function (form) {
		var jmeno = form.getAttribute('data-prepinac');
		var prepni = function () {
			var zvolene = form.querySelector('[name="' + jmeno + '"]:checked') || form.querySelector('select[name="' + jmeno + '"]');
			form.querySelectorAll('[data-pro]').forEach(function (radek) { radek.hidden = !zvolene || radek.getAttribute('data-pro').split(' ').indexOf(zvolene.value) === -1; });
		};
		form.addEventListener('change', function (e) { if (e.target.name === jmeno) { prepni(); } });
		prepni();
	});

	// Formulář bloku: ukazuje jen pole, která zvolený typ bloku používá (data-pro="zkratka zkratka")
	var formBloku = document.querySelector('[data-blok-formular]');
	if (formBloku) {
		var ukazPole = function () {
			var typ = formBloku.sys_funkce.value;
			formBloku.querySelectorAll('[data-pro]').forEach(function (radek) {
				radek.hidden = radek.getAttribute('data-pro').split(' ').indexOf(typ) === -1;
			});
		};
		formBloku.sys_funkce.addEventListener('change', function () {
			if (formBloku.nazev.value === '' && formBloku.sys_funkce.value !== '') { formBloku.nazev.value = formBloku.sys_funkce.selectedOptions[0].textContent.split(' – ')[0]; }
			ukazPole();
		});
		ukazPole();
	}

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
