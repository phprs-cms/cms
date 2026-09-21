<?php
/**
 * @var PhpRS\Core\App $app
 * @var PhpRS\Admin\Moduly\Autori $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $autori
 */
?>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url('novy')) ?>"><?= e(t('Nový uživatel')) ?></a></p>
<div class="tab-obal">
<table class="vypis">
<thead><tr><th><?= e(t('Uživatel')) ?></th><th><?= e(t('Jméno')) ?></th><th><?= e(t('E-mail')) ?></th><th><?= e(t('Role')) ?></th><th><?= e(t('Vydává')) ?></th><th><?= e(t('Článků')) ?></th><th><?= e(t('Poslední přihlášení')) ?></th><th><?= e(t('Akce')) ?></th></tr></thead>
<tbody>
<?php foreach ($autori as $a): ?>
<tr<?= $a['blokovat'] ? ' class="nevydany"' : '' ?>>
	<td><a href="<?= e($modul->url('edit', ['id' => $a['idu']])) ?>"><?= e($a['user']) ?></a><?= $a['blokovat'] ? ' <strong>(blokován)</strong>' : '' ?><?= $a['totp_tajemstvi'] !== '' ? ' <span class="stitek stitek-vydano" title="dvoufázové přihlášení">2FA</span>' : '' ?></td>
	<td><?= e($a['jmeno']) ?></td>
	<td><?= e($a['email']) ?></td>
	<td><?= e(t(PhpRS\Core\Auth::TYPY[(int) $a['admin']] ?? '?')) ?></td>
	<td class="stred"><?= (int) $a['admin'] >= PhpRS\Core\Auth::REDAKTOR || $a['pravo_vydavat'] ? e(t('Ano')) : e(t('Ne')) ?></td>
	<td class="cislo"><?= (int) $a['pocet_clanku'] ?></td>
	<td class="cislo"><?= e(datum($a['posledni_login'], true)) ?: '-' ?></td>
	<td class="akce">
		<a href="<?= e($modul->url('edit', ['id' => $a['idu']])) ?>"><?= e(t('Upravit')) ?></a>
<?php if ((int) $a['idu'] !== $app->auth()->id()): ?>
		/ <form method="post" action="<?= e($modul->url('smaz')) ?>" style="display:inline" data-potvrdit="<?= e(t('Opravdu smazat autora?')) ?>"><?= $csrf ?><input type="hidden" name="idu" value="<?= (int) $a['idu'] ?>"><button class="navigace" type="submit"><?= e(t('Smaž')) ?></button></form>
<?php endif ?>
	</td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
