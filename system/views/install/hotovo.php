<?php
/**
 * @var string $base
 * @var bool $jizNainstalovano
 */
?>
<!doctype html>
<html lang="cs">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Instalace phpRS 3</title>
<link rel="stylesheet" href="<?= e($base) ?>/image/install.css?v=<?= e(PHPRS_VERSION) ?>">
</head>
<body>
<main class="instalator">
<header class="uvod">
	<div class="znacka">php<b>RS</b><sup>3</sup></div>
<?php if ($jizNainstalovano): ?>
	<h1>phpRS 3 je už nainstalován</h1>
	<p>Soubor config.php existuje, instalátor proto nic nemění.</p>
<?php else: ?>
	<h1>Hotovo, magazín běží</h1>
	<p>Databáze je připravena a konfigurace zapsána.</p>
<?php endif ?>
</header>
<p class="hlaska <?= $jizNainstalovano ? 'hlaska-chyba' : 'hlaska-ok' ?>">Z bezpečnostních důvodů teď ze serveru smažte soubor <strong>install.php</strong>.</p>
<div class="akce">
	<a class="tlacitko" href="<?= e($base) ?>/admin.php">Přejít do administrace</a>
	<a class="tlacitko druhe" href="<?= e($base) ?>/">Zobrazit web</a>
</div>
</main>
</body>
</html>
