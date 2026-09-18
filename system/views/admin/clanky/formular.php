<?php
/**
 * Editor článku: vlevo text, vpravo nastavení (v retro prostředí pod sebou).
 *
 * @var PhpRS\Admin\Moduly\Clanky $modul
 * @var string $csrf
 * @var array<string, mixed> $clanek
 * @var array<string, string> $chyby
 * @var list<array<string, mixed>> $rubriky
 * @var array<int, string> $autori
 * @var array<int, string> $sablony
 * @var bool $smiVydavat
 */
$dt = fn (?string $v): string => $v ? date('Y-m-d\TH:i', strtotime($v)) : '';
$chyba = fn (string $pole): string => isset($chyby[$pole]) ? '<span class="chyba-pole" role="alert">' . e($chyby[$pole]) . '</span>' : '';
?>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url()) ?>">Zpět na přehled článků</a></p>

<form class="formular formular-clanek" method="post" action="<?= e($modul->url('uloz')) ?>" data-koncept="clanek-<?= (int) $clanek['idc'] ?>">
<?= $csrf ?>
<input type="hidden" name="idc" value="<?= (int) $clanek['idc'] ?>">

<div class="clanek-hlavni">
	<div class="radek pres-celou">
		<label for="titulek">Titulek</label>
		<input class="textpole siroke titulek-pole" type="text" id="titulek" name="titulek" value="<?= e($clanek['titulek']) ?>" maxlength="255" required placeholder="Titulek článku"><?= $chyba('titulek') ?>
	</div>
	<div class="radek pres-celou">
		<label for="uvod">Perex (úvod)</label>
		<textarea class="textbox" id="uvod" name="uvod" rows="7" data-editor="maly"><?= e($clanek['uvod']) ?></textarea>
		<span class="napoveda">Zobrazuje se ve výpisech i na začátku článku – v textu ho neopakujte.</span>
	</div>
	<div class="radek pres-celou">
		<label for="text">Text článku</label>
		<textarea class="textbox vysoky" id="text" name="text" rows="20" data-editor><?= e($clanek['text']) ?></textarea>
	</div>
</div>

<aside class="clanek-nastaveni">
<fieldset>
<legend>Vydání</legend>
<div class="radek">
	<label for="stav">Stav</label>
	<div><select id="stav" name="stav"<?= $smiVydavat ? '' : ' disabled' ?>>
		<option value="koncept"<?= $clanek['visible'] ? '' : ' selected' ?>>Koncept – na webu není vidět</option>
		<option value="vydany"<?= $clanek['visible'] ? ' selected' : '' ?>>Vydaný</option>
	</select>
<?php if (!$smiVydavat): ?>
	<span class="napoveda">Nemáte právo vydávat. Článek po uložení vydá redaktor.</span>
<?php endif ?>
	</div>
</div>
<div class="radek">
	<label for="datum">Datum vydání</label>
	<div><input class="textpole" type="datetime-local" id="datum" name="datum" value="<?= e($dt($clanek['datum'])) ?>" required>
	<span class="napoveda">Budoucí datum = článek se vydá sám v daný čas.</span></div>
</div>
<div class="radek">
	<span class="popisek">Hlavní stránka</span>
	<div class="volby">
		<label><input type="checkbox" name="zobr_na_indexu" value="1"<?= $clanek['zobr_na_indexu'] ? ' checked' : '' ?>> Zobrazit na hlavní stránce</label><br>
		<label><input type="checkbox" name="pripnout" value="1"<?= $clanek['priority'] > 0 ? ' checked' : '' ?>> Připnout nahoru (otvírák)</label>
	</div>
</div>
<p class="tlacitka">
	<button class="tl" type="submit" name="po_ulozeni" value="vypis">Uložit</button>
	<button class="tl" type="submit" name="po_ulozeni" value="zustat">Uložit a pokračovat</button>
<?php if ($clanek['idc']): ?>
	<a class="navigace" href="<?= e($modul->app()->url('clanek/' . $clanek['seo_link'] . '?nahled=1')) ?>" target="_blank" rel="noopener">Náhled</a>
<?php endif ?>
</p>
</fieldset>

<fieldset>
<legend>Zařazení</legend>
<div class="radek">
	<label for="tema">Rubrika</label>
	<div><select id="tema" name="tema" required>
		<option value="">– vyberte –</option>
