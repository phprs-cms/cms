<?php
/**
 * Bloky rozložené do sloupců stejně jako na webu.
 *
 * @var PhpRS\Admin\Moduly\Bloky $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $sloupce
 * @var array<int, list<array<string, mixed>>> $bloky
 */
use PhpRS\Admin\Moduly\Bloky;
?>
<p class="navigace-radek">
	<a class="navigace" href="<?= e($modul->url('novy')) ?>">Přidat nový blok</a>
<?php foreach (Bloky::SYSTEMOVE as $zkratka => $nazev): if ($zkratka === 'hlb') { continue; } ?>
	<a class="navigace" href="<?= e($modul->url('novy', ['sys' => $zkratka])) ?>">+ <?= e($nazev) ?></a>
<?php endforeach ?>
</p>
<div class="sloupce">
<?php foreach ($sloupce as $s): ?>
	<div class="sloupec">
		<strong><?= e($s['nazev'] !== '' ? $s['nazev'] : 'sloupec ' . $s['ids']) ?></strong>
<?php foreach ($bloky[(int) $s['ids']] ?? [] as $b): ?>
		<div class="<?= $b['sys_funkce'] !== '' ? 'blok-sys' : 'blok-std' ?><?= $b['zobrazit'] ? '' : ' blok-skryty' ?>">
			<strong><?= e($b['nazev']) ?></strong><br>
			<?= $b['sys_funkce'] !== '' ? 'systémový blok: ' . e($b['sys_funkce']) : 'běžný blok' ?><br>
			priorita: <?= (int) $b['hodnost'] ?><br>
			vzhled: typ <?= (int) $b['typ'] ?><br>
			zobrazit: <?= $b['zobrazit'] ? e(Bloky::KDE[(int) $b['zobrazit_kde']]) : '<strong>ne</strong>' ?><br><br>
			<a href="<?= e($modul->url('edit', ['id' => $b['idb']])) ?>">Edituj</a>
<?php if ($b['sys_funkce'] !== 'hlb'): ?>
			/ <form method="post" action="<?= e($modul->url('smaz')) ?>" style="display:inline" onsubmit="return confirm('Opravdu smazat blok?');"><?= $csrf ?><input type="hidden" name="idb" value="<?= (int) $b['idb'] ?>"><button class="navigace" type="submit">Smaž</button></form>
<?php endif ?>
		</div>
<?php endforeach ?>
		<a class="navigace stred" href="<?= e($modul->url('novy', ['sloupec' => $s['ids']])) ?>">Přidat blok sem</a>
	</div>
<?php endforeach ?>
</div>
<p class="stred smltxt">Šedý blok = běžný (vlastní HTML), fialový = systémový. V každém sloupci se bloky řadí podle priority, vyšší je výš.</p>
