<?php
/**
 * Layout "Classic Newspaper" - globální šablona stránky.
 *
 * Vzhled klasického deníku: datum, hlavička s názvem listu, lišta rubrik, obsah a vpravo
 * úzký sloupec, do kterého se poskládají všechny postranní sloupce bloků z administrace.
 *
 * @var PhpRS\Core\Settings $web
 * @var string $titulek  prázdný na hlavní stránce
 * @var array{hlavni:bool, popis:string, klicova_slova:string, obrazek:string, typ:string, noindex:bool} $meta
 * @var string $obsah  hotové HTML obsahu stránky (výpis, článek...)
 * @var array{hlavicka:string, leva:string, nad:string, pod:string, prava:string, paticka:string} $zony  HTML bloků v zónách
 * @var string $rozvrzeni  tri | dva | jeden | plna - zvolené v Úpravě bloků
 * @var list<array<string, mixed>> $rubriky
 * @var callable(string): string $url
 * @var string $kanonicka
 */
$nazevWebu = $web->get('nazev_webu');
?>
<!doctype html>
<html lang="cs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titulek !== '' ? $titulek . ' - ' . $nazevWebu : $nazevWebu) ?></title>
<?php if ($meta['popis'] !== ''): ?>
<meta name="description" content="<?= e($meta['popis']) ?>">
<?php endif ?>
<?php if ($meta['klicova_slova'] !== ''): ?>
<meta name="keywords" content="<?= e($meta['klicova_slova']) ?>">
<?php endif ?>
<?php if ($meta['noindex']): ?>
<meta name="robots" content="noindex, follow">
<?php else: ?>
<link rel="canonical" href="<?= e($kanonicka) ?>">
<?php endif ?>
<meta property="og:type" content="<?= e($meta['typ']) ?>">
<meta property="og:title" content="<?= e($titulek !== '' ? $titulek : $nazevWebu) ?>">
<meta property="og:site_name" content="<?= e($nazevWebu) ?>">
<?php if ($meta['obrazek'] !== ''): ?>
<meta property="og:image" content="<?= e($meta['obrazek']) ?>">
<?php endif ?>
<link rel="alternate" type="application/rss+xml" title="<?= e($nazevWebu) ?>" href="<?= e($url('rss.xml')) ?>">
<link rel="stylesheet" href="<?= e($url('layout/classic-newspaper/style.css')) ?>?v=<?= e(PHPRS_VERSION) ?>">
</head>
<body>
<a class="preskocit" href="#obsah">Přeskočit na obsah</a>
<header class="hlavicka">
	<div class="obal">
		<div class="hlavicka-lista">
			<span class="dnes"><?= e(datum_slovy()) ?></span>
			<span class="sluzby"><a href="<?= e($url('hledani')) ?>">Hledat</a><a href="<?= e($url('rss.xml')) ?>">RSS</a></span>
		</div>
		<a class="titul-listu" href="<?= e($url('')) ?>"><?= e($nazevWebu) ?></a>
<?php if ($web->get('popis_webu') !== ''): ?>
		<p class="motto"><?= e($web->get('popis_webu')) ?></p>
<?php endif ?>
		<nav class="rubriky-lista" aria-label="Rubriky">
<?php foreach ($rubriky as $r): if ($r['uroven'] > 0) { continue; } ?>
			<a href="<?= e($url('rubrika/' . $r['seo_link'])) ?>"><?= e($r['nazev']) ?></a>
<?php endforeach ?>
		</nav>
	</div>
</header>
<?php if ($zony['hlavicka'] !== ''): ?>
<div class="obal zona zona-hlavicka"><?= $zony['hlavicka'] ?></div>
<?php endif ?>
<div class="obal stranka rozvrzeni-<?= e($rozvrzeni) ?><?= $zony['leva'] !== '' ? ' ma-levou' : '' ?><?= $zony['prava'] !== '' ? ' ma-pravou' : '' ?><?= $meta['typ'] === 'article' ? ' stranka-clanek' : '' ?>">
<?php if ($zony['leva'] !== ''): ?>
	<aside class="zona zona-leva" aria-label="Levý sloupec"><?= $zony['leva'] ?></aside>
<?php endif ?>
	<main id="obsah" class="hlavni">
<?php if ($zony['nad'] !== ''): ?>
		<div class="zona zona-nad"><?= $zony['nad'] ?></div>
<?php endif ?>
<?= $obsah ?>
<?php if ($zony['pod'] !== ''): ?>
		<div class="zona zona-pod"><?= $zony['pod'] ?></div>
<?php endif ?>
	</main>
<?php if ($zony['prava'] !== ''): ?>
	<aside class="zona zona-prava" aria-label="Pravý sloupec"><?= $zony['prava'] ?></aside>
<?php endif ?>
</div>
<?php if ($zony['paticka'] !== ''): ?>
<div class="zona-paticka-obal"><div class="obal zona zona-paticka"><?= $zony['paticka'] ?></div></div>
<?php endif ?>
<footer class="paticka">
	<div class="obal">
		<span class="paticka-titul"><?= e($nazevWebu) ?></span>
		<span>&copy; <?= date('Y') ?> &middot; <a href="<?= e($url('rss.xml')) ?>">RSS</a> &middot; běží na phpRS 3</span>
	</div>
</footer>
</body>
</html>