<?php foreach ($rubriky as $r): ?>
		<option value="<?= (int) $r['idt'] ?>"<?= (int) $clanek['tema'] === (int) $r['idt'] ? ' selected' : '' ?>><?= str_repeat('&nbsp;&nbsp;', $r['uroven']) . e($r['nazev']) ?></option>
<?php endforeach ?>
	</select><?= $chyba('tema') ?></div>
</div>
<div class="radek">
	<label for="autor">Autor</label>
	<div><select id="autor" name="autor">
<?php foreach ($autori as $idu => $jmeno): ?>
		<option value="<?= (int) $idu ?>"<?= (int) $clanek['autor'] === (int) $idu ? ' selected' : '' ?>><?= e($jmeno) ?></option>
<?php endforeach ?>
	</select><?= $chyba('autor') ?></div>
</div>
</fieldset>

<fieldset>
<legend>Hlavní obrázek</legend>
<div class="radek pres-celou">
	<input class="textpole siroke" type="text" id="obrazek" name="obrazek" value="<?= e($clanek['obrazek']) ?>" maxlength="255" placeholder="vyberte z médií, nebo vložte adresu" aria-label="Hlavní obrázek" data-obrazek>
	<span class="napoveda">Použije se ve výpisech a při sdílení na sociálních sítích.<?php if ($clanek['idc']): ?> <a href="<?= e($modul->app()->url('admin.php?modul=intergal&clanek=' . (int) $clanek['idc'])) ?>" target="_blank" rel="noopener">Média použitá v článku</a><?php endif ?></span>
</div>
</fieldset>

<details class="pokrocile"<?= $clanek['datum_pl'] || $clanek['zdroj'] !== '' || $clanek['t_slova'] !== '' || (int) $clanek['typ_clanku'] === 2 ? ' open' : '' ?>>
<summary>Další nastavení</summary>
<div class="radek">
	<label for="seo_link">Adresa článku</label>
	<div><input class="textpole siroke" type="text" id="seo_link" name="seo_link" value="<?= e($clanek['seo_link']) ?>" maxlength="150" placeholder="vytvoří se z titulku">
	<span class="napoveda">Část adresy za /clanek/. Po vydání ji raději neměňte.</span></div>
</div>
<div class="radek">
	<label for="t_slova">Klíčová slova</label>
	<div><input class="textpole siroke" type="text" id="t_slova" name="t_slova" value="<?= e($clanek['t_slova']) ?>" maxlength="500">
	<span class="napoveda">Oddělená čárkou; pomáhají vyhledávání na webu.</span></div>
</div>
<div class="radek">
	<label for="zdroj">Zdroj</label>
	<div><input class="textpole siroke" type="text" id="zdroj" name="zdroj" value="<?= e($clanek['zdroj']) ?>" maxlength="255">
	<span class="napoveda">U převzatých textů: odkud pocházejí.</span></div>
</div>
<div class="radek">
	<label for="datum_pl">Stáhnout z hlavní stránky</label>
	<div><input class="textpole" type="datetime-local" id="datum_pl" name="datum_pl" value="<?= e($dt($clanek['datum_pl'])) ?>">
	<span class="napoveda">Nepovinné. V rubrice a ve vyhledávání článek zůstane.</span></div>
</div>
<div class="radek">
	<span class="popisek">Možnosti</span>
	<div class="volby">
		<label><input type="checkbox" name="povolit_kom" value="1"<?= $clanek['povolit_kom'] ? ' checked' : '' ?>> Povolit komentáře</label><br>
		<label><input type="checkbox" name="kratky" value="1"<?= (int) $clanek['typ_clanku'] === 2 ? ' checked' : '' ?>> Krátká zpráva – jen perex, bez vlastní stránky</label>
	</div>
</div>
<?php if (count($sablony) > 1): ?>
<div class="radek">
	<label for="sablona">Šablona článku</label>
	<select id="sablona" name="sablona">
<?php foreach ($sablony as $ids => $nazev): ?>
		<option value="<?= (int) $ids ?>"<?= (int) $clanek['sablona'] === (int) $ids ? ' selected' : '' ?>><?= e($nazev) ?></option>
<?php endforeach ?>
	</select>
</div>
<?php else: ?>
<input type="hidden" name="sablona" value="<?= (int) (array_key_first($sablony) ?? 0) ?>">
<?php endif ?>
<?php if ($clanek['link'] !== ''): ?>
<p class="napoveda">Číslo článku: <?= e($clanek['link']) ?> (stará adresa phpRS: view.php?cisloclanku=<?= e($clanek['link']) ?>)</p>
<?php endif ?>
</details>
</aside>
</form>
