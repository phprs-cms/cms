<?php
/**
 * @var PhpRS\Admin\Moduly\Autori $modul
 * @var string $csrf
 * @var array<string, mixed> $autor
 * @var array<string, string> $chyby
 * @var bool $sam  admin upravuje vlastní účet
 * @var array<string, string> $moduly  ident => název (jen moduly, ke kterým se práva nastavují)
 * @var list<string> $maModuly
 * @var array<int, string> $ostatni
 * @var list<int> $maPodrizene
 */
$chyba = fn (string $pole): string => isset($chyby[$pole]) ? '<span class="chyba-pole" role="alert">' . e($chyby[$pole]) . '</span>' : '';
$typy = [0 => 'autor - píše články', 1 => 'redaktor', 2 => 'admin - přístup ke všemu'];
?>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url()) ?>">Zpět na hlavní stránku sekce</a></p>
<form class="formular" method="post" action="<?= e($modul->url('uloz')) ?>" autocomplete="off">
<?= $csrf ?>
<input type="hidden" name="idu" value="<?= (int) $autor['idu'] ?>">
<div class="radek">
	<label for="user">Uživatel (login)</label>
	<div><input class="textpole" type="text" id="user" name="user" value="<?= e($autor['user']) ?>" maxlength="40" size="30" required><?= $chyba('user') ?></div>
</div>
<div class="radek">
	<label for="jmeno">Jméno</label>
	<div><input class="textpole siroke" type="text" id="jmeno" name="jmeno" value="<?= e($autor['jmeno']) ?>" maxlength="100">
	<span class="napoveda">Zobrazuje se u článků na webu.</span></div>
</div>
<div class="radek">
	<label for="email">E-mail</label>
	<div><input class="textpole siroke" type="email" id="email" name="email" value="<?= e($autor['email']) ?>" maxlength="190"><?= $chyba('email') ?></div>
</div>
<div class="radek">
	<label for="url">Web autora</label>
	<input class="textpole siroke" type="url" id="url" name="url" value="<?= e($autor['url']) ?>" maxlength="255" placeholder="https://">
</div>
<div class="radek">
	<label for="password"><?= $autor['idu'] ? 'Nové heslo' : 'Heslo' ?></label>
	<div><input class="textpole" type="password" id="password" name="password" size="30" autocomplete="new-password"<?= $autor['idu'] ? '' : ' required' ?>><?= $chyba('password') ?>
	<span class="napoveda">Alespoň 10 znaků.<?= $autor['idu'] ? ' Nechte prázdné, pokud heslo neměníte.' : '' ?></span></div>
</div>
<div class="radek">
	<label for="password2">Heslo znovu</label>
	<input class="textpole" type="password" id="password2" name="password2" size="30" autocomplete="new-password">
</div>

<fieldset>
<legend>Nastav práva</legend>
<div class="radek">
	<label for="admin">Typ uživatele</label>
	<div><select id="admin" name="admin"<?= $sam ? ' disabled' : '' ?>>
<?php foreach ($typy as $hodnota => $popis): ?>
		<option value="<?= $hodnota ?>"<?= (int) $autor['admin'] === $hodnota ? ' selected' : '' ?>><?= e($popis) ?></option>
<?php endforeach ?>
	</select>
<?php if ($sam): ?>
	<span class="napoveda">Vlastní účet nemůžete zbavit práv administrátora.</span>
<?php endif ?>
	</div>
</div>
<div class="radek">
	<span class="popisek">Právo vydávat</span>
	<div class="volby"><label><input type="checkbox" name="pravo_vydavat" value="1"<?= $autor['pravo_vydavat'] ? ' checked' : '' ?>> Smí vydávat články a měnit už vydané</label>
	<span class="napoveda">Bez tohoto práva autor článek jen připraví a vydá ho redaktor. Admin může vydávat vždy.</span></div>
</div>
<div class="radek">
	<span class="popisek">Přístup k modulům</span>
	<div class="volby">
<?php foreach ($moduly as $ident => $nazev): ?>
		<label><input type="checkbox" name="moduly[]" value="<?= e($ident) ?>"<?= in_array($ident, $maModuly, true) ? ' checked' : '' ?>> <?= e($nazev) ?></label><br>
<?php endforeach ?>
		<span class="napoveda">Admin má přístup ke všem modulům automaticky.</span>
	</div>
</div>
<?php if ($ostatni !== []): ?>
<div class="radek">
	<span class="popisek">Nastav vazby - podřízení autoři</span>
	<div class="volby">
<?php foreach ($ostatni as $idu => $jmeno): ?>
		<label><input type="checkbox" name="podrizeni[]" value="<?= (int) $idu ?>"<?= in_array((int) $idu, $maPodrizene, true) ? ' checked' : '' ?>> <?= e($jmeno) ?></label><br>
<?php endforeach ?>
		<span class="napoveda">Uživatel vidí a může upravovat články svých podřízených.</span>
	</div>
</div>
<?php endif ?>
<?php if (!$sam): ?>
<div class="radek">
	<span class="popisek">Blokovat</span>
	<div class="volby"><label><input type="checkbox" name="blokovat" value="1"<?= $autor['blokovat'] ? ' checked' : '' ?>> Účet je zablokován (nelze se přihlásit)</label></div>
</div>
<?php endif ?>
</fieldset>
<p class="tlacitka"><input class="tl" type="submit" value="<?= $autor['idu'] ? 'Ulož' : 'Přidej' ?>"></p>
</form>
