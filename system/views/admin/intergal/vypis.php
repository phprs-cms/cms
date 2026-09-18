<?php
/**
 * Média: vlevo složky a filtry, vpravo nahrávání a mřížka obrázků.
 *
 * @var PhpRS\Core\App $app
 * @var PhpRS\Admin\Moduly\Galerie $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $obrazky
 * @var int $strana
 * @var int $stran
 * @var int $celkem
 * @var string $limit
 * @var array{sekce: ?int, clanek: int, nepouzite: bool} $filtr
 * @var list<array<string, mixed>> $slozky
 * @var string|null $clanek  titulek článku, podle kterého se filtruje
 */
$aktivniSlozka = null;
foreach ($slozky as $s) {
    if ((int) $s['ids'] === $filtr['sekce']) {
        $aktivniSlozka = $s;
    }
}
$parametry = array_filter(['sekce' => $filtr['sekce'], 'clanek' => $filtr['clanek'] ?: null, 'nepouzite' => $filtr['nepouzite'] ? 1 : null], fn ($v): bool => $v !== null);
$jeVse = $filtr['sekce'] === null && $filtr['clanek'] === 0 && !$filtr['nepouzite'];
?>
<div class="media">
<nav class="media-slozky" aria-label="Složky">
	<a href="<?= e($modul->url()) ?>"<?= $jeVse ? ' class="aktivni"' : '' ?>>Všechna média</a>
	<a href="<?= e($modul->url('', ['sekce' => 0])) ?>"<?= $filtr['sekce'] === 0 ? ' class="aktivni"' : '' ?>>Nezařazené</a>
	<a href="<?= e($modul->url('', ['nepouzite' => 1])) ?>"<?= $filtr['nepouzite'] ? ' class="aktivni"' : '' ?>>Nepoužité v článcích</a>
	<strong>Složky</strong>
<?php foreach ($slozky as $s): ?>
	<a href="<?= e($modul->url('', ['sekce' => $s['ids']])) ?>"<?= $aktivniSlozka === $s ? ' class="aktivni"' : '' ?>><?= e($s['nazev']) ?> <small>(<?= (int) $s['pocet'] ?>)</small></a>
<?php endforeach ?>
	<form method="post" action="<?= e($modul->url('slozka')) ?>">
		<?= $csrf ?>
		<input class="textpole" type="text" name="nazev" placeholder="nová složka" maxlength="100" required aria-label="Název nové složky">
		<button class="navigace" type="submit">Přidat</button>
	</form>
</nav>

<div class="media-obsah">
<?php if ($clanek !== null): ?>
<p class="hlaska">Obrázky použité v článku „<?= e($clanek) ?>“. <a href="<?= e($modul->url()) ?>">Zobrazit všechna média</a></p>
<?php endif ?>
<?php if ($aktivniSlozka !== null): ?>
<div class="media-slozka-uprava">
	<form method="post" action="<?= e($modul->url('slozka')) ?>"><?= $csrf ?><input type="hidden" name="ids" value="<?= (int) $aktivniSlozka['ids'] ?>"><input class="textpole" type="text" name="nazev" value="<?= e($aktivniSlozka['nazev']) ?>" maxlength="100" required aria-label="Název složky"> <button class="navigace" type="submit">Přejmenovat</button></form>
<?php if ($app->auth()->isAdmin()): ?>
	<form method="post" action="<?= e($modul->url('slozka_smaz')) ?>" data-potvrdit="Smazat složku? Obrázky v ní zůstanou a přejdou mezi nezařazené."><?= $csrf ?><input type="hidden" name="ids" value="<?= (int) $aktivniSlozka['ids'] ?>"><button class="navigace" type="submit">Smazat složku</button></form>
<?php endif ?>
</div>
<?php endif ?>

<form class="nahravani" method="post" enctype="multipart/form-data" action="<?= e($modul->url('nahraj')) ?>" data-nahravani>
	<?= $csrf ?>
	<input type="hidden" name="sekce" value="<?= (int) ($aktivniSlozka['ids'] ?? 0) ?>">
	<label for="soubory"><strong>Nahrát obrázky<?= $aktivniSlozka !== null ? ' do složky „' . e($aktivniSlozka['nazev']) . '“' : '' ?></strong> – vyberte soubory, nebo je sem přetáhněte myší</label>
	<input type="file" id="soubory" name="soubory[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple required>
	<input class="tl" type="submit" value="Nahraj">
	<span class="napoveda">JPG, PNG, WebP nebo GIF, nejvýše <?= e($limit) ?> na soubor. Velké fotografie se samy zmenší na <?= PhpRS\Core\Obrazky::MAX_STRANA ?> px a odstraní se z nich údaje o poloze.</span>
</form>

<?php if ($obrazky === []): ?>
<p>Žádné obrázky.</p>
<?php else: ?>
<form method="post" action="<?= e($modul->url('hromadne')) ?>">
<?= $csrf ?>
<div class="galerie-mrizka">
<?php foreach ($obrazky as $o): ?>
	<figure class="galerie-polozka">
		<a href="<?= e($app->url($o['obr_poloha'])) ?>" target="_blank" rel="noopener"><img src="<?= e($app->url($o['nahl_poloha'])) ?>" alt="<?= e($o['nazev']) ?>" loading="lazy" width="<?= (int) $o['nahl_width'] ?>" height="<?= (int) $o['nahl_height'] ?>"></a>
		<figcaption>
			<strong title="<?= e($o['nazev']) ?>"><?= e($o['nazev'] !== '' ? $o['nazev'] : 'bez názvu') ?></strong>
			<span><?= (int) $o['obr_width'] ?>&times;<?= (int) $o['obr_height'] ?> &middot; <?= number_format($o['obr_vel'] / 1024, 0, ',', ' ') ?> kB &middot; <?= (int) $o['pouzito'] > 0 ? 'použito ' . (int) $o['pouzito'] . '&times;' : 'nepoužito' ?></span>
			<span><label><input type="checkbox" name="oznacene[]" value="<?= (int) $o['ido'] ?>"> označit</label> &middot; <a href="<?= e($modul->url('vypis', $parametry + ['uprav' => $o['ido'], 'strana' => $strana])) ?>#uprav">popis</a></span>
		</figcaption>
	</figure>
<?php endforeach ?>
</div>
<p class="media-hromadne">
	S označenými:
	<select name="do_sekce" aria-label="Cílová složka">
		<option value="0">– nezařazené –</option>
<?php foreach ($slozky as $s): ?>
		<option value="<?= (int) $s['ids'] ?>"><?= e($s['nazev']) ?></option>
<?php endforeach ?>
	</select>
	<button class="navigace" type="submit" name="provest" value="presun">Přesunout do složky</button>
	<button class="navigace" type="submit" name="provest" value="smaz" data-potvrdit="Opravdu smazat označené obrázky? Z článků, kde jsou použité, zmizí.">Smazat</button>
</p>
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
	<?= $s === $strana ? '<strong>[' . $s . ']</strong>' : '<a href="' . e($modul->url('', $parametry + ['strana' => $s])) . '">' . $s . '</a>' ?>
<?php endfor ?>
</p>
<?php endif ?>
<?php endif ?>
</div>
</div>
