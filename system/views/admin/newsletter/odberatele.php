<?php
/**
 * @var PhpRS\Admin\Moduly\NewsletterAdmin $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $odberatele
 */
?>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url()) ?>">Zpět na newsletter</a> <a class="navigace" href="<?= e($modul->url('odberatele', ['format' => 'csv'])) ?>">Stáhnout CSV</a></p>
<div class="tab-obal"><table class="vypis">
<thead><tr><th>E-mail</th><th>Přihlášen</th><th>Stav</th><th>Akce</th></tr></thead>
<tbody>
<?php foreach ($odberatele as $o): ?>
<tr><td><?= e($o['email']) ?></td><td class="cislo"><?= e(datum($o['prihlasen'], true)) ?></td>
	<td><span class="stitek stitek-<?= $o['potvrzen'] ? 'vydano' : 'koncept' ?>"><?= $o['potvrzen'] ? 'odebírá' : 'nepotvrdil' ?></span></td>
	<td class="akce"><form method="post" action="<?= e($modul->url('smaz_odberatele')) ?>" style="display:inline" data-potvrdit="Odstranit odběratele?"><?= $csrf ?><input type="hidden" name="ido" value="<?= (int) $o['ido'] ?>"><button class="navigace" type="submit">Odstranit</button></form></td></tr>
<?php endforeach ?>
</tbody></table></div>
