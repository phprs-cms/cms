<?php
/**
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
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url()) ?>">Zpět na hlavní stránku sekce</a></p>

<form class="formular" method="post" action="<?= e($modul->url('uloz')) ?>">
<?= $csrf ?>
<input type="hidden" name="idc" value="<?= (int) $clanek['idc'] ?>">

<div class="radek">
	<span class="popisek">Volací link článku</span>
	<input class="textpole" type="text" value="<?= e($clanek['link']) ?>" placeholder="bude automaticky doplněn" size="22" disabled>
</div>
<div class="radek">
	<label for="titulek">Titulek článku</label>
	<div><input class="textpole siroke" type="text" id="titulek" name="titulek" value="<?= e($clanek['titulek']) ?>" maxlength="255" required><?= $chyba('titulek') ?></div>
</div>
<div class="radek">
	<label for="seo_link">Adresa článku (SEO link)</label>
	<div><input class="textpole siroke" type="text" id="seo_link" name="seo_link" value="<?= e($clanek['seo_link']) ?>" maxlength="150" placeholder="vytvoří se z titulku">
	<span class="napoveda">Jen malá písmena, číslice a pomlčky. Po vydání článku už adresu raději neměňte.</span></div>
</div>
<div class="radek pres-celou">
	<label for="uvod">Úvod</label>
	<div data-nastroje="uvod"></div>
	<textarea class="textbox" id="uvod" name="uvod" rows="7"><?= e($clanek['uvod']) ?></textarea>
	<span class="napoveda">Zobrazuje se v náhledu na hlavní stránce i na začátku celého článku - v hlavním textu ho neopakujte. HTML je povoleno.</span>
</div>
<div class="radek pres-celou">
	<label for="text">Hlavní text</label>
	<div data-nastroje="text"></div>
	<textarea class="textbox vysoky" id="text" name="text" rows="20"><?= e($clanek['text']) ?></textarea>
</div>
<div class="radek">
	<label for="obrazek">Hlavní obrázek</label>
	<div><input class="textpole siroke" type="text" id="obrazek" name="obrazek" value="<?= e($clanek['obrazek']) ?>" maxlength="255" placeholder="adresa obrázku, např. storage-public/2026/foto.jpg">
	<span class="napoveda">Nepovinné. Layout ho může použít v náhledu článku a pro sdílení na sociálních sítích.</span></div>
</div>

<fieldset>
<legend>Zařazení</legend>
<div class="radek">
	<label for="tema">Rubrika</label>
	<div><select id="tema" name="tema" required>
		<option value="">- vyberte -</option>
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
<div class="radek">
	<span class="popisek">Typ článku</span>
	<div class="volby">
		<label><input type="radio" name="typ_clanku" value="1"<?= (int) $clanek['typ_clanku'] !== 2 ? ' checked' : '' ?>> Dlouhý (standardní) - náhled + celý článek</label><br>
		<label><input type="radio" name="typ_clanku" value="2"<?= (int) $clanek['typ_clanku'] === 2 ? ' checked' : '' ?>> Krátký - jen úvod, bez samostatné stránky</label>
	</div>
</div>
<div class="radek">
	<label for="sablona">Šablona</label>
	<select id="sablona" name="sablona">
<?php foreach ($sablony as $ids => $nazev): ?>
		<option value="<?= (int) $ids ?>"<?= (int) $clanek['sablona'] === (int) $ids ? ' selected' : '' ?>><?= e($nazev) ?></option>
<?php endforeach ?>
	</select>
</div>
<div class="radek">
	<label for="t_slova">Klíčová slova</label>
	<div><input class="textpole siroke" type="text" id="t_slova" name="t_slova" value="<?= e($clanek['t_slova']) ?>" maxlength="500">
	<span class="napoveda">Slovní spojení odpovídající danému článku, oddělená čárkou. Používá je vyhledávání a meta keywords.</span></div>
</div>
<div class="radek">
	<label for="zdroj">Zdroj článku</label>
	<div><input class="textpole siroke" type="text" id="zdroj" name="zdroj" value="<?= e($clanek['zdroj']) ?>" maxlength="255">
	<span class="napoveda">U převzatých článků: odkud text pochází.</span></div>
</div>
</fieldset>

<fieldset>
<legend>Vydání</legend>
<div class="radek">
	<label for="datum">Datum vydání</label>
	<div><input class="textpole" type="datetime-local" id="datum" name="datum" value="<?= e($dt($clanek['datum'])) ?>" required>
	<span class="napoveda">Článek s budoucím datem se na webu objeví sám, až datum nastane.</span></div>
</div>
<div class="radek">
	<label for="datum_pl">Datum stažení</label>
	<div><input class="textpole" type="datetime-local" id="datum_pl" name="datum_pl" value="<?= e($dt($clanek['datum_pl'])) ?>">
	<span class="napoveda">Nepovinné. Po tomto datu článek zmizí z hlavní stránky; v rubrice a ve vyhledávání zůstává.</span></div>
</div>
<div class="radek">
	<span class="popisek">Vydat článek</span>
	<div class="volby">
		<label><input type="checkbox" name="visible" value="1"<?= $clanek['visible'] ? ' checked' : '' ?><?= $smiVydavat ? '' : ' disabled' ?>> Ano</label>
<?php if (!$smiVydavat): ?>
		<span class="napoveda">Nemáte právo vydávat. Článek po uložení vydá redaktor.</span>
<?php endif ?>
	</div>
</div>
<div class="radek">
	<span class="popisek">Zobrazit na hlavní stránce</span>
	<div class="volby"><label><input type="checkbox" name="zobr_na_indexu" value="1"<?= $clanek['zobr_na_indexu'] ? ' checked' : '' ?>> Ano</label></div>
</div>
<div class="radek">
	<label for="priority">Priorita</label>
	<div><input class="textpole" type="number" id="priority" name="priority" value="<?= (int) $clanek['priority'] ?>" min="0" max="255" style="width:70px">
	<span class="napoveda">0 = běžný článek. Článek s vyšší prioritou drží na hlavní stránce nahoře bez ohledu na datum.</span></div>
</div>
<div class="radek">
	<span class="popisek">Komentáře</span>
	<div class="volby"><label><input type="checkbox" name="povolit_kom" value="1"<?= $clanek['povolit_kom'] ? ' checked' : '' ?>> Povolit komentáře u článku</label></div>
</div>
<div class="radek">
	<span class="popisek">phpRS značky</span>
	<div class="volby"><label><input type="checkbox" name="znacky" value="1"<?= $clanek['znacky'] ? ' checked' : '' ?>> Zpracovávat značky v textu (&lt;obrazek id="…"&gt;)</label></div>
</div>
</fieldset>

<p class="tlacitka">
	<button class="tl" type="submit" name="po_ulozeni" value="vypis"><?= $clanek['idc'] ? 'Ulož' : 'Přidej' ?></button>
	<button class="tl" type="submit" name="po_ulozeni" value="zustat">Ulož a pokračuj v úpravách</button>
</p>
</form>
