<?php
/**
 * @var PhpRS\Admin\Moduly\Bloky $modul
 * @var string $csrf
 * @var array<string, mixed> $blok
 * @var array<string, string> $chyby
 * @var array<int, string> $sloupce
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
	<span class="napoveda">Zobrazuje se jako nadpis bloku.</span></div>
</div>
<div class="radek">
	<label for="sys_funkce">Druh bloku</label>
	<div><select id="sys_funkce" name="sys_funkce">
		<option value="">běžný blok - vlastní HTML</option>
<?php foreach (Bloky::SYSTEMOVE as $zkratka => $nazev): ?>
		<option value="<?= e($zkratka) ?>"<?= $blok['sys_funkce'] === $zkratka ? ' selected' : '' ?>>systémový: <?= e($nazev) ?></option>
<?php endforeach ?>
	</select><?= $chyba('sys_funkce') ?></div>
</div>
<div class="radek">
	<label for="obsah">Obsah bloku (HTML)</label>
	<div><textarea class="textbox kod" id="obsah" name="obsah" rows="10"><?= e($blok['obsah']) ?></textarea>
	<span class="napoveda">Jen u běžného bloku; systémový blok si obsah vykreslí sám.</span></div>
</div>
<div class="radek">
	<label for="id_sloupec">Sloupec</label>
	<div><select id="id_sloupec" name="id_sloupec">
<?php foreach ($sloupce as $ids => $nazev): ?>
		<option value="<?= (int) $ids ?>"<?= (int) $blok['id_sloupec'] === (int) $ids ? ' selected' : '' ?>><?= e($nazev) ?></option>
<?php endforeach ?>
	</select><?= $chyba('id_sloupec') ?></div>
</div>
<div class="radek">
	<label for="hodnost">Priorita</label>
	<div><input class="textpole" type="number" id="hodnost" name="hodnost" value="<?= (int) $blok['hodnost'] ?>" min="0" max="65535" style="width:80px">
	<span class="napoveda">Vyšší číslo = výš ve sloupci.</span></div>
</div>
<div class="radek">
	<label for="typ">Vzhled bloku</label>
	<div><select id="typ" name="typ">
<?php for ($t = 1; $t <= 5; $t++): ?>
		<option value="<?= $t ?>"<?= (int) $blok['typ'] === $t ? ' selected' : '' ?>>typ <?= $t ?></option>
<?php endfor ?>
	</select>
	<span class="napoveda">Layout webu podle typu blok obarví (CSS třída blok-typ1 až blok-typ5).</span></div>
</div>
<div class="radek">
	<span class="popisek">Zobrazit blok</span>
	<div class="volby"><label><input type="checkbox" name="zobrazit" value="1"<?= $blok['zobrazit'] ? ' checked' : '' ?>> Ano</label></div>
</div>
<div class="radek">
	<label for="zobrazit_kde">Kde zobrazit</label>
	<select id="zobrazit_kde" name="zobrazit_kde">
<?php foreach (Bloky::KDE as $hodnota => $popis): ?>
		<option value="<?= $hodnota ?>"<?= (int) $blok['zobrazit_kde'] === $hodnota ? ' selected' : '' ?>><?= e($popis) ?></option>
<?php endforeach ?>
	</select>
</div>
<p class="tlacitka"><input class="tl" type="submit" value="<?= $blok['idb'] ? 'Ulož' : 'Přidej' ?>"></p>
</form>
