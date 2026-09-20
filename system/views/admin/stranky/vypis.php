<?php
/**
 * @var PhpRS\Core\App $app
 * @var PhpRS\Admin\Moduly\Stranky $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $stranky
 */
?>
<p class="navigace-radek"><a class="tl" href="<?= e($modul->url('novy')) ?>"><?= e(t('Nová stránka')) ?></a></p>
<?php if ($stranky === []): ?>
<?= $app->view->render('admin/prazdno', ['ikona' => 'stranky', 'nadpis' => t('Zatím žádné stránky.'), 'text' => t('Hodí se například O nás, Kontakt nebo Zásady ochrany soukromí.'), 'akce' => [$modul->url('novy'), t('Založit první stránku')]]) ?>
<?php else: ?>
<div class="tab-obal">
<table class="vypis">
<thead><tr><th><?= e(t('Název')) ?></th><th><?= e(t('Adresa')) ?></th><th><?= e(t('Stav')) ?></th><th><?= e(t('V navigaci')) ?></th><th><?= e(t('Akce')) ?></th></tr></thead>
<tbody>
<?php foreach ($stranky as $s): ?>
<tr<?= $s['zobrazit'] ? '' : ' class="nevydany"' ?>>
	<td><a href="<?= e($modul->url('edit', ['id' => $s['ids']])) ?>"><?= e($s['titulek']) ?></a></td>
	<td><a href="<?= e($app->url($s['seo_link'])) ?>" target="_blank" rel="noopener">/<?= e($s['seo_link']) ?></a></td>
	<td><span class="stitek stitek-<?= $s['zobrazit'] ? 'vydano' : 'koncept' ?>"><?= $s['zobrazit'] ? 'zveřejněná' : 'skrytá' ?></span></td>
	<td><?= $s['v_menu'] ? 'Ano' : 'Ne' ?></td>
	<td class="akce"><a href="<?= e($modul->url('edit', ['id' => $s['ids']])) ?>"><?= e(t('Upravit')) ?></a> ·
		<form method="post" action="<?= e($modul->url('smaz')) ?>" style="display:inline" data-potvrdit="<?= e(t('Opravdu smazat stránku?')) ?>"><?= $csrf ?><input type="hidden" name="ids" value="<?= (int) $s['ids'] ?>"><button class="navigace" type="submit"><?= e(t('Smaž')) ?></button></form></td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<?php endif ?>
