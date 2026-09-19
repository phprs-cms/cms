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
 * @var callable(string): string $url
 */
$zpravy = [
    'poslano' => ['ok', 'Poslali jsme vám e-mail. Registraci dokončíte kliknutím na odkaz v něm.'],
    'heslo-poslano' => ['ok', 'Pokud u nás tento e-mail má účet, poslali jsme na něj odkaz pro nastavení nového hesla.'],
    'vitejte' => ['ok', 'Registrace je dokončená. Vítejte!'],
    'heslo-zmeneno' => ['ok', 'Heslo je změněné a jste přihlášeni.'],
    'ulozeno' => ['ok', 'Uloženo.'],
    'smazano' => ['ok', 'Váš účet i všechny údaje o něm jsme smazali.'],
    'spatne' => ['chyba', 'E-mail nebo heslo nesouhlasí.'],
    'udaje' => ['chyba', 'Zadejte platný e-mail a heslo aspoň o 8 znacích.'],
    'heslo-chyba' => ['chyba', 'Stávající heslo nesouhlasí, nebo je nové kratší než 8 znaků.'],
    'pomalu' => ['chyba', 'Formulář se nepodařilo ověřit. Počkejte pár vteřin a zkuste to znovu.'],
    'zavreno' => ['chyba', 'Nové registrace jsou teď vypnuté.'],
];
[$typ, $zprava] = $zpravy[$stav] ?? ['', ''];
$skryte = $pole . '<input type="hidden" name="zpet" value="' . e($zpet) . '">';
?>
<article class="clanek clanek-cely rs-ucet">
	<header class="clanek-hlavicka obal-uzky"><h1><?= $ctenar === null ? 'Přihlášení čtenáře' : 'Můj účet' ?></h1></header>
	<div class="obal-uzky">
<?php if ($zprava !== ''): ?>
	<p class="rs-zprava rs-zprava-<?= $typ ?>" role="status"><?= e($zprava) ?></p>
<?php endif ?>
<?php if ($ctenar === null): ?>
	<div class="rs-ucet-sloupce">
		<form class="rs-formular" method="post" action="<?= e($akce) ?>">
			<h2>Přihlásit se</h2>
			<?= $skryte ?><input type="hidden" name="akce" value="prihlaseni">
			<label>E-mail <input type="email" name="email" required autocomplete="username" maxlength="190"></label>
			<label>Heslo <input type="password" name="heslo" required autocomplete="current-password"></label>
			<button type="submit">Přihlásit se</button>
			<details>
				<summary>Zapomněli jste heslo?</summary>
				<p>Zadejte nahoře svůj e-mail a klepněte sem – pošleme vám odkaz pro nastavení nového hesla.</p>
				<button type="submit" name="akce" value="zapomenute" formnovalidate class="rs-tl-vedlejsi">Poslat odkaz</button>
			</details>
		</form>
<?php if ($registrace): ?>
		<form class="rs-formular" method="post" action="<?= e($akce) ?>">
			<h2>Jsem tu poprvé</h2>
			<?= $skryte ?><input type="hidden" name="akce" value="registrace">
			<label>E-mail <input type="email" name="email" required autocomplete="email" maxlength="190"></label>
			<label>Jméno <small>(nepovinné)</small> <input type="text" name="jmeno" autocomplete="name" maxlength="80"></label>
			<label>Heslo <small>(aspoň 8 znaků)</small> <input type="password" name="heslo" required minlength="8" autocomplete="new-password"></label>
			<button type="submit">Zaregistrovat se zdarma</button>
			<p class="rs-drobne">Pošleme vám e-mail s potvrzovacím odkazem. Účet můžete kdykoli sami smazat.</p>
		</form>
<?php endif ?>
	</div>
<?php else: ?>
	<p>Přihlášen: <strong><?= e($ctenar['email']) ?></strong>
<?php if ($predplatitel): ?>
		· předplatné do <strong><?= e(datum($ctenar['predplatne_do'])) ?></strong>
<?php endif ?>
	</p>
<?php if ($zpet !== ''): ?>
	<p><a class="rs-tl" href="<?= e($url($zpet)) ?>">Pokračovat ve čtení</a></p>
<?php endif ?>
	<form class="rs-formular" method="post" action="<?= e($akce) ?>">
		<input type="hidden" name="akce" value="ucet"><input type="hidden" name="podpis" value="<?= e($podpis) ?>">
		<label>Jméno <input type="text" name="jmeno" value="<?= e($ctenar['jmeno']) ?>" autocomplete="name" maxlength="80"></label>
		<details>
			<summary>Změnit heslo</summary>
			<label>Stávající heslo <input type="password" name="heslo_stare" autocomplete="current-password"></label>
			<label>Nové heslo <small>(aspoň 8 znaků)</small> <input type="password" name="heslo" minlength="8" autocomplete="new-password"></label>
		</details>
		<button type="submit">Uložit</button>
	</form>
	<form method="post" action="<?= e($akce) ?>" class="rs-formular rs-formular-radek">
		<input type="hidden" name="akce" value="odhlasit">
		<button type="submit" class="rs-tl-vedlejsi">Odhlásit se</button>
	</form>
	<details class="rs-formular">
		<summary>Smazat účet</summary>
		<form method="post" action="<?= e($akce) ?>">
			<input type="hidden" name="akce" value="smazat"><input type="hidden" name="podpis" value="<?= e($podpis) ?>">
			<p>Smažeme účet i všechny údaje o něm. Nejde to vrátit. Pro potvrzení zadejte heslo.</p>
			<label>Heslo <input type="password" name="heslo" required autocomplete="current-password"></label>
			<button type="submit" class="rs-tl-vedlejsi">Smazat můj účet</button>
		</form>
	</details>
<?php endif ?>
	</div>
</article>
