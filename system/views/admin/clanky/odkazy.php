<?php
/**
 * Nefunkční odkazy v článcích.
 *
 * @var PhpRS\Admin\Moduly\Clanky $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $odkazy
 * @var int $zkontrolovano
 * @var int $celkem
 * @var bool $zapnuto
 */
?>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url()) ?>"><?= e(t('Zpět na přehled článků')) ?></a></p>
<?php if (!$zapnuto): ?>
<p class="hlaska"><?= e(t('Kontrola odkazů je vypnutá (Nastavení → Základní → Další možnosti).')) ?></p>
<?php endif ?>
<p class="smltxt"><?= e(t('Systém na pozadí prochází vydané články – jeden za pět minut, každý jednou za měsíc – a zkouší, jestli odkazy v nich ještě fungují. Zkontrolováno článků: %s z %s.', $zkontrolovano, $celkem)) ?></p>
<?php if ($odkazy === []): ?>
<p><?= e(t('Žádný nefunkční odkaz nebyl nalezen.')) ?></p>
<?php else: ?>
<div class="tab-obal"><table class="vypis">
<thead><tr><th><?= e(t('Článek')) ?></th><th><?= e(t('Odkaz')) ?></th><th><?= e(t('Problém')) ?></th><th><?= e(t('Zjištěno')) ?></th><th><?= e(t('Akce')) ?></th></tr></thead>
<tbody>
<?php foreach ($odkazy as $o): ?>
<tr>
	<td><a href="<?= e($modul->url('edit', ['id' => (int) $o['idc']])) ?>"><?= e($o['titulek']) ?></a></td>
	<td style="word-break:break-all"><a href="<?= e($o['url']) ?>" target="_blank" rel="noopener noreferrer"><?= e(mb_strimwidth($o['url'], 0, 90, '…')) ?></a></td>
	<td><?= e((int) $o['stav'] === 0 ? t('server neodpovídá') : ((int) $o['stav'] === 404 ? t('stránka neexistuje (404)') : t('chyba %s', (int) $o['stav']))) ?></td>
	<td class="cislo"><?= e(datum($o['cas'])) ?></td>
	<td class="akce"><form method="post" action="<?= e($modul->url('odkazy')) ?>" style="display:inline"><?= $csrf ?><input type="hidden" name="idc" value="<?= (int) $o['idc'] ?>"><button class="navigace" type="submit"><?= e(t('Zkontrolovat znovu')) ?></button></form></td>
</tr>
<?php endforeach ?>
</tbody></table></div>
<?php endif ?>
