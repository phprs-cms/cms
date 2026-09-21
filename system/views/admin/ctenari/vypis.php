<?php
/**
 * @var PhpRS\Admin\Moduly\CtenariAdmin $modul
 * @var PhpRS\Core\App $app
 * @var string $csrf
 * @var string $q
 * @var list<array<string, mixed>> $ctenari
 * @var array<string, int> $pocty
 */
?>
<div class="dlazdice">
<?php foreach ($pocty as $popis => $cislo): ?>
	<div class="dlazdice-polozka"><strong><?= $cislo ?></strong><span><?= e(t($popis)) ?></span></div>
<?php endforeach ?>
</div>
<form class="smltxt" method="get" action="<?= e($app->url('admin.php')) ?>">
	<input type="hidden" name="modul" value="ctenari">
	<input class="textpole" type="search" name="q" value="<?= e($q) ?>" placeholder="<?= e(t('hledat e-mail nebo jméno')) ?>" aria-label="<?= e(t('Hledat čtenáře')) ?>">
	<input class="tl" type="submit" value="<?= e(t('Hledat')) ?>">
	<a class="navigace" href="<?= e($modul->url('', ['format' => 'csv'])) ?>"><?= e(t('Stáhnout CSV')) ?></a>
</form>
<p class="smltxt"><?= e(t('Článek zamknete v jeho editoru volbou „Kdo smí číst“. Předplatné se zapisuje ručně – například po přijetí platby na účet.')) ?></p>
<?php if ($ctenari === []): ?>
<p><?= e(t('Zatím se nikdo nezaregistroval. Přihlášení čtenářů je na adrese')) ?> <a href="<?= e($app->url('ctenar')) ?>" target="_blank" rel="noopener"><?= e($app->url('ctenar')) ?></a> <?= e(t('– odkaz přidáte i blokem „Účet čtenáře“.')) ?></p>
<?php else: ?>
<div class="tab-obal"><table class="vypis">
<thead><tr><th scope="col"><?= e(t('E-mail')) ?></th><th scope="col"><?= e(t('Jméno')) ?></th><th scope="col"><?= e(t('Registrace')) ?></th><th scope="col"><?= e(t('Naposledy')) ?></th><th scope="col"><?= e(t('Předplatné')) ?></th><th scope="col"><?= e(t('Akce')) ?></th></tr></thead>
<tbody>
<?php foreach ($ctenari as $c): $plati = $c['predplatne_do'] !== null && $c['predplatne_do'] >= date('Y-m-d'); ?>
<tr>
	<td><?= e($c['email']) ?><?= $c['potvrzen'] ? '' : ' <span class="stitek stitek-koncept">nepotvrdil e-mail</span>' ?></td>
	<td><?= e($c['jmeno']) ?></td>
	<td class="cislo"><?= e(datum($c['vytvoren'])) ?></td>
	<td class="cislo"><?= $c['naposledy'] ? e(datum($c['naposledy'])) : '–' ?></td>
	<td>
		<form method="post" action="<?= e($modul->url('predplatne')) ?>" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
			<?= $csrf ?><input type="hidden" name="idct" value="<?= (int) $c['idct'] ?>">
			<span class="stitek stitek-<?= $plati ? 'vydano' : 'koncept' ?>"><?= e($plati ? t('do %s', datum($c['predplatne_do'])) : t($c['predplatne_do'] !== null ? 'skončilo' : 'nemá')) ?></span>
			<select name="volba" aria-label="<?= e(t('Změna předplatného')) ?>" data-odeslat-pri-zmene>
				<option value=""><?= e(t('změnit…')) ?></option>
				<option value="1"><?= e(t('+ 1 měsíc')) ?></option>
				<option value="3"><?= e(t('+ 3 měsíce')) ?></option>
				<option value="12"><?= e(t('+ 1 rok')) ?></option>
<?php if ($c['predplatne_do'] !== null): ?>
				<option value="zrusit"><?= e(t('zrušit')) ?></option>
<?php endif ?>
			</select>
			<noscript><button class="navigace" type="submit">OK</button></noscript>
		</form>
	</td>
	<td class="akce"><form class="vradku" method="post" action="<?= e($modul->url('smaz')) ?>" data-potvrdit="Smazat účet čtenáře <?= e($c['email']) ?>?"><?= $csrf ?><input type="hidden" name="idct" value="<?= (int) $c['idct'] ?>"><button class="navigace" type="submit"><?= e(t('Smaž')) ?></button></form></td>
</tr>
<?php endforeach ?>
</tbody></table></div>
<?php endif ?>
