<?php /** Záložka Základní. Proměnné a funkce $pole viz vypis.php. */ ?>
<fieldset>
<legend><?= e(t('Web')) ?></legend>
<?php
$pole('nazev_webu', 'Název webu', 'text', '', 'maxlength="150" required');
$pole('popis_webu', 'Popis webu', 'radky', 'Jedna až dvě věty – motto, popis pro vyhledávače a RSS.');
$pole('email_webu', 'E-mail redakce', 'email', 'Chodí na něj upozornění systému.');
?>
<div class="radek">
	<label for="jazyk_webu"><?= e(t('Jazyk webu')) ?></label>
	<div><select id="jazyk_webu" name="jazyk_webu">
<?php foreach (PhpRS\Core\Jazyk::DOSTUPNE as $kod => [$nazevJazyka]): ?>
		<option value="<?= e($kod) ?>"<?= $hodnoty['jazyk_webu'] === $kod ? ' selected' : '' ?>><?= e($nazevJazyka) ?></option>
<?php endforeach ?>
	</select>
	<span class="napoveda"><?= e(t('V tomto jazyce jsou texty šablony (Hledat, Celý článek, Komentáře…) a web se tak hlásí vyhledávačům.')) ?></span></div>
</div>
<?php if (PhpRS\Core\Rozsireni::je($app->settings(), 'jazyky')): ?>
<div class="radek">
	<span class="popisek"><?= e(t('Další jazykové verze')) ?></span>
	<div class="volby">
<?php foreach (PhpRS\Core\Jazyk::DOSTUPNE as $kod => [$nazevJazyka]): if ($kod === $hodnoty['jazyk_webu']) { continue; } ?>
		<label><input type="checkbox" name="jazyky_dalsi[]" value="<?= e($kod) ?>"<?= in_array($kod, explode(',', $hodnoty['jazyky_dalsi']), true) ? ' checked' : '' ?>> <?= e($nazevJazyka) ?> <small>(/<?= e($kod) ?>/)</small></label><br>
<?php endforeach ?>
		<span class="napoveda"><?= e(t('Každá verze má své rubriky, články a stránky. Jazyk se volí u rubriky – článek ho převezme. Překlad článku propojíte v jeho editoru.')) ?></span>
	</div>
</div>
<?php else: ?>
<?php foreach (array_filter(explode(',', $hodnoty['jazyky_dalsi'])) as $kod): ?><input type="hidden" name="jazyky_dalsi[]" value="<?= e($kod) ?>"><?php endforeach ?>
<?php endif ?>
</fieldset>
<fieldset>
<legend><?= e(t('Články a čtenáři')) ?></legend>
<?php
$pole('pocet_clanku', 'Článků na stránku', 'cislo', '', 'min="1" max="100" style="width:90px"');
$pole('povolit_komentare', 'Komentáře pod články', 'ano');
?>
<div class="radek">
	<label for="komentare_rezim"><?= e(t('Nový komentář')) ?></label>
	<select id="komentare_rezim" name="komentare_rezim">
		<option value="hned"<?= $hodnoty['komentare_rezim'] === 'hned' ? ' selected' : '' ?>><?= e(t('zveřejnit hned (podezřelé počkají na schválení)')) ?></option>
		<option value="schvalovat"<?= $hodnoty['komentare_rezim'] === 'schvalovat' ? ' selected' : '' ?>><?= e(t('zveřejnit až po schválení redakcí')) ?></option>
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
<?php if (PhpRS\Core\Rozsireni::je($app->settings(), 'ctenari')): ?>
<details class="pokrocile">
<summary><?= e(t('Čtenáři a zamčený obsah')) ?></summary>
<?php
$pole('ctenari_registrace', 'Povolit nové registrace', 'ano');
$pole('zamek_odstavcu', 'Ukázka zamčeného článku', 'cislo', 'Kolik odstavců textu uvidí čtenář bez přístupu (perex vidí vždy). 0 = jen perex.', 'min="0" max="10" style="width:90px"');
$pole('paywall_zdarma', 'Článků zdarma měsíčně', 'cislo', 'Měkký paywall: tolik zamčených článků si každý měsíc přečte kdokoli bez přihlášení, potom uvidí výzvu. 0 = vypnuto. Počítá se v prohlížeči čtenáře, vyhledávače vidí články celé.', 'min="0" max="50" style="width:90px"');
$pole('zamek_text', 'Text výzvy pod ukázkou', 'text', 'Nepovinné – například proč se registrovat nebo jak získat předplatné.', 'maxlength="300"');
?>
</details>
<?php else: ?>
<input type="hidden" name="ctenari_registrace" value="<?= e($hodnoty['ctenari_registrace']) ?>"><input type="hidden" name="paywall_zdarma" value="<?= e($hodnoty['paywall_zdarma']) ?>"><input type="hidden" name="zamek_odstavcu" value="<?= e($hodnoty['zamek_odstavcu']) ?>"><input type="hidden" name="zamek_text" value="<?= e($hodnoty['zamek_text']) ?>">
<?php endif ?>
<details class="pokrocile">
<summary><?= e(t('Sociální sítě')) ?></summary>
<?php foreach (PhpRS\Admin\Moduly\Konfigurace::SITE as $klic => $nazev) { $pole($klic, $nazev, 'url', '', 'placeholder="https://"'); } ?>
<p class="napoveda"><?= e(t('Vyplněné profily se zobrazí v patičce webu a předají se vyhledávačům.')) ?></p>
</details>
<details class="pokrocile">
<summary><?= e(t('Další možnosti')) ?></summary>
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
