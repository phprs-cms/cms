<?php
/**
 * @var PhpRS\Admin\Moduly\Rubriky $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $rubriky
 */
?>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url('novy')) ?>">Přidat novou rubriku</a></p>
<?php if ($rubriky === []): ?>
<p class="stred">Zatím není založena žádná rubrika.</p>
<?php else: ?>
<div class="tab-obal">
<table class="vypis">
<thead><tr><th>Název rubriky</th><th>Adresa</th><th>Článků</th><th>Pořadí</th><th>Zobrazit</th><th>Akce</th></tr></thead>
<tbody>
<?php foreach ($rubriky as $r): ?>
<tr<?= $r['zobrazit'] ? '' : ' class="nevydany"' ?>>
	<td><?= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $r['uroven']) . ($r['uroven'] > 0 ? '&#9492; ' : '') ?><a href="<?= e($modul->url('edit', ['id' => $r['idt']])) ?>"><?= e($r['nazev']) ?></a></td>
	<td>/rubrika/<?= e($r['seo_link']) ?></td>
	<td class="cislo"><?= (int) $r['pocet_clanku'] ?></td>
	<td class="cislo"><?= (int) $r['hodnost'] ?></td>
	<td class="stred"><?= $r['zobrazit'] ? 'Ano' : '<strong>Ne</strong>' ?></td>
	<td class="akce">
		<a href="<?= e($modul->url('edit', ['id' => $r['idt']])) ?>">Edituj</a> /
		<form method="post" action="<?= e($modul->url('smaz')) ?>" style="display:inline" onsubmit="return confirm('Opravdu smazat rubriku?');"><?= $csrf ?><input type="hidden" name="idt" value="<?= (int) $r['idt'] ?>"><button class="navigace" type="submit">Smaž</button></form>
	</td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<?php endif ?>
