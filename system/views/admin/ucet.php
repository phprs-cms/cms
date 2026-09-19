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
 * @var bool $claude  je zapnuté rozšíření Napojení na Claude
 * @var list<array<string, mixed>> $tokeny
 * @var string $novyToken  právě vytvořený token (zobrazí se jen jednou)
 * @var string $adresaMcp
 */
$akce = e($app->url('admin.php?akce=ucet'));
?>
<?php if ($zalozniKody !== []): ?>
<div class="hlaska hlaska-ok">
	<p><strong><?= e(t('Dvoufázové přihlášení je zapnuté.')) ?></strong> <?= e(t('Uložte si záložní kódy – každý jde použít jednou, když nebudete mít telefon. Už se nezobrazí.')) ?></p>
	<p class="zalozni-kody"><?= implode(' &nbsp; ', array_map(e(...), $zalozniKody)) ?></p>
</div>
<?php endif ?>

<form class="formular" method="post" action="<?= $akce ?>">
<?= $csrf ?><input type="hidden" name="co" value="profil">
<fieldset><legend><?= e(t('Moje údaje')) ?></legend>
<div class="radek"><span class="popisek"><?= e(t('Přihlašovací jméno')) ?></span><div><?= e($user['user']) ?> <span class="napoveda"><?= e(t('Mění administrátor v sekci Uživatelé.')) ?></span></div></div>
<div class="radek"><label for="jmeno"><?= e(t('Jméno')) ?></label><div><input class="textpole siroke" type="text" id="jmeno" name="jmeno" value="<?= e($user['jmeno']) ?>" maxlength="100"><span class="napoveda"><?= e(t('Zobrazuje se u článků na webu.')) ?></span></div></div>
<div class="radek"><label for="email"><?= e(t('E-mail')) ?></label><input class="textpole siroke" type="email" id="email" name="email" value="<?= e($user['email']) ?>" maxlength="190"></div>
<div class="radek"><label for="url"><?= e(t('Můj web')) ?></label><input class="textpole siroke" type="url" id="url" name="url" value="<?= e($user['url']) ?>" maxlength="255" placeholder="https://"></div>
<div class="radek"><label for="jazyk"><?= e(t('Jazyk administrace')) ?></label><div><select id="jazyk" name="jazyk">
<?php foreach (PhpRS\Core\Jazyk::ADMINISTRACE as $kodJazyka => $nazevJazyka): ?>
	<option value="<?= e($kodJazyka) ?>"<?= ($user['jazyk'] ?: 'cs') === $kodJazyka ? ' selected' : '' ?>><?= e($nazevJazyka) ?></option>
<?php endforeach ?>
</select><span class="napoveda"><?= e(t('Language / Jazyk. Přeložené je menu, přehled, Můj účet a psaní článků; nastavení webu zůstává česky.')) ?></span></div></div>
</fieldset>
<p class="tlacitka"><input class="tl" type="submit" value="<?= e(t('Uložit údaje')) ?>"></p>
</form>

<form class="formular" method="post" action="<?= $akce ?>" autocomplete="off" style="margin-top:20px">
<?= $csrf ?><input type="hidden" name="co" value="heslo">
<fieldset><legend><?= e(t('Změna hesla')) ?></legend>
<div class="radek"><label for="soucasne"><?= e(t('Současné heslo')) ?></label><input class="textpole" type="password" id="soucasne" name="soucasne" size="30" autocomplete="current-password" required></div>
<div class="radek"><label for="nove"><?= e(t('Nové heslo')) ?></label><div><input class="textpole" type="password" id="nove" name="nove" size="30" minlength="10" autocomplete="new-password" required><span class="napoveda"><?= e(t('Alespoň 10 znaků.')) ?></span></div></div>
<div class="radek"><label for="nove2"><?= e(t('Nové heslo znovu')) ?></label><input class="textpole" type="password" id="nove2" name="nove2" size="30" autocomplete="new-password" required></div>
</fieldset>
<p class="tlacitka"><input class="tl" type="submit" value="<?= e(t('Změnit heslo')) ?>"></p>
</form>

