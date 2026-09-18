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
<link rel="stylesheet" href="<?= e($base) ?>/image/admin.css">
</head>
<body>
<div class="obsah">
<img class="logo" style="margin-top:20px" src="<?= e($base) ?>/image/phprs_logo.svg" width="280" height="100" alt="phpRS 3">
<?php if ($jizNainstalovano): ?>
<h2>phpRS 3 je už nainstalován</h2>
<p class="hlaska">Soubor config.php existuje. Instalátor z bezpečnostních důvodů nic nemění - soubor install.php ze serveru smažte.</p>
<?php else: ?>
<h2>Instalace proběhla úspěšně</h2>
<p class="hlaska hlaska-ok">Databáze je připravena a config.php zapsán. Nyní ze serveru smažte soubor install.php.</p>
<?php endif ?>
<p class="navigace-radek">
	<a class="navigace" href="<?= e($base) ?>/admin.php">Přejít do administrace</a>
	<a class="navigace" href="<?= e($base) ?>/">Zobrazit web</a>
</p>
</div>
</body>
</html>
