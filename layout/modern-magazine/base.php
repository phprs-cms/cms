<?php
/**
 * Layout "Modern Magazine" - globální šablona stránky.
 *
 * Výrazný online magazín: černá lišta s rubrikami, obsah přes celou šířku a pod ním
 * pás, do kterého se vedle sebe poskládají všechny postranní bloky z administrace.
 *
 * @var PhpRS\Core\Settings $web
 * @var string $titulek  prázdný na hlavní stránce
 * @var array{hlavni:bool, popis:string, klicova_slova:string, obrazek:string, typ:string, noindex:bool} $meta
 * @var list<array{ids:int, html:string, hlavni:bool}> $sloupce
 * @var list<array<string, mixed>> $rubriky
 * @var callable(string): string $url
 * @var string $kanonicka
 */
$nazevWebu = $web->get('nazev_webu');
$hlavniObsah = '';
$postranni = '';
foreach ($sloupce as $sloupec) {
    if ($sloupec['hlavni']) {
        $hlavniObsah .= $sloupec['html'];
    } else {
        $postranni .= $sloupec['html'];
    }
}
$jeClanek = $meta['typ'] === 'article';
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
<link rel="stylesheet" href="<?= e($url('layout/modern-magazine/style.css')) ?>?v=<?= e(PHPRS_VERSION) ?>">
</head>
<body>
<a class="preskocit" href="#obsah">Přeskočit na obsah</a>
<header class="hlavicka">
	<div class="obal">
		<a class="logo" href="<?= e($url('')) ?>"><?= e($nazevWebu) ?></a>
		<nav class="rubriky-lista" aria-label="Rubriky">
<?php foreach ($rubriky as $r): if ($r['uroven'] > 0) { continue; } ?>
			<a href="<?= e($url('rubrika/' . $r['seo_link'])) ?>"><?= e($r['nazev']) ?></a>
<?php endforeach ?>
		</nav>
		<a class="hledat" href="<?= e($url('hledani')) ?>">Hledat</a>
	</div>
</header>
<main id="obsah" class="hlavni<?= $jeClanek ? ' hlavni-clanek' : '' ?>">
<?= $hlavniObsah ?>
</main>
<?php if (trim($postranni) !== ''): ?>
<aside class="pas-bloku" aria-label="Další obsah">
	<div class="obal">
<?= $postranni ?>
	</div>
</aside>
<?php endif ?>
<footer class="paticka">
	<div class="obal">
		<span class="logo"><?= e($nazevWebu) ?></span>
<?php if ($web->get('popis_webu') !== ''): ?>
		<p><?= e($web->get('popis_webu')) ?></p>
<?php endif ?>
		<p class="drobne">&copy; <?= date('Y') ?> &middot; <a href="<?= e($url('rss.xml')) ?>">RSS</a> &middot; běží na phpRS 3</p>
	</div>
</footer>
</body>
</html>
