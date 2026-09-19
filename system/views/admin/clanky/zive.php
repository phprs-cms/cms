<?php
/**
 * Živá reportáž: psaní průběžných zápisů.
 *
 * @var PhpRS\Admin\Moduly\Clanky $modul
 * @var string $csrf
 * @var array<string, mixed> $clanek
 * @var list<array<string, mixed>> $zapisy
 */
$bezi = (int) $clanek['zive'] === 1;
?>
<p class="navigace-radek">
	<a class="navigace" href="<?= e($modul->url('edit', ['id' => (int) $clanek['idc']])) ?>">Zpět do článku</a>
	<a class="navigace" href="<?= e($modul->app()->url('clanek/' . $clanek['seo_link']) . ($clanek['visible'] ? '' : '?nahled=1')) ?>" target="_blank" rel="noopener">Zobrazit na webu</a>
</p>
<h3><?= e($clanek['titulek']) ?> <span class="stitek stitek-<?= $bezi ? 'vydano' : 'koncept' ?>"><?= $bezi ? 'běží' : 'neběží' ?></span></h3>
<?php if (!$clanek['visible']): ?>
<p class="hlaska">Článek zatím není vydaný – zápisy uvidí čtenáři až po vydání.</p>
<?php endif ?>
<form class="formular" method="post" action="<?= e($modul->url('zive')) ?>">
<?= $csrf ?>
<input type="hidden" name="idc" value="<?= (int) $clanek['idc'] ?>">
<div class="radek pres-celou">
	<label for="text">Nový zápis</label>
	<textarea class="textbox" id="text" name="text" rows="5" data-editor="maly"></textarea>
</div>
<p class="tlacitka">
	<input class="tl" type="submit" value="Zveřejnit zápis">
	<label><input type="checkbox" name="dulezite" value="1"> Důležitý – zvýraznit</label>
</p>
</form>
<form method="post" action="<?= e($modul->url('zive')) ?>"<?= $bezi ? ' data-potvrdit="Ukončit živou reportáž? Zápisy na webu zůstanou, jen se přestanou načítat nové."' : '' ?>>
<?= $csrf ?><input type="hidden" name="idc" value="<?= (int) $clanek['idc'] ?>"><input type="hidden" name="stav" value="<?= $bezi ? 'ukoncit' : 'spustit' ?>">
<p><button class="navigace" type="submit"><?= $bezi ? 'Ukončit reportáž' : 'Spustit reportáž' ?></button> <span class="smltxt">Čtenářům se nové zápisy načítají samy každých 30 vteřin.</span></p>
</form>
<?php if ($zapisy !== []): ?>
<div class="tab-obal"><table class="vypis">
<thead><tr><th>Čas</th><th>Zápis</th><th>Autor</th><th>Akce</th></tr></thead>
<tbody>
<?php foreach ($zapisy as $z): ?>
<tr>
	<td class="cislo"><?= e(datum($z['cas'], true)) ?></td>
	<td><?= $z['dulezite'] ? '<strong>' : '' ?><?= e(mb_strimwidth(trim(strip_tags($z['text'])), 0, 160, '…')) ?><?= $z['dulezite'] ? '</strong>' : '' ?></td>
	<td><?= e((string) $z['autor_jm']) ?></td>
	<td class="akce"><form method="post" action="<?= e($modul->url('zive')) ?>" style="display:inline" data-potvrdit="Smazat zápis?"><?= $csrf ?><input type="hidden" name="idc" value="<?= (int) $clanek['idc'] ?>"><input type="hidden" name="smazat" value="<?= (int) $z['idz'] ?>"><button class="navigace" type="submit">Smaž</button></form></td>
</tr>
<?php endforeach ?>
</tbody></table></div>
<?php endif ?>
