<?php /** Záložka Pošta: odkud a jak web odesílá e-maily. Proměnné a funkce $pole viz vypis.php. */ ?>
<p class="hlaska">Web posílá potvrzení odběru newsletteru a registrace čtenářů, nová hesla, newslettery a upozornění redakci. Přes vlastní SMTP server zprávy odcházejí z ověřené schránky a nekončí ve spamu.</p>
<fieldset>
<legend>Způsob odesílání</legend>
<div class="karty-volby">
	<label class="karta-volba"><input type="radio" name="posta_rezim" value="mail"<?= $hodnoty['posta_rezim'] !== 'smtp' ? ' checked' : '' ?> data-prepni="smtp:0"><strong>Server hostingu</strong><span>Funkce mail(). Nic se nenastavuje; u některých hostingů ale zprávy padají do spamu.</span></label>
	<label class="karta-volba"><input type="radio" name="posta_rezim" value="smtp"<?= $hodnoty['posta_rezim'] === 'smtp' ? ' checked' : '' ?> data-prepni="smtp:1"><strong>Vlastní SMTP server</strong><span>Schránka u hostingu, Google Workspace, Seznam, nebo služba Brevo, Mailgun, Amazon SES… Doporučeno pro newslettery.</span></label>
</div>
</fieldset>
<fieldset data-sekce="smtp"<?= $hodnoty['posta_rezim'] === 'smtp' ? '' : ' hidden' ?>>
<legend>SMTP server</legend>
<?php
$pole('smtp_host', 'Adresa serveru', 'text', 'Například smtp.gmail.com, smtp.seznam.cz, smtp-relay.brevo.com nebo smtp.vasedomena.cz.', 'maxlength="120" placeholder="smtp.example.com" autocomplete="off"');
?>
<div class="radek">
	<label for="smtp_sifrovani">Zabezpečení</label>
	<div><select id="smtp_sifrovani" name="smtp_sifrovani">
		<option value="tls"<?= $hodnoty['smtp_sifrovani'] === 'tls' ? ' selected' : '' ?>>STARTTLS – port 587 (nejběžnější)</option>
		<option value="ssl"<?= $hodnoty['smtp_sifrovani'] === 'ssl' ? ' selected' : '' ?>>SSL/TLS – port 465</option>
		<option value="zadne"<?= $hodnoty['smtp_sifrovani'] === 'zadne' ? ' selected' : '' ?>>žádné – jen pro server ve vlastní síti</option>
	</select></div>
</div>
<?php
$pole('smtp_port', 'Port', 'cislo', '', 'min="1" max="65535" style="width:100px"');
$pole('smtp_uzivatel', 'Přihlašovací jméno', 'text', 'Obvykle celá e-mailová adresa schránky.', 'maxlength="190" autocomplete="off"');
?>
<div class="radek">
	<label for="smtp_heslo">Heslo</label>
	<div><input class="textpole siroke" type="password" id="smtp_heslo" name="smtp_heslo" value="" autocomplete="new-password" placeholder="<?= $hodnoty['smtp_heslo'] !== '' ? 'heslo je uložené – nové vložte jen při změně' : '' ?>">
	<span class="napoveda">U Gmailu a Seznamu použijte „heslo pro aplikace“, ne heslo k účtu. Heslo se ukládá jen na vašem webu a nikdy se nevypisuje zpět.</span>
<?php if ($hodnoty['smtp_heslo'] !== ''): ?>
	<label><input type="checkbox" name="smtp_heslo_smazat" value="1"> Odebrat uložené heslo</label>
<?php endif ?>
	</div>
</div>
</fieldset>
<details class="pokrocile"<?= $hodnoty['posta_od'] !== '' || $hodnoty['posta_odpoved'] !== '' ? ' open' : '' ?>>
<summary>Odesílatel a odpovědi</summary>
<?php
$pole('posta_od', 'Adresa odesílatele', 'email', 'Prázdné = E-mail redakce. U SMTP musí jít o adresu, ze které smí vaše schránka odesílat.');
$pole('posta_odpoved', 'Odpovědi posílat na', 'email', 'Nepovinné – když mají odpovědi čtenářů chodit jinam než odesílateli.');
?>
</details>
<p><button class="navigace" type="submit" formaction="<?= e($modul->url('test_posty')) ?>">Odeslat zkušební e-mail na adresu redakce</button> <span class="smltxt">Nejdřív nastavení uložte – zkouška použije uložené hodnoty.</span></p>
