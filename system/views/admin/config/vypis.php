<?php
/**
 * Nastavení: záložky + formulář zvolené záložky (config/<zalozka>.php).
 *
 * @var PhpRS\Admin\Moduly\Konfigurace $modul
 * @var string $csrf
 * @var string $zalozka
 * @var array<string, string> $hodnoty
 * @var array<string, array{nazev:string, popis:string, rozvrzeni:string}> $layouty
 * @var list<array{skupina:string, nazev:string, stav:string, info:string}> $kontroly
 * @var string $adresaWebu
 * @var list<string> $zapnutaRozsireni
 * @var list<array{soubor:string, velikost:int, cas:int}> $zalohy
 * @var array<string, mixed>|null $aktualizace
 * @var list<array{kategorie:string, pocet:int}> $souhlasy
 * @var list<string> $chybyLog  poslední řádky záznamu chyb
 * @var string $vzdalenaStav  výsledek posledního nahrání zálohy mimo server
 * @var string $ulohyToken  tajná část adresy /ulohy pro cron
 * @var bool $demoNahrano  na webu je ukázkový obsah (Core\Demo)
 */
use PhpRS\Admin\Moduly\Konfigurace;

/** Řádek formuláře: $pole('klic', 'Popisek', 'text|radky|kod|ano|cislo|url|email', 'nápověda', [atributy]) */
$pole = function (string $klic, string $popisek, string $druh = 'text', string $napoveda = '', string $atributy = '') use ($hodnoty): void {
    $h = $hodnoty[$klic] ?? '';
    $popisek = t($popisek);
    $napoveda = $napoveda === '' ? '' : t($napoveda);
    $nap = $napoveda !== '' ? '<span class="napoveda">' . $napoveda . '</span>' : '';
    echo '<div class="radek">';
    if ($druh === 'ano') {
        echo '<span class="popisek">' . e($popisek) . '</span><div class="volby"><label><input type="checkbox" name="' . e($klic) . '" value="1"' . ($h === '1' ? ' checked' : '') . '> ' . e(t('Ano')) . '</label>' . $nap . '</div>';
    } elseif ($druh === 'radky' || $druh === 'kod') {
        echo '<label for="' . e($klic) . '">' . e($popisek) . '</label><div><textarea class="textbox' . ($druh === 'kod' ? ' kod' : '') . '" id="' . e($klic) . '" name="' . e($klic) . '" rows="4" style="min-height:80px" ' . $atributy . '>' . e($h) . '</textarea>' . $nap . '</div>';
    } else {
        $typ = ['cislo' => 'number', 'url' => 'url', 'email' => 'email'][$druh] ?? 'text';
        echo '<label for="' . e($klic) . '">' . e($popisek) . '</label><div><input class="textpole' . ($typ === 'number' ? '' : ' siroke') . '" type="' . $typ . '" id="' . e($klic) . '" name="' . e($klic) . '" value="' . e($h) . '" ' . $atributy . '>' . $nap . '</div>';
    }
    echo '</div>';
};
?>
<nav class="zalozky" aria-label="<?= e(t('Sekce nastavení')) ?>">
<?php foreach (Konfigurace::ZALOZKY as $klic => $nazev): ?>
	<a href="<?= e($modul->url('', ['zalozka' => $klic])) ?>"<?= $zalozka === $klic ? ' class="aktivni" aria-current="page"' : '' ?>><?= e(t($nazev)) ?></a>
<?php endforeach ?>
</nav>
<form class="formular" method="post" action="<?= e($modul->url('uloz')) ?>">
<?= $csrf ?>
<input type="hidden" name="zalozka" value="<?= e($zalozka) ?>">
<?php require __DIR__ . '/' . $zalozka . '.php'; ?>
<?php if ($zalozka !== 'stav'): ?>
<p class="tlacitka"><input class="tl" type="submit" value="<?= e(t('Uložit nastavení')) ?>"></p>
<?php endif ?>
</form>
<p class="verze">phpRS <?= e(PHPRS_VERSION) ?> · PHP <?= e(PHP_VERSION) ?></p>
