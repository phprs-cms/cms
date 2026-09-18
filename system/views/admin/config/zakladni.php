<?php /** Záložka Základní. Proměnné a funkce $pole viz vypis.php. */ ?>
<fieldset>
<legend>Web</legend>
<?php
$pole('nazev_webu', 'Název webu', 'text', '', 'maxlength="150" required');
$pole('popis_webu', 'Popis webu', 'radky', 'Jedna až dvě věty – motto, popis pro vyhledávače a RSS.');
$pole('email_webu', 'E-mail redakce', 'email', 'Chodí na něj upozornění systému.');
?>
</fieldset>
<fieldset>
<legend>Články a čtenáři</legend>
<?php
$pole('pocet_clanku', 'Článků na stránku', 'cislo', '', 'min="1" max="100" style="width:90px"');
$pole('povolit_komentare', 'Komentáře pod články', 'ano');
?>
<div class="radek">
	<label for="komentare_rezim">Nový komentář</label>
	<select id="komentare_rezim" name="komentare_rezim">
		<option value="hned"<?= $hodnoty['komentare_rezim'] === 'hned' ? ' selected' : '' ?>>zveřejnit hned (podezřelé počkají na schválení)</option>
		<option value="schvalovat"<?= $hodnoty['komentare_rezim'] === 'schvalovat' ? ' selected' : '' ?>>zveřejnit až po schválení redakcí</option>
	</select>
</div>
</fieldset>
<details class="pokrocile"<?= $hodnoty['udrzba'] === '1' ? ' open' : '' ?>>
<summary>Režim údržby<?= $hodnoty['udrzba'] === '1' ? ' – ZAPNUTÝ' : '' ?></summary>
<?php
$pole('udrzba', 'Web je dočasně mimo provoz', 'ano', 'Návštěvníci uvidí jen oznámení níže. Přihlášená redakce vidí web normálně.');
$pole('udrzba_text', 'Text oznámení', 'text', '', 'maxlength="300"');
?>
</details>
<details class="pokrocile">
<summary>Sociální sítě</summary>
<?php foreach (PhpRS\Admin\Moduly\Konfigurace::SITE as $klic => $nazev) { $pole($klic, $nazev, 'url', '', 'placeholder="https://"'); } ?>
<p class="napoveda">Vyplněné profily se zobrazí v patičce webu a předají se vyhledávačům.</p>
</details>
<details class="pokrocile">
<summary>Další možnosti</summary>
<?php
$pole('text_paticky', 'Text v patičce', 'text', 'Například vydavatel, ISSN nebo kontakt.', 'maxlength="300"');
$pole('klicova_slova', 'Klíčová slova webu', 'text');
$pole('pocet_novinek', 'Novinek v bloku', 'cislo', '', 'min="0" max="50" style="width:90px"');
$pole('povolit_hodnoceni', 'Hodnocení článků hvězdičkami', 'ano');
$pole('hlidat_platnost', 'Stahovat články z hlavní stránky', 'ano', 'Článek po svém „datu stažení“ zmizí z hlavní stránky; v rubrice zůstane.');
$pole('webhook_url', 'Webhook po vydání článku', 'url', 'Adresa ze služby Make, Zapier, IFTTT nebo n8n. Po vydání článku na ni systém pošle titulek, perex, adresu a obrázek – služba je pak sama sdílí na Facebook, X, Mastodon, do Slacku apod.', 'placeholder="https://"');
$pole('cache_stranek', 'Cache stránek', 'ano', 'Hotové stránky se čtenářům podávají z paměti – web je rychlejší a vydrží nápor. Nechte zapnuté.');
?>
</details>
