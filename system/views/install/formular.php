<?php
/**
 * @var string $base
 * @var list<array{nazev:string, ok:bool, info:string}> $pozadavky
 * @var array<string, string> $data
 * @var array<string, string> $chyby
 * @var array<string, array{nazev:string, popis:string}> $layouty
 */
$chyba = fn (string $pole): string => isset($chyby[$pole]) ? '<span class="chyba-pole" role="alert">' . e($chyby[$pole]) . '</span>' : '';
$splneno = !in_array(false, array_column($pozadavky, 'ok'), true);
$nahledy = require __DIR__ . '/nahledy.php';
$prostredi = [
    '2026' => ['phpRS 2026', 'Moderní, minimalistické a responzivní prostředí s postranním menu a přehledem redakce.'],
    'retro' => ['phpRS retro', 'Pro zábavu a pro pamětníky: vzhled původního phpRS z let 2001–2007 – modré menu, šedá tlačítka, Verdana.'],
];
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
	<h1>Instalace redakčního systému</h1>
	<p>Pět krátkých kroků a váš magazín běží. Vše lze později změnit v administraci.</p>
</header>

<section class="krok">
	<h2><span>1</span> Kontrola serveru</h2>
	<ul class="kontrola">
<?php foreach ($pozadavky as $p): ?>
		<li<?= $p['ok'] ? '' : ' class="spatne"' ?>><div><?= e($p['nazev']) ?> <small>– <?= e($p['info']) ?></small></div></li>
<?php endforeach ?>
	</ul>
</section>

<?php if (!$splneno): ?>
<p class="hlaska hlaska-chyba" role="alert">Server nesplňuje požadavky. Opravte položky označené křížkem a obnovte stránku.</p>
<?php else: ?>
<?php if ($chyby !== []): ?>
<p class="hlaska hlaska-chyba" role="alert">Instalaci se nepodařilo dokončit – zkontrolujte zvýrazněná pole.</p>
<?php endif ?>
<form method="post" autocomplete="off">
<section class="krok">
	<h2><span>2</span> Databáze</h2>
	<p>MySQL nebo MariaDB. Prázdnou databázi založte předem – na hostingu v jeho administraci.</p>
	<div class="pole">
		<div class="cele s-portem">
			<div><label for="db_host">Server</label><input type="text" id="db_host" name="db_host" value="<?= e($data['db_host']) ?>"></div>
			<div><label for="db_port">Port</label><input type="number" id="db_port" name="db_port" value="<?= e($data['db_port']) ?>"></div>
		</div>
		<div><label for="db_name">Název databáze</label><input type="text" id="db_name" name="db_name" value="<?= e($data['db_name']) ?>" required><?= $chyba('db_name') ?></div>
		<div><label for="db_prefix">Předpona tabulek</label><input type="text" id="db_prefix" name="db_prefix" value="<?= e($data['db_prefix']) ?>" required><?= $chyba('db_prefix') ?></div>
		<div><label for="db_user">Uživatel</label><input type="text" id="db_user" name="db_user" value="<?= e($data['db_user']) ?>" required></div>
		<div><label for="db_password">Heslo</label><input type="password" id="db_password" name="db_password" autocomplete="off"></div>
	</div>
</section>

<section class="krok">
	<h2><span>3</span> Web a administrátor</h2>
	<p>Účet, kterým se poprvé přihlásíte do administrace.</p>
	<div class="pole">
		<div class="cele"><label for="nazev_webu">Název webu</label><input type="text" id="nazev_webu" name="nazev_webu" value="<?= e($data['nazev_webu']) ?>" required></div>
		<div><label for="user">Přihlašovací jméno</label><input type="text" id="user" name="user" value="<?= e($data['user']) ?>" required><?= $chyba('user') ?></div>
		<div><label for="jmeno">Jméno a příjmení</label><input type="text" id="jmeno" name="jmeno" value="<?= e($data['jmeno']) ?>"><span class="napoveda">Zobrazuje se u článků.</span></div>
		<div class="cele"><label for="email">E-mail</label><input type="email" id="email" name="email" value="<?= e($data['email']) ?>"><?= $chyba('email') ?></div>
		<div><label for="password">Heslo</label><input type="password" id="password" name="password" autocomplete="new-password" minlength="10" required><?= $chyba('password') ?><span class="napoveda">Alespoň 10 znaků.</span></div>
		<div><label for="password2">Heslo znovu</label><input type="password" id="password2" name="password2" autocomplete="new-password" required></div>
	</div>
</section>

<section class="krok">
	<h2><span>4</span> Prostředí administrace</h2>
	<p>Stejné funkce, dva vzhledy. Každý autor si mezi nimi přepíná jedním kliknutím přímo v administraci.</p>
	<div class="volby" role="radiogroup" aria-label="Prostředí administrace">
<?php foreach ($prostredi as $klic => [$nazev, $popis]): $klic = (string) $klic; // klíč '2026' je v PHP int ?>
		<label class="volba">
			<input type="radio" name="prostredi" value="<?= e($klic) ?>"<?= $data['prostredi'] === $klic ? ' checked' : '' ?>>
			<?= $nahledy[$klic] ?>
			<strong><?= e($nazev) ?></strong>
			<span><?= e($popis) ?></span>
		</label>
<?php endforeach ?>
	</div>
</section>

<section class="krok">
	<h2><span>5</span> Šablona webu</h2>
	<p>Jak uvidí magazín čtenáři.</p>
	<div class="volby" role="radiogroup" aria-label="Šablona webu">
<?php foreach ($layouty as $slozka => $l): ?>
		<label class="volba">
			<input type="radio" name="layout" value="<?= e($slozka) ?>"<?= $data['layout'] === $slozka ? ' checked' : '' ?>>
			<?= $nahledy[$slozka] ?? $nahledy['default'] ?>
			<strong><?= e($l['nazev']) ?></strong>
			<span><?= e($l['popis']) ?></span>
		</label>
<?php endforeach ?>
	</div>
</section>

<div class="akce">
	<button class="tlacitko" type="submit">Nainstalovat phpRS 3</button>
	<small>Vytvoří tabulky v databázi a soubor config.php.</small>
</div>
</form>
<?php endif ?>
</main>
</body>
</html>
