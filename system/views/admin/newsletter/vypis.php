<?php
/**
 * @var PhpRS\Core\App $app
 * @var PhpRS\Admin\Moduly\NewsletterAdmin $modul
 * @var string $csrf
 * @var int $odberatelu
 * @var int $nepotvrzenych
 * @var list<array<string, mixed>> $vydani
 * @var list<array<string, mixed>> $clanky
 * @var bool $maEmail
 * @var bool $maBlok
 */
?>
<div class="dlazdice">
	<div class="dlazdice-polozka"><strong><?= $odberatelu ?></strong><span><?= e(t('Odběratelů ·')) ?> <a href="<?= e($modul->url('odberatele')) ?>"><?= e(t('zobrazit')) ?></a></span></div>
	<div class="dlazdice-polozka"><strong><?= $nepotvrzenych ?></strong><span><?= e(t('Čeká na potvrzení e-mailem')) ?></span></div>
</div>
<?php if (!$maEmail): ?>
<p class="hlaska hlaska-chyba"><?= e(t('Nejprve vyplňte E-mail redakce v Nastavení – z něj newsletter odchází.')) ?></p>
<?php endif ?>
<?php if (!$maBlok): ?>
<p class="hlaska"><?= e(t('Přihlašovací formulář dáte na web blokem „Newsletter“ v sekci')) ?> <a href="<?= e($app->url('admin.php?modul=bloky')) ?>"><?= e(t('Bloky a rozvržení')) ?></a>.</p>
<?php endif ?>

<form class="formular" method="post" action="<?= e($modul->url('uloz')) ?>">
<?= $csrf ?>
<fieldset>
<legend><?= e(t('Nové vydání')) ?></legend>
<div class="radek"><label for="predmet"><?= e(t('Předmět e-mailu')) ?></label><input class="textpole siroke" type="text" id="predmet" name="predmet" maxlength="200" required placeholder="<?= e(t('např. Co jsme tento týden napsali')) ?>"></div>
<div class="radek"><label for="uvod"><?= e(t('Úvodní slovo')) ?></label><div><textarea class="textbox" id="uvod" name="uvod" rows="3" style="min-height:70px"></textarea><span class="napoveda"><?= e(t('Nepovinné – pár vět před výčtem článků.')) ?></span></div></div>
<div class="radek"><span class="popisek"><?= e(t('Články')) ?></span><div class="volby">
<?php foreach ($clanky as $c): ?>
	<label style="white-space:normal"><input type="checkbox" name="clanky[]" value="<?= (int) $c['idc'] ?>"<?= $c['novy'] ? ' checked' : '' ?>> <?= e($c['titulek']) ?> <small>(<?= e(datum($c['datum'])) ?>)</small></label><br>
<?php endforeach ?>
	<span class="napoveda"><?= e(t('Předvybrané jsou články vydané od posledního newsletteru.')) ?></span>
</div></div>
</fieldset>
<p class="tlacitka">
	<button class="tl" type="submit" name="co" value="rozeslat" data-potvrdit="Rozeslat newsletter <?= $odberatelu ?> odběratelům?"><?= e(t('Rozeslat odběratelům')) ?></button>
	<button class="tl" type="submit" name="co" value="zkouska"><?= e(t('Poslat na zkoušku redakci')) ?></button>
</p>
</form>

<?php if ($vydani !== []): ?>
<h3><?= e(t('Odeslaná vydání')) ?></h3>
<div class="tab-obal"><table class="vypis">
<thead><tr><th><?= e(t('Předmět')) ?></th><th><?= e(t('Vytvořeno')) ?></th><th><?= e(t('Stav')) ?></th><th><?= e(t('Příjemců')) ?></th></tr></thead>
<tbody>
<?php foreach ($vydani as $v): ?>
<tr><td><?= e($v['predmet']) ?></td><td class="cislo"><?= e(datum($v['vytvoreno'], true)) ?></td>
	<td><?= $v['odeslano'] ? '<span class="stitek stitek-vydano">odesláno</span>' : '<a href="' . e($modul->url('rozeslat', ['id' => $v['idn']])) . '">pokračovat v rozesílce</a>' ?></td>
	<td class="cislo"><?= (int) $v['pocet'] ?></td></tr>
<?php endforeach ?>
</tbody></table></div>
<?php endif ?>
