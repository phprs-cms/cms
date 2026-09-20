<?php
/**
 * Jednotkové testy jádra phpRS 3 - bez frameworku a bez databáze: php tools/testy.php
 *
 * Hlídají to, co kouřový test (tools/test.sh) nepozná: kryptografii, parsování a převody textu.
 * Nový test = další volání over('popis', $skutecne, $ocekavane).
 */

declare(strict_types=1);

require dirname(__DIR__) . '/system/bootstrap.php';

use PhpRS\Core\Hledani;
use PhpRS\Core\Migrace;
use PhpRS\Core\Soubory;
use PhpRS\Core\Totp;
use PhpRS\Front\Seo;
use PhpRS\Front\TypyObsahu;

$chyb = 0;
$celkem = 0;
function over(string $popis, mixed $skutecne, mixed $ocekavane): void
{
    global $chyb, $celkem;
    $celkem++;
    if ($skutecne === $ocekavane) {
        return;
    }
    $chyb++;
    echo "  CHYBA  {$popis}\n         čekal jsem: " . var_export($ocekavane, true) . "\n         dostal jsem: " . var_export($skutecne, true) . "\n";
}

/* ---------- převody textu ---------- */
over('slugify: diakritika a mezery', slugify('Příliš žluťoučký kůň!'), 'prilis-zlutoucky-kun');
over('slugify: prázdný vstup', slugify('***'), 'n-a');
over('slugify: délka', strlen(slugify(str_repeat('abc ', 100), 20)) <= 20, true);
over('bez_diakritiky', bez_diakritiky('Ďábelské ÓDY – Straße'), 'Dabelske ODY – Strasse');
over('e(): uvozovky a značky', e('<a href="x">\'</a>'), '&lt;a href=&quot;x&quot;&gt;&#039;&lt;/a&gt;');
over('datum', datum('2026-09-05 07:03:00', true), '5. 9. 2026 07:03');

/* ---------- hledání ---------- */
over('Hledani::normalizuj', Hledani::normalizuj('<p>Nábřeží&nbsp;<b>Vltavy</b></p><h2>Proměna!</h2>'), 'nabrezi vltavy promena');
over('Hledani::dotaz: krátká slova vypadnou', Hledani::dotaz('co je na Nábřeží'), '+nabrezi*');
over('Hledani::dotaz: operátory fulltextu se neprosadí', Hledani::dotaz('+tajne -verejne "fraze" (x) ~y*'), '+tajne* +verejne* +fraze*');
over('Hledani::dotaz: nejvýš 8 slov', substr_count(Hledani::dotaz('aaa bbb ccc ddd eee fff ggg hhh iii jjj'), '+'), 8);

/* ---------- TOTP (RFC 6238, tajemství "12345678901234567890") ---------- */
$tajemstvi = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';
over('TOTP: vektor T=59', Totp::kod($tajemstvi, intdiv(59, 30)), '287082');
over('TOTP: vektor T=1111111109', Totp::kod($tajemstvi, intdiv(1111111109, 30)), '081804');
over('TOTP: vektor T=2000000000', Totp::kod($tajemstvi, intdiv(2000000000, 30)), '279037');
over('TOTP: platný kód projde', Totp::over($tajemstvi, '287082', 59), true);
over('TOTP: sousední okno projde', Totp::over($tajemstvi, '287082', 59 + 30), true);
over('TOTP: starý kód neprojde', Totp::over($tajemstvi, '287082', 59 + 300), false);
over('TOTP: nesmysl neprojde', Totp::over($tajemstvi, 'abcdef', 59), false);
over('TOTP: nové tajemství má 160 bitů', strlen(Totp::noveTajemstvi()), 32);

/* ---------- migrace: dělení SQL na příkazy ---------- */
$sql = "-- komentář\nALTER TABLE rs_clanky ADD COLUMN x INT;   -- poznámka za příkazem\nCREATE TABLE rs_nova (\n  a VARCHAR(10) DEFAULT ';'\n);\nALTER TABLE rs_a ADD CONSTRAINT fk_a FOREIGN KEY (b) REFERENCES rs_b (id);\n";
$prikazy = Migrace::prikazy($sql, 'web_');
over('Migrace::prikazy: počet', count($prikazy), 3);
over('Migrace::prikazy: předpona tabulek', str_contains($prikazy[1], 'CREATE TABLE web_nova'), true);
over('Migrace::prikazy: středník v hodnotě příkaz nerozdělí', str_contains($prikazy[1], "DEFAULT ';'"), true);
over('Migrace::prikazy: předpona omezení', str_contains($prikazy[2], 'CONSTRAINT web_fk_a') && str_contains($prikazy[2], 'REFERENCES web_b'), true);
over('Migrace: PHPRS_VERZE_DB odpovídá souborům', PHPRS_VERZE_DB, Migrace::posledni());

