<?php
/**
 * @var PhpRS\Core\App $app
 * @var PhpRS\Admin\Moduly\Statistika $modul
 * @var int $dni
 * @var array<string, array{navstevy:int, zobrazeni:int}> $graf
 * @var bool $zapnuto
 * @var list<array<string, mixed>> $clanky
 * @var list<array<string, mixed>> $zdroje
 */
$max = max(1, ...array_column($graf, 'zobrazeni'));
$navstev = array_sum(array_column($graf, 'navstevy'));
$zobrazeni = array_sum(array_column($graf, 'zobrazeni'));
?>
<?php if (!$zapnuto): ?>
<p class="hlaska hlaska-chyba">Měření je vypnuté. Zapnete ho v Nastavení → Měření.</p>
<?php endif ?>
<nav class="zalozky" aria-label="Období">
<?php foreach ([7 => '7 dní', 30 => '30 dní', 90 => '90 dní'] as $d => $nazev): ?>
	<a href="<?= e($modul->url('', ['dni' => $d])) ?>"<?= $dni === $d ? ' class="aktivni"' : '' ?>><?= $nazev ?></a>
<?php endforeach ?>
</nav>
<div class="dlazdice">
	<div class="dlazdice-polozka"><strong><?= number_format($navstev, 0, ',', ' ') ?></strong><span>Návštěvy</span></div>
	<div class="dlazdice-polozka"><strong><?= number_format($zobrazeni, 0, ',', ' ') ?></strong><span>Zobrazené stránky</span></div>
	<div class="dlazdice-polozka"><strong><?= $navstev > 0 ? number_format($zobrazeni / $navstev, 1, ',', ' ') : '0' ?></strong><span>Stránek na návštěvu</span></div>
</div>
<h3>Zobrazení a návštěvy po dnech</h3>
<div class="graf" role="img" aria-label="Sloupcový graf zobrazení stránek po dnech">
<?php foreach ($graf as $den => $h): ?>
	<div class="graf-sloupec" title="<?= e(datum($den)) ?>: <?= $h['zobrazeni'] ?> zobrazení, <?= $h['navstevy'] ?> návštěv"><i style="height:<?= round($h['zobrazeni'] / $max * 100, 1) ?>%"><b style="height:<?= $h['zobrazeni'] > 0 ? round($h['navstevy'] / $h['zobrazeni'] * 100, 1) : 0 ?>%"></b></i></div>
<?php endforeach ?>
</div>
<p class="smltxt"><?= e(datum(array_key_first($graf))) ?> – <?= e(datum(array_key_last($graf))) ?> · světlá část sloupce jsou zobrazení stránek, tmavá návštěvy. Měření nepoužívá cookies a neukládá IP adresy; roboty nepočítá.</p>

<div class="stat-tabulky">
<div>
<h3>Nejčtenější články</h3>
<?php if ($clanky === []): ?><p>Zatím žádná data.</p><?php else: ?>
<div class="tab-obal"><table class="vypis"><tbody>
<?php foreach ($clanky as $c): ?>
<tr><td><a href="<?= e($app->url('admin.php?modul=clanky&akce=edit&id=' . (int) $c['idc'])) ?>"><?= e($c['titulek']) ?></a></td><td class="cislo"><?= number_format((int) $c['pocet'], 0, ',', ' ') ?>×</td></tr>
<?php endforeach ?>
</tbody></table></div>
<?php endif ?>
</div>
<div>
<h3>Odkud čtenáři přicházejí</h3>
<?php if ($zdroje === []): ?><p>Zatím žádná data.</p><?php else: ?>
<div class="tab-obal"><table class="vypis"><tbody>
<?php foreach ($zdroje as $z): ?>
<tr><td><?= e($z['zdroj']) ?></td><td class="cislo"><?= number_format((int) $z['pocet'], 0, ',', ' ') ?>×</td></tr>
<?php endforeach ?>
</tbody></table></div>
<?php endif ?>
</div>
</div>
