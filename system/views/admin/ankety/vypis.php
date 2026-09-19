<?php
/**
 * @var PhpRS\Core\App $app
 * @var PhpRS\Admin\Moduly\Ankety $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $ankety
 * @var int $aktivni
 * @var bool $maBlok
 */
?>
<p class="navigace-radek"><a class="tl" href="<?= e($modul->url('novy')) ?>"><?= e(t('Nová anketa')) ?></a></p>
<?php if (!$maBlok): ?>
<p class="hlaska"><?= e(t('Anketa se na webu zobrazuje v bloku „Anketa“. Zatím ho nemáte – přidejte ho v sekci')) ?> <a href="<?= e($app->url('admin.php?modul=bloky&akce=novy&sys=ank')) ?>"><?= e(t('Bloky a rozvržení')) ?></a>.</p>
<?php endif ?>
<?php if ($ankety !== []): ?>
<div class="tab-obal">
<table class="vypis">
<thead><tr><th><?= e(t('Otázka')) ?></th><th><?= e(t('Hlasů')) ?></th><th><?= e(t('Stav')) ?></th><th><?= e(t('Vytvořena')) ?></th><th><?= e(t('Akce')) ?></th></tr></thead>
<tbody>
<?php foreach ($ankety as $a): ?>
<tr>
	<td><a href="<?= e($modul->url('edit', ['id' => $a['ida']])) ?>"><?= e($a['otazka']) ?></a></td>
	<td class="cislo"><?= (int) $a['hlasu'] ?></td>
	<td><?php if ((int) $a['ida'] === $aktivni): ?><span class="stitek stitek-vydano"><?= e(t('na webu')) ?></span> <?php endif ?><?php if ($a['uzavrena']): ?><span class="stitek"><?= e(t('uzavřená')) ?></span><?php endif ?></td>
	<td class="cislo"><?= e(datum($a['datum'])) ?></td>
	<td class="akce"><a href="<?= e($modul->url('edit', ['id' => $a['ida']])) ?>"><?= e(t('Upravit')) ?></a> ·
		<form method="post" action="<?= e($modul->url('smaz')) ?>" style="display:inline" data-potvrdit="<?= e(t('Opravdu smazat anketu i s hlasy?')) ?>"><?= $csrf ?><input type="hidden" name="ida" value="<?= (int) $a['ida'] ?>"><button class="navigace" type="submit"><?= e(t('Smaž')) ?></button></form></td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<?php endif ?>
