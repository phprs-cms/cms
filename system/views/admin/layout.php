<?php
/**
 * Rámec administrace: menu, login proužek, nadpis sekce, hlášky, obsah.
 * HTML je pro obě prostředí stejné; vzhled určuje stylesheet (image/admin.css = retro, image/admin-2026.css).
 *
 * @var PhpRS\Core\App $app
 * @var string $nadpis
 * @var string $obsah  hotové HTML modulu
 * @var array<string, class-string<PhpRS\Admin\Modul>> $moduly
 * @var string $aktivni
 * @var array<string, mixed>|null $user
 * @var list<array{typ:string, text:string}> $hlasky
 * @var string $prostredi  retro | 2026
 */
$css = $prostredi === '2026' ? 'image/admin-2026.css' : 'image/admin.css';
$ikona = require __DIR__ . '/ikony.php';

// paleta příkazů (Ctrl/⌘+K): jen to, kam přihlášený smí – seznam modulů už je podle práv
$prikazy = [];
if ($user !== null) {
    $adm = fn (string $dotaz = ''): string => $app->url('admin.php' . ($dotaz !== '' ? '?' . $dotaz : ''));
    $prikazy[] = ['n' => t('Přehled'), 'u' => $adm(), 's' => ''];
    foreach ($moduly as $ident => $class) {
        $prikazy[] = ['n' => t($class::NAZEV), 'u' => $adm('modul=' . $ident), 's' => t($class::SKUPINA)];
    }
    $rychle = [
        'clanky' => [['Nový článek', 'modul=clanky&akce=novy'], ['Redakční kalendář', 'modul=clanky&akce=kalendar'], ['Titulní strana', 'modul=clanky&akce=titulni'], ['Nefunkční odkazy', 'modul=clanky&akce=odkazy']],
        'stranky' => [['Nová stránka', 'modul=stranky&akce=novy']],
        'topic' => [['Nová rubrika', 'modul=topic&akce=novy']],
        'ankety' => [['Nová anketa', 'modul=ankety&akce=novy']],
        'reklama' => [['Nová reklama', 'modul=reklama&akce=novy']],
        'users' => [['Nový uživatel', 'modul=users&akce=novy']],
        'newsletter' => [['Odběratelé', 'modul=newsletter&akce=odberatele']],
    ];
    foreach ($rychle as $ident => $polozky) {
        foreach (isset($moduly[$ident]) ? $polozky : [] as [$nazev, $dotaz]) {
            $prikazy[] = ['n' => t($nazev), 'u' => $adm($dotaz), 's' => t($moduly[$ident]::NAZEV)];
        }
    }
    if (isset($moduly['bloky'])) {
        $prikazy[] = ['n' => t('Upravit rozvržení přímo na webu'), 'u' => $app->url('') . '?upravit=1', 's' => t('Bloky a rozvržení')];
    }
    foreach (isset($moduly['config']) ? PhpRS\Admin\Moduly\Konfigurace::ZALOZKY : [] as $klic => $nazev) {
        $prikazy[] = ['n' => t('Nastavení') . ' → ' . t($nazev), 'u' => $adm('modul=config&zalozka=' . $klic), 's' => t('Nastavení')];
    }
    $prikazy[] = ['n' => t('Můj účet'), 'u' => $adm('akce=ucet'), 's' => ''];
    $prikazy[] = ['n' => t('Zobrazit web'), 'u' => $app->url(''), 's' => ''];
}
?>
<!doctype html>
<html lang="<?= e(PhpRS\Core\Jazyk::kod()) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<script>try { var t = localStorage.getItem('phprs3-tema'); if (t) { document.documentElement.setAttribute('data-tema', t); } } catch (e) {}</script>
<title><?= $nadpis !== '' ? e($nadpis) . ' - ' : '' ?>phpRS admin rozhraní</title>
<link rel="stylesheet" href="<?= e($app->url($css)) ?>?v=<?= e(PHPRS_VERSION) ?>">
<link rel="stylesheet" href="<?= e($app->url('image/editor.css')) ?>?v=<?= e(PHPRS_VERSION) ?>">
</head>
<body class="prostredi-<?= e($prostredi) ?>">
<?php if ($user !== null): ?>
<header class="hlavicka">
	<a class="znacka" href="<?= e($app->url('admin.php')) ?>"><span class="znacka-znak">RS</span><span>php<b>RS</b></span></a>
	<button class="menu-prepinac" type="button" aria-expanded="false" aria-controls="menu"><?= e(t('Menu')) ?></button>
	<ul class="menu rammodry-vypln" id="menu">
		<li class="menu-prehled<?= $aktivni === '' ? ' aktivni' : '' ?>"><a href="<?= e($app->url('admin.php')) ?>"><?= $ikona('prehled') ?><?= e(t('Přehled')) ?></a></li>
<?php $skupina = ''; foreach ($moduly as $ident => $class): ?>
<?php if ($class::SKUPINA !== $skupina): $skupina = $class::SKUPINA; ?>
		<li class="menu-skupina" aria-hidden="true"><?= e(t($skupina)) ?></li>
