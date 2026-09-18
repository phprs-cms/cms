<?php
/**
 * @var PhpRS\Core\App $app
 * @var PhpRS\Admin\Moduly\Reklama $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $reklamy
 * @var string $adsTxt
 */
use PhpRS\Admin\Moduly\Reklama;
?>
<p class="navigace-radek"><a class="tl" href="<?= e($modul->url('novy')) ?>">Nová reklama</a></p>
<p class="smltxt">Reklama pod článkem se zobrazuje sama. Ostatní pozice umístíte na web blokem „Reklama“ v sekci <a href="<?= e($app->url('admin.php?modul=bloky')) ?>">Bloky a rozvržení</a>. Každá reklama je na webu označena slovem „Reklama“.</p>
<?php if ($reklamy !== []): ?>
<div class="tab-obal">
<table class="vypis">
<thead><tr><th>Název</th><th>Pozice</th><th>Platnost</th><th>Zobrazení</th><th>Kliky</th><th>CTR</th><th>Stav</th><th>Akce</th></tr></thead>
<tbody>
<?php foreach ($reklamy as $r):
    $bezi = $r['aktivni'] && ($r['platna_od'] === null || strtotime($r['platna_od']) <= time()) && ($r['platna_do'] === null || strtotime($r['platna_do']) > time())
        && ($r['max_zobrazeni'] === null || $r['zobrazeni'] < $r['max_zobrazeni']); ?>
<tr<?= $bezi ? '' : ' class="nevydany"' ?>>
	<td><a href="<?= e($modul->url('edit', ['id' => $r['idr']])) ?>"><?= e($r['nazev']) ?></a><br><small><?= $r['typ'] === 'kod' ? 'reklamní kód' : 'banner' ?></small></td>
	<td><?= e(explode(' (', Reklama::POZICE[$r['pozice']] ?? $r['pozice'])[0]) ?></td>
	<td class="cislo"><?= $r['platna_od'] ? e(datum($r['platna_od'])) : '…' ?> – <?= $r['platna_do'] ? e(datum($r['platna_do'])) : '…' ?></td>
	<td class="cislo"><?= number_format((int) $r['zobrazeni'], 0, ',', ' ') ?><?= $r['max_zobrazeni'] !== null ? ' / ' . number_format((int) $r['max_zobrazeni'], 0, ',', ' ') : '' ?></td>
	<td class="cislo"><?= $r['typ'] === 'kod' ? '–' : number_format((int) $r['kliky'], 0, ',', ' ') ?></td>
	<td class="cislo"><?= $r['typ'] === 'kod' || $r['zobrazeni'] == 0 ? '–' : number_format($r['kliky'] / $r['zobrazeni'] * 100, 2, ',', ' ') . ' %' ?></td>
	<td><span class="stitek stitek-<?= $bezi ? 'vydano' : 'koncept' ?>"><?= $bezi ? 'běží' : 'neběží' ?></span></td>
	<td class="akce"><a href="<?= e($modul->url('edit', ['id' => $r['idr']])) ?>">Upravit</a> ·
		<form method="post" action="<?= e($modul->url('prepni')) ?>" style="display:inline"><?= $csrf ?><input type="hidden" name="idr" value="<?= (int) $r['idr'] ?>"><button class="navigace" type="submit"><?= $r['aktivni'] ? 'Vypnout' : 'Zapnout' ?></button></form> ·
		<form method="post" action="<?= e($modul->url('smaz')) ?>" style="display:inline" data-potvrdit="Opravdu smazat reklamu i s jejími počty?"><?= $csrf ?><input type="hidden" name="idr" value="<?= (int) $r['idr'] ?>"><button class="navigace" type="submit">Smaž</button></form></td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<?php endif ?>
<details class="pokrocile"<?= $adsTxt !== '' ? ' open' : '' ?>>
<summary>Soubor ads.txt (vyžadují ho reklamní sítě)</summary>
<form class="formular" method="post" action="<?= e($modul->url('ads_txt')) ?>">
<?= $csrf ?>
<div class="radek"><label for="ads_txt">Soubor ads.txt</label><div><textarea class="textbox kod" id="ads_txt" name="ads_txt" rows="4" style="min-height:80px" spellcheck="false"><?= e($adsTxt) ?></textarea><span class="napoveda">Seznam autorizovaných prodejců reklamy, jak vám ho dodala reklamní síť (např. google.com, pub-…, DIRECT, …). Bude dostupný na adrese /ads.txt.</span></div></div>
<p class="tlacitka"><input class="tl" type="submit" value="Uložit ads.txt"></p>
</form>
</details>
