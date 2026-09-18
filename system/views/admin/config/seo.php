<?php /** Záložka SEO a GEO. */ ?>
<fieldset>
<legend>Vyhledávače (SEO)</legend>
<?php
$pole('indexovani', 'Povolit indexování webu', 'ano', 'Vypněte jen u webu ve výstavbě: stránky dostanou noindex a robots.txt zakáže procházení.');
$pole('schema_org', 'Strukturovaná data', 'ano', 'Schema.org (NewsArticle, Organization, BreadcrumbList) – vyhledávače díky nim zobrazí titulek, datum, autora a obrázek.');
$pole('og_obrazek', 'Výchozí obrázek pro sdílení', 'text', 'Použije se na sociálních sítích u stránek bez vlastního obrázku. Ideálně 1200×630 px.', 'data-obrazek maxlength="255"');
$pole('overeni_google', 'Ověření Google Search Console', 'text', 'Jen hodnota atributu content z meta tagu google-site-verification.', 'maxlength="100"');
$pole('overeni_bing', 'Ověření Bing Webmaster', 'text', 'Hodnota content z meta tagu msvalidate.01.', 'maxlength="64"');
$pole('robots_extra', 'Vlastní pravidla robots.txt', 'radky', 'Připojí se na konec souboru. Sitemap a pravidla níže doplňuje systém sám.', 'spellcheck="false"');
?>
<p class="napoveda">Systém generuje: <a href="<?= e($adresaWebu) ?>robots.txt" target="_blank" rel="noopener">robots.txt</a> · <a href="<?= e($adresaWebu) ?>sitemap.xml" target="_blank" rel="noopener">sitemap.xml</a> · <a href="<?= e($adresaWebu) ?>rss.xml" target="_blank" rel="noopener">rss.xml</a>. Adresu sitemapy vložte do Search Console.</p>
</fieldset>
<fieldset>
<legend>AI vyhledávače a asistenti (GEO)</legend>
<div class="radek">
	<label for="ai_crawlery">Roboti AI služeb</label>
	<div><select id="ai_crawlery" name="ai_crawlery">
		<option value="povolit"<?= $hodnoty['ai_crawlery'] === 'povolit' ? ' selected' : '' ?>>Povolit – obsah se může objevit v odpovědích AI s odkazem na web</option>
		<option value="zakazat"<?= $hodnoty['ai_crawlery'] === 'zakazat' ? ' selected' : '' ?>>Zakázat – GPTBot, ClaudeBot, PerplexityBot, Google-Extended a další</option>
	</select>
	<span class="napoveda">Zapisuje se do robots.txt. Slušní roboti pravidlo respektují; nejde o technickou ochranu.</span></div>
</div>
<?php
$pole('llms_txt', 'Soubor llms.txt', 'ano', 'Stručný průvodce webem pro jazykové modely: název, popis, rubriky a nejnovější články. <a href="' . e($adresaWebu) . 'llms.txt" target="_blank" rel="noopener">Zobrazit</a>');
$pole('markdown_clanky', 'Čistá verze článků', 'ano', 'Každý článek je dostupný i jako prostý Markdown na adrese /clanek/adresa.md – bez navigace a reklam, s autorem, datem a zdrojem.');
?>
</fieldset>
