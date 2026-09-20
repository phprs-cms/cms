<?php
/**
 * Účet čtenáře: přihlášení a registrace, po přihlášení Můj účet.
 *
 * @var array<string, mixed>|null $ctenar
 * @var bool $predplatitel
 * @var string $akce
 * @var string $zpet     kam se po přihlášení vrátit (cesta na webu)
 * @var string $stav     výsledek předchozí akce
 * @var string $pole     skrytá pole antispamu
 * @var string $podpis   podpis formulářů přihlášeného čtenáře
 * @var bool $registrace jsou povolené nové registrace
 * @var string $koren  kořen webu bez předpony jazykové verze
 * @var list<array<string, mixed>> $ulozene  uložené články přihlášeného čtenáře
 * @var bool $newsletter  web má newsletter - při registraci jde rovnou přihlásit odběr
 * @var callable(string): string $url
 */
$zpravy = [
    'poslano' => ['ok', 'Poslali jsme vám e-mail. Registraci dokončíte kliknutím na odkaz v něm a nastavením hesla.'],
    'odkaz-poslan' => ['ok', 'Pokud u nás tento e-mail má účet, poslali jsme na něj přihlašovací odkaz. Platí 20 minut.'],
    'heslo-poslano' => ['ok', 'Pokud u nás tento e-mail má účet, poslali jsme na něj odkaz pro nastavení nového hesla.'],
    'vitejte' => ['ok', 'Registrace je dokončená. Vítejte!'],
    'heslo-zmeneno' => ['ok', 'Heslo je změněné a jste přihlášeni.'],
    'ulozeno' => ['ok', 'Uloženo.'],
    'smazano' => ['ok', 'Váš účet i všechny údaje o něm jsme smazali.'],
    'spatne' => ['chyba', 'E-mail nebo heslo nesouhlasí.'],
    'zamceno' => ['chyba', 'Příliš mnoho chybných pokusů. Zkuste to znovu za 15 minut, nebo si nechte poslat přihlašovací odkaz e-mailem.'],
    'udaje' => ['chyba', 'Zadejte platný e-mail.'],
    'heslo-chyba' => ['chyba', 'Stávající heslo nesouhlasí, nebo je nové kratší než 8 znaků.'],
    'pomalu' => ['chyba', 'Formulář se nepodařilo ověřit. Počkejte pár vteřin a zkuste to znovu.'],
    'zavreno' => ['chyba', 'Nové registrace jsou teď vypnuté.'],
];
[$typ, $zprava] = $zpravy[$stav] ?? ['', ''];
$zprava = t($zprava);
$skryte = $pole . '<input type="hidden" name="zpet" value="' . e($zpet) . '">';
?>
<article class="clanek clanek-cely rs-ucet">
	<header class="clanek-hlavicka obal-uzky"><h1><?= e(t($ctenar === null ? 'Přihlášení čtenáře' : 'Můj účet')) ?></h1></header>
	<div class="obal-uzky">
<?php if ($zprava !== ''): ?>
	<p class="rs-zprava rs-zprava-<?= $typ ?>" role="status"><?= e($zprava) ?></p>
<?php endif ?>
<?php if ($ctenar === null): ?>
	<div class="rs-ucet-sloupce">
		<form class="rs-formular" method="post" action="<?= e($akce) ?>">
			<h2><?= e(t('Přihlásit se')) ?></h2>
			<?= $skryte ?><input type="hidden" name="akce" value="prihlaseni">
			<label><?= e(t('E-mail')) ?> <input type="email" name="email" required autocomplete="username" maxlength="190"></label>
			<label><?= e(t('Heslo')) ?> <input type="password" name="heslo" required autocomplete="current-password"></label>
			<button type="submit"><?= e(t('Přihlásit se')) ?></button>
			<button type="submit" name="akce" value="odkaz" formnovalidate class="rs-tl-vedlejsi"><?= e(t('Přihlásit se odkazem z e-mailu')) ?></button>
			<p class="rs-drobne"><?= e(t('Bez hesla: vyplňte jen e-mail a pošleme vám jednorázový přihlašovací odkaz.')) ?></p>
			<details>
				<summary><?= e(t('Zapomněli jste heslo?')) ?></summary>
				<p><?= e(t('Zadejte nahoře svůj e-mail a klepněte sem – pošleme vám odkaz pro nastavení nového hesla.')) ?></p>
				<button type="submit" name="akce" value="zapomenute" formnovalidate class="rs-tl-vedlejsi"><?= e(t('Poslat odkaz')) ?></button>
			</details>
		</form>
