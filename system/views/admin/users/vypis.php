<?php
/**
 * @var PhpRS\Core\App $app
 * @var PhpRS\Admin\Moduly\Autori $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $autori
 */
?>
<p class="navigace-radek"><a class="navigace" href="<?= e($modul->url('novy')) ?>">Přidat nového autora</a></p>
<div class="tab-obal">
<table class="vypis">
<thead><tr><th>Uživatel</th><th>Jméno</th><th>E-mail</th><th>Typ</th><th>Právo vydávat</th><th>Článků</th><th>Poslední přihlášení</th><th>Akce</th></tr></thead>
<tbody>
<?php foreach ($autori as $a): ?>
<tr<?= $a['blokovat'] ? ' class="nevydany"' : '' ?>>
	<td><a href="<?= e($modul->url('edit', ['id' => $a['idu']])) ?>"><?= e($a['user']) ?></a><?= $a['blokovat'] ? ' <strong>(blokován)</strong>' : '' ?></td>
	<td><?= e($a['jmeno']) ?></td>
	<td><?= e($a['email']) ?></td>
	<td><?= e(PhpRS\Core\Auth::TYPY[(int) $a['admin']] ?? '?') ?></td>
	<td class="stred"><?= (int) $a['admin'] === PhpRS\Core\Auth::ADMIN || $a['pravo_vydavat'] ? 'Ano' : 'Ne' ?></td>
	<td class="cislo"><?= (int) $a['pocet_clanku'] ?></td>
	<td class="cislo"><?= e(datum($a['posledni_login'], true)) ?: '-' ?></td>
	<td class="akce">
		<a href="<?= e($modul->url('edit', ['id' => $a['idu']])) ?>">Edituj / Nastav práva</a>
<?php if ((int) $a['idu'] !== $app->auth()->id()): ?>
		/ <form method="post" action="<?= e($modul->url('smaz')) ?>" style="display:inline" onsubmit="return confirm('Opravdu smazat autora?');"><?= $csrf ?><input type="hidden" name="idu" value="<?= (int) $a['idu'] ?>"><button class="navigace" type="submit">Smaž</button></form>
<?php endif ?>
	</td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
