<?php
/**
 * @var PhpRS\Admin\Moduly\Rubriky $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $rubriky
 */
?>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url('novy')) ?>"><?= e(t('Nová rubrika')) ?></a></p>
<?php if ($rubriky === []): ?>
<p class="stred"><?= e(t('Zatím není založena žádná rubrika.')) ?></p>
<?php else: ?>
<div class="tab-obal">
<table class="vypis">
<thead><tr><th><?= e(t('Název rubriky')) ?></th><th><?= e(t('Adresa')) ?></th><th><?= e(t('Článků')) ?></th><th><?= e(t('Pořadí')) ?></th><th><?= e(t('Zobrazit')) ?></th><th><?= e(t('Akce')) ?></th></tr></thead>
<tbody>
<?php foreach ($rubriky as $r): ?>
<tr<?= $r['zobrazit'] ? '' : ' class="nevydany"' ?>>
	<td><?= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $r['uroven']) . ($r['uroven'] > 0 ? '&#9492; ' : '') ?><a href="<?= e($modul->url('edit', ['id' => $r['idt']])) ?>"><?= e($r['nazev']) ?></a></td>
	<td>/rubrika/<?= e($r['seo_link']) ?></td>
	<td class="cislo"><?= (int) $r['pocet_clanku'] ?></td>
	<td class="cislo"><?= (int) $r['hodnost'] ?></td>
	<td class="stred"><?= $r['zobrazit'] ? 'Ano' : '<strong>Ne</strong>' ?></td>
	<td class="akce">
		<a href="<?= e($modul->url('edit', ['id' => $r['idt']])) ?>"><?= e(t('Upravit')) ?></a> ·
		<form method="post" action="<?= e($modul->url('smaz')) ?>" style="display:inline" data-potvrdit="<?= e(t('Opravdu smazat rubriku?')) ?>"><?= $csrf ?><input type="hidden" name="idt" value="<?= (int) $r['idt'] ?>"><button class="navigace" type="submit"><?= e(t('Smaž')) ?></button></form>
	</td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<?php endif ?>
