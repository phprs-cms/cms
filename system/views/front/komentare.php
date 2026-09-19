<?php
/**
 * Komentáře pod článkem: výpis (reakce o úroveň níž) a formulář.
 *
 * @var array<string, mixed> $clanek
 * @var list<array<string, mixed>> $koreny
 * @var array<int, list<array<string, mixed>>> $reakce
 * @var int $pocet
 * @var string $akce   adresa pro odeslání
 * @var string $pole   skrytá pole antispamu
 * @var string $zprava
 * @var string $chyba
 */
$komentar = function (array $k) use (&$komentar, $reakce): void { ?>
	<li class="komentar" id="komentar-<?= (int) $k['idk'] ?>">
		<p class="komentar-hlava"><strong><?= e($k['od']) ?></strong> <time datetime="<?= e(date('c', strtotime($k['datum']))) ?>"><?= e(datum($k['datum'], true)) ?></time>
<?php if ($k['reakce_na'] === null): ?>
			<button type="button" class="komentar-reagovat" data-reagovat="<?= (int) $k['idk'] ?>" data-jmeno="<?= e($k['od']) ?>"><?= e(t('Reagovat')) ?></button>
<?php endif ?>
		</p>
		<div class="komentar-text"><?= nl2br(e($k['obsah'])) ?></div>
<?php if (!empty($reakce[(int) $k['idk']])): ?>
		<ul class="komentare-reakce"><?php foreach ($reakce[(int) $k['idk']] as $r) { $komentar($r); } ?></ul>
<?php endif ?>
	</li>
<?php };
?>
<section class="komentare obal-uzky" id="komentare">
	<h2><?= e(t('Komentáře')) ?><?= $pocet > 0 ? ' (' . $pocet . ')' : '' ?></h2>
<?php if ($zprava !== ''): ?>
	<p class="komentare-zprava" role="status"><?= e($zprava) ?></p>
<?php endif ?>
<?php if ($chyba !== ''): ?>
	<p class="komentare-zprava komentare-chyba" role="alert"><?= e($chyba) ?></p>
<?php endif ?>
<?php if ($koreny !== []): ?>
	<ul class="komentare-seznam"><?php foreach ($koreny as $k) { $komentar($k); } ?></ul>
<?php else: ?>
	<p><?= e(t('Zatím tu žádný komentář není. Buďte první.')) ?></p>
<?php endif ?>
	<form class="komentar-formular" method="post" action="<?= e($akce) ?>">
		<?= $pole ?>
		<input type="hidden" name="idc" value="<?= (int) $clanek['idc'] ?>">
		<input type="hidden" name="reakce_na" value="0">
		<p class="komentar-reakce-info" hidden><?= e(t('Reagujete na komentář:')) ?> <strong></strong> <button type="button" data-zrusit-reakci><?= e(t('zrušit')) ?></button></p>
		<p><label for="kom-od"><?= e(t('Jméno')) ?></label><input type="text" id="kom-od" name="od" maxlength="60" required autocomplete="name"></p>
		<p><label for="kom-mail"><?= e(t('E-mail')) ?> <small><?= e(t('(nepovinný, nezveřejní se)')) ?></small></label><input type="email" id="kom-mail" name="od_mail" maxlength="190" autocomplete="email"></p>
		<p><label for="kom-obsah"><?= e(t('Komentář')) ?></label><textarea id="kom-obsah" name="obsah" rows="5" maxlength="5000" required></textarea></p>
		<p><button type="submit"><?= e(t('Odeslat komentář')) ?></button></p>
	</form>
</section>
<script>
document.querySelectorAll('[data-reagovat]').forEach(function (b) {
	b.addEventListener('click', function () {
		var f = document.querySelector('.komentar-formular'), info = f.querySelector('.komentar-reakce-info');
		f.reakce_na.value = b.getAttribute('data-reagovat'); info.hidden = false; info.querySelector('strong').textContent = b.getAttribute('data-jmeno');
		f.scrollIntoView({ behavior: 'smooth', block: 'center' }); f.obsah.focus();
	});
});
document.querySelectorAll('[data-zrusit-reakci]').forEach(function (b) {
	b.addEventListener('click', function () { var f = b.closest('form'); f.reakce_na.value = '0'; b.parentNode.hidden = true; });
});
</script>
