<?php
/**
 * @var PhpRS\Admin\Moduly\Presmerovani $modul
 * @var string $csrf
 * @var list<array<string, mixed>> $zaznamy
 */
?>
<form class="formular" method="post" action="<?= e($modul->url('uloz')) ?>">
<?= $csrf ?>
<div class="radek"><label for="z_adresy"><?= e(t('Stará adresa')) ?></label><div><input class="textpole siroke" type="text" id="z_adresy" name="z_adresy" maxlength="255" required placeholder="<?= e(t('/stara-stranka.html')) ?>"><span class="napoveda"><?= e(t('Cesta na tomto webu, která už neexistuje.')) ?></span></div></div>
<div class="radek"><label for="na_adresu"><?= e(t('Přesměrovat na')) ?></label><div><input class="textpole siroke" type="text" id="na_adresu" name="na_adresu" maxlength="255" required placeholder="<?= e(t('/clanek/nova-adresa nebo https://…')) ?>"></div></div>
<p class="tlacitka"><input class="tl" type="submit" value="<?= e(t('Přidat přesměrování')) ?>"></p>
</form>
<p class="smltxt"><?= e(t('Přesměrování se použije jen tehdy, když na staré adrese nic není. Při změně adresy vydaného článku vzniká samo.')) ?></p>
<?php if ($zaznamy !== []): ?>
<div class="tab-obal">
<table class="vypis">
<thead><tr><th><?= e(t('Stará adresa')) ?></th><th><?= e(t('Cíl')) ?></th><th><?= e(t('Použito')) ?></th><th><?= e(t('Vytvořeno')) ?></th><th><?= e(t('Akce')) ?></th></tr></thead>
<tbody>
<?php foreach ($zaznamy as $z): ?>
<tr>
	<td>/<?= e($z['z_adresy']) ?></td>
	<td><?= e(preg_match('#^https?://#i', $z['na_adresu']) ? $z['na_adresu'] : '/' . $z['na_adresu']) ?></td>
	<td class="cislo"><?= (int) $z['pocet'] ?>×</td>
	<td class="cislo"><?= e(datum($z['vytvoreno'])) ?></td>
	<td class="akce"><form method="post" action="<?= e($modul->url('smaz')) ?>" style="display:inline"><?= $csrf ?><input type="hidden" name="idp" value="<?= (int) $z['idp'] ?>"><button class="navigace" type="submit"><?= e(t('Smaž')) ?></button></form></td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
<?php endif ?>
