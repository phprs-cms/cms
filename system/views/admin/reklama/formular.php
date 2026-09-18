<?php
/**
 * @var PhpRS\Admin\Moduly\Reklama $modul
 * @var string $csrf
 * @var array<string, mixed> $reklama
 * @var array<string, string> $chyby
 */
use PhpRS\Admin\Moduly\Reklama;

$chyba = fn (string $pole): string => isset($chyby[$pole]) ? '<span class="chyba-pole" role="alert">' . e($chyby[$pole]) . '</span>' : '';
$dt = fn (?string $v): string => $v ? date('Y-m-d\TH:i', strtotime($v)) : '';
?>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url()) ?>">Zpět na přehled</a></p>
<form class="formular" method="post" action="<?= e($modul->url('uloz')) ?>">
<?= $csrf ?>
<input type="hidden" name="idr" value="<?= (int) $reklama['idr'] ?>">
<div class="radek"><label for="nazev">Název</label><div><input class="textpole siroke" type="text" id="nazev" name="nazev" value="<?= e($reklama['nazev']) ?>" maxlength="150" required><?= $chyba('nazev') ?><span class="napoveda">Jen pro vás – klient a kampaň.</span></div></div>
<div class="radek"><label for="pozice">Pozice</label><select id="pozice" name="pozice">
<?php foreach (Reklama::POZICE as $klic => $nazev): ?>
	<option value="<?= e($klic) ?>"<?= $reklama['pozice'] === $klic ? ' selected' : '' ?>><?= e($nazev) ?></option>
<?php endforeach ?>
</select></div>
<div class="radek"><span class="popisek">Druh</span><div class="volby">
	<label><input type="radio" name="typ" value="obrazek"<?= $reklama['typ'] !== 'kod' ? ' checked' : '' ?>> Banner – obrázek s odkazem (počítají se i prokliky)</label><br>
	<label><input type="radio" name="typ" value="kod"<?= $reklama['typ'] === 'kod' ? ' checked' : '' ?>> Kód reklamní sítě (Sklik, Google AdSense…)</label>
</div></div>
<fieldset>
<legend>Banner</legend>
<div class="radek"><label for="obrazek">Obrázek</label><div><input class="textpole siroke" type="text" id="obrazek" name="obrazek" value="<?= e($reklama['obrazek']) ?>" maxlength="255" data-obrazek><?= $chyba('obrazek') ?></div></div>
<div class="radek"><label for="cil_url">Cílová adresa</label><input class="textpole siroke" type="url" id="cil_url" name="cil_url" value="<?= e($reklama['cil_url']) ?>" maxlength="500" placeholder="https://"></div>
</fieldset>
<fieldset>
<legend>Kód reklamní sítě</legend>
<div class="radek"><label for="kod">Kód</label><div><textarea class="textbox kod" id="kod" name="kod" rows="6" spellcheck="false"><?= e((string) $reklama['kod']) ?></textarea><?= $chyba('kod') ?><span class="napoveda">Kódy, které ukládají cookies, podléhají souhlasu návštěvníka – při zapnuté cookie liště se spustí až po souhlasu s marketingem.</span></div></div>
</fieldset>
<fieldset>
<legend>Kampaň</legend>
<div class="radek"><label for="platna_od">Zobrazovat od</label><input class="textpole" type="datetime-local" id="platna_od" name="platna_od" value="<?= e($dt($reklama['platna_od'])) ?>"></div>
<div class="radek"><label for="platna_do">Zobrazovat do</label><input class="textpole" type="datetime-local" id="platna_do" name="platna_do" value="<?= e($dt($reklama['platna_do'])) ?>"></div>
<div class="radek"><label for="max_zobrazeni">Strop zobrazení</label><div><input class="textpole" type="number" id="max_zobrazeni" name="max_zobrazeni" value="<?= e((string) $reklama['max_zobrazeni']) ?>" min="0" style="width:140px"><span class="napoveda">Nepovinné. Po dosažení se reklama přestane zobrazovat.</span></div></div>
<div class="radek"><label for="vaha">Váha</label><div><input class="textpole" type="number" id="vaha" name="vaha" value="<?= (int) $reklama['vaha'] ?>" min="1" max="10" style="width:90px"><span class="napoveda">1–10. Reklama s váhou 2 se na své pozici zobrazí dvakrát častěji než s váhou 1.</span></div></div>
<div class="radek"><span class="popisek">Stav</span><div class="volby"><label><input type="checkbox" name="aktivni" value="1"<?= $reklama['aktivni'] ? ' checked' : '' ?>> Reklama je zapnutá</label></div></div>
</fieldset>
<p class="tlacitka"><input class="tl" type="submit" value="Uložit"></p>
</form>