<?php if ($registrace): ?>
		<form class="rs-formular" method="post" action="<?= e($akce) ?>">
			<h2><?= e(t('Jsem tu poprvé')) ?></h2>
			<?= $skryte ?><input type="hidden" name="akce" value="registrace">
			<label><?= e(t('E-mail')) ?> <input type="email" name="email" required autocomplete="email" maxlength="190"></label>
			<label><?= e(t('Jméno')) ?> <small><?= e(t('(nepovinné)')) ?></small> <input type="text" name="jmeno" autocomplete="name" maxlength="80"></label>
<?php if ($newsletter): ?>
			<label class="rs-volba"><input type="checkbox" name="newsletter" value="1"> <?= e(t('Chci dostávat newsletter')) ?></label>
<?php endif ?>
			<button type="submit"><?= e(t('Zaregistrovat se zdarma')) ?></button>
			<p class="rs-drobne"><?= e(t('Pošleme vám e-mail s odkazem, na kterém si nastavíte heslo. Účet můžete kdykoli sami smazat.')) ?></p>
		</form>
<?php endif ?>
	</div>
<?php else: ?>
	<p><?= e(t('Přihlášen:')) ?> <strong><?= e($ctenar['email']) ?></strong>
<?php if ($predplatitel): ?>
		· <?= e(t('předplatné do')) ?> <strong><?= e(datum($ctenar['predplatne_do'])) ?></strong>
<?php endif ?>
	</p>
<?php if ($zpet !== ''): ?>
	<p><a class="rs-tl" href="<?= e($url($zpet)) ?>"><?= e(t('Pokračovat ve čtení')) ?></a></p>
<?php endif ?>
<?php if ($ulozene !== []): ?>
	<h2><?= e(t('Uložené články')) ?></h2>
	<ul class="rs-ulozene">
<?php foreach ($ulozene as $u): ?>
		<li><a href="<?= e($koren . ($u['jazyk'] !== '' ? $u['jazyk'] . '/' : '') . 'clanek/' . $u['seo_link']) ?>"><?= e($u['titulek']) ?></a> <small><?= e(datum($u['datum'])) ?></small>
			<form method="post" action="<?= e($akce) ?>"><input type="hidden" name="akce" value="ulozit"><input type="hidden" name="z_uctu" value="1"><input type="hidden" name="idc" value="<?= (int) $u['idc'] ?>"><input type="hidden" name="podpis" value="<?= e($podpis) ?>"><button type="submit" aria-label="<?= e(t('Odebrat z uložených')) ?>">×</button></form></li>
<?php endforeach ?>
	</ul>
<?php endif ?>
	<form class="rs-formular" method="post" action="<?= e($akce) ?>">
		<input type="hidden" name="akce" value="ucet"><input type="hidden" name="podpis" value="<?= e($podpis) ?>">
		<label><?= e(t('Jméno')) ?> <input type="text" name="jmeno" value="<?= e($ctenar['jmeno']) ?>" autocomplete="name" maxlength="80"></label>
		<details>
			<summary><?= e(t('Změnit heslo')) ?></summary>
			<label><?= e(t('Stávající heslo')) ?> <input type="password" name="heslo_stare" autocomplete="current-password"></label>
			<label><?= e(t('Nové heslo')) ?> <small><?= e(t('(aspoň 8 znaků)')) ?></small> <input type="password" name="heslo" minlength="8" autocomplete="new-password"></label>
		</details>
		<button type="submit"><?= e(t('Uložit')) ?></button>
	</form>
	<form method="post" action="<?= e($akce) ?>" class="rs-formular rs-formular-radek">
		<input type="hidden" name="akce" value="odhlasit">
		<button type="submit" class="rs-tl-vedlejsi"><?= e(t('Odhlásit se')) ?></button>
	</form>
	<details class="rs-formular">
		<summary><?= e(t('Smazat účet')) ?></summary>
		<form method="post" action="<?= e($akce) ?>">
			<input type="hidden" name="akce" value="smazat"><input type="hidden" name="podpis" value="<?= e($podpis) ?>">
			<p><?= e(t('Smažeme účet i všechny údaje o něm. Nejde to vrátit. Pro potvrzení zadejte heslo.')) ?></p>
			<label><?= e(t('Heslo')) ?> <input type="password" name="heslo" required autocomplete="current-password"></label>
			<button type="submit" class="rs-tl-vedlejsi"><?= e(t('Smazat můj účet')) ?></button>
		</form>
	</details>
<?php endif ?>
	</div>
</article>