/* ---------- přílohy ---------- */
over('Soubory: PDF je příloha', Soubory::jePriloha('Zpráva.PDF'), true);
over('Soubory: PHP není příloha', Soubory::jePriloha('shell.php'), false);
over('Soubory: dvojitá přípona', Soubory::jePriloha('shell.pdf.php'), false);
over('Soubory: SVG a HTML ne', Soubory::jePriloha('x.svg') || Soubory::jePriloha('x.html'), false);
over('Soubory: velikost', Soubory::velikost(1536), '2 kB');
over('Soubory: velikost v MB', Soubory::velikost(5 * 1048576), '5,0 MB');

/* ---------- přehrávač a vložené adresy ---------- */
over('prehravac: YouTube bez cookies', str_contains(TypyObsahu::prehravac('https://www.youtube.com/watch?v=dQw4w9WgXcQ', '', 'T'), 'youtube-nocookie.com/embed/dQw4w9WgXcQ'), true);
over('prehravac: youtu.be', str_contains(TypyObsahu::prehravac('https://youtu.be/dQw4w9WgXcQ', '', 'T'), 'embed/dQw4w9WgXcQ'), true);
over('prehravac: MP3 je <audio>', str_contains(TypyObsahu::prehravac('media/2026/09/epizoda.mp3', '/magazin', 'T'), '<audio controls preload="none" src="/magazin/media/2026/09/epizoda.mp3">'), true);
over('prehravac: neznámá adresa v režimu jenZname', TypyObsahu::prehravac('https://example.com/video', '', 'T', true), '');
over('prehravac: titulek se escapuje', str_contains(TypyObsahu::prehravac('https://vimeo.com/123', '', '"><script>'), '<script>'), false);
$typy = (new ReflectionClass(TypyObsahu::class))->newInstanceWithoutConstructor();
$html = $typy->vlozeneAdresy('<p>Úvod</p><p>https://youtu.be/dQw4w9WgXcQ</p><p>Viz https://youtu.be/dQw4w9WgXcQ v textu.</p>');
over('vlozeneAdresy: jen samostatný řádek', [substr_count($html, 'data-vlozit'), substr_count($html, 'Viz https://youtu.be')], [1, 1]);

/* ---------- bezpečný dialekt šablon (ukládání přes napojení na Claude) ---------- */
use PhpRS\Core\SablonaKontrola;

$vadne = [];
foreach (glob(dirname(__DIR__) . '/layout/*/*.php') as $soubor) {
    if (SablonaKontrola::over((string) file_get_contents($soubor)) !== []) {
        $vadne[] = basename(dirname($soubor)) . '/' . basename($soubor);
    }
}
over('SablonaKontrola: vestavěné šablony dialektem projdou', $vadne, []);
$utoky = [
    '<?php file_put_contents(PHPRS_ROOT . "/system/x.php", "x");', '<?= file_get_contents("../config.php") ?>', '<?php eval($_GET["c"]);', '<?php include "../config.php";',
    '<?php system("id");', '<?php echo `id`;', '<?php $f = "sys" . "tem"; $f("id");', '<?php array_map("system", ["id"]);', '<?php array_map("sys" . "tem", ["id"]);',
    '<?php $x = "system"; usort($a, $x);', '<?php call_user_func("system", "id");', '<?php $d = new PDO("mysql:host=x");', '<?php \\PhpRS\\Core\\App::boot();',
    '<?php $web->db()->run("DROP TABLE rs_clanky");', '<?php $web->set("ai_klic", "x");', '<?= $_COOKIE["phprs3"] ?>', '<?php $a = "_GET"; echo $$a["x"];',
    '<?php echo "{$web->db()->run(1)}";', '<?php (fn () => 1)()("x");', '<?php [$web, "set"]("a", "b");', '<?php function system2() {}', '<?php ($web->x)("id");', '<?php exit;',
    '<?php use PhpRS\\Core\\Db as e;', '<?php echo constant("PHPRS_ROOT");', '<?php preg_replace_callback("/x/", "system", "x");', '<?php highlight_file("../config.php");',
    '<?php $m = "db"; $web->$m();', '<?php array_map(system(...), ["id"]);', '<?php $web?->db();', '<?php echo $app->settings()->get("ai_klic");', '<?php mail("a@b.cz", "x", "y");',
    '<?php curl_init("https://example.com");', '<?php fopen("php://input", "r");', '<?php unlink("index.php");', '<?php putenv("A=B");', '<?php extract($_POST);',
];
$prosle = array_values(array_filter($utoky, fn (string $php): bool => SablonaKontrola::over($php) === []));
over('SablonaKontrola: žádný z ' . count($utoky) . ' útoků neprojde', $prosle, []);
over('SablonaKontrola: běžná šablona projde', SablonaKontrola::over('<?php $x = fn (array $c): string => e($c["titulek"]); ?><h1><?= $x($clanek) ?></h1><?php foreach (array_map(trim(...), explode(",", "a,b")) as $s): ?><?= e(t("Štítek")) ?> <?= e($url("stitek/" . $s)) ?><?php endforeach; usort($a, fn ($p, $q) => $p <=> $q); if ($web->get("logo_webu") !== "") { echo e(datum($clanek["datum"], true)); }'), []);

