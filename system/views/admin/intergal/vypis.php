<?php
/**
 * @var PhpRS\Core\App $app
 * @var PhpRS\Admin\Moduly\Galerie $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $obrazky
 * @var int $strana
 * @var int $stran
 * @var string $limit
 */
?>
<form class="nahravani" method="post" enctype="multipart/form-data" action="<?= e($modul->url('nahraj')) ?>" data-nahravani>
	<?= $csrf ?>
	<label for="soubory"><strong>Nahrát obrázky</strong> – vyberte soubory, nebo je sem přetáhněte myší</label>
	<input type="file" id="soubory" name="soubory[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple required>
	<input class="tl" type="submit" value="Nahraj">
	<span class="napoveda">JPG, PNG, WebP nebo GIF, nejvýše <?= e($limit) ?> na soubor. Velké fotografie se samy zmenší na <?= PhpRS\Core\Obrazky::MAX_STRANA ?> px a odstraní se z nich údaje o poloze.</span>
</form>

<?php if ($obrazky === []): ?>
<p class="stred">V galerii zatím nejsou žádné obrázky.</p>
<?php else: ?>
<form method="post" action="<?= e($modul->url('smaz')) ?>" onsubmit="return confirm('Opravdu vymazat všechny označené obrázky? Z článků, kde jsou použité, zmizí.');">
<?= $csrf ?>
<div class="galerie-mrizka">
<?php foreach ($obrazky as $o): ?>
	<figure class="galerie-polozka">
		<a href="<?= e($app->url($o['obr_poloha'])) ?>" target="_blank" rel="noopener"><img src="<?= e($app->url($o['nahl_poloha'])) ?>" alt="<?= e($o['nazev']) ?>" loading="lazy" width="<?= (int) $o['nahl_width'] ?>" height="<?= (int) $o['nahl_height'] ?>"></a>
		<figcaption>
			<strong title="<?= e($o['nazev']) ?>"><?= e($o['nazev'] !== '' ? $o['nazev'] : 'bez názvu') ?></strong>
			<span><?= (int) $o['obr_width'] ?>&times;<?= (int) $o['obr_height'] ?> &middot; <?= number_format($o['obr_vel'] / 1024, 0, ',', ' ') ?> kB &middot; id <?= (int) $o['ido'] ?></span>
			<span><a href="<?= e($modul->url('vypis', ['uprav' => $o['ido'], 'strana' => $strana])) ?>#uprav">Popis</a> &middot; <label><input type="checkbox" name="smaz[]" value="<?= (int) $o['ido'] ?>"> smazat</label></span>
		</figcaption>
	</figure>
<?php endforeach ?>
</div>
<p class="stred"><input class="tl" type="submit" value="Vymaž všechny označené obrázky"></p>
</form>

<?php foreach ($obrazky as $o): if ((int) $o['ido'] !== $app->request->getInt('uprav')) { continue; } ?>
<form class="formular" id="uprav" method="post" action="<?= e($modul->url('uloz')) ?>">
	<?= $csrf ?>
	<input type="hidden" name="ido" value="<?= (int) $o['ido'] ?>">
	<div class="radek"><label for="nazev">Název (alternativní text)</label><div><input class="textpole siroke" type="text" id="nazev" name="nazev" value="<?= e($o['nazev']) ?>" maxlength="150"><span class="napoveda">Popište, co na obrázku je - čtou ho čtečky obrazovky i vyhledávače.</span></div></div>
	<div class="radek"><label for="popis">Popisek pod obrázkem</label><input class="textpole siroke" type="text" id="popis" name="popis" value="<?= e($o['popis']) ?>" maxlength="500"></div>
	<p class="tlacitka"><input class="tl" type="submit" value="Ulož"></p>
</form>
<?php endforeach ?>

<?php if ($stran > 1): ?>
<p class="strankovani">
<?php for ($s = 1; $s <= $stran; $s++): ?>
	<?= $s === $strana ? '<strong>[' . $s . ']</strong>' : '<a href="' . e($modul->url('', ['strana' => $s])) . '">' . $s . '</a>' ?>
<?php endfor ?>
</p>
<?php endif ?>
<?php endif ?>
