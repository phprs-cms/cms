<?php
/**
 * @var PhpRS\Admin\Moduly\Bloky $modul
 * @var string $csrf
 * @var array<string, mixed> $blok
 * @var array<string, string> $chyby
 * @var array<string, string> $zony  zóny dostupné ve zvoleném rozvržení
 */
use PhpRS\Admin\Moduly\Bloky;

$chyba = fn (string $pole): string => isset($chyby[$pole]) ? '<span class="chyba-pole" role="alert">' . e($chyby[$pole]) . '</span>' : '';
?>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url()) ?>">Zpět na hlavní stránku sekce</a></p>
<form class="formular" method="post" action="<?= e($modul->url('uloz')) ?>">
<?= $csrf ?>
<input type="hidden" name="idb" value="<?= (int) $blok['idb'] ?>">
<div class="radek">
	<label for="nazev">Název bloku</label>
	<div><input class="textpole siroke" type="text" id="nazev" name="nazev" value="<?= e($blok['nazev']) ?>" maxlength="100" required><?= $chyba('nazev') ?>
	<span class="napoveda">Zobrazuje se jako nadpis bloku (pokud nezvolíte vzhled „Bez nadpisu“).</span></div>
</div>
<div class="radek">
	<label for="sys_funkce">Co blok zobrazuje</label>
	<select id="sys_funkce" name="sys_funkce">
		<option value="">vlastní obsah (HTML)</option>
<?php foreach (Bloky::SYSTEMOVE as $zkratka => $nazev): ?>
		<option value="<?= e($zkratka) ?>"<?= $blok['sys_funkce'] === $zkratka ? ' selected' : '' ?>><?= e($nazev) ?></option>
<?php endforeach ?>
	</select>
</div>
<div class="radek">
	<label for="obsah">Vlastní obsah (HTML)</label>
	<div><textarea class="textbox kod" id="obsah" name="obsah" rows="10"><?= e($blok['obsah']) ?></textarea>
	<span class="napoveda">Použije se jen u bloku s vlastním obsahem.</span></div>
</div>
<div class="radek">
	<label for="zona">Umístění</label>
	<div><select id="zona" name="zona">
<?php foreach ($zony as $klic => $nazev): ?>
		<option value="<?= e($klic) ?>"<?= $blok['zona'] === $klic ? ' selected' : '' ?>><?= e($nazev) ?></option>
<?php endforeach ?>
	</select>
	<span class="napoveda">Pořadí uvnitř zóny změníte přetažením v přehledu bloků.</span></div>
</div>
<div class="radek">
	<label for="typ">Vzhled bloku</label>
	<select id="typ" name="typ">
<?php foreach (Bloky::VZHLEDY as $cislo => $nazev): ?>
		<option value="<?= $cislo ?>"<?= (int) $blok['typ'] === $cislo ? ' selected' : '' ?>><?= e($nazev) ?></option>
<?php endforeach ?>
	</select>
</div>
<div class="radek">
	<label for="zobrazit_kde">Kde blok zobrazit</label>
	<select id="zobrazit_kde" name="zobrazit_kde">
<?php foreach (Bloky::KDE as $hodnota => $popis): ?>
		<option value="<?= $hodnota ?>"<?= (int) $blok['zobrazit_kde'] === $hodnota ? ' selected' : '' ?>><?= e($popis) ?></option>
<?php endforeach ?>
	</select>
</div>
<div class="radek">
	<span class="popisek">Zobrazit blok</span>
	<div class="volby"><label><input type="checkbox" name="zobrazit" value="1"<?= $blok['zobrazit'] ? ' checked' : '' ?>> Ano</label></div>
</div>
<p class="tlacitka"><input class="tl" type="submit" value="<?= $blok['idb'] ? 'Ulož' : 'Přidej' ?>"></p>
</form>
