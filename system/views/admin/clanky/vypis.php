<?php
/**
 * @var PhpRS\Core\App $app
 * @var PhpRS\Admin\Moduly\Clanky $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $clanky
 * @var int $celkem
 * @var int $strana
 * @var int $stran
 * @var list<array<string, mixed>> $rubriky
 * @var array{tema:int, hledat:string, moje:string} $filtr
 */
$strankaUrl = fn (int $s): string => $modul->url('', array_filter($filtr) + ['strana' => $s]);
?>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url('novy')) ?>">Přidat nový článek</a></p>

<form method="get" action="<?= e($app->url('admin.php')) ?>" class="stred smltxt">
	<input type="hidden" name="modul" value="clanky">
	<label>Rubrika:
		<select name="tema">
			<option value="0">všechny</option>
<?php foreach ($rubriky as $r): ?>
			<option value="<?= (int) $r['idt'] ?>"<?= $filtr['tema'] === (int) $r['idt'] ? ' selected' : '' ?>><?= str_repeat('&nbsp;&nbsp;', $r['uroven']) . e($r['nazev']) ?></option>
<?php endforeach ?>
		</select>
	</label>
	<label>Titulek obsahuje: <input class="textpole" type="search" name="hledat" value="<?= e($filtr['hledat']) ?>" size="20"></label>
	<label><input type="checkbox" name="moje" value="1"<?= $filtr['moje'] === '1' ? ' checked' : '' ?>> Zobrazit pouze mé články</label>
	<input class="tl" type="submit" value="Zobraz články">
	(Celkový počet článků: <?= $celkem ?>)
</form>
<br>

<?php if ($clanky === []): ?>
<p class="stred">Žádné články.</p>
<?php else: ?>
<form method="post" action="<?= e($modul->url('smaz')) ?>" onsubmit="return confirm('Opravdu vymazat všechny označené články?');">
<?= $csrf ?>
<div class="tab-obal">
<table class="vypis">
<thead>
<tr><th>Link</th><th>Titulek</th><th>Rubrika</th><th>Autor</th><th>Datum vydání</th><th>Vydán</th><th>Čteno</th><th>Akce</th><th>Smaž</th></tr>
</thead>
<tbody>
<?php foreach ($clanky as $c): ?>
<tr<?= $c['visible'] ? '' : ' class="nevydany"' ?>>
	<td class="cislo"><?= e($c['link']) ?></td>
	<td><a href="<?= e($modul->url('edit', ['id' => $c['idc']])) ?>"><?= e($c['titulek']) ?></a><?= $c['priority'] > 0 ? ' <span title="priorita">[' . (int) $c['priority'] . ']</span>' : '' ?></td>
	<td><?= e($c['tema_jm']) ?></td>
	<td><?= e($c['autor_jm'] ?: $c['autor_login']) ?></td>
	<td class="cislo"><?= e(datum($c['datum'], true)) ?><?= strtotime($c['datum']) > time() ? '<br><em>čeká na datum</em>' : '' ?></td>
	<td class="stred"><?= $c['visible'] ? 'Ano' : '<strong>Ne</strong>' ?></td>
	<td class="cislo"><?= (int) $c['visit'] ?>x</td>
	<td class="akce"><a href="<?= e($modul->url('edit', ['id' => $c['idc']])) ?>">Edituj</a> / <a href="<?= e($app->url('clanek/' . $c['seo_link'] . '?nahled=1')) ?>" target="_blank" rel="noopener">Preview</a></td>
	<td class="stred"><input type="checkbox" name="smaz[]" value="<?= (int) $c['idc'] ?>" aria-label="Označit ke smazání: <?= e($c['titulek']) ?>"></td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<p class="stred"><input class="tl" type="submit" value="Vymaž všechny označené články"></p>
</form>

<?php if ($stran > 1): ?>
<p class="strankovani">
<?php for ($s = 1; $s <= $stran; $s++): ?>
	<?= $s === $strana ? '<strong>[' . $s . ']</strong>' : '<a href="' . e($strankaUrl($s)) . '">' . $s . '</a>' ?>
<?php endfor ?>
</p>
<?php endif ?>
<?php endif ?>
