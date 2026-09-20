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
<p class="verze"><?= e(t('Retro prostředí je vzpomínka na původní phpRS Jiřího Lukáše (2001–2007). Do současnosti vás vrátí přepínač „2026“ vpravo nahoře.')) ?></p>
<?php else: ?>
<div class="prehled-hlavicka">
	<h2><?= e(t('Přehled')) ?></h2>
<?php if (isset($moduly['clanky'])): ?>
	<a class="tl" href="<?= e($app->url('admin.php?modul=clanky&akce=novy')) ?>"><?= e(t('Napsat článek')) ?></a>
<?php endif ?>
</div>
<?php if (!empty($pruvodce)): $hotovych = count(array_filter($pruvodce, fn (array $k): bool => $k['hotovo'])); ?>
<section class="pruvodce" aria-label="<?= e(t('První kroky')) ?>">
	<div class="pruvodce-hlava">
		<h3><?= e(t('První kroky')) ?> <small><?= $hotovych ?> / <?= count($pruvodce) ?></small></h3>
		<form method="post" action="<?= e($app->url('admin.php?akce=pruvodce_skryt')) ?>"><?= $app->session->csrfField() ?><button class="navigace" type="submit"><?= e(t('Skrýt')) ?></button></form>
	</div>
	<ol class="pruvodce-kroky">
<?php foreach ($pruvodce as $k): ?>
		<li class="<?= $k['hotovo'] ? 'hotovo' : '' ?>"><a href="<?= e($k['url']) ?>"><strong><?= e(t($k['nazev'])) ?></strong><span><?= e(t($k['popis'])) ?></span></a></li>
<?php endforeach ?>
	</ol>
</section>
<?php endif ?>
<div class="dlazdice">
<?php foreach ($pocty as $popis => $pocet): ?>
	<div class="dlazdice-polozka"><strong><?= number_format($pocet, 0, ',', ' ') ?></strong><span><?= e(t($popis)) ?></span></div>
<?php endforeach ?>
</div>
<?php if (count($navstevnost) >= 2):
    // sloupcový graf v čistém SVG: jeden sloupec na den, výška podle návštěv
    $dny = [];
    for ($i = 13; $i >= 0; $i--) { $dny[date('Y-m-d', strtotime("-{$i} day"))] = 0; }
    foreach ($navstevnost as $n) { $dny[$n['den']] = (int) $n['navstevy']; }
    $max = max(1, ...array_values($dny));
?>
<section class="prehled-graf" aria-label="<?= e(t('Návštěvnost za 14 dní')) ?>">
	<h3><?= e(t('Návštěvnost za 14 dní')) ?> <small><?= e(t('%s návštěv', number_format(array_sum($dny), 0, ',', ' '))) ?></small></h3>
	<svg viewBox="0 0 280 70" preserveAspectRatio="none" role="img" aria-label="<?= e(t('Návštěvnost za 14 dní')) ?>">
<?php $x = 0; foreach ($dny as $den => $pocet): $v = max(1, (int) round($pocet / $max * 62)); ?>
		<rect x="<?= $x * 20 + 2 ?>" y="<?= 66 - $v ?>" width="16" height="<?= $v ?>" rx="2"><title><?= e(datum($den)) ?>: <?= $pocet ?></title></rect>
<?php $x++; endforeach ?>
	</svg>
	<p class="smltxt"><a href="<?= e($app->url('admin.php?modul=stat')) ?>"><?= e(t('Celá statistika')) ?></a></p>
</section>
<?php endif ?>
<?php if ($fronta !== [] && isset($moduly['clanky'])): ?>
<h3><?= e(t('Čeká na vás')) ?></h3>
<div class="tab-obal">
<table class="vypis">
<tbody>
<?php foreach ($fronta as $c): ?>
<tr>
	<td><a href="<?= e($app->url('admin.php?modul=clanky&akce=edit&id=' . (int) $c['idc'])) ?>"><?= e($c['titulek']) ?></a></td>
	<td><?= e((string) $c['autor_jm']) ?></td>
	<td><span class="stitek stitek-<?= $c['visible'] ? 'vydano' : 'koncept' ?>"><?= e(t($c['visible'] ? 'naplánováno' : ($c['stav_redakce'] === 'korektura' ? 'ke korektuře' : 'schváleno'))) ?></span></td>
	<td class="cislo"><?= e(datum($c['datum'], true)) ?></td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<?php endif ?>
<?php if ($posledni !== [] && isset($moduly['clanky'])): ?>
<h3><?= e(t('Naposledy upravené články')) ?></h3>
<div class="tab-obal">
<table class="vypis">
<thead><tr><th><?= e(t('Titulek')) ?></th><th><?= e(t('Rubrika')) ?></th><th><?= e(t('Datum vydání')) ?></th><th><?= e(t('Vydán')) ?></th><th><?= e(t('Čteno')) ?></th></tr></thead>
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
