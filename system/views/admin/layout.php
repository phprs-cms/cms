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
?>
<!doctype html>
<html lang="cs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= $nadpis !== '' ? e($nadpis) . ' - ' : '' ?>phpRS admin rozhraní</title>
<link rel="stylesheet" href="<?= e($app->url($css)) ?>?v=<?= e(PHPRS_VERSION) ?>">
</head>
<body class="prostredi-<?= e($prostredi) ?>">
<?php if ($user !== null): ?>
<header class="hlavicka">
	<a class="znacka" href="<?= e($app->url('admin.php')) ?>">php<b>RS</b></a>
	<button class="menu-prepinac" type="button" aria-expanded="false" aria-controls="menu">Menu</button>
	<ul class="menu rammodry-vypln" id="menu">
<?php foreach ($moduly as $ident => $class): ?>
		<li<?= $ident === $aktivni ? ' class="aktivni"' : '' ?>><a href="<?= e($app->url('admin.php?modul=' . $ident)) ?>"<?= $ident === $aktivni ? ' aria-current="page"' : '' ?>><?= e($class::NAZEV) ?></a></li>
<?php endforeach ?>
		<li class="menu-web"><a href="<?= e($app->url('')) ?>" target="_blank" rel="noopener">Zobrazit web</a></li>
		<li class="menu-logout"><form method="post" action="<?= e($app->url('admin.php?akce=logout')) ?>"><?= $app->session->csrfField() ?><button type="submit">Logout</button></form></li>
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
	<span class="prihlasen">login: <?= e($user['user']) ?> (<?= e(PhpRS\Core\Auth::TYPY[(int) $user['admin']] ?? '') ?>) - <?= date('d.m.Y') ?></span>
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
</body>
</html>
