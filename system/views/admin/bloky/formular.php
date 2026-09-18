<?php
/**
 * @var PhpRS\Admin\Moduly\Bloky $modul
 * @var string $csrf
 * @var array<string, mixed> $blok
 * @var array<string, string> $chyby
 * @var array<string, string> $zony  zóny dostupné ve zvoleném rozvržení
 * @var list<array<string, mixed>> $rubriky
 */
use PhpRS\Admin\Moduly\Bloky;
use PhpRS\Admin\Moduly\Reklama;

$chyba = fn (string $pole): string => isset($chyby[$pole]) ? '<span class="chyba-pole" role="alert">' . e($chyby[$pole]) . '</span>' : '';
$data = (string) ($blok['data_sys'] ?? '');
[$dataRubrika, $dataPocet] = $blok['sys_funkce'] === 'cla' ? array_map(intval(...), explode(':', $data . ':5')) : [0, (int) $data ?: 5];
$rubrikySelect = function (string $name, int $vybrana, string $prazdna) use ($rubriky): void { ?>
	<select id="<?= e($name) ?>" name="<?= e($name) ?>">
		<option value="0"><?= e($prazdna) ?></option>
<?php foreach ($rubriky as $r): ?>
		<option value="<?= (int) $r['idt'] ?>"<?= $vybrana === (int) $r['idt'] ? ' selected' : '' ?>><?= str_repeat('&nbsp;&nbsp;', $r['uroven']) . e($r['nazev']) ?></option>
<?php endforeach ?>
	</select>
<?php };
?>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url()) ?>">Zpět na přehled</a></p>
<form class="formular" method="post" action="<?= e($modul->url('uloz')) ?>" data-blok-formular>
<?= $csrf ?>
<input type="hidden" name="idb" value="<?= (int) $blok['idb'] ?>">
<div class="radek">
	<label for="sys_funkce">Typ bloku</label>
	<select id="sys_funkce" name="sys_funkce">
		<option value="">Vlastní obsah (HTML, vložený kód, video…)</option>
<?php foreach (Bloky::SYSTEMOVE as $zkratka => $nazev): ?>
		<option value="<?= e($zkratka) ?>"<?= $blok['sys_funkce'] === $zkratka ? ' selected' : '' ?>><?= e($nazev) ?></option>
<?php endforeach ?>
	</select>
</div>
<div class="radek">
	<label for="nazev">Nadpis bloku</label>
	<div><input class="textpole siroke" type="text" id="nazev" name="nazev" value="<?= e($blok['nazev']) ?>" maxlength="100" required><?= $chyba('nazev') ?></div>
</div>
<div class="radek" data-pro="">
	<label for="obsah">Vlastní obsah (HTML)</label>
	<textarea class="textbox kod" id="obsah" name="obsah" rows="10"><?= e($blok['obsah']) ?></textarea>
</div>
<div class="radek" data-pro="men">
	<label for="obsah-menu">Odkazy menu</label>
	<div><textarea class="textbox" id="obsah-menu" name="obsah_menu" rows="6" style="min-height:110px" placeholder="O nás | /o-nas&#10;Inzerce | /inzerce&#10;Facebook | https://facebook.com/…"><?= $blok['sys_funkce'] === 'men' ? e($blok['obsah']) : '' ?></textarea>
	<span class="napoveda">Každý odkaz na vlastní řádek ve tvaru: text | adresa.</span></div>
</div>
<div class="radek" data-pro="cla">
	<label for="blok_rubrika">Rubrika</label>
	<div><?php $rubrikySelect('blok_rubrika', $dataRubrika, '– nejnovější ze všech rubrik –') ?></div>
</div>
<div class="radek" data-pro="cla nej sti aut arc">
	<label for="blok_pocet">Počet položek</label>
	<input class="textpole" type="number" id="blok_pocet" name="blok_pocet" value="<?= $dataPocet ?>" min="1" max="50" style="width:90px">
</div>
<div class="radek" data-pro="rek">
	<label for="data_sys">Reklamní pozice</label>
	<select id="data_sys" name="data_sys">
<?php foreach (Reklama::POZICE as $klic => $nazev): if ($klic === 'pod-clankem') { continue; } ?>
		<option value="<?= e($klic) ?>"<?= $data === $klic ? ' selected' : '' ?>><?= e($nazev) ?></option>
<?php endforeach ?>
	</select>
</div>

<fieldset>
<legend>Umístění a zobrazení</legend>
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
	<label for="zobrazit_kde">Na kterých stránkách</label>
	<select id="zobrazit_kde" name="zobrazit_kde">
<?php foreach (Bloky::KDE as $hodnota => $popis): ?>
		<option value="<?= $hodnota ?>"<?= (int) $blok['zobrazit_kde'] === $hodnota ? ' selected' : '' ?>><?= e($popis) ?></option>
<?php endforeach ?>
	</select>
</div>
<div class="radek">
	<label for="jen_rubrika">Jen v rubrice</label>
	<div><?php $rubrikySelect('jen_rubrika', (int) ($blok['jen_rubrika'] ?? 0), '– ve všech –') ?>
	<span class="napoveda">Blok se ukáže jen na stránce rubriky a u jejích článků – např. partner sportovní rubriky.</span></div>
</div>
<div class="radek">
	<label for="zarizeni">Zařízení</label>
	<select id="zarizeni" name="zarizeni">
<?php foreach (Bloky::ZARIZENI as $klic => $popis): ?>
		<option value="<?= e($klic) ?>"<?= ($blok['zarizeni'] ?? 'vse') === $klic ? ' selected' : '' ?>><?= e($popis) ?></option>
<?php endforeach ?>
	</select>
</div>
<div class="radek">
	<span class="popisek">Zobrazit blok</span>
	<div class="volby"><label><input type="checkbox" name="zobrazit" value="1"<?= $blok['zobrazit'] ? ' checked' : '' ?>> Ano</label></div>
</div>
</fieldset>
<p class="tlacitka"><input class="tl" type="submit" value="<?= $blok['idb'] ? 'Ulož' : 'Přidej' ?>"></p>
</form>
