<?php
/**
 * Layout "default" - globální šablona stránky.
 *
 * Kolem obsahu vykreslí sloupce s bloky tak, jak jsou nastavené v administraci (Úprava bloků).
 * Vlastní layout = kopie této složky pod jiným názvem; vybírá se v Konfiguraci.
 *
 * @var PhpRS\Core\Settings $web
 * @var string $titulek  prázdný na hlavní stránce
 * @var array{hlavni:bool, popis:string, klicova_slova:string, obrazek:string, typ:string, noindex:bool} $meta
 * @var list<array{ids:int, html:string, hlavni:bool}> $sloupce
 * @var callable(string): string $url
 * @var string $kanonicka
 */
$nazevWebu = $web->get('nazev_webu');
$pocetSloupcu = count(array_filter($sloupce, fn (array $s): bool => trim($s['html']) !== ''));
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
<link rel="stylesheet" href="<?= e($url('layout/default/style.css')) ?>?v=<?= e(PHPRS_VERSION) ?>">
</head>
<body>
<header class="zahlavi">
	<div class="obal">
		<a class="nazev-webu" href="<?= e($url('')) ?>"><?= e($nazevWebu) ?></a>
<?php if ($web->get('popis_webu') !== ''): ?>
		<p class="motto"><?= e($web->get('popis_webu')) ?></p>
<?php endif ?>
	</div>
</header>
<div class="obal sloupce sloupce-<?= $pocetSloupcu ?>">
<?php foreach ($sloupce as $i => $sloupec): if (trim($sloupec['html']) === '') { continue; } ?>
<?php if ($sloupec['hlavni']): ?>
	<main class="sloupec sloupec-hlavni" id="obsah">
<?= $sloupec['html'] ?>
	</main>
<?php else: ?>
	<aside class="sloupec sloupec-bocni sloupec-<?= $i === 0 ? 'levy' : 'pravy' ?>">
<?= $sloupec['html'] ?>
	</aside>
<?php endif ?>
<?php endforeach ?>
</div>
<footer class="zapati">
	<div class="obal">
		&copy; <?= date('Y') ?> <?= e($nazevWebu) ?> &middot; <a href="<?= e($url('rss.xml')) ?>">RSS</a> &middot; běží na phpRS 3
	</div>
</footer>
</body>
</html>
