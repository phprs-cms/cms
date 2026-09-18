<?php
/**
 * Rámec administrace: menu, login proužek, nadpis sekce, hlášky, obsah.
 * HTML je pro obě prostředí stejné; vzhled určuje stylesheet (image/admin.css = retro, image/admin-2026.css).
 *
 * @var PhpRS\Core\App $app
 * @var string $nadpis
 * @var string $obsah  hotové HTML modulu
 * @var array<string, class-string<PhpRS\Admin\Modul>> $moduly
 * @var string $aktivni
 * @var array<string, mixed>|null $user
 * @var list<array{typ:string, text:string}> $hlasky
 * @var string $prostredi  retro | 2026
 */
$css = $prostredi === '2026' ? 'image/admin-2026.css' : 'image/admin.css';
$ikona = require __DIR__ . '/ikony.php';
?>
<!doctype html>
<html lang="cs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<script>try { var t = localStorage.getItem('phprs3-tema'); if (t) { document.documentElement.setAttribute('data-tema', t); } } catch (e) {}</script>
<title><?= $nadpis !== '' ? e($nadpis) . ' - ' : '' ?>phpRS admin rozhraní</title>
<link rel="stylesheet" href="<?= e($app->url($css)) ?>?v=<?= e(PHPRS_VERSION) ?>">
<link rel="stylesheet" href="<?= e($app->url('image/editor.css')) ?>?v=<?= e(PHPRS_VERSION) ?>">
</head>
<body class="prostredi-<?= e($prostredi) ?>">
<?php if ($user !== null): ?>
<header class="hlavicka">
	<a class="znacka" href="<?= e($app->url('admin.php')) ?>"><span class="znacka-znak">RS</span><span>php<b>RS</b></span></a>
	<button class="menu-prepinac" type="button" aria-expanded="false" aria-controls="menu">Menu</button>
	<ul class="menu rammodry-vypln" id="menu">
		<li class="menu-prehled<?= $aktivni === '' ? ' aktivni' : '' ?>"><a href="<?= e($app->url('admin.php')) ?>"><?= $ikona('prehled') ?>Přehled</a></li>
<?php $skupina = ''; foreach ($moduly as $ident => $class): ?>
<?php if ($class::SKUPINA !== $skupina): $skupina = $class::SKUPINA; ?>
		<li class="menu-skupina" aria-hidden="true"><?= e($skupina) ?></li>
<?php endif ?>
		<li<?= $ident === $aktivni ? ' class="aktivni"' : '' ?>><a href="<?= e($app->url('admin.php?modul=' . $ident)) ?>"<?= $ident === $aktivni ? ' aria-current="page"' : '' ?>><?= $ikona($class::IKONA) ?><?= e($prostredi === 'retro' && $class::NAZEV_RETRO !== '' ? $class::NAZEV_RETRO : $class::NAZEV) ?></a></li>
<?php endforeach ?>
		<li class="menu-web"><a href="<?= e($app->url('')) ?>" target="_blank" rel="noopener"><?= $ikona('web') ?>Zobrazit web</a></li>
		<li class="menu-logout"><form method="post" action="<?= e($app->url('admin.php?akce=logout')) ?>"><?= $app->session->csrfField() ?><button type="submit"><?= $ikona('odhlasit') ?><?= $prostredi === 'retro' ? 'Logout' : 'Odhlásit se' ?></button></form></li>
	</ul>
</header>
<div class="loginprouzek">
	<form class="prepinac-prostredi" method="post" action="<?= e($app->url('admin.php?akce=prostredi' . ($aktivni !== '' ? '&modul=' . rawurlencode($aktivni) : ''))) ?>">
		<?= $app->session->csrfField() ?>
		<span>prostředí:</span>
<?php foreach (PhpRS\Admin\Kernel::PROSTREDI as $klic => $nazev): $klic = (string) $klic; // klíč '2026' je v PHP int ?>
		<button type="submit" name="prostredi" value="<?= e($klic) ?>"<?= $klic === $prostredi ? ' class="aktivni" aria-pressed="true"' : ' aria-pressed="false"' ?>><?= e($klic) ?></button>
<?php endforeach ?>
	</form>
	<button class="tema-prepinac" type="button" data-tema-prepinac title="Světlý / tmavý režim" aria-label="Přepnout světlý a tmavý režim"><?= $ikona('tema') ?></button>
	<span class="prihlasen"><span class="prihlasen-text">login: <?= e($user['user']) ?> (<?= e(PhpRS\Core\Auth::TYPY[(int) $user['admin']] ?? '') ?>) - <?= date('d.m.Y') ?></span><span class="avatar" title="<?= e(($user['jmeno'] ?: $user['user']) . ' – ' . (PhpRS\Core\Auth::TYPY[(int) $user['admin']] ?? '')) ?>" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($user['jmeno'] ?: $user['user'], 0, 1))) ?></span></span>
</div>
<?php endif ?>
<main class="obsah">
<?php if ($nadpis !== ''): ?>
<h2><?= e($nadpis) ?></h2>
<?php endif ?>
<?php foreach ($hlasky as $hlaska): ?>
<p class="hlaska hlaska-<?= e($hlaska['typ']) ?>" role="status"><?= e($hlaska['text']) ?></p>
<?php endforeach ?>
<?= $obsah ?>
</main>
<script src="<?= e($app->url('image/admin.js')) ?>?v=<?= e(PHPRS_VERSION) ?>" defer></script>
<script src="<?= e($app->url('image/editor.js')) ?>?v=<?= e(PHPRS_VERSION) ?>" data-admin-url="<?= e($app->url('admin.php')) ?>" defer></script>
</body>
</html>
