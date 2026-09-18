<?php /** Záložka Měření. */ ?>
<fieldset>
<legend>Vestavěná statistika</legend>
<?php $pole('statistika', 'Měřit návštěvnost', 'ano', 'Návštěvy, zobrazení stránek, nejčtenější články a zdroje návštěv. Nepoužívá cookies ani neukládá IP adresy, takže nepotřebuje souhlas. Výsledky jsou v sekci Statistika.'); ?>
</fieldset>
<p class="hlaska">Externí nástroje níže se spouštějí podle nastavení v záložce Soukromí a cookies.</p>
<fieldset>
<legend>Google Analytics 4</legend>
<?php $pole('ga4_id', 'ID měření', 'text', 'Ve tvaru G-XXXXXXXXXX. Systém vloží měřicí kód včetně Consent Mode v2.', 'placeholder="G-" maxlength="24"'); ?>
</fieldset>
<fieldset>
<legend>Matomo</legend>
<?php
$pole('matomo_url', 'Adresa Matomo', 'url', 'Adresa vaší instalace, např. https://statistiky.example.cz/', 'placeholder="https://"');
$pole('matomo_id', 'ID webu', 'cislo', '', 'min="0" style="width:110px"');
?>
</fieldset>
<fieldset>
<legend>Plausible</legend>
<?php $pole('plausible_domena', 'Doména webu', 'text', 'Plausible nepoužívá cookies, proto se načítá vždy bez souhlasu.', 'placeholder="example.cz" maxlength="100"'); ?>
</fieldset>
<fieldset>
<legend>Vlastní kód</legend>
<?php $pole('kod_hlava', 'Kód do hlavičky stránky', 'kod', 'Vloží se před &lt;/head&gt; na každé stránce bez ohledu na souhlas – jen pro kódy, které neukládají cookies (ověření vlastnictví, písma apod.).', 'spellcheck="false"'); ?>
</fieldset>
