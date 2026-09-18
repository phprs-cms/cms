<?php
/**
 * Můj účet.
 *
 * @var PhpRS\Core\App $app
 * @var array<string, mixed> $user
 * @var string $csrf
 * @var list<string> $zalozniKody  právě vytvořené záložní kódy (zobrazí se jen jednou)
 * @var string $noveTajemstvi      rozpracované zapínání dvoufázového přihlášení
 * @var string $uri
 * @var int $zbyvaKodu
 */
$akce = e($app->url('admin.php?akce=ucet'));
?>
<?php if ($zalozniKody !== []): ?>
<div class="hlaska hlaska-ok">
	<p><strong>Dvoufázové přihlášení je zapnuté.</strong> Uložte si záložní kódy – každý jde použít jednou, když nebudete mít telefon. Už se nezobrazí.</p>
	<p class="zalozni-kody"><?= implode(' &nbsp; ', array_map(e(...), $zalozniKody)) ?></p>
</div>
<?php endif ?>

<form class="formular" method="post" action="<?= $akce ?>">
<?= $csrf ?><input type="hidden" name="co" value="profil">
<fieldset><legend>Moje údaje</legend>
<div class="radek"><span class="popisek">Přihlašovací jméno</span><div><?= e($user['user']) ?> <span class="napoveda">Mění administrátor v sekci Uživatelé.</span></div></div>
<div class="radek"><label for="jmeno">Jméno</label><div><input class="textpole siroke" type="text" id="jmeno" name="jmeno" value="<?= e($user['jmeno']) ?>" maxlength="100"><span class="napoveda">Zobrazuje se u článků na webu.</span></div></div>
<div class="radek"><label for="email">E-mail</label><input class="textpole siroke" type="email" id="email" name="email" value="<?= e($user['email']) ?>" maxlength="190"></div>
<div class="radek"><label for="url">Můj web</label><input class="textpole siroke" type="url" id="url" name="url" value="<?= e($user['url']) ?>" maxlength="255" placeholder="https://"></div>
</fieldset>
<p class="tlacitka"><input class="tl" type="submit" value="Uložit údaje"></p>
</form>

<form class="formular" method="post" action="<?= $akce ?>" autocomplete="off" style="margin-top:20px">
<?= $csrf ?><input type="hidden" name="co" value="heslo">
<fieldset><legend>Změna hesla</legend>
<div class="radek"><label for="soucasne">Současné heslo</label><input class="textpole" type="password" id="soucasne" name="soucasne" size="30" autocomplete="current-password" required></div>
<div class="radek"><label for="nove">Nové heslo</label><div><input class="textpole" type="password" id="nove" name="nove" size="30" minlength="10" autocomplete="new-password" required><span class="napoveda">Alespoň 10 znaků.</span></div></div>
<div class="radek"><label for="nove2">Nové heslo znovu</label><input class="textpole" type="password" id="nove2" name="nove2" size="30" autocomplete="new-password" required></div>
</fieldset>
<p class="tlacitka"><input class="tl" type="submit" value="Změnit heslo"></p>
</form>

<form class="formular" method="post" action="<?= $akce ?>" autocomplete="off" style="margin-top:20px">
<?= $csrf ?>
<fieldset><legend>Dvoufázové přihlášení</legend>
<?php if ($user['totp_tajemstvi'] !== ''): ?>
<p><span class="stitek stitek-vydano">zapnuté</span> Při přihlášení zadáváte kromě hesla i kód z aplikace. Zbývá záložních kódů: <?= $zbyvaKodu ?>.</p>
<input type="hidden" name="co" value="totp_vypni">
<div class="radek"><label for="vyp-heslo">Heslo pro potvrzení</label><input class="textpole" type="password" id="vyp-heslo" name="soucasne" size="30" autocomplete="current-password" required></div>
<p class="tlacitka"><button class="navigace" type="submit">Vypnout dvoufázové přihlášení</button></p>
<?php elseif ($noveTajemstvi !== ''): ?>
<input type="hidden" name="co" value="totp_potvrd">
<ol>
	<li>V ověřovací aplikaci (Google Authenticator, Microsoft Authenticator, 1Password, Aegis…) přidejte nový účet ručním zadáním klíče:<br><code class="totp-klic"><?= e(trim(chunk_split($noveTajemstvi, 4, ' '))) ?></code><br><small>Na mobilu můžete použít <a href="<?= e($uri) ?>">tento odkaz</a>, který aplikaci otevře.</small></li>
	<li>Opište šestimístný kód, který aplikace ukazuje:</li>
</ol>
<div class="radek"><label for="kod">Kód z aplikace</label><input class="textpole" type="text" id="kod" name="kod" size="12" maxlength="7" inputmode="numeric" autocomplete="one-time-code" required autofocus></div>
<p class="tlacitka"><input class="tl" type="submit" value="Potvrdit a zapnout"></p>
<?php else: ?>
<p>Účet je chráněný jen heslem. S dvoufázovým přihlášením se bez vašeho telefonu nepřihlásí ani ten, kdo heslo uhodne nebo ukradne.</p>
<input type="hidden" name="co" value="totp_start">
<p class="tlacitka"><input class="tl" type="submit" value="Zapnout dvoufázové přihlášení"></p>
<?php endif ?>
</fieldset>
</form>
