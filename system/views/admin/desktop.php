<?php
/**
 * Úvodní obrazovka administrace.
 * Retro: jako v originále jen menu a logo. 2026: přehled redakce.
 *
 * @var PhpRS\Core\App $app
 * @var string $prostredi
 * @var array<string, class-string<PhpRS\Admin\Modul>> $moduly
 * @var array<string, int> $pocty      jen 2026
 * @var list<array<string, mixed>> $posledni  jen 2026
 */
?>
<?php if ($prostredi === 'retro'): ?>
<img class="logo" src="<?= e($app->url('image/phprs_logo.svg')) ?>" width="420" height="150" alt="phpRS - redakční a informační systém">
<p class="verze">verze <?= e(PHPRS_VERSION) ?></p>
<?php else: ?>
<div class="prehled-hlavicka">
	<h2>Přehled</h2>
<?php if (isset($moduly['clanky'])): ?>
	<a class="tl" href="<?= e($app->url('admin.php?modul=clanky&akce=novy')) ?>">Napsat článek</a>
<?php endif ?>
</div>
<div class="dlazdice">
<?php foreach ($pocty as $popis => $pocet): ?>
	<div class="dlazdice-polozka"><strong><?= number_format($pocet, 0, ',', ' ') ?></strong><span><?= e($popis) ?></span></div>
<?php endforeach ?>
</div>
<?php if ($posledni !== [] && isset($moduly['clanky'])): ?>
<h3>Naposledy upravené články</h3>
<div class="tab-obal">
<table class="vypis">
<thead><tr><th>Titulek</th><th>Rubrika</th><th>Datum vydání</th><th>Vydán</th><th>Čteno</th></tr></thead>
<tbody>
<?php foreach ($posledni as $c): ?>
<tr<?= $c['visible'] ? '' : ' class="nevydany"' ?>>
	<td><a href="<?= e($app->url('admin.php?modul=clanky&akce=edit&id=' . (int) $c['idc'])) ?>"><?= e($c['titulek']) ?></a></td>
	<td><?= e($c['tema_jm']) ?></td>
	<td class="cislo"><?= e(datum($c['datum'], true)) ?></td>
	<td class="stred"><?= $c['visible'] ? 'Ano' : '<strong>Ne</strong>' ?></td>
	<td class="cislo"><?= (int) $c['visit'] ?>x</td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<?php endif ?>
<p class="verze">phpRS <?= e(PHPRS_VERSION) ?></p>
<?php endif ?>
