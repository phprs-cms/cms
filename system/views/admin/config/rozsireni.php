<?php
/** Záložka Rozšíření. */
use PhpRS\Core\Rozsireni;
?>
<p class="hlaska">Rozšíření jsou volitelné části phpRS. Všechna jsou součástí systému a udržuje je tým phpRS – nic se nestahuje ani neinstaluje. Vypnuté rozšíření zmizí z menu i z webu, jeho data zůstanou a po zapnutí se vrátí.</p>
<div class="rozsireni-seznam">
<?php foreach (Rozsireni::SEZNAM as $klic => [$nazev, $popis]): ?>
	<label class="rozsireni-karta">
		<input type="checkbox" name="rozsireni[]" value="<?= e($klic) ?>"<?= in_array($klic, $zapnutaRozsireni, true) ? ' checked' : '' ?>>
		<span><strong><?= e($nazev) ?></strong><br><?= e($popis) ?></span>
	</label>
<?php endforeach ?>
</div>
<p class="napoveda">Vždy zapnuté jádro: Články, Média, Rubriky, Stránky, Bloky a rozvržení, Uživatelé, Nastavení.</p>