/* ---------- šablona Rozhovor: otázka = odstavec celý tučně ---------- */
$otazky = preg_replace('#<p>(\s*<(strong|b)>(?:(?!</?(?:strong|b|p)\b).)*</\2>\s*)</p>#is', '<p class="rs-otazka">$1</p>', '<p><strong>Proč?</strong></p><p><strong>Tučně</strong> a dál text.</p><p>Odpověď.</p>');
over('Rozhovor: jen celý tučný odstavec je otázka', substr_count((string) $otazky, 'rs-otazka'), 1);

/* ---------- porovnání verzí ---------- */
$r = PhpRS\Core\Rozdil::html('<p>Radnice schválila plán.</p><p>Druhý odstavec.</p>', '<p>Radnice včera schválila nový plán.</p><p>Druhý odstavec.</p><p>Třetí.</p>');
over('Rozdil: slova ve změněném odstavci', str_contains($r['html'], '<ins>včera </ins>') && str_contains($r['html'], '<ins>nový </ins>'), true);
over('Rozdil: nezměněný odstavec bez značek', str_contains($r['html'], '<p>Druhý odstavec.</p>'), true);
over('Rozdil: nový odstavec', str_contains($r['html'], '<p><ins>Třetí.</ins></p>'), true);
over('Rozdil: HTML ve vstupu se escapuje', str_contains(PhpRS\Core\Rozdil::html('', '<p>a &lt;script&gt; b</p>')['html'], '<script>'), false);
over('Rozdil: shodné texty', PhpRS\Core\Rozdil::html('<p>Stejné</p>', '<p>Stejné</p>')['pridano'], 0);

/* ---------- FAQ ---------- */
over('Seo::faq', Seo::faq("Kdy to začne?\nV pondělí.\n\nKolik to stojí?\nNic."), [['Kdy to začne?', 'V pondělí.'], ['Kolik to stojí?', 'Nic.']]);
over('Seo::faq: prázdný vstup', Seo::faq(null), []);

/* ---------- Web Push: podpis ES256 (DER -> r||s) ---------- */
if (function_exists('openssl_pkey_new')) {
    $par = openssl_pkey_new(['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]);
    $ok = true;
    $derNaRaw = new ReflectionMethod(PhpRS\Core\Push::class, 'derNaRaw');
    for ($i = 0; $i < 40 && $ok; $i++) { // r a s mají proměnnou délku - zkouší se víc podpisů
        openssl_sign('zprava' . $i, $der, $par, OPENSSL_ALGO_SHA256);
        $raw = $derNaRaw->invoke(null, $der);
        $cislo = fn (string $v): string => "\x02" . chr(strlen($v = (ord(($v = ltrim($v, "\0") ?: "\0")[0]) > 0x7f ? "\0" : '') . $v)) . $v;
        $zpet = $cislo(substr($raw, 0, 32)) . $cislo(substr($raw, 32));
        $ok = strlen($raw) === 64 && openssl_verify('zprava' . $i, "\x30" . chr(strlen($zpet)) . $zpet, openssl_pkey_get_details($par)['key'], OPENSSL_ALGO_SHA256) === 1;
    }
    over('Push::derNaRaw: 40 podpisů jde ověřit zpět', $ok, true);
}

/* ---------- zálohy do S3: podpis AWS Signature V4 (hodnota ověřená nezávislým výpočtem) ---------- */
$h = PhpRS\Core\VzdalenaZaloha::podpisS3('PUT', 's3.eu-central-1.amazonaws.com', '/muj-bucket/phprs-zaloha.sql.gz', hash('sha256', 'obsah'), 'eu-central-1', 'AKIDEXAMPLE', 'wJalrXUtnFEMI/K7MDENG+bPxRfiCYEXAMPLEKEY', 1789900000);
over('S3: rozsah a podepsané hlavičky', str_contains($h['Authorization'], 'Credential=AKIDEXAMPLE/20260920/eu-central-1/s3/aws4_request, SignedHeaders=host;x-amz-content-sha256;x-amz-date, Signature='), true);
over('S3: podpis má 64 šestnáctkových znaků', (bool) preg_match('/Signature=[0-9a-f]{64}$/', $h['Authorization']), true);

/* ---------- antispam: otisk IP ---------- */
over('Antispam::otisk: není to IP adresa', str_contains(PhpRS\Core\Antispam::otisk('203.0.113.7'), '203'), false);
over('Antispam::otisk: stejná adresa = stejný otisk', PhpRS\Core\Antispam::otisk('203.0.113.7'), PhpRS\Core\Antispam::otisk('203.0.113.7'));

echo $chyb === 0 ? "  ok     jednotkové testy ({$celkem})\n" : "  NALEZENO CHYB: {$chyb} z {$celkem}\n";
exit($chyb === 0 ? 0 : 1);
