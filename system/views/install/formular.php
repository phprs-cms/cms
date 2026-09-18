<?php
/**
 * @var string $base
 * @var list<array{nazev:string, ok:bool, info:string}> $pozadavky
 * @var array<string, string> $data
 * @var array<string, string> $chyby
 */
$chyba = fn (string $pole): string => isset($chyby[$pole]) ? '<span class="chyba-pole" role="alert">' . e($chyby[$pole]) . '</span>' : '';
$splneno = !in_array(false, array_column($pozadavky, 'ok'), true);
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
<h2>Instalace redakčního systému</h2>

<h3 class="stred">1. Kontrola serveru</h3>
<table class="vypis">
<tbody>
<?php foreach ($pozadavky as $p): ?>
<tr<?= $p['ok'] ? '' : ' class="nevydany"' ?>><td><?= e($p['nazev']) ?></td><td><?= e($p['info']) ?></td><td class="stred"><strong><?= $p['ok'] ? 'OK' : 'CHYBA' ?></strong></td></tr>
<?php endforeach ?>
</tbody>
</table>

<?php if (!$splneno): ?>
<p class="hlaska hlaska-chyba">Server nesplňuje požadavky. Opravte položky označené CHYBA a obnovte stránku.</p>
<?php else: ?>
<form class="formular" method="post" autocomplete="off">
<fieldset>
<legend>2. Databáze MySQL / MariaDB</legend>
<div class="radek"><label for="db_host">Server</label><div><input class="textpole" type="text" id="db_host" name="db_host" value="<?= e($data['db_host']) ?>" size="30"> port <input class="textpole" type="number" name="db_port" value="<?= e($data['db_port']) ?>" style="width:80px" aria-label="Port"></div></div>
<div class="radek"><label for="db_name">Název databáze</label><div><input class="textpole" type="text" id="db_name" name="db_name" value="<?= e($data['db_name']) ?>" size="30" required><?= $chyba('db_name') ?></div></div>
<div class="radek"><label for="db_user">Uživatel</label><input class="textpole" type="text" id="db_user" name="db_user" value="<?= e($data['db_user']) ?>" size="30" required></div>
<div class="radek"><label for="db_password">Heslo</label><input class="textpole" type="password" id="db_password" name="db_password" size="30" autocomplete="off"></div>
<div class="radek"><label for="db_prefix">Předpona tabulek</label><div><input class="textpole" type="text" id="db_prefix" name="db_prefix" value="<?= e($data['db_prefix']) ?>" size="12" required><?= $chyba('db_prefix') ?>
<span class="napoveda">Databázi je potřeba mít předem založenou (na hostingu v jeho administraci).</span></div></div>
</fieldset>
<fieldset>
<legend>3. Web a administrátor</legend>
<div class="radek"><label for="nazev_webu">Název webu</label><input class="textpole siroke" type="text" id="nazev_webu" name="nazev_webu" value="<?= e($data['nazev_webu']) ?>" required></div>
<div class="radek"><label for="user">Uživatel (login)</label><div><input class="textpole" type="text" id="user" name="user" value="<?= e($data['user']) ?>" size="30" required><?= $chyba('user') ?></div></div>
<div class="radek"><label for="jmeno">Jméno</label><input class="textpole siroke" type="text" id="jmeno" name="jmeno" value="<?= e($data['jmeno']) ?>"></div>
<div class="radek"><label for="email">E-mail</label><div><input class="textpole siroke" type="email" id="email" name="email" value="<?= e($data['email']) ?>"><?= $chyba('email') ?></div></div>
<div class="radek"><label for="password">Heslo</label><div><input class="textpole" type="password" id="password" name="password" size="30" autocomplete="new-password" required><?= $chyba('password') ?><span class="napoveda">Alespoň 10 znaků.</span></div></div>
<div class="radek"><label for="password2">Heslo znovu</label><input class="textpole" type="password" id="password2" name="password2" size="30" autocomplete="new-password" required></div>
</fieldset>
<p class="tlacitka"><input class="tl" type="submit" value="Nainstalovat phpRS 3"></p>
</form>
<?php endif ?>
</div>
</body>
</html>
