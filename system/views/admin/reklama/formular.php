<?php
/**
 * Reklama: co se zobrazí, kde - a volitelně kdy. Pole druhého druhu reklamy se schovají (admin.js, data-pro).
 *
 * @var PhpRS\Admin\Moduly\Reklama $modul
 * @var string $csrf
 * @var array<string, mixed> $reklama
 * @var array<string, string> $chyby
 */
$chyba = fn (string $pole): string => isset($chyby[$pole]) ? '<span class="chyba-pole" role="alert">' . e($chyby[$pole]) . '</span>' : '';
$dt = fn (?string $v): string => $v ? date('Y-m-d\TH:i', strtotime($v)) : '';
$pozice = [
    'sloupec' => ['Ve sloupci', 'Čtverec nebo obdélník v bočním sloupci, např. 300×250.'],
    'pod-clankem' => ['Pod článkem', 'Pod každým článkem – zobrazuje se sama, blok není potřeba.'],
    'hlavicka' => ['V hlavičce', 'Široký pruh nahoře, např. 970×210.'],
    'paticka' => ['V patičce', 'Široký pruh dole.'],
];
$planovani = $reklama['platna_od'] || $reklama['platna_do'] || $reklama['max_zobrazeni'] !== null || (int) $reklama['vaha'] !== 1 || !$reklama['aktivni'];
?>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url()) ?>">Zpět na přehled</a></p>
<form class="formular" method="post" action="<?= e($modul->url('uloz')) ?>" data-prepinac="typ">
<?= $csrf ?>
<input type="hidden" name="idr" value="<?= (int) $reklama['idr'] ?>">
<div class="radek">
	<label for="nazev">Název</label>
	<div><input class="textpole siroke" type="text" id="nazev" name="nazev" value="<?= e($reklama['nazev']) ?>" maxlength="150" required placeholder="např. Knihkupectví – podzimní akce"><?= $chyba('nazev') ?><span class="napoveda">Jen pro vás, na webu se neukazuje.</span></div>
</div>

<fieldset>
<legend>Co se má zobrazit</legend>
<div class="karty-volby karty-volby-text">
	<label class="karta-volba"><input type="radio" name="typ" value="obrazek"<?= $reklama['typ'] !== 'kod' ? ' checked' : '' ?>><strong>Banner</strong><span>Váš obrázek s odkazem. Počítají se zobrazení i prokliky.</span></label>
	<label class="karta-volba"><input type="radio" name="typ" value="kod"<?= $reklama['typ'] === 'kod' ? ' checked' : '' ?>><strong>Kód reklamní sítě</strong><span>Sklik, Google AdSense a podobně – vložíte kód, který vám síť dala.</span></label>
</div>
<div class="radek" data-pro="obrazek"><label for="obrazek">Obrázek</label><div><input class="textpole siroke" type="text" id="obrazek" name="obrazek" value="<?= e($reklama['obrazek']) ?>" maxlength="255" data-obrazek><?= $chyba('obrazek') ?></div></div>
<div class="radek" data-pro="obrazek"><label for="cil_url">Kam banner vede</label><input class="textpole siroke" type="url" id="cil_url" name="cil_url" value="<?= e($reklama['cil_url']) ?>" maxlength="500" placeholder="https://"></div>
<div class="radek" data-pro="kod"><label for="kod">Kód</label><div><textarea class="textbox kod" id="kod" name="kod" rows="6" spellcheck="false"><?= e((string) $reklama['kod']) ?></textarea><?= $chyba('kod') ?><span class="napoveda">Při zapnuté cookie liště se spustí až po souhlasu návštěvníka s marketingem.</span></div></div>
</fieldset>

<fieldset>
<legend>Kde</legend>
<div class="karty-volby karty-volby-text">
<?php foreach ($pozice as $klic => [$nazev, $popis]): ?>
	<label class="karta-volba"><input type="radio" name="pozice" value="<?= e($klic) ?>"<?= $reklama['pozice'] === $klic ? ' checked' : '' ?>><strong><?= e($nazev) ?></strong><span><?= e($popis) ?></span></label>
<?php endforeach ?>
</div>
<p class="napoveda">Pozice ve sloupci, hlavičce a patičce umístíte na web blokem „Reklama“ (Bloky a rozvržení → Přidat blok).</p>
</fieldset>

<details class="pokrocile"<?= $planovani ? ' open' : '' ?>>
<summary>Plánování a limity</summary>
<div class="radek"><label for="platna_od">Zobrazovat od</label><input class="textpole" type="datetime-local" id="platna_od" name="platna_od" value="<?= e($dt($reklama['platna_od'])) ?>"></div>
<div class="radek"><label for="platna_do">Zobrazovat do</label><input class="textpole" type="datetime-local" id="platna_do" name="platna_do" value="<?= e($dt($reklama['platna_do'])) ?>"></div>
<div class="radek"><label for="max_zobrazeni">Nejvýše zobrazení</label><div><input class="textpole" type="number" id="max_zobrazeni" name="max_zobrazeni" value="<?= e((string) $reklama['max_zobrazeni']) ?>" min="0" style="width:140px"><span class="napoveda">Po dosažení se reklama vypne sama.</span></div></div>
<div class="radek"><label for="vaha">Váha</label><div><input class="textpole" type="number" id="vaha" name="vaha" value="<?= (int) $reklama['vaha'] ?>" min="1" max="10" style="width:90px"><span class="napoveda">Když je na pozici víc reklam: váha 2 = zobrazí se dvakrát častěji než váha 1.</span></div></div>
<div class="radek"><span class="popisek">Stav</span><div class="volby"><label><input type="checkbox" name="aktivni" value="1"<?= $reklama['aktivni'] ? ' checked' : '' ?>> reklama je zapnutá</label></div></div>
</details>
<p class="tlacitka"><input class="tl" type="submit" value="Uložit"></p>
</form>
