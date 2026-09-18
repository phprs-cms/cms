<?php
/**
 * Plátno stránky: zóny podle zvoleného rozvržení a v nich bloky, které lze přetahovat.
 *
 * @var PhpRS\Admin\Moduly\Bloky $modul
 * @var string $csrf
 * @var string $rozvrzeni
 * @var array<string, list<array<string, mixed>>> $bloky  zóna => bloky v pořadí shora
 */
use PhpRS\Admin\Moduly\Bloky;

$zona = function (string $klic) use ($bloky, $modul, $csrf): void {
    if (!isset($bloky[$klic])) {
        return;
    } ?>
	<section class="zona zona-<?= e($klic) ?>" data-zona="<?= e($klic) ?>" aria-label="<?= e(Bloky::ZONY[$klic]) ?>">
		<h3><?= e(Bloky::ZONY[$klic]) ?></h3>
		<div class="zona-bloky">
<?php foreach ($bloky[$klic] as $b): ?>
			<article class="blok-karta<?= $b['sys_funkce'] !== '' ? ' blok-karta-sys' : '' ?><?= $b['zobrazit'] ? '' : ' blok-skryty' ?>" draggable="true" data-idb="<?= (int) $b['idb'] ?>">
				<strong><?= e($b['nazev']) ?></strong>
				<span><?= $b['sys_funkce'] !== '' ? 'systémový · ' . e(explode(' – ', Bloky::SYSTEMOVE[$b['sys_funkce']] ?? $b['sys_funkce'])[0]) : 'vlastní HTML' ?><?= $b['zobrazit'] ? ((int) $b['zobrazit_kde'] !== 0 ? ' · ' . e(Bloky::KDE[(int) $b['zobrazit_kde']]) : '') : ' · <b>skrytý</b>' ?></span>
				<span class="blok-karta-akce">
					<button type="button" class="navigace" data-posun="-1" title="Posunout výš" aria-label="Posunout blok <?= e($b['nazev']) ?> výš">↑</button>
					<button type="button" class="navigace" data-posun="1" title="Posunout níž" aria-label="Posunout blok <?= e($b['nazev']) ?> níž">↓</button>
					<a href="<?= e($modul->url('edit', ['id' => $b['idb']])) ?>">Upravit</a>
					<form method="post" action="<?= e($modul->url('smaz')) ?>" onsubmit="return confirm('Opravdu smazat blok?');"><?= $csrf ?><input type="hidden" name="idb" value="<?= (int) $b['idb'] ?>"><button class="navigace" type="submit">Smaž</button></form>
				</span>
			</article>
<?php endforeach ?>
		</div>
		<a class="navigace zona-pridat" href="<?= e($modul->url('novy', ['zona' => $klic])) ?>">+ přidat blok</a>
	</section>
<?php };
?>
<form class="rozvrzeni-volba" method="post" action="<?= e($modul->url('rozvrzeni')) ?>">
	<?= $csrf ?>
	<span class="popisek">Rozvržení stránky:</span>
<?php foreach (Bloky::ROZVRZENI as $klic => [$nazev, $popis]): ?>
	<button type="submit" name="rozvrzeni" value="<?= e($klic) ?>" class="rozvrzeni-tl<?= $klic === $rozvrzeni ? ' aktivni' : '' ?>" aria-pressed="<?= $klic === $rozvrzeni ? 'true' : 'false' ?>" title="<?= e($popis) ?>">
		<i class="rozvrzeni-ikona rozvrzeni-ikona-<?= e($klic) ?>" aria-hidden="true"><b></b><b></b><b></b></i><?= e($nazev) ?>
	</button>
<?php endforeach ?>
</form>

<div class="platno platno-<?= e($rozvrzeni) ?>" data-platno data-url="<?= e($modul->url('poradi')) ?>">
	<?php $zona('hlavicka') ?>
	<div class="platno-stred">
		<?php $zona('leva') ?>
		<div class="platno-obsah">
			<?php $zona('nad') ?>
			<div class="platno-misto-obsahu">Obsah stránky<br><small>výpis článků, článek, rubrika, vyhledávání</small></div>
			<?php $zona('pod') ?>
		</div>
		<?php $zona('prava') ?>
	</div>
	<?php $zona('paticka') ?>
</div>
<p class="smltxt">Bloky přetáhněte myší na nové místo nebo do jiné zóny, případně použijte šipky. Pořadí se ukládá samo. Fialově jsou systémové bloky, šedě bloky s vlastním HTML. <span data-stav-poradi role="status"></span></p>
