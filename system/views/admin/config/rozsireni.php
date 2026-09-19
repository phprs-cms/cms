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
<details class="pokrocile"<?= in_array('asistent', $zapnutaRozsireni, true) ? ' open' : '' ?>>
<summary>AI asistent – klíč a model</summary>
<div class="radek">
	<label for="ai_klic">Klíč Claude API</label>
	<div><input class="textpole siroke" type="password" id="ai_klic" name="ai_klic" value="" autocomplete="off" placeholder="<?= $hodnoty['ai_klic'] !== '' ? 'uložen klíč končící ' . e($hodnoty['ai_klic']) . ' – nový vložte jen při změně' : 'sk-ant-…' ?>">
	<span class="napoveda">Klíč si vytvoříte na <a href="https://console.anthropic.com/" target="_blank" rel="noopener">console.anthropic.com</a> → API Keys. Platíte jen za skutečné použití, jeden návrh stojí řádově haléře. Klíč se ukládá jen na vašem webu.</span>
<?php if ($hodnoty['ai_klic'] !== ''): ?>
	<label><input type="checkbox" name="ai_klic_smazat" value="1"> Odebrat uložený klíč</label>
<?php endif ?>
	</div>
</div>
<div class="radek">
	<label for="ai_model">Model</label>
	<select id="ai_model" name="ai_model">
<?php foreach (PhpRS\Core\Asistent::MODELY as $klic => $nazev): ?>
		<option value="<?= e($klic) ?>"<?= $hodnoty['ai_model'] === $klic ? ' selected' : '' ?>><?= e($nazev) ?></option>
<?php endforeach ?>
	</select>
</div>
<p class="napoveda">Asistent jen navrhuje – o každé změně rozhoduje redaktor. Při použití se text rozepsaného článku odešle službě Anthropic (Claude); bez kliknutí na tlačítko asistenta se nikam nic neposílá.</p>
</details>