<?php endif ?>
		<li<?= $ident === $aktivni ? ' class="aktivni"' : '' ?>><a href="<?= e($app->url('admin.php?modul=' . $ident)) ?>"<?= $ident === $aktivni ? ' aria-current="page"' : '' ?>><?= $ikona($class::IKONA) ?><?= e($prostredi === 'retro' && $class::NAZEV_RETRO !== '' ? $class::NAZEV_RETRO : t($class::NAZEV)) ?></a></li>
<?php endforeach ?>
		<li class="menu-web"><a href="<?= e($app->url('')) ?>" target="_blank" rel="noopener"><?= $ikona('web') ?><?= e(t('Zobrazit web')) ?></a></li>
		<li class="menu-logout"><form method="post" action="<?= e($app->url('admin.php?akce=logout')) ?>"><?= $app->session->csrfField() ?><button type="submit"><?= $ikona('odhlasit') ?><?= $prostredi === 'retro' ? 'Logout' : e(t('Odhlásit se')) ?></button></form></li>
	</ul>
</header>
<div class="loginprouzek">
	<form class="prepinac-prostredi" method="post" action="<?= e($app->url('admin.php?akce=prostredi' . ($aktivni !== '' ? '&modul=' . rawurlencode($aktivni) : ''))) ?>">
		<?= $app->session->csrfField() ?>
		<span><?= e(t('prostředí:')) ?></span>
<?php foreach (PhpRS\Admin\Kernel::PROSTREDI as $klic => $nazev): $klic = (string) $klic; // klíč '2026' je v PHP int ?>
		<button type="submit" name="prostredi" value="<?= e($klic) ?>"<?= $klic === $prostredi ? ' class="aktivni" aria-pressed="true"' : ' aria-pressed="false"' ?>><?= e($klic) ?></button>
<?php endforeach ?>
	</form>
	<button class="paleta-spustit" type="button" data-paleta title="<?= e(t('Rychlé hledání a příkazy')) ?>"><span><?= e(t('Hledat…')) ?></span> <kbd>Ctrl K</kbd></button>
	<button class="tema-prepinac" type="button" data-tema-prepinac title="<?= e(t('Světlý / tmavý režim')) ?>" aria-label="<?= e(t('Přepnout světlý a tmavý režim')) ?>"><?= $ikona('tema') ?></button>
	<a class="prihlasen" href="<?= e($app->url('admin.php?akce=ucet')) ?>" title="<?= e(t('Můj účet')) ?>"><span class="prihlasen-text">login: <?= e($user['user']) ?> (<?= e(PhpRS\Core\Auth::TYPY[(int) $user['admin']] ?? '') ?>) - <?= date('d.m.Y') ?></span><span class="avatar" title="<?= e(($user['jmeno'] ?: $user['user']) . ' – ' . (PhpRS\Core\Auth::TYPY[(int) $user['admin']] ?? '')) ?>" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($user['jmeno'] ?: $user['user'], 0, 1))) ?></span></a>
</div>
<?php endif ?>
<?php if ($prikazy !== []): ?>
<dialog class="paleta" id="paleta" aria-label="<?= e(t('Rychlé hledání a příkazy')) ?>"<?= isset($moduly['clanky']) ? ' data-clanky="' . e($app->url('admin.php?modul=clanky&akce=hledej_json&uprava=1')) . '"' : '' ?>>
	<input class="paleta-pole" type="search" autocomplete="off" spellcheck="false" placeholder="<?= e(t('Kam chcete jít? Napište název sekce, akce nebo článku…')) ?>" aria-controls="paleta-seznam">
	<ul class="paleta-seznam" id="paleta-seznam" role="listbox"></ul>
	<p class="paleta-napoveda"><kbd>↑</kbd> <kbd>↓</kbd> <?= e(t('výběr')) ?> · <kbd>Enter</kbd> <?= e(t('otevřít')) ?> · <kbd>Esc</kbd> <?= e(t('zavřít')) ?></p>
	<script type="application/json" id="paleta-data"><?= json_encode($prikazy, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
</dialog>
<?php endif ?>
<main class="obsah">
<?php if ($nadpis !== ''): ?>
<h2><?= e($nadpis) ?></h2>
<?php endif ?>
<?php foreach ($hlasky as $hlaska): ?>
<p class="hlaska hlaska-<?= e($hlaska['typ']) ?>" role="status"><?= e(t($hlaska['text'])) ?></p>
<?php endforeach ?>
<?= $obsah ?>
</main>
<?php if (PhpRS\Core\Jazyk::kod() !== 'cs' && is_file(PHPRS_ROOT . '/image/jazyky/admin-' . PhpRS\Core\Jazyk::kod() . '.js')): ?>
<script src="<?= e($app->url('image/jazyky/admin-' . PhpRS\Core\Jazyk::kod() . '.js')) ?>?v=<?= e(PHPRS_VERSION) ?>" defer></script>
<?php endif ?>
<script src="<?= e($app->url('image/admin.js')) ?>?v=<?= e(PHPRS_VERSION) ?>" defer></script>
<script src="<?= e($app->url('image/editor.js')) ?>?v=<?= e(PHPRS_VERSION) ?>" data-admin-url="<?= e($app->url('admin.php')) ?>" defer></script>
</body>
</html>
