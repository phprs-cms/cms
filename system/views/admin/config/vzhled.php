<?php /** Záložka Vzhled. */ ?>
<fieldset>
<legend>Web</legend>
<div class="radek">
	<label for="layout">Šablona webu</label>
	<div><select id="layout" name="layout">
<?php foreach ($layouty as $slozka => $l): ?>
		<option value="<?= e($slozka) ?>"<?= $hodnoty['layout'] === $slozka ? ' selected' : '' ?>><?= e($l['nazev']) ?> – <?= e($l['popis']) ?></option>
<?php endforeach ?>
	</select>
	<span class="napoveda">Se šablonou se nastaví i rozvržení stránky, které jí sluší. Změnit ho můžete v sekci Bloky a rozvržení.</span></div>
</div>
<div class="radek">
	<label for="logo_webu">Logo</label>
	<div><input class="textpole siroke" type="text" id="logo_webu" name="logo_webu" value="<?= e($hodnoty['logo_webu']) ?>" maxlength="255" placeholder="nepovinné – jinak se v záhlaví zobrazí název webu" data-obrazek></div>
</div>
</fieldset>
<fieldset>
<legend>Administrace</legend>
<div class="radek">
	<label for="prostredi_admin">Výchozí prostředí</label>
	<div><select id="prostredi_admin" name="prostredi_admin">
<?php foreach ($prostredi as $klic => $nazev): $klic = (string) $klic; // klíč '2026' je v PHP int ?>
		<option value="<?= e($klic) ?>"<?= $hodnoty['prostredi_admin'] === $klic ? ' selected' : '' ?>><?= e($nazev) ?></option>
<?php endforeach ?>
	</select>
	<span class="napoveda">Pro přihlašovací stránku a pro uživatele, kteří si sami nevybrali. Každý si prostředí přepíná v horní liště.</span></div>
</div>
</fieldset>
