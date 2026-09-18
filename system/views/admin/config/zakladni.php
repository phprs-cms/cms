<?php /** Záložka Základní. Proměnné viz vypis.php. */ ?>
<fieldset>
<legend>Web</legend>
<?php
$pole('nazev_webu', 'Název webu', 'text', '', 'maxlength="150" required');
$pole('popis_webu', 'Popis webu', 'radky', 'Jedna až dvě věty. Použije se jako motto, popis pro vyhledávače a v RSS.');
$pole('klicova_slova', 'Klíčová slova', 'text');
$pole('email_webu', 'E-mail redakce', 'email');
$pole('text_paticky', 'Text v patičce', 'text', 'Například vydavatel, ISSN nebo kontakt.', 'maxlength="300"');
?>
</fieldset>
<fieldset>
<legend>Sociální sítě</legend>
<?php foreach (PhpRS\Admin\Moduly\Konfigurace::SITE as $klic => $nazev) { $pole($klic, $nazev, 'url', '', 'placeholder="https://"'); } ?>
<p class="napoveda">Vyplněné profily se zobrazí v patičce webu a ve strukturovaných datech pro vyhledávače.</p>
</fieldset>
<fieldset>
<legend>Obsah</legend>
<?php
$pole('pocet_clanku', 'Článků na stránku', 'cislo', '', 'min="1" max="100" style="width:90px"');
$pole('pocet_novinek', 'Novinek v bloku', 'cislo', '', 'min="0" max="50" style="width:90px"');
$pole('hlidat_platnost', 'Stahovat články z hlavní stránky', 'ano', 'Článek po svém „datu stažení“ zmizí z hlavní stránky; v rubrice zůstane.');
$pole('povolit_komentare', 'Komentáře u článků', 'ano', 'U jednotlivého článku je lze vypnout v jeho Dalším nastavení.');
?>
<div class="radek">
	<label for="komentare_rezim">Zveřejňování komentářů</label>
	<div><select id="komentare_rezim" name="komentare_rezim">
		<option value="hned"<?= $hodnoty['komentare_rezim'] === 'hned' ? ' selected' : '' ?>>Hned – podezřelé komentáře čekají na schválení</option>
		<option value="schvalovat"<?= $hodnoty['komentare_rezim'] === 'schvalovat' ? ' selected' : '' ?>>Až po schválení redakcí</option>
	</select></div>
</div>
<?php
$pole('povolit_hodnoceni', 'Hodnocení článků hvězdičkami', 'ano');
?>
</fieldset>
