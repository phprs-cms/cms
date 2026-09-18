<?php
/**
 * @var PhpRS\Core\App $app
 * @var PhpRS\Admin\Moduly\Stranky $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $stranky
 */
?>
<p class="navigace-radek"><a class="tl" href="<?= e($modul->url('novy')) ?>">Nová stránka</a></p>
<?php if ($stranky === []): ?>
<p>Zatím žádné stránky. Hodí se například O nás, Kontakt nebo Zásady ochrany soukromí.</p>
<?php else: ?>
<div class="tab-obal">
<table class="vypis">
<thead><tr><th>Název</th><th>Adresa</th><th>Stav</th><th>V navigaci</th><th>Akce</th></tr></thead>
<tbody>
<?php foreach ($stranky as $s): ?>
<tr<?= $s['zobrazit'] ? '' : ' class="nevydany"' ?>>
	<td><a href="<?= e($modul->url('edit', ['id' => $s['ids']])) ?>"><?= e($s['titulek']) ?></a></td>
	<td><a href="<?= e($app->url($s['seo_link'])) ?>" target="_blank" rel="noopener">/<?= e($s['seo_link']) ?></a></td>
	<td><span class="stitek stitek-<?= $s['zobrazit'] ? 'vydano' : 'koncept' ?>"><?= $s['zobrazit'] ? 'zveřejněná' : 'skrytá' ?></span></td>
	<td><?= $s['v_menu'] ? 'Ano' : 'Ne' ?></td>
	<td class="akce"><a href="<?= e($modul->url('edit', ['id' => $s['ids']])) ?>">Upravit</a> ·
		<form method="post" action="<?= e($modul->url('smaz')) ?>" style="display:inline" onsubmit="return confirm('Opravdu smazat stránku?');"><?= $csrf ?><input type="hidden" name="ids" value="<?= (int) $s['ids'] ?>"><button class="navigace" type="submit">Smaž</button></form></td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<?php endif ?>
