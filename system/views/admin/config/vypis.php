<?php
/**
 * @var PhpRS\Admin\Moduly\Konfigurace $modul
 * @var string $csrf
 * @var array<string, string> $hodnoty
 * @var list<string> $layouty
 * @var array<int, string> $ankety
 */
?>
<form class="formular" method="post" action="<?= e($modul->url('uloz')) ?>">
<?= $csrf ?>
<fieldset>
<legend>Konfigurace základního nastavení</legend>
<div class="radek">
	<label for="nazev_webu">Název webu</label>
	<input class="textpole siroke" type="text" id="nazev_webu" name="nazev_webu" value="<?= e($hodnoty['nazev_webu']) ?>" maxlength="150" required>
</div>
<div class="radek">
	<label for="popis_webu">Popis webu</label>
	<div><textarea class="textbox" id="popis_webu" name="popis_webu" rows="3" style="min-height:60px"><?= e($hodnoty['popis_webu']) ?></textarea>
	<span class="napoveda">Meta description hlavní stránky a popis RSS kanálu.</span></div>
</div>
<div class="radek">
	<label for="klicova_slova">Klíčová slova webu</label>
	<input class="textpole siroke" type="text" id="klicova_slova" name="klicova_slova" value="<?= e($hodnoty['klicova_slova']) ?>" maxlength="500">
</div>
<div class="radek">
	<label for="email_webu">E-mail redakce</label>
	<input class="textpole siroke" type="email" id="email_webu" name="email_webu" value="<?= e($hodnoty['email_webu']) ?>" maxlength="190">
</div>
<div class="radek">
	<label for="layout">Layout webu</label>
	<div><select id="layout" name="layout">
<?php foreach ($layouty as $l): ?>
		<option value="<?= e($l) ?>"<?= $hodnoty['layout'] === $l ? ' selected' : '' ?>><?= e($l) ?></option>
<?php endforeach ?>
	</select>
	<span class="napoveda">Adresáře ve složce layout/.</span></div>
</div>
</fieldset>

<fieldset>
<legend>Hlavní stránka</legend>
<div class="radek">
	<label for="pocet_clanku">Počet článků na stránku</label>
	<input class="textpole" type="number" id="pocet_clanku" name="pocet_clanku" value="<?= (int) $hodnoty['pocet_clanku'] ?>" min="1" max="100" style="width:70px">
</div>
<div class="radek">
	<label for="pocet_novinek">Počet novinek v bloku</label>
	<input class="textpole" type="number" id="pocet_novinek" name="pocet_novinek" value="<?= (int) $hodnoty['pocet_novinek'] ?>" min="0" max="50" style="width:70px">
</div>
<div class="radek">
	<span class="popisek">Platnost článků</span>
	<div class="volby"><label><input type="checkbox" name="hlidat_platnost" value="1"<?= $hodnoty['hlidat_platnost'] === '1' ? ' checked' : '' ?>> Hlídat platnost článků na hlavní stránce (datum stažení)</label></div>
</div>
<div class="radek">
	<span class="popisek">Komentáře</span>
	<div class="volby"><label><input type="checkbox" name="povolit_komentare" value="1"<?= $hodnoty['povolit_komentare'] === '1' ? ' checked' : '' ?>> Povolit komentáře u článků</label></div>
</div>
<div class="radek">
	<label for="aktivni_anketa">Aktivní anketa</label>
	<select id="aktivni_anketa" name="aktivni_anketa">
		<option value="0">- žádná -</option>
<?php foreach ($ankety as $ida => $titulek): ?>
		<option value="<?= (int) $ida ?>"<?= (int) $hodnoty['aktivni_anketa'] === (int) $ida ? ' selected' : '' ?>><?= e($titulek) ?></option>
<?php endforeach ?>
	</select>
</div>
</fieldset>
<p class="tlacitka"><input class="tl" type="submit" value="Ulož nastavení"></p>
</form>
<p class="verze">phpRS <?= e(PHPRS_VERSION) ?> · PHP <?= e(PHP_VERSION) ?></p>
