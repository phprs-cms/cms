/* phpRS 3 - skript webu pro čtenáře. Bez knihoven; vše je volitelné vylepšení, web funguje i bez JavaScriptu. */
(function () {
	'use strict';

	/* ---------- prohlížečka fotek: fotogalerie i jednotlivé obrázky v textu článku ---------- */

	var okno = null, fotky = [], pozice = 0;

	function ukaz(i) {
		pozice = (i + fotky.length) % fotky.length;
		var zdroj = fotky[pozice];
		okno.querySelector('img').src = zdroj.getAttribute('src');
		okno.querySelector('img').alt = zdroj.alt;
		var popis = zdroj.alt || ((zdroj.closest('figure') || document).querySelector('figcaption') || {}).textContent || '';
		okno.querySelector('p').textContent = (fotky.length > 1 ? (pozice + 1) + ' / ' + fotky.length + (popis ? ' · ' : '') : '') + popis;
	}

	function otevri(seznam, index) {
		if (!okno) {
			okno = document.createElement('dialog');
			okno.className = 'rs-prohlizecka';
			okno.innerHTML = '<img alt=""><p aria-live="polite"></p><button type="button" data-krok="-1" aria-label="Předchozí fotka">‹</button>'
				+ '<button type="button" data-krok="1" aria-label="Další fotka">›</button><button type="button" data-zavrit aria-label="Zavřít">×</button>';
			document.body.appendChild(okno);
			okno.addEventListener('click', function (e) {
				var krok = e.target.getAttribute('data-krok');
				if (krok) { ukaz(pozice + parseInt(krok, 10)); } else if (e.target.tagName !== 'IMG') { okno.close(); }
			});
			okno.addEventListener('keydown', function (e) {
				if (e.key === 'ArrowLeft') { ukaz(pozice - 1); }
				if (e.key === 'ArrowRight') { ukaz(pozice + 1); }
			});
			var start = null;
			okno.addEventListener('touchstart', function (e) { start = e.changedTouches[0].clientX; }, { passive: true });
			okno.addEventListener('touchend', function (e) {
				var posun = e.changedTouches[0].clientX - start;
				if (Math.abs(posun) > 50 && fotky.length > 1) { ukaz(pozice + (posun < 0 ? 1 : -1)); }
			}, { passive: true });
		}
		fotky = seznam;
		okno.querySelectorAll('[data-krok]').forEach(function (b) { b.hidden = fotky.length < 2; });
		ukaz(index);
		okno.showModal();
	}

	document.addEventListener('click', function (e) {
		var img = e.target;
		if (img.tagName !== 'IMG' || img.closest('a') || !img.closest('.clanek-text, .perex, figure.galerie')) { return; }
		var galerie = img.closest('figure.galerie');
		var seznam = Array.prototype.slice.call((galerie || img.closest('.clanek-text, .perex')).querySelectorAll(galerie ? 'img' : 'figure:not(.galerie) img'));
		if (seznam.indexOf(img) === -1) { seznam = [img]; }
		otevri(seznam, seznam.indexOf(img));
	});

	/* ---------- sdílení článku: systémové sdílení (telefon) a kopírování odkazu ---------- */

	document.querySelectorAll('[data-sdilet]').forEach(function (tl) {
		if (!navigator.share) { return; }
		tl.hidden = false;
		tl.addEventListener('click', function () {
			navigator.share({ title: tl.getAttribute('data-titulek'), url: tl.getAttribute('data-adresa') }).catch(function () { /* čtenář sdílení zavřel */ });
		});
	});
	document.addEventListener('click', function (e) {
		var tl = e.target.closest && e.target.closest('[data-kopirovat]');
		if (!tl || !navigator.clipboard) { return; }
		var puvodni = tl.textContent;
		navigator.clipboard.writeText(tl.getAttribute('data-kopirovat')).then(function () {
			tl.textContent = tl.getAttribute('data-hotovo');
			setTimeout(function () { tl.textContent = puvodni; }, 2000);
		});
	});

	/* ---------- přehrávač cizí služby se vloží až po kliknutí ---------- */

	document.addEventListener('click', function (e) {
		var tl = e.target.closest && e.target.closest('[data-vlozit]');
		if (!tl) { return; }
		var ram = document.createElement('iframe');
		ram.src = tl.getAttribute('data-vlozit');
		ram.title = tl.getAttribute('data-titulek') || '';
		ram.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
		ram.allowFullscreen = true;
		ram.loading = 'lazy';
		tl.replaceWith(ram);
	});

	/* ---------- živá reportáž: nové zápisy se načítají samy ---------- */

	var zive = document.querySelector('[data-zive]');
	if (zive) {
		var nacti = function () {
			if (document.hidden) { return; }
			var prvni = zive.querySelector('[data-zapis]');
			fetch(zive.getAttribute('data-zive') + '?od=' + (prvni ? prvni.getAttribute('data-zapis') : 0), { cache: 'no-store' })
				.then(function (r) { return r.json(); })
				.then(function (j) {
					if (j.html) { zive.querySelector('.rs-zive-zapisy').insertAdjacentHTML('afterbegin', j.html); }
					if (!j.bezi) { clearInterval(casovac); }
				}).catch(function () { /* další pokus za chvíli */ });
		};
		var casovac = setInterval(nacti, 30000);
		document.addEventListener('visibilitychange', nacti);
	}

	/* ---------- oznámení o nových článcích (Web Push) ---------- */

	var meta = document.querySelector('meta[name="rs-push"]');
	var bloky = document.querySelectorAll('[data-push]');
	if (meta && bloky.length && 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window) {
		var koren = meta.getAttribute('data-koren');
		var klic = meta.getAttribute('content');
		var naBajty = function (b64) {
			var t = atob((b64 + '===='.slice((b64.length + 3) % 4 + 1)).replace(/-/g, '+').replace(/_/g, '/'));
			return Uint8Array.from(t, function (z) { return z.charCodeAt(0); });
		};
		var posli = function (cesta, odber) {
			return fetch(koren + cesta, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(odber) });
		};
		var prekresli = function (odber, zprava) {
			bloky.forEach(function (b) {
				b.hidden = false;
				b.querySelector('[data-push-tl]').textContent = odber ? 'Vypnout oznámení' : 'Zapnout oznámení';
				b.querySelector('[data-push-tl]').classList.toggle('rs-tl-vedlejsi', !!odber);
				b.querySelector('[data-push-stav]').textContent = zprava || (odber ? 'Oznámení jsou v tomto prohlížeči zapnutá.' : '');
			});
		};
		navigator.serviceWorker.register(koren + 'sw.js', { scope: koren }).then(function (reg) {
			reg.pushManager.getSubscription().then(function (odber) { prekresli(odber); });
			bloky.forEach(function (b) {
				b.querySelector('[data-push-tl]').addEventListener('click', function () {
					reg.pushManager.getSubscription().then(function (odber) {
						if (odber) {
							return posli('push/zrusit', odber).then(function () { return odber.unsubscribe(); }).then(function () { prekresli(null, 'Oznámení jsou vypnutá.'); });
						}
						return reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: naBajty(klic) }).then(function (novy) {
							return posli('push/odber', novy).then(function (r) {
								if (!r.ok) { return novy.unsubscribe().then(function () { prekresli(null, 'Oznámení se nepodařilo zapnout. Zkuste to později.'); }); }
								prekresli(novy);
							});
						});
					}).catch(function () {
						prekresli(null, Notification.permission === 'denied' ? 'Oznámení máte pro tento web v prohlížeči zakázaná. Povolíte je v nastavení webu u adresního řádku.' : 'Oznámení se nepodařilo zapnout.');
					});
				});
			});
		}).catch(function () { /* bez service workeru zůstane blok skrytý */ });
	}
})();
