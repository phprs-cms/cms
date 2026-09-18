<?php
/**
 * Rámec administrace: menu, login proužek, nadpis sekce, hlášky, obsah.
 *
 * @var PhpRS\Core\App $app
 * @var string $nadpis
 * @var string $obsah  hotové HTML modulu
 * @var array<string, class-string<PhpRS\Admin\Modul>> $moduly
 * @var string $aktivni
 * @var array<string, mixed>|null $user
 * @var list<array{typ:string, text:string}> $hlasky
 */
?>
<!doctype html>
<html lang="cs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= $nadpis !== '' ? e($nadpis) . ' - ' : '' ?>phpRS admin rozhraní</title>
<link rel="stylesheet" href="<?= e($app->url('image/admin.css')) ?>?v=<?= e(PHPRS_VERSION) ?>">
</head>
<body>
<?php if ($user !== null): ?>
<ul class="menu rammodry-vypln">
<?php foreach ($moduly as $ident => $class): ?>
	<li<?= $ident === $aktivni ? ' class="aktivni"' : '' ?>><a href="<?= e($app->url('admin.php?modul=' . $ident)) ?>"><?= e($class::NAZEV) ?></a></li>
<?php endforeach ?>
	<li><a href="<?= e($app->url('')) ?>" target="_blank" rel="noopener">Zobrazit web</a></li>
	<li><form method="post" action="<?= e($app->url('admin.php?akce=logout')) ?>"><?= $app->session->csrfField() ?><button type="submit">Logout</button></form></li>
</ul>
<div class="loginprouzek">login: <?= e($user['user']) ?> (<?= e(PhpRS\Core\Auth::TYPY[(int) $user['admin']] ?? '') ?>) - <?= date('d.m.Y') ?></div>
<?php endif ?>
<div class="obsah">
<?php if ($nadpis !== ''): ?>
<h2><?= e($nadpis) ?></h2>
<?php endif ?>
<?php foreach ($hlasky as $hlaska): ?>
<p class="hlaska hlaska-<?= e($hlaska['typ']) ?>" role="status"><?= e($hlaska['text']) ?></p>
<?php endforeach ?>
<?= $obsah ?>
</div>
<script src="<?= e($app->url('image/admin.js')) ?>?v=<?= e(PHPRS_VERSION) ?>" defer></script>
</body>
</html>
