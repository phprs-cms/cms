<?php
/**
 * @var PhpRS\Admin\Moduly\Ankety $modul
 * @var string $csrf
 * @var array<string, mixed> $anketa
 * @var list<array<string, mixed>> $odpovedi
 * @var bool $aktivni
 */
?>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url()) ?>">Zpět na přehled</a></p>
<form class="formular" method="post" action="<?= e($modul->url('uloz')) ?>">
<?= $csrf ?>
<input type="hidden" name="ida" value="<?= (int) $anketa['ida'] ?>">
<div class="radek"><label for="otazka">Otázka</label><input class="textpole siroke" type="text" id="otazka" name="otazka" value="<?= e($anketa['otazka']) ?>" maxlength="255" required></div>
<div class="radek">
	<span class="popisek">Odpovědi</span>
	<div>
<?php foreach ($odpovedi as $o): ?>
		<p style="margin:0 0 6px"><input class="textpole" type="text" name="odpoved[<?= (int) $o['ido'] ?>]" value="<?= e($o['odpoved']) ?>" maxlength="255" style="width:70%" aria-label="Odpověď"> <small><?= (int) $o['pocitadlo'] ?> hlasů</small></p>
<?php endforeach ?>
<?php for ($i = 0; $i < ($odpovedi === [] ? 4 : 2); $i++): ?>
		<p style="margin:0 0 6px"><input class="textpole" type="text" name="nova[]" maxlength="255" style="width:70%" placeholder="nová odpověď" aria-label="Nová odpověď"></p>
<?php endfor ?>
		<span class="napoveda">Odpověď smažete vymazáním jejího textu. Další pole pro odpovědi přibudou po uložení.</span>
	</div>
</div>
<div class="radek">
	<span class="popisek">Zobrazení</span>
	<div class="volby">
		<label><input type="checkbox" name="aktivni" value="1"<?= $aktivni ? ' checked' : '' ?>> Zobrazit tuto anketu na webu (nahradí dosavadní)</label><br>
		<label><input type="checkbox" name="zobrazit" value="1"<?= $anketa['zobrazit'] ? ' checked' : '' ?>> Anketa je veřejná</label><br>
		<label><input type="checkbox" name="uzavrena" value="1"<?= $anketa['uzavrena'] ? ' checked' : '' ?>> Uzavřít hlasování – zobrazovat jen výsledky</label>
	</div>
</div>
<p class="tlacitka"><input class="tl" type="submit" value="Uložit"></p>
</form>
