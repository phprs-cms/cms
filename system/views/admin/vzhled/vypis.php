<?php
/**
 * Identita webu: šablona, logo, barva a písma s živou ukázkou.
 *
 * @var PhpRS\Core\App $app
 * @var PhpRS\Admin\Moduly\Vzhled $modul
 * @var string $csrf
 * @var array<string, array{nazev:string, popis:string, rozvrzeni:string}> $layouty
 * @var array<string, string> $hodnoty
 */
use PhpRS\Front\Identita;

$nahledy = require dirname(__DIR__, 2) . '/install/nahledy.php';
$barvy = ['#1f4fe0' => 'modrá', '#326891' => 'novinová modrá', '#b3261e' => 'červená', '#c2410c' => 'oranžová', '#0f766e' => 'smaragdová',
    '#15803d' => 'zelená', '#7c3aed' => 'fialová', '#be185d' => 'purpurová', '#ff2d6f' => 'magazínová růžová', '#111111' => 'černá'];
$akcent = $hodnoty['brand_akcent'] !== '' ? $hodnoty['brand_akcent'] : '#1f4fe0';
?>
<form class="formular identita" method="post" action="<?= e($modul->url('uloz')) ?>" data-identita>
<?= $csrf ?>
<fieldset>
<legend>Šablona</legend>
<div class="karty-volby">
<?php foreach ($layouty as $slozka => $l): ?>
	<label class="karta-volba">
		<input type="radio" name="layout" value="<?= e($slozka) ?>"<?= $hodnoty['layout'] === $slozka ? ' checked' : '' ?>>
		<?= $nahledy[$slozka] ?? $nahledy['default'] ?>
		<strong><?= e($l['nazev']) ?></strong>
		<span><?= e($l['popis']) ?></span>
	</label>
<?php endforeach ?>
</div>
<p class="napoveda">Bloky a rozvržení stránky doladíte přímo na webu v sekci <a href="<?= e($app->url('admin.php?modul=bloky')) ?>">Bloky a rozvržení</a>.</p>
</fieldset>

<fieldset>
<legend>Logo</legend>
<div class="radek"><label for="logo_webu">Logo</label><div><input class="textpole siroke" type="text" id="logo_webu" name="logo_webu" value="<?= e($hodnoty['logo_webu']) ?>" maxlength="255" placeholder="bez loga se v záhlaví zobrazí název webu" data-obrazek><span class="napoveda">Nejlépe PNG s průhledným pozadím, výška aspoň 120 px.</span></div></div>
<div class="radek"><label for="favicon">Ikona webu</label><div><input class="textpole siroke" type="text" id="favicon" name="favicon" value="<?= e($hodnoty['favicon']) ?>" maxlength="255" data-obrazek><span class="napoveda">Malý čtvercový obrázek na kartě prohlížeče a v záložkách. Stačí 256×256 px.</span></div></div>
</fieldset>

<fieldset>
<legend>Barva</legend>
<div class="radek">
	<span class="popisek">Hlavní barva</span>
	<div>
		<label class="identita-prepinac"><input type="radio" name="akcent_vlastni" value="0"<?= $hodnoty['brand_akcent'] === '' ? ' checked' : '' ?>> barva šablony</label>
		<label class="identita-prepinac"><input type="radio" name="akcent_vlastni" value="1"<?= $hodnoty['brand_akcent'] !== '' ? ' checked' : '' ?>> vlastní:</label>
		<input type="color" name="brand_akcent" value="<?= e($akcent) ?>" aria-label="Vlastní hlavní barva">
		<div class="identita-barvy">
<?php foreach ($barvy as $hex => $nazev): ?>
			<button type="button" data-barva="<?= e($hex) ?>" style="background:<?= e($hex) ?>" title="<?= e($nazev) ?>" aria-label="<?= e($nazev) ?>"></button>
<?php endforeach ?>
		</div>
		<span class="napoveda">Použije se na odkazy, štítky rubrik, tlačítka a zvýraznění. <strong data-kontrast hidden>Pozor: tahle barva je na bílém pozadí špatně čitelná – zvolte tmavší.</strong></span>
	</div>
</div>
</fieldset>

<fieldset>
<legend>Písmo</legend>
<div class="radek">
	<label for="brand_pismo_titulky">Titulky</label>
	<select id="brand_pismo_titulky" name="brand_pismo_titulky">
<?php foreach (Identita::PISMA_TITULKU as $klic => [$nazev, $popis, $css]): ?>
		<option value="<?= e($klic) ?>" data-css="<?= e($css) ?>"<?= $hodnoty['brand_pismo_titulky'] === $klic ? ' selected' : '' ?>><?= e($nazev . ($popis !== '' ? ' – ' . $popis : '')) ?></option>
<?php endforeach ?>
	</select>
</div>
<div class="radek">
	<label for="brand_pismo_text">Text článků</label>
	<div><select id="brand_pismo_text" name="brand_pismo_text">
<?php foreach (Identita::PISMA_TEXTU as $klic => [$nazev, $popis, $css]): ?>
		<option value="<?= e($klic) ?>" data-css="<?= e($css) ?>"<?= $hodnoty['brand_pismo_text'] === $klic ? ' selected' : '' ?>><?= e($nazev . ($popis !== '' ? ' – ' . $popis : '')) ?></option>
<?php endforeach ?>
	</select>
	<span class="napoveda">Písma jsou systémová: nic se nestahuje z cizích serverů, web je rychlý a nepotřebuje kvůli nim souhlas návštěvníka.</span></div>
</div>
</fieldset>

<fieldset>
<legend>Ukázka</legend>
<div class="identita-ukazka" data-ukazka>
	<span class="identita-ukazka-rubrika">Kultura</span>
	<h3><?= e($hodnoty['nazev_webu']) ?>: titulek článku vypadá takto</h3>
	<p>Takhle bude vypadat běžný text článku. Obsahuje i <a href="#" onclick="return false">odkaz v hlavní barvě</a> a dost slov na to, abyste posoudili čitelnost zvoleného písma.</p>
	<span class="identita-ukazka-tlacitko">Tlačítko</span>
</div>
<p class="napoveda">Ukázka je orientační – skutečný výsledek uvidíte po uložení na webu.</p>
</fieldset>
<p class="tlacitka"><input class="tl" type="submit" value="Uložit"> <a class="navigace" href="<?= e($app->url('')) ?>" target="_blank" rel="noopener">Zobrazit web</a></p>
</form>
