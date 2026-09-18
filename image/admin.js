/* phpRS 3.0 - drobnosti administrace. Bez knihoven, bez build kroku. */

(function () {
	'use strict';

	// Prostředí 2026: rozbalení menu na mobilu
	var prepinac = document.querySelector('.menu-prepinac');
	if (prepinac) {
		prepinac.addEventListener('click', function () {
			var otevrene = document.body.classList.toggle('menu-otevrene');
			prepinac.setAttribute('aria-expanded', otevrene ? 'true' : 'false');
		});
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
