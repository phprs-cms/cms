<?php /** Záložka Soukromí a cookies. */ ?>
<fieldset>
<legend>Cookie lišta a souhlasy</legend>
<div class="radek">
	<label for="cookies_rezim">Řešení souhlasů</label>
	<div><select id="cookies_rezim" name="cookies_rezim">
		<option value="vestavena"<?= $hodnoty['cookies_rezim'] === 'vestavena' ? ' selected' : '' ?>>Vestavěná lišta phpRS</option>
		<option value="externi"<?= $hodnoty['cookies_rezim'] === 'externi' ? ' selected' : '' ?>>Externí služba (Cookiebot, CookieYes, Usercentrics…)</option>
		<option value="zadna"<?= $hodnoty['cookies_rezim'] === 'zadna' ? ' selected' : '' ?>>Žádná – měřicí kódy se spouštějí hned</option>
	</select>
	<span class="napoveda">Vestavěná lišta se zobrazí, jen když je co odsouhlasit (vyplněné Google Analytics, Matomo nebo marketingový kód). Měřicí kódy se spustí až po souhlasu návštěvníka. Volbu „Žádná“ použijte jen tehdy, když souhlas řešíte jinak nebo ho nepotřebujete.</span></div>
</div>
<?php
$pole('cookies_text', 'Text lišty', 'radky');
$pole('cookies_zasady_url', 'Odkaz na zásady', 'text', 'Adresa stránky se zásadami ochrany soukromí, např. /zasady-ochrany-soukromi (vytvoříte ji v sekci Stránky).', 'maxlength="255"');
?>
</fieldset>
<fieldset>
<legend>Externí služba</legend>
<?php $pole('cookies_externi_kod', 'Kód služby', 'kod', 'Vložte skript od poskytovatele (u Cookiebotu řádek s data-cbid). Načte se jako první v hlavičce. Měřicí kódy phpRS označí atributy <code>type="text/plain"</code> a <code>data-cookieconsent</code>, kterým Cookiebot a kompatibilní služby rozumí.', 'spellcheck="false"'); ?>
</fieldset>
<fieldset>
<legend>Marketingové kódy</legend>
<?php $pole('kod_marketing', 'Kód spouštěný po souhlasu s marketingem', 'kod', 'Meta Pixel, Sklik retargeting, Google Ads apod. Vkládejte celé značky &lt;script&gt;.', 'spellcheck="false"'); ?>
</fieldset>
