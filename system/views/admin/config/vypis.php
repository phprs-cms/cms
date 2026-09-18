<?php
/**
 * @var PhpRS\Admin\Moduly\Konfigurace $modul
 * @var string $csrf
 * @var array<string, string> $hodnoty
 * @var array<string, array{nazev:string, popis:string}> $layouty
 * @var array<string, string> $prostredi
 * @var array<int, string> $ankety
 */
?>
<form class="formular" method="post" action="<?= e($modul->url('uloz')) ?>">
<?= $csrf ?>
<fieldset>
<legend>Web</legend>
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
	<label for="logo_webu">Logo</label>
	<div><input class="textpole siroke" type="text" id="logo_webu" name="logo_webu" value="<?= e($hodnoty['logo_webu']) ?>" maxlength="255" placeholder="nepovinné – jinak se v záhlaví zobrazí název webu" data-obrazek></div>
</div>
<div class="radek">
	<label for="text_paticky">Text v patičce</label>
	<input class="textpole siroke" type="text" id="text_paticky" name="text_paticky" value="<?= e($hodnoty['text_paticky']) ?>" maxlength="300" placeholder="např. vydavatel, ISSN, kontakt">
</div>
</fieldset>

<fieldset>
<legend>Sociální sítě</legend>
<?php foreach (PhpRS\Admin\Moduly\Konfigurace::SITE as $klic => $nazev): ?>
<div class="radek">
	<label for="<?= e($klic) ?>"><?= e($nazev) ?></label>
	<input class="textpole siroke" type="url" id="<?= e($klic) ?>" name="<?= e($klic) ?>" value="<?= e($hodnoty[$klic]) ?>" maxlength="255" placeholder="https://">
</div>
<?php endforeach ?>
<p class="napoveda">Vyplněné profily se zobrazí v patičce webu a ve strukturovaných datech pro vyhledávače.</p>
</fieldset>

<fieldset>
<legend>Vzhled</legend>
<div class="radek">
	<label for="layout">Šablona webu (layout)</label>
	<div><select id="layout" name="layout">
<?php foreach ($layouty as $slozka => $l): ?>
		<option value="<?= e($slozka) ?>"<?= $hodnoty['layout'] === $slozka ? ' selected' : '' ?>><?= e($l['nazev']) ?></option>
<?php endforeach ?>
	</select>
	<span class="napoveda">Vzhled webu pro čtenáře. Layouty jsou složky v layout/.</span></div>
</div>
<div class="radek">
	<label for="prostredi_admin">Výchozí prostředí administrace</label>
	<div><select id="prostredi_admin" name="prostredi_admin">
<?php foreach ($prostredi as $klic => $nazev): $klic = (string) $klic; // klíč '2026' je v PHP int ?>
		<option value="<?= e($klic) ?>"<?= $hodnoty['prostredi_admin'] === $klic ? ' selected' : '' ?>><?= e($nazev) ?></option>
<?php endforeach ?>
	</select>
	<span class="napoveda">Platí pro přihlašovací stránku a pro autory, kteří si sami nevybrali. Každý si prostředí přepíná v horní liště.</span></div>
</div>
</fieldset>

<fieldset>
<legend>Obsah</legend>
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