<form class="formular" method="post" action="<?= $akce ?>" autocomplete="off" style="margin-top:20px">
<?= $csrf ?>
<fieldset><legend><?= e(t('Dvoufázové přihlášení')) ?></legend>
<?php if ($user['totp_tajemstvi'] !== ''): ?>
<p><span class="stitek stitek-vydano"><?= e(t('zapnuté')) ?></span> Při přihlášení zadáváte kromě hesla i kód z aplikace. Zbývá záložních kódů: <?= $zbyvaKodu ?>.</p>
<input type="hidden" name="co" value="totp_vypni">
<div class="radek"><label for="vyp-heslo"><?= e(t('Heslo pro potvrzení')) ?></label><input class="textpole" type="password" id="vyp-heslo" name="soucasne" size="30" autocomplete="current-password" required></div>
<p class="tlacitka"><button class="navigace" type="submit"><?= e(t('Vypnout dvoufázové přihlášení')) ?></button></p>
<?php elseif ($noveTajemstvi !== ''): ?>
<input type="hidden" name="co" value="totp_potvrd">
<ol>
	<li><?= e(t('V ověřovací aplikaci (Google Authenticator, Microsoft Authenticator, 1Password, Aegis…) přidejte nový účet ručním zadáním klíče:')) ?><br><code class="totp-klic"><?= e(trim(chunk_split($noveTajemstvi, 4, ' '))) ?></code><br><small>Na mobilu můžete použít <a href="<?= e($uri) ?>">tento odkaz</a>, který aplikaci otevře.</small></li>
	<li><?= e(t('Opište šestimístný kód, který aplikace ukazuje:')) ?></li>
</ol>
<div class="radek"><label for="kod"><?= e(t('Kód z aplikace')) ?></label><input class="textpole" type="text" id="kod" name="kod" size="12" maxlength="7" inputmode="numeric" autocomplete="one-time-code" required autofocus></div>
<p class="tlacitka"><input class="tl" type="submit" value="<?= e(t('Potvrdit a zapnout')) ?>"></p>
<?php else: ?>
<p><?= e(t('Účet je chráněný jen heslem. S dvoufázovým přihlášením se bez vašeho telefonu nepřihlásí ani ten, kdo heslo uhodne nebo ukradne.')) ?></p>
<input type="hidden" name="co" value="totp_start">
<p class="tlacitka"><input class="tl" type="submit" value="<?= e(t('Zapnout dvoufázové přihlášení')) ?>"></p>
<?php endif ?>
</fieldset>
</form>

<?php if ($claude): ?>
<form class="formular" method="post" action="<?= $akce ?>" style="margin-top:20px">
<?= $csrf ?>
<fieldset><legend><?= e(t('Napojení na Claude')) ?></legend>
<?php if ($novyToken !== ''): ?>
<div class="hlaska hlaska-ok">
	<p><strong><?= e(t('Token je vytvořený.')) ?></strong> <?= e(t('Zkopírujte si ho teď – už se nezobrazí.')) ?></p>
	<p><code class="totp-klic"><?= e($novyToken) ?></code></p>
	<p><?= e(t('V Claude Code spusťte:')) ?></p>
	<p><code class="totp-klic" style="font-size:12px">claude mcp add --transport http phprs <?= e($adresaMcp) ?> --header "Authorization: Bearer <?= e($novyToken) ?>"</code></p>
	<p class="napoveda">V aplikaci Claude přidejte vlastní konektor s adresou <?= e($adresaMcp) ?> a stejnou hlavičkou Authorization.</p>
</div>
<?php endif ?>
<p><?= e(t('Claude bude s webem pracovat')) ?> <strong><?= e(t('vaším jménem a s vašimi právy')) ?></strong>: psát a upravovat články<?= (int) $user['admin'] === 2 ? ', spravovat bloky a tvořit šablony webu' : '' ?>. Nové články zakládá jako koncepty. Všechny jeho zásahy najdete v Protokolu změn. Token chraňte jako heslo.</p>
<?php foreach ($tokeny as $t): ?>
<p><span class="stitek"><?= e($t['nazev']) ?></span> vytvořen <?= e(datum($t['vytvoren'])) ?>, <?= $t['pouzit'] ? 'naposledy použit ' . e(datum($t['pouzit'], true)) : 'zatím nepoužit' ?>
	<button class="navigace" type="submit" name="smaz_token" value="<?= (int) $t['idt'] ?>" data-potvrdit="<?= e(t('Zrušit token? Claude se jím už nepřihlásí.')) ?>"><?= e(t('Zrušit')) ?></button></p>
<?php endforeach ?>
<div class="radek"><label for="token-nazev"><?= e(t('Název nového tokenu')) ?></label><input class="textpole" type="text" id="token-nazev" name="nazev" maxlength="100" size="30" placeholder="<?= e(t('např. Claude na notebooku')) ?>"></div>
<p class="tlacitka"><button class="tl" type="submit" name="co" value="token_novy"><?= e(t('Vytvořit token')) ?></button></p>
</fieldset>
</form>
<?php endif ?>
