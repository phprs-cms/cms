<?php
/** Záložka Vzhled: šablona webu a prostředí administrace jako obrázkové karty. */
$nahledy = require dirname(__DIR__, 2) . '/install/nahledy.php';
?>
<fieldset>
<legend>Šablona webu</legend>
<div class="karty-volby">
<?php foreach ($layouty as $slozka => $l): ?>
	<label class="karta-volba">
		<input type="radio" name="layout" value="<?= e($slozka) ?>"<?= $hodnoty['layout'] === $slozka ? ' checked' : '' ?>>
		<?= $nahledy[$slozka] ?? $nahledy['default'] ?>
		<strong><?= e($l['nazev']) ?></strong>
		<span><?= e($l['popis']) ?></span>
	</label>
<?php endforeach ?>
</div>
<p class="napoveda">Se šablonou se nastaví i rozvržení stránky, které jí sluší. Bloky a rozvržení pak doladíte přímo na webu v sekci Bloky a rozvržení.</p>
<div class="radek" style="margin-top:16px">
	<label for="logo_webu">Logo</label>
	<div><input class="textpole siroke" type="text" id="logo_webu" name="logo_webu" value="<?= e($hodnoty['logo_webu']) ?>" maxlength="255" placeholder="nepovinné – jinak se v záhlaví zobrazí název webu" data-obrazek></div>
</div>
</fieldset>
<fieldset>
<legend>Vzhled administrace</legend>
<div class="karty-volby">
<?php foreach (['2026' => ['phpRS 2026', 'Moderní prostředí s postranním menu.'], 'retro' => ['phpRS retro', 'Pro zábavu: vzhled původního phpRS.']] as $klic => [$nazev, $popis]): $klic = (string) $klic; // klíč '2026' je v PHP int ?>
	<label class="karta-volba">
		<input type="radio" name="prostredi_admin" value="<?= e($klic) ?>"<?= $hodnoty['prostredi_admin'] === $klic ? ' checked' : '' ?>>
		<?= $nahledy[$klic] ?>
		<strong><?= e($nazev) ?></strong>
		<span><?= e($popis) ?></span>
	</label>
<?php endforeach ?>
</div>
<p class="napoveda">Výchozí volba pro přihlašovací stránku a nové uživatele. Každý si prostředí přepíná sám v horní liště.</p>
</fieldset>
