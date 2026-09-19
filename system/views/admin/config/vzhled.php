<?php
/** Záložka Administrace: výchozí prostředí administrace. */
$nahledy = require dirname(__DIR__, 2) . '/install/nahledy.php';
?>
<p class="hlaska">Šablonu webu, logo, barvu a písma najdete v sekci <a href="<?= e($modul->app()->url('admin.php?modul=vzhled')) ?>">Identita webu</a>.</p>
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
