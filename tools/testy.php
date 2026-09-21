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
over('Šablony: výchozí šablona existuje a je i výchozí hodnotou nastavení', [is_file(dirname(__DIR__) . '/layout/' . \PhpRS\Front\Layouty::VYCHOZI . '/base.php'), \PhpRS\Core\Settings::DEFAULTS['layout']], [true, \PhpRS\Front\Layouty::VYCHOZI]);
over('Šablony: zrušená šablona „default“ se nevrátila', is_dir(dirname(__DIR__) . '/layout/default'), false);
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

/* ---------- kontrola odkazů: jen veřejné adresy (ochrana před ohledáváním vnitřní sítě) ---------- */
over('Odkazy: výběr odkazů z HTML', PhpRS\Core\Odkazy::odkazy('<p><a href="https://example.com/a?x=1&amp;y=2">a</a> <a href="mailto:a@b.cz">m</a> <a href="#kotva">k</a> <a class="x" href="/clanek/muj">c</a> <a href="https://example.com/a?x=1&amp;y=2">znovu</a></p>'), ['https://example.com/a?x=1&y=2', '/clanek/muj']);
foreach (['http://127.0.0.1/', 'http://localhost/', 'http://10.0.0.5/admin', 'http://192.168.1.1/', 'http://169.254.169.254/latest/meta-data/', 'http://[::1]/', 'ftp://example.com/', 'https://example.com:8443/', 'file:///etc/passwd', 'gopher://x/'] as $vnitrni) {
    over('Odkazy: nekontroluje se ' . $vnitrni, PhpRS\Core\Odkazy::jeVerejna($vnitrni), false);
}
over('Odkazy: veřejná adresa se kontroluje', PhpRS\Core\Odkazy::jeVerejna('https://93.184.216.34/stranka'), true);

/* ---------- asistent: překlad článku (kostra HTML z originálu, texty od modelu) ---------- */
$clanekHtml = '<h2>Nadpis oddílu</h2><p>První <strong>tučný</strong> a <a href="/x?a=1&amp;b=2">odkaz</a>.</p><figure><img src="a.jpg" alt="x"><figcaption>Popisek fotky</figcaption></figure><script>alert("nepřekládat")</script><p>2024</p>';
$r = PhpRS\Core\Asistent::rozloz($clanekHtml);
over('Asistent::rozloz: úseky k překladu', $r['useky'], ['Nadpis oddílu', 'První [[0]]tučný[[1]] a [[2]]odkaz[[3]].', 'Popisek fotky']);
over('Asistent::sloz: beze změny textu vrátí původní HTML', PhpRS\Core\Asistent::sloz($r['kostra'], $r['useky']), $clanekHtml);
over('Asistent::sloz: HTML od modelu se vypíše jako text', str_contains(PhpRS\Core\Asistent::sloz($r['kostra'], ['<script>alert(1)</script>', 'x', '<img src=x onerror=alert(1)>']), '<script>alert(1)') || str_contains(PhpRS\Core\Asistent::sloz($r['kostra'], ['a', 'b', '<img src=x onerror=alert(1)>']), '<img src=x'), false);
over('Asistent::sloz: chybějící symbol = úsek bez formátování', PhpRS\Core\Asistent::sloz($r['kostra'], ['N', 'First [[0]]bold[[1]] and link.', 'P']), '<h2>N</h2><p>First bold and link.</p><figure><img src="a.jpg" alt="x"><figcaption>P</figcaption></figure><script>alert("nepřekládat")</script><p>2024</p>');
over('Asistent::sloz: špatně vnořené symboly = úsek bez formátování', str_contains(PhpRS\Core\Asistent::sloz($r['kostra'], ['N', '[[1]]bold[[0]] [[2]]link[[3]]', 'P']), '<strong>'), false);
over('Asistent::sloz: přeházené pořadí slov formátování zachová', str_contains(PhpRS\Core\Asistent::sloz($r['kostra'], ['N', 'A [[2]]link[[3]] and [[0]]bold[[1]] first.', 'P']), '<p>A <a href="/x?a=1&amp;b=2">link</a> and <strong>bold</strong> first.</p>'), true);

$nastaveni = (new ReflectionClass(PhpRS\Core\Settings::class))->newInstanceWithoutConstructor();
(new ReflectionProperty(PhpRS\Core\Settings::class, 'values'))->setValue($nastaveni, ['nazev_webu' => 'Test', 'ai_klic' => 'x']);
$falesny = new class($nastaveni) extends PhpRS\Core\Asistent {
    public int $volani = 0;

    protected function zavolej(array $telo): array
    {
        $this->volani++;
        preg_match('#<useky>\n(.*)\n</useky>#s', $telo['messages'][0]['content'], $m);

        return ['content' => [['type' => 'text', 'text' => json_encode(['preklady' => array_map(mb_strtoupper(...), json_decode($m[1], true))], JSON_UNESCAPED_UNICODE)]]];
    }
};
$prelozeno = $falesny->preloz(['titulek' => 'Tom & Jerry „znovu“ ve městě, tentokrát úplně jinak než kdy dřív', 'text' => '<p>Krátký <em>text</em> článku, který má aspoň pár desítek znaků.</p>', 'seo_popis' => ''], 'en', ['titulek', 'seo_popis']);
over('Asistent::preloz: prostý text se neescapuje dvakrát', $prelozeno['titulek'], 'TOM & JERRY „ZNOVU“ VE MĚSTĚ, TENTOKRÁT ÚPLNĚ JINAK NEŽ KDY DŘÍV');
over('Asistent::preloz: HTML pole drží kostru', $prelozeno['text'], '<p>KRÁTKÝ <em>TEXT</em> ČLÁNKU, KTERÝ MÁ ASPOŇ PÁR DESÍTEK ZNAKŮ.</p>');
over('Asistent::preloz: prázdné pole zůstane prázdné', $prelozeno['seo_popis'], '');
$falesny->volani = 0;
$dlouhy = $falesny->preloz(['text' => str_repeat('<p>' . str_repeat('Věta o něčem. ', 100) . '</p>', 9)], 'de');
over('Asistent::preloz: dlouhý článek jde po dávkách', [$falesny->volani > 1, substr_count($dlouhy['text'], '<p>')], [true, 9]);
try {
    $falesny->preloz(['text' => '<p>nic</p>'], 'xx');
    over('Asistent::preloz: neznámý jazyk odmítne', 'prošlo', 'výjimka');
} catch (RuntimeException) {
    over('Asistent::preloz: neznámý jazyk odmítne', 'výjimka', 'výjimka');
}

/* ---------- vložení příspěvku ze sítí adresou ---------- */
foreach ([
    'https://x.com/nasa/status/1790000000000000000' => 'platform.twitter.com/embed/Tweet.html?dnt=true&amp;id=1790000000000000000',
    'https://twitter.com/nasa/status/1790000000000000000?s=20' => 'id=1790000000000000000"',
    'https://www.instagram.com/p/C1aBcDeFgH_/' => 'https://www.instagram.com/p/C1aBcDeFgH_/embed/',
    'https://www.tiktok.com/@redakce/video/7300000000000000000' => 'https://www.tiktok.com/embed/v2/7300000000000000000',
    'https://mastodon.social/@Gargron/111111111111111111' => 'https://mastodon.social/@Gargron/111111111111111111/embed',
] as $adresa => $ocekavane) {
    over('TypyObsahu::prispevek: ' . parse_url($adresa, PHP_URL_HOST), str_contains(PhpRS\Front\TypyObsahu::prispevek($adresa), $ocekavane), true);
}
foreach (['https://x.com/nasa', 'http://x.com/nasa/status/1790000000000000000', 'https://x.com.utocnik.cz/a/status/1790000000000000000', 'https://example.com/clanek/123', 'https://mastodon.social/@a/1"onload="x', 'javascript:alert(1)'] as $adresa) {
    over('TypyObsahu::prispevek: nevkládá ' . $adresa, PhpRS\Front\TypyObsahu::prispevek($adresa), '');
}

/* ---------- dočasné přepnutí jazyka (e-maily v jazyce příjemce) ---------- */
PhpRS\Core\Jazyk::nastav('cs');
over('Jazyk::docasne: uvnitř platí cizí jazyk', PhpRS\Core\Jazyk::docasne('en', fn (): string => PhpRS\Core\Jazyk::kod() . '|' . t('Číst článek →')), 'en|Read article →');
over('Jazyk::docasne: potom se jazyk vrátí', PhpRS\Core\Jazyk::kod() . '|' . t('Číst článek →'), 'cs|Číst článek →');
try {
    PhpRS\Core\Jazyk::docasne('de', function (): never { throw new RuntimeException('x'); });
} catch (RuntimeException) {
}
over('Jazyk::docasne: jazyk se vrátí i po výjimce', PhpRS\Core\Jazyk::kod(), 'cs');

/* ---------- převládající barva obrázku ---------- */
if (function_exists('imagecreatetruecolor')) {
    $docasny = tempnam(sys_get_temp_dir(), 'rs') . '.png';
    $platno = imagecreatetruecolor(40, 20);
    imagefill($platno, 0, 0, imagecolorallocate($platno, 200, 30, 60));
    imagepng($platno, $docasny);
    over('Obrazky::barva: jednobarevný obrázek', PhpRS\Core\Obrazky::barva($docasny), '#c81e3c');
    unlink($docasny);
    over('Obrazky::barva: chybějící soubor', PhpRS\Core\Obrazky::barva($docasny), null);
}

/* ---------- skripty: nesmí hledat prvek (data-atribut), který nikde nevzniká – tak se rozbil dialog Médií ---------- */
$kdeVznika = [
    'image/editor.js' => ['system/views/admin'], 'image/admin.js' => ['system/views/admin', 'system/src/Admin'], 'image/pomocnik.js' => ['system/views/admin'],
    'image/vizual.js' => ['system/views/front', 'system/src/Front'], 'image/web.js' => ['system/views/front', 'system/src/Front', 'layout'],
];
foreach ($kdeVznika as $skript => $slozky) {
    $zdroj = (string) file_get_contents(PHPRS_ROOT . '/' . $skript);
    preg_match_all('/querySelector(?:All)?\(\'\[(data-[a-z0-9-]+)\]\'\)/', $zdroj, $odkazy);
    $bezHledani = (string) preg_replace('/(querySelector(All)?|closest|matches)\([^)]*\)/', '', $zdroj);
    $chybi = [];
    foreach (array_unique($odkazy[1]) as $atribut) {
        $vSablonach = false;
        foreach ($slozky as $slozka) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(PHPRS_ROOT . '/' . $slozka, FilesystemIterator::SKIP_DOTS)) as $soubor) {
                $vSablonach = $vSablonach || str_contains((string) file_get_contents($soubor->getPathname()), $atribut);
            }
        }
        if (!$vSablonach && !preg_match('/[\s"\']' . preg_quote($atribut, '/') . '[\s>="\']/', $bezHledani) && !str_contains($bezHledani, "setAttribute('" . $atribut . "'")) {
            $chybi[] = $atribut;
        }
    }
    over($skript . ': každý hledaný data-atribut někde vzniká', $chybi, []);
}

/* ---------- administrace má Content-Security-Policy bez 'unsafe-inline': žádné inline skripty ani obsluhy událostí ---------- */
$inline = [];
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(PHPRS_ROOT . '/system/views/admin', FilesystemIterator::SKIP_DOTS)) as $soubor) {
    $zdroj = (string) file_get_contents($soubor->getPathname());
    if (preg_match('#<script(?![^>]*\bsrc=)(?![^>]*type="application/json")[^>]*>|\son(?:click|change|input|submit|load|error|key\w+|mouse\w+)="#i', $zdroj)) {
        $inline[] = substr($soubor->getPathname(), strlen(PHPRS_ROOT) + 1);
    }
}
over('šablony administrace neobsahují inline skripty (CSP)', $inline, []);

/* ---------- podpisy vydavatele: víc klíčů, výměna a odvolání klíče ---------- */
if (function_exists('sodium_crypto_sign_keypair')) {
    $par = fn (): array => (fn (string $p): array => [sodium_crypto_sign_secretkey($p), sodium_crypto_sign_publickey($p)])(sodium_crypto_sign_keypair());
    [[$skProvozni, $pkProvozni], [$skZalozni, $pkZalozni], [$skNovy, $pkNovy], [$skCizi]] = [$par(), $par(), $par(), $par()];
    $podepis = fn (string $zprava, string $sk): string => base64_encode(sodium_crypto_sign_detached($zprava, $sk));
    $pub = tempnam(sys_get_temp_dir(), 'rs');
    file_put_contents($pub, "# poznámka\n" . base64_encode($pkProvozni) . " provozni\n\nnesmysl-ktery-neni-klic\n" . base64_encode($pkZalozni) . " zalozni 2026-09-20\n");
    $zprava = PhpRS\Core\Podpis::zpravaBalicku('3.0.1', str_repeat('A', 64), false);
    over('Podpis::klice: dva platné klíče, poznámky a nesmysly se přeskočí', count(PhpRS\Core\Podpis::klice($pub)), 2);
    over('Podpis: provozní klíč platí', PhpRS\Core\Podpis::plati($zprava, $podepis($zprava, $skProvozni), $pub), true);
    over('Podpis: záložní klíč platí také', PhpRS\Core\Podpis::plati($zprava, $podepis($zprava, $skZalozni), $pub), true);
    over('Podpis: cizí klíč neplatí', PhpRS\Core\Podpis::plati($zprava, $podepis($zprava, $skCizi), $pub), false);
    over('Podpis: poškozený podpis neplatí', PhpRS\Core\Podpis::plati($zprava, 'AAAA', $pub), false);
    over('Podpis: běžné vydání nejde prohlásit za bezpečnostní', PhpRS\Core\Podpis::plati(PhpRS\Core\Podpis::zpravaBalicku('3.0.1', str_repeat('A', 64), true), $podepis($zprava, $skProvozni), $pub), false);
    over('Podpis: otisk balíčku se porovnává bez ohledu na velikost písmen', PhpRS\Core\Podpis::zpravaBalicku('3.0.1', 'ABC', false), '3.0.1|abc|bezne');
    // únik provozního klíče: vydání podepsané záložním přinese soubor bez něj a s novým provozním
    file_put_contents($pub, base64_encode($pkNovy) . " provozni\n" . base64_encode($pkZalozni) . " zalozni\n");
    over('výměna klíče: odvolaný klíč už neplatí', PhpRS\Core\Podpis::plati($zprava, $podepis($zprava, $skProvozni), $pub), false);
    over('výměna klíče: nový provozní klíč platí', PhpRS\Core\Podpis::plati($zprava, $podepis($zprava, $skNovy), $pub), true);
    file_put_contents($pub, '');
    over('Podpis: bez klíčů neplatí nic', PhpRS\Core\Podpis::plati($zprava, $podepis($zprava, $skNovy), $pub), false);
    unlink($pub);
}
over('system/aktualizace.pub obsahuje aspoň jeden platný klíč', count(PhpRS\Core\Podpis::klice(PHPRS_ROOT . '/system/aktualizace.pub')) >= 1, true);

/* ---------- instalátor: každý text má překlad ve všech jazycích ---------- */
$klice = [];
foreach (['system/views/install/formular.php', 'system/views/install/hotovo.php', 'system/src/Install/Installer.php'] as $soubor) {
    preg_match_all("/\\bt\\('((?:[^'\\\\]|\\\\.)*)'/", (string) file_get_contents(PHPRS_ROOT . '/' . $soubor), $nalezene);
    foreach ($nalezene[1] as $text) {
        $klice[stripslashes($text)] = true;
    }
}
foreach (['sk', 'en', 'de'] as $kod) {
    $slovnik = require PHPRS_ROOT . '/system/jazyky/install-' . $kod . '.php';
    // slovenština smí text nechat stejný jako čeština (slovník pak položku neobsahuje)
    // mezinárodní slova se nepřekládají (nástroj na slovníky shodné položky nezapisuje)
    $chybi = $kod === 'sk' ? [] : array_values(array_diff(array_keys($klice), array_keys($slovnik), ['Server', 'Port', 'E-mail']));
    over('instalátor: úplný slovník ' . $kod, $chybi, []);
}

/* ---------- ukázkový obsah (system/demo): úplný ve všech jazycích, obrázky na místě ---------- */
$demo = require PHPRS_ROOT . '/system/demo/obsah.php';
over('demo: deset článků, právě jeden otvírák', [count($demo['clanky']), count(array_filter(array_column($demo['clanky'], 'pripnout')))], [10, 1]);
foreach ($demo['clanky'] as $klic => $spolecne) {
    over("demo: článek {$klic} patří do existující rubriky", isset($demo['cs']['rubriky'][$spolecne['rubrika']]), true);
    over("demo: obrázek článku {$klic} je v system/demo/img", $spolecne['obrazek'] === '' || is_file(PHPRS_ROOT . '/system/demo/img/' . $spolecne['obrazek']), true);
}
foreach (PhpRS\Core\Demo::JAZYKY as $kod) {
    over("demo {$kod}: stejné rubriky jako čeština", array_keys($demo[$kod]['rubriky']), array_keys($demo['cs']['rubriky']));
    over("demo {$kod}: pět rubrik s názvem", count(array_filter($demo[$kod]['rubriky'], fn (string $n): bool => trim($n) !== '')), 5);
    over("demo {$kod}: texty ke všem článkům", array_keys($demo[$kod]['clanky']), array_keys($demo['clanky']));
    foreach ($demo[$kod]['clanky'] as $klic => $clanek) {
        $prazdne = array_filter(['titulek', 'uvod', 'text'], fn (string $cast): bool => trim(strip_tags($clanek[$cast] ?? '')) === '');
        over("demo {$kod}/{$klic}: titulek, perex a text nejsou prázdné", array_values($prazdne), []);
        over("demo {$kod}/{$klic}: text má aspoň tři odstavce a jeden mezititulek", [substr_count($clanek['text'], '<p>') >= 3, substr_count($clanek['text'], '<h2>')], [true, 1]);
        over("demo {$kod}/{$klic}: titulek se vejde do sloupce", mb_strlen($clanek['titulek']) <= 160, true);
    }
}
over('demo: obrázky se vejdou do 1 MB', array_sum(array_map(filesize(...), glob(PHPRS_ROOT . '/system/demo/img/*.jpg') ?: [])) < 1024 * 1024, true);

/* ---------- nápověda: adresy příručky odpovídají osnově (docs/ nejsou v balíčku, test běží jen ve vývojové kopii) ---------- */
if (is_file(PHPRS_ROOT . '/docs/prirucka/osnova.json')) {
    $osnova = json_decode((string) file_get_contents(PHPRS_ROOT . '/docs/prirucka/osnova.json'), true);
    foreach (['en', 'de'] as $kod) {
        over('Napoveda: překlad adres ' . $kod . ' odpovídá osnova.json', PhpRS\Core\Napoveda::ADRESY[$kod][1], $osnova['adresy'][$kod]);
    }
    over('Napoveda: adresa stránky', PhpRS\Core\Napoveda::url('provoz/posta', 'en'), 'https://phprs.eu/en/docs/operations/mail/');
    over('Napoveda: slovenština vede na českou příručku', PhpRS\Core\Napoveda::url('', 'sk'), 'https://phprs.eu/cs/dokumentace/');
}

/* ---------- aktualizace: úklid souborů, které nové vydání už neobsahuje ---------- */
$uklid = sys_get_temp_dir() . '/phprs-uklid-' . bin2hex(random_bytes(4));
mkdir($uklid . '/system/stare', 0775, true);
mkdir($uklid . '/media', 0775, true);
foreach (['index.php', 'system/stare/zrusene.php', 'system/zustava.php', 'media/foto.jpg', 'config.php', 'vlastni.php'] as $f) {
    file_put_contents($uklid . '/' . $f, 'x');
}
$smazano = PhpRS\Core\Aktualizace::uklidZastarale($uklid, ['index.php', 'system/stare/zrusene.php', 'system/zustava.php', 'media/foto.jpg', 'config.php', '../mimo.php'], ['index.php', 'system/zustava.php']);
over('Aktualizace: smaže jen soubor zrušený novým vydáním', $smazano, 1);
over('Aktualizace: zrušený soubor i jeho prázdná složka jsou pryč', is_dir($uklid . '/system/stare'), false);
over('Aktualizace: chráněné cesty a vlastní soubory zůstávají', [is_file($uklid . '/media/foto.jpg'), is_file($uklid . '/config.php'), is_file($uklid . '/vlastni.php'), is_file($uklid . '/system/zustava.php')], [true, true, true, true]);
exec('rm -rf ' . escapeshellarg($uklid));

/* ---------- přihlašovací klíče (WebAuthn): softwarový autentikátor proti ověřovacímu jádru ---------- */
$pkRp = 'redakce.example'; $pkPuvod = 'https://redakce.example';
$pkKlic = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1']);
$pkPopis = openssl_pkey_get_details($pkKlic);
$pkDer = base64_decode(preg_replace('/-----[^-]+-----|\s/', '', $pkPopis['key']));
$pkX = str_pad($pkPopis['ec']['x'], 32, "\0", STR_PAD_LEFT); $pkY = str_pad($pkPopis['ec']['y'], 32, "\0", STR_PAD_LEFT);
$pkCose = "\xA5\x01\x02\x03\x26\x20\x01\x21\x58\x20" . $pkX . "\x22\x58\x20" . $pkY;
$pkId = random_bytes(20);
$pkKlient = static fn (string $typ, string $vyzva, string $puvod): string => PhpRS\Core\Passkey::b64((string) json_encode(['type' => $typ, 'challenge' => $vyzva, 'origin' => $puvod, 'crossOrigin' => false], JSON_UNESCAPED_SLASHES));
$pkRegData = static fn (string $rp, int $priznaky = 0x45): string => hash('sha256', $rp, true) . chr($priznaky) . pack('N', 0) . str_repeat("\0", 16) . pack('n', strlen($pkId)) . $pkId . $pkCose;
$pkVyzva = PhpRS\Core\Passkey::vyzva();
$pkReg = ['clientDataJSON' => $pkKlient('webauthn.create', $pkVyzva, $pkPuvod), 'authenticatorData' => PhpRS\Core\Passkey::b64($pkRegData($pkRp)), 'publicKey' => PhpRS\Core\Passkey::b64($pkDer), 'publicKeyAlgorithm' => -7];
$pkUlozeno = PhpRS\Core\Passkey::overRegistraci($pkReg, $pkVyzva, $pkPuvod, $pkRp);
over('Passkey: registrace vrátí id klíče', $pkUlozeno['id'], PhpRS\Core\Passkey::b64($pkId));
over('Passkey: registrace vrátí veřejný klíč v PEM', str_contains($pkUlozeno['klic'], 'BEGIN PUBLIC KEY'), true);
$pkOdmitne = static function (callable $f): bool { try { $f(); return false; } catch (RuntimeException) { return true; } };
over('Passkey: registrace s cizí výzvou neprojde', $pkOdmitne(fn () => PhpRS\Core\Passkey::overRegistraci($pkReg, PhpRS\Core\Passkey::vyzva(), $pkPuvod, $pkRp)), true);
over('Passkey: registrace z jiného původu neprojde', $pkOdmitne(fn () => PhpRS\Core\Passkey::overRegistraci($pkReg, $pkVyzva, 'https://podvrh.example', $pkRp)), true);
over('Passkey: registrace pro jinou doménu neprojde', $pkOdmitne(fn () => PhpRS\Core\Passkey::overRegistraci($pkReg, $pkVyzva, $pkPuvod, 'jina.example')), true);
$pkCizi = openssl_pkey_get_details(openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1']));
over('Passkey: podstrčený veřejný klíč neprojde', $pkOdmitne(fn () => PhpRS\Core\Passkey::overRegistraci(['publicKey' => PhpRS\Core\Passkey::b64(base64_decode(preg_replace('/-----[^-]+-----|\s/', '', $pkCizi['key'])))] + $pkReg, $pkVyzva, $pkPuvod, $pkRp)), true);
over('Passkey: odpověď z přihlášení nejde použít k registraci', $pkOdmitne(fn () => PhpRS\Core\Passkey::overRegistraci(['clientDataJSON' => $pkKlient('webauthn.get', $pkVyzva, $pkPuvod)] + $pkReg, $pkVyzva, $pkPuvod, $pkRp)), true);
$pkPrihlas = static function (string $vyzva, int $pocitadlo, string $rp = 'redakce.example', string $puvod = 'https://redakce.example', int $priznaky = 0x05) use ($pkKlic, $pkKlient): array {
    $data = hash('sha256', $rp, true) . chr($priznaky) . pack('N', $pocitadlo);
    $klient = $pkKlient('webauthn.get', $vyzva, $puvod);
    openssl_sign($data . hash('sha256', PhpRS\Core\Passkey::zB64($klient), true), $podpis, $pkKlic, OPENSSL_ALGO_SHA256);

    return ['clientDataJSON' => $klient, 'authenticatorData' => PhpRS\Core\Passkey::b64($data), 'signature' => PhpRS\Core\Passkey::b64($podpis)];
};
$pkV2 = PhpRS\Core\Passkey::vyzva();
over('Passkey: platné přihlášení vrátí nové počitadlo', PhpRS\Core\Passkey::overPrihlaseni($pkPrihlas($pkV2, 5), $pkV2, $pkPuvod, $pkRp, $pkUlozeno['klic'], 4), 5);
over('Passkey: synchronizovaný klíč s nulovým počitadlem projde', PhpRS\Core\Passkey::overPrihlaseni($pkPrihlas($pkV2, 0), $pkV2, $pkPuvod, $pkRp, $pkUlozeno['klic'], 0), 0);
over('Passkey: přehraná odpověď (jiná výzva) neprojde', $pkOdmitne(fn () => PhpRS\Core\Passkey::overPrihlaseni($pkPrihlas($pkV2, 6), PhpRS\Core\Passkey::vyzva(), $pkPuvod, $pkRp, $pkUlozeno['klic'], 5)), true);
over('Passkey: počitadlo, které neroste, neprojde', $pkOdmitne(fn () => PhpRS\Core\Passkey::overPrihlaseni($pkPrihlas($pkV2, 5), $pkV2, $pkPuvod, $pkRp, $pkUlozeno['klic'], 5)), true);
over('Passkey: podpis jiným klíčem neprojde', $pkOdmitne(fn () => PhpRS\Core\Passkey::overPrihlaseni($pkPrihlas($pkV2, 6), $pkV2, $pkPuvod, $pkRp, $pkCizi['key'], 5)), true);
over('Passkey: odpověď z podvržené domény neprojde', $pkOdmitne(fn () => PhpRS\Core\Passkey::overPrihlaseni($pkPrihlas($pkV2, 6, 'redakce.example', 'https://redakce.example.podvrh.cz'), $pkV2, $pkPuvod, $pkRp, $pkUlozeno['klic'], 5)), true);
over('Passkey: klíč jiné domény neprojde', $pkOdmitne(fn () => PhpRS\Core\Passkey::overPrihlaseni($pkPrihlas($pkV2, 6, 'jina.example'), $pkV2, $pkPuvod, $pkRp, $pkUlozeno['klic'], 5)), true);
over('Passkey: bez potvrzení přítomnosti uživatele neprojde', $pkOdmitne(fn () => PhpRS\Core\Passkey::overPrihlaseni($pkPrihlas($pkV2, 6, 'redakce.example', 'https://redakce.example', 0x00), $pkV2, $pkPuvod, $pkRp, $pkUlozeno['klic'], 5)), true);
$pkZmeneno = $pkPrihlas($pkV2, 6); $pkZmeneno['authenticatorData'] = PhpRS\Core\Passkey::b64(PhpRS\Core\Passkey::zB64($pkZmeneno['authenticatorData']) . 'x');
over('Passkey: pozměněná data zařízení neprojdou', $pkOdmitne(fn () => PhpRS\Core\Passkey::overPrihlaseni($pkZmeneno, $pkV2, $pkPuvod, $pkRp, $pkUlozeno['klic'], 5)), true);
over('Passkey: původ a doména z adresy webu', [PhpRS\Core\Passkey::puvod('https://WWW.Web.cz/'), PhpRS\Core\Passkey::puvod('http://localhost:8080'), PhpRS\Core\Passkey::rpId('https://www.web.cz:8443/x')], ['https://www.web.cz', 'http://localhost:8080', 'www.web.cz']);

/* ---------- .htaccess: cíle přepisů jsou adresy, ne relativní cesty ---------- */
// Relativní cíl (RewriteRule ^ index.php) skončí na hostinzích, které mapují subdomény do složky mimo kořen webu, smyčkou a chybou 500.
$htaccess = (string) file_get_contents(PHPRS_ROOT . '/.htaccess');
preg_match_all('/^\s*RewriteRule\s+\S+\s+(\S+)/m', $htaccess, $cile);
over('.htaccess: žádný přepis nemá relativní cíl', array_values(array_filter($cile[1], static fn (string $c): bool => $c !== '-' && !str_starts_with($c, '%{ENV:BASE}/'))), []);
over('.htaccess: složka webu se počítá z adresy požadavku', str_contains($htaccess, 'E=BASE:%1'), true);

/* ---------- cesty v nabídce jako odkazy (hlášky, Stav systému, nápovědy) ---------- */
PhpRS\Core\Jazyk::nastav('cs', 'admin-');
$cestyHtml = PhpRS\Admin\Cesty::odkazy('/admin.php', 'Je k dispozici nová verze 3.0.1 – nainstalujete ji v Nastavení → Zálohy a aktualizace. <b>', ['config']);
over('Cesty: známá cesta je odkaz', str_contains($cestyHtml, '<a href="/admin.php?modul=config&amp;zalozka=zalohy">Nastavení → Zálohy a aktualizace</a>'), true);
over('Cesty: zbytek textu zůstává escapovaný', str_contains($cestyHtml, '&lt;b&gt;'), true);
over('Cesty: delší cesta má přednost a odkaz se nevnořuje', substr_count($cestyHtml, '<a '), 1);
over('Cesty: bez práva k modulu žádný odkaz', str_contains(PhpRS\Admin\Cesty::odkazy('/admin.php', 'Nastavení → Pošta', []), '<a '), false);
PhpRS\Core\Jazyk::nastav('en', 'admin-');
over('Cesty: v angličtině se odkazuje přeložená cesta', str_contains(PhpRS\Admin\Cesty::odkazy('/admin.php', t('Je k dispozici nová verze %s – nainstalujete ji v Nastavení → Zálohy a aktualizace.', '3.0.1'), ['config']), '>Settings → Backups and updates</a>'), true);
PhpRS\Core\Jazyk::nastav('cs', 'admin-');

/* ---------- aktualizace: úklid známých zrušených souborů (sirotci po přeskočené verzi) ---------- */
$zr = sys_get_temp_dir() . '/phprs-zrusene-' . bin2hex(random_bytes(4));
mkdir($zr . '/layout/default', 0775, true);
file_put_contents($zr . '/layout/default/info.php', 'upraveno správcem');
over('Aktualizace: upravený zrušený soubor zůstává', [PhpRS\Core\Aktualizace::uklidZrusene($zr), is_file($zr . '/layout/default/info.php')], [0, true]);
exec('rm -rf ' . escapeshellarg($zr));
if (is_file(PHPRS_ROOT . '/dist/phprs-3.0.0-beta.3.zip') && class_exists(ZipArchive::class)) {
    // skutečné soubory z vydané bety: musí zmizet i se složkou; používaná šablona se nemaže
    foreach (['', 'default'] as $pouzivana) {
        mkdir($zr . '/layout/default', 0775, true);
        $zipZr = new ZipArchive();
        $zipZr->open(PHPRS_ROOT . '/dist/phprs-3.0.0-beta.3.zip');
        foreach (['base.php', 'blok.php', 'cla_standard.php', 'info.php', 'style.css'] as $f) {
            file_put_contents($zr . '/layout/default/' . $f, $zipZr->getFromName('layout/default/' . $f));
        }
        $zipZr->close();
        over('Aktualizace: zrušená šablona ' . ($pouzivana === '' ? 'zmizí i se složkou' : 'zůstane, když ji web používá'), [PhpRS\Core\Aktualizace::uklidZrusene($zr, $pouzivana), is_dir($zr . '/layout/default')], $pouzivana === '' ? [5, false] : [0, true]);
        exec('rm -rf ' . escapeshellarg($zr));
    }
}

/* ---------- antispam: otisk IP ---------- */
over('Antispam::otisk: není to IP adresa', str_contains(PhpRS\Core\Antispam::otisk('203.0.113.7'), '203'), false);
over('Antispam::otisk: stejná adresa = stejný otisk', PhpRS\Core\Antispam::otisk('203.0.113.7'), PhpRS\Core\Antispam::otisk('203.0.113.7'));

/* ---------- import z WordPressu: čtení exportu (tools/fixtures/wordpress-ukazka.xml), náhled, bezpečné XML ---------- */
$wpCesta = PHPRS_ROOT . '/tools/fixtures/wordpress-ukazka.xml';
$wpOdmitne = static function (callable $f): bool { try { $f(); return false; } catch (RuntimeException) { return true; } };
$wp = new PhpRS\Core\WpSoubor($wpCesta);
over('WpSoubor: ukázkový export projde ověřením', $wpOdmitne(fn () => $wp->over()), false);
$wpHlavicka = $wp->hlavicka();
over('WpSoubor: starý web z <channel><link>', [$wpHlavicka['nazev'], $wpHlavicka['adresa']], ['Podhorský zpravodaj', 'https://www.podhorsky-zpravodaj.example']);
over('WpSoubor: autoři jako přihlašovací jméno => zobrazované jméno', $wpHlavicka['autori'], ['redakce' => 'Redakce Zpravodaje', 'bhorakova' => 'Běla Horáková']);
over('WpSoubor: rubriky s hierarchií', $wpHlavicka['rubriky'], ['zpravy' => ['nazev' => 'Zprávy', 'predek' => ''], 'z-radnice' => ['nazev' => 'Z radnice', 'predek' => 'zpravy']]);
over('WpSoubor: tři štítky', array_keys($wpHlavicka['stitky']), ['most', 'doprava', 'slavnosti']);
$wpPolozky = iterator_to_array($wp->polozky());
over('WpSoubor: devět položek, typy v pořadí souboru', array_column($wpPolozky, 'typ'), ['post', 'post', 'post', 'post', 'page', 'nav_menu_item', 'attachment', 'attachment', 'attachment']);
over('WpSoubor: přeskočení už zpracovaných položek drží pořadí', array_keys(iterator_to_array($wp->polozky(7))), [7, 8]);
over('WpSoubor: první příspěvek', [$wpPolozky[0]['id'], $wpPolozky[0]['stav'], $wpPolozky[0]['pripnuty'], $wpPolozky[0]['nahled'], $wpPolozky[0]['rubriky'], array_keys($wpPolozky[0]['stitky'])], [101, 'publish', true, 201, ['z-radnice' => 'Z radnice'], ['most', 'doprava']]);
over('WpSoubor: komentáře bez e-mailu a IP adresy', array_keys($wpPolozky[0]['komentare'][0]), ['id', 'autor', 'datum', 'text', 'predek', 'schvalen', 'typ']);
over('WpSoubor: e-mail ani IP se z exportu nikam nedostanou', (bool) preg_match('/posta\.example|198\.51\.100|203\.0\.113/', (string) json_encode($wpPolozky)), false);
over('WpImport: k importu jsou jen schválené komentáře (2 ze 3), odpověď zná svého předka', [count(array_filter($wpPolozky[0]['komentare'], PhpRS\Core\WpImport::jeKomentarKImportu(...))), $wpPolozky[0]['komentare'][1]['predek']], [2, 11]);
$wpStav = PhpRS\Core\WpImport::novyStav('wordpress-ukazka.xml');
PhpRS\Core\WpImport::analyzuj($wpStav, 30, $wpCesta);
over('WpImport náhled: fáze a počet položek', [$wpStav['faze'], $wpStav['celkem'], $wpStav['pozice']], ['nahled', 9, 0]);
over('WpImport náhled: příspěvky podle stavu a stránky', [$wpStav['prehled']['clanky'], $wpStav['prehled']['stranky']], [['publish' => 3, 'draft' => 1], ['publish' => 1]]);
over('WpImport náhled: rubriky, štítky, autoři, schválené komentáře, přílohy', [$wpStav['prehled']['rubriky'], $wpStav['prehled']['stitky'], $wpStav['prehled']['autori'], $wpStav['prehled']['komentare'], $wpStav['prehled']['prilohy']], [2, 3, 2, 2, 3]);
over('WpImport náhled: upozorní na cizí typ obsahu a zkratku doplňku', [$wpStav['prehled']['jine'], $wpStav['prehled']['zkratky']], [['nav_menu_item' => 1], ['kontaktni-formular' => 1]]);
over('WpImport náhled: adresy příloh pro galerie a hlavní obrázky', $wpStav['prilohy'][202] ?? '', 'https://www.podhorsky-zpravodaj.example/wp-content/uploads/2026/05/pohled.jpg');

$wpTmp = sys_get_temp_dir() . '/phprs-wp-' . bin2hex(random_bytes(4));
mkdir($wpTmp);
$wpHlava = '<rss version="2.0" xmlns:wp="http://wordpress.org/export/1.2/" xmlns:content="http://purl.org/rss/1.0/modules/content/"><channel><title>T</title><link>https://stary.example</link>';
file_put_contents($wpTmp . '/tajne.txt', 'TAJNY-OBSAH-SERVERU');
$wpSkodlive = [
    'vnější entita (XXE)' => '<?xml version="1.0"?><!DOCTYPE rss [<!ENTITY xxe SYSTEM "file://' . $wpTmp . '/tajne.txt">]>' . $wpHlava . '<item><title>&xxe;</title><content:encoded>&xxe;</content:encoded></item></channel></rss>',
    'miliarda smíchů' => '<?xml version="1.0"?><!DOCTYPE rss [<!ENTITY a "haha"><!ENTITY b "&a;&a;&a;&a;&a;&a;&a;&a;"><!ENTITY c "&b;&b;&b;&b;&b;&b;&b;&b;">]>' . $wpHlava . '<item><title>&c;</title></item></channel></rss>',
    'vnější DTD' => '<?xml version="1.0"?><!DOCTYPE rss SYSTEM "http://127.0.0.1:1/zly.dtd">' . $wpHlava . '</channel></rss>',
    'nedefinovaná entita' => '<?xml version="1.0"?>' . $wpHlava . '<item><title>&neexistuje;</title></item></channel></rss>',
    'jiné XML než export WordPressu' => '<?xml version="1.0"?><rss version="2.0"><channel><title>Obyčejné RSS</title><item><title>x</title></item></channel></rss>',
    'poškozené XML' => '<?xml version="1.0"?>' . $wpHlava . '<item><title>neuzavřeno</item>',
    'HTML místo XML' => '<html><body>xmlns:wp="http://wordpress.org/export/1.2/"</body></html>',
];
foreach ($wpSkodlive as $popis => $xml) {
    file_put_contents($wpTmp . '/zly.xml', $xml);
    $precteno = '';
    $odmitnuto = $wpOdmitne(function () use ($wpTmp, &$precteno): void {
        $zly = new PhpRS\Core\WpSoubor($wpTmp . '/zly.xml');
        $zly->over();
        $precteno = (string) json_encode([$zly->hlavicka(), iterator_to_array($zly->polozky())]);
    });
    over('WpSoubor odmítne: ' . $popis, [$odmitnuto, str_contains($precteno, 'TAJNY-OBSAH') || str_contains($precteno, 'hahahaha')], [true, false]);
}
file_put_contents($wpTmp . '/dobry.xml', '<?xml version="1.0"?>' . $wpHlava . '<item><title>A &amp; B</title></item></channel></rss>');
over('WpSoubor: běžné entity (&amp;) jsou v pořádku', iterator_to_array((new PhpRS\Core\WpSoubor($wpTmp . '/dobry.xml'))->polozky())[0]['titulek'], 'A & B');
exec('rm -rf ' . escapeshellarg($wpTmp));
foreach (['export.xml' => true, 'Můj web.WordPress.2026-09-21.XML' => true, '../config.xml' => false, 'slozka/export.xml' => false, '.skryty.xml' => false, 'export.php' => false, 'export.xml.php' => false, "export\0.xml" => false, '' => false] as $nazev => $ocekavano) {
    over('WpSoubor::platnyNazev ' . json_encode((string) $nazev), PhpRS\Core\WpSoubor::platnyNazev((string) $nazev), $ocekavano);
}
over('WpSoubor: název nahraného souboru bez diakritiky a vždy .xml', PhpRS\Core\WpSoubor::nazevProNahrani('Můj web.WordPress.2026-09-21.xml'), 'muj-web-wordpress-2026-09-21.xml');

/* ---------- import z WordPressu: čištění obsahu ---------- */
$wpCisti = PhpRS\Core\WpObsah::vycisti(...);
over('WpObsah: klasický editor – odstavce z prázdných řádků, <br> z konců řádků', $wpCisti("První řádek\ndruhý řádek\n\nDruhý odstavec"), "<p>První řádek<br>\ndruhý řádek</p>\n<p>Druhý odstavec</p>");
over('WpObsah: blokové značky se do <p> nebalí', $wpCisti("Úvod\n\n<h2>Titulek</h2>\n<ul>\n<li>a</li>\n<li>b</li>\n</ul>"), "<p>Úvod</p>\n<h2>Titulek</h2>\n<ul>\n<li>a</li>\n<li>b</li>\n</ul>");
over('WpObsah: komentáře Gutenbergu mizí, odstavce zůstávají', $wpCisti("<!-- wp:paragraph -->\n<p>Text</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:heading {\"level\":1} -->\n<h1 class=\"wp-block-heading\">Nadpis</h1>\n<!-- /wp:heading -->"), "<p>Text</p>\n<h2>Nadpis</h2>");
over('WpObsah: [caption] → figure s popiskem, odkaz na velký obrázek mizí', $wpCisti('[caption id="attachment_5" align="alignnone" width="300"]<a href="https://stary.example/wp-content/uploads/most.jpg"><img class="size-medium" src="https://stary.example/wp-content/uploads/most-300x200.jpg" alt="Most" width="300" height="200" /></a> Most přes řeku[/caption]'), '<figure><img src="https://stary.example/wp-content/uploads/most-300x200.jpg" alt="Most" width="300" height="200" loading="lazy"><figcaption>Most přes řeku</figcaption></figure>');
over('WpObsah: [gallery ids] → naše galerie jen ze známých obrázků', $wpCisti('[gallery ids="5,6,7,99" columns="2"]', [5 => 'https://stary.example/a.jpg', 6 => 'https://stary.example/b.png', 7 => 'https://stary.example/dokument.pdf']), '<figure class="galerie"><img src="https://stary.example/a.jpg" alt="" loading="lazy"><img src="https://stary.example/b.png" alt="" loading="lazy"></figure>');
over('WpObsah: blok galerie Gutenbergu → naše galerie', $wpCisti('<!-- wp:gallery {"linkTo":"none"} --><figure class="wp-block-gallery"><!-- wp:image {"id":5} --><figure class="wp-block-image"><img src="https://stary.example/a.jpg" alt="A" class="wp-image-5"/></figure><!-- /wp:image --></figure><!-- /wp:gallery -->'), '<figure class="galerie"><img src="https://stary.example/a.jpg" alt="A" loading="lazy"></figure>');
over('WpObsah: adresa YouTube na samostatném řádku je vlastní odstavec', $wpCisti("Text před\nhttps://www.youtube.com/watch?v=dQw4w9WgXcQ\nText po"), "<p>Text před</p>\n<p>https://www.youtube.com/watch?v=dQw4w9WgXcQ</p>\n<p>Text po</p>");
over('WpObsah: takový odstavec web promění v přehrávač', str_contains((new ReflectionClass(TypyObsahu::class))->newInstanceWithoutConstructor()->vlozeneAdresy($wpCisti("https://www.youtube.com/watch?v=dQw4w9WgXcQ")), 'youtube-nocookie.com/embed/dQw4w9WgXcQ'), true);
over('WpObsah: blok embed → adresa v odstavci', $wpCisti('<!-- wp:embed {"url":"https://vimeo.com/76979871","type":"video"} --><figure class="wp-block-embed"><div class="wp-block-embed__wrapper">https://vimeo.com/76979871</div></figure><!-- /wp:embed -->'), '<p>https://vimeo.com/76979871</p>');
over('WpObsah: iframe YouTube → adresa, cizí iframe pryč', $wpCisti('<iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" width="560"></iframe><iframe src="https://zly.example/"></iframe>'), '<p>https://www.youtube.com/watch?v=dQw4w9WgXcQ</p>');
over('WpObsah: zkratky doplňků mizí, jejich text a [sic] zůstávají', $wpCisti('[vc_row][vc_column width="1/2"]Text uvnitř[/vc_column][/vc_row] [contact-form-7 id="1"] citace [sic] a [[ukázka]]'), '<p>Text uvnitř  citace [sic] a [[ukázka]]</p>');
over('WpObsah: v ukázce kódu se závorky nemění', $wpCisti("<pre>pole[muj_klic] = 1;\n\nkonec</pre>"), "<pre>pole[muj_klic] = 1;\n\nkonec</pre>");
$wpNebezpecne = $wpCisti('<p onclick="x()" style="color:red">Klik <a href="java&#9;script:alert(1)" onmouseover="x()">odkaz</a> <a href="https://dobry.example/" target="_blank">ven</a></p><script>alert(1)</script><style>p{}</style><img src="data:image/svg+xml;base64,AAAA"><img src="https://stary.example/a.jpg" onerror="alert(1)" srcset="x 2x"><svg onload="alert(1)"><circle/></svg><form action="/x"><input name="a"></form><object data="x"></object><div class="obal"><span>Text v divu</span></div>');
over('WpObsah: skripty, styly, obsluhy událostí, javascript: a data: adresy neprojdou', $wpNebezpecne, "<p>Klik odkaz <a href=\"https://dobry.example/\" target=\"_blank\" rel=\"noopener\">ven</a></p>\n<figure><img src=\"https://stary.example/a.jpg\" alt=\"\" loading=\"lazy\"></figure>\n<p>Text v divu</p>");
over('WpObsah: po čištění nezbyde nic nebezpečného', (bool) preg_match('/<script|<style|<svg|<form|<iframe|<object|\son[a-z]+=|javascript:|data:|style=|srcset=/i', $wpNebezpecne), false);
over('WpObsah::bezpecnaAdresa', array_map(PhpRS\Core\WpObsah::bezpecnaAdresa(...), ['https://a.cz/', '/clanek/x', '#kotva', 'mailto:a@b.cz', "java\nscript:alert(1)", ' JAVASCRIPT:alert(1)', 'data:text/html,x', 'vbscript:x', '']), [true, true, true, true, false, false, false, false, false]);
over('WpObsah: perex z výtahu WordPressu, text celý', PhpRS\Core\WpObsah::perexAText('Ruční <b>výtah</b> &amp; spol.', "Odstavec jedna\n\nOdstavec dva"), ['<p>Ruční výtah &amp; spol.</p>', "<p>Odstavec jedna</p>\n<p>Odstavec dva</p>"]);
over('WpObsah: bez výtahu je perexem první odstavec a v textu se neopakuje', PhpRS\Core\WpObsah::perexAText('', "[caption]<img src=\"https://stary.example/a.jpg\" alt=\"\"> Popisek[/caption]\n\nOdstavec jedna\n\nOdstavec dva"), ['<p>Odstavec jedna</p>', "<figure><img src=\"https://stary.example/a.jpg\" alt=\"\" loading=\"lazy\"><figcaption>Popisek</figcaption></figure>\n<p>Odstavec dva</p>"]);
over('WpObsah: značka „Číst dál“ dělí perex a text', PhpRS\Core\WpObsah::perexAText('', "Před značkou\n<!--more-->\nZa značkou"), ['<p>Před značkou</p>', '<p>Za značkou</p>']);
over('WpObsah: cizí zkratky pro varování v náhledu', PhpRS\Core\WpObsah::ciziZkratky('[gallery ids="1"] [caption]x[/caption] [et_pb_section]a[/et_pb_section] [sic] <code>[muj_klic]</code>'), ['et_pb_section']);
[$wpUvod, $wpText] = PhpRS\Core\WpObsah::perexAText($wpPolozky[0]['perex'], $wpPolozky[0]['obsah'], $wpStav['prilohy']);
over('WpObsah: ukázkový příspěvek – perex, obrázek s popiskem, video, galerie, bez skriptu a zkratky', [str_starts_with($wpUvod, '<p>Po dvanácti měsících'), substr_count($wpText, '<figcaption>'), str_contains($wpText, '<p>https://www.youtube.com/watch?v=dQw4w9WgXcQ</p>'), substr_count($wpText, 'class="galerie"'), (bool) preg_match('/script|onclick|kontaktni-formular|javascript/i', $wpText)], [true, 1, true, 1, false]);

/* ---------- import z WordPressu: stav, datum, adresy ---------- */
$wpStavy = [];
foreach (['publish', 'future', 'draft', 'pending', 'private', 'trash', 'auto-draft', 'inherit', 'nesmysl'] as $wpS) {
    $wpM = PhpRS\Core\WpImport::stavClanku($wpS);
    $wpStavy[$wpS] = $wpM === null ? 'vynechat' : ($wpM['visible'] ? 'vydany' : 'koncept' . ($wpM['stav_redakce'] !== '' ? ':' . $wpM['stav_redakce'] : ''));
}
over('WpImport::stavClanku', $wpStavy, ['publish' => 'vydany', 'future' => 'vydany', 'draft' => 'koncept', 'pending' => 'koncept:korektura', 'private' => 'vynechat', 'trash' => 'vynechat', 'auto-draft' => 'vynechat', 'inherit' => 'vynechat', 'nesmysl' => 'vynechat']);
over('WpImport::stavClanku: příspěvek chráněný heslem se nezveřejní', PhpRS\Core\WpImport::stavClanku('publish', true), ['visible' => 0, 'stav_redakce' => '']);
over('WpImport::datum: místní čas starého webu', PhpRS\Core\WpImport::datum(['datum' => '2026-05-12 09:30:00', 'datum_gmt' => '2026-05-12 07:30:00']), '2026-05-12 09:30:00');
over('WpImport::datum: koncept s nulovým datem dostane dnešek', PhpRS\Core\WpImport::datum(['datum' => '0000-00-00 00:00:00', 'datum_gmt' => '0000-00-00 00:00:00', 'vydano' => ''], 1789000000), date('Y-m-d H:i:s', 1789000000));
$wpObsazene = ['lavka', 'lavka-2'];
over('WpImport::volnaAdresa: obsazená adresa dostane číslo', PhpRS\Core\WpImport::volnaAdresa('lavka', fn (string $a): bool => in_array($a, $wpObsazene, true)), 'lavka-3');
over('WpImport::volnaAdresa: volná zůstává', PhpRS\Core\WpImport::volnaAdresa('most', fn (string $a): bool => in_array($a, $wpObsazene, true)), 'most');
over('WpImport::staraCesta', array_map(PhpRS\Core\WpImport::staraCesta(...), ['https://stary.example/2026/05/lavka/', 'https://stary.example/?p=104', 'https://stary.example/blog/p%C5%99%C3%ADklad/', 'https://stary.example/' . str_repeat('x', 300)]), ['2026/05/lavka', '', 'blog/příklad', '']);
over('WpImport::bezRozmeru', array_map(PhpRS\Core\WpImport::bezRozmeru(...), ['https://s.example/u/foto-300x200.jpg', 'https://s.example/u/foto-1024x683.JPG?ver=2', 'https://s.example/u/foto.png', 'https://s.example/u/plan-2x4.pdf']), ['https://s.example/u/foto.jpg', 'https://s.example/u/foto.JPG', 'https://s.example/u/foto.png', 'https://s.example/u/plan-2x4.pdf']);
over('WpImport::zdroj: doména starého webu, nejvýš 40 znaků', [PhpRS\Core\WpImport::zdroj('https://WWW.Stary.example/blog'), PhpRS\Core\WpImport::zdroj(''), strlen(PhpRS\Core\WpImport::zdroj('https://' . str_repeat('a', 60) . '.example'))], ['wp:stary.example', 'wp', 40]);
over('ExportWebu::cesta: jen názvy exportů, nic mimo složku', [PhpRS\Core\ExportWebu::cesta('../config.php'), PhpRS\Core\ExportWebu::cesta('export-20260921-101500.zip/../../config.php'), PhpRS\Core\ExportWebu::cesta('phprs-20260918-130917-rucni-7d777965.sql.gz')], [null, null, null]);

/* ---------- import z WordPressu: stahování obrázků jen ze starého webu a jen z veřejných adres (ochrana před SSRF) ---------- */
$wpStahovani = new PhpRS\Core\StahovaniObrazku('https://www.stary-web.example/blog/');
over('StahovaniObrazku: doména starého webu bez www', $wpStahovani->domena(), 'stary-web.example');
foreach ([
    'https://www.stary-web.example/wp-content/uploads/a.jpg' => true,
    'http://stary-web.example/a.png' => true,
    'https://STARY-WEB.example./a.png' => true,
    'https://stary-web.example:443/a.png' => true,
    'https://stary-web.example:8443/a.png' => false,                 // jiný port
    'http://stary-web.example:22/a.png' => false,
    'https://cdn.stary-web.example/a.png' => false,                  // jiná (pod)doména
    'https://stary-web.example.utocnik.example/a.png' => false,
    'https://utocnik.example/stary-web.example/a.png' => false,
    'https://stary-web.example@utocnik.example/a.png' => false,      // doména schovaná za jménem
    'https://uzivatel:heslo@stary-web.example/a.png' => false,       // přihlašovací údaje v adrese
    'ftp://stary-web.example/a.png' => false,
    'file:///etc/passwd' => false,
    'gopher://stary-web.example/' => false,
    '//stary-web.example/a.png' => false,
    'http://127.0.0.1/a.png' => false,
    'http://169.254.169.254/latest/meta-data/' => false,
    "https://stary-web.example/a.png\r\nHost: jinam" => false,       // vložené hlavičky
    'https://stary-web.example\\@utocnik.example/a.png' => false,
    '' => false,
] as $wpUrl => $ocekavano) {
    over('StahovaniObrazku::povolenaAdresa ' . json_encode((string) $wpUrl), $wpStahovani->povolenaAdresa((string) $wpUrl), $ocekavano);
}
over('StahovaniObrazku: bez adresy starého webu se nestahuje nic', (new PhpRS\Core\StahovaniObrazku(''))->povolenaAdresa('https://cokoli.example/a.png'), false);
foreach ([
    '93.184.216.34' => true, '8.8.8.8' => true, '172.32.0.1' => true, '100.128.0.1' => true, '2606:4700:4700::1111' => true, '::ffff:93.184.216.34' => true,
    '10.0.0.5' => false, '172.16.0.1' => false, '172.31.255.255' => false, '192.168.1.1' => false, '127.0.0.1' => false, '127.255.255.254' => false,
    '169.254.169.254' => false, '100.64.0.1' => false, '100.127.255.255' => false, '0.0.0.0' => false, '0.1.2.3' => false, '224.0.0.1' => false, '255.255.255.255' => false,
    '192.0.2.10' => false, '198.18.0.1' => false, '::1' => false, '::' => false, 'fc00::1' => false, 'fd12:3456::1' => false, 'fe80::1' => false, 'ff02::1' => false,
    '::ffff:10.0.0.1' => false, '::ffff:127.0.0.1' => false, '64:ff9b::a00:1' => false, '::10.0.0.1' => false, '2002:a00:1::1' => false, '2001:0:4136:e378:8000:63bf:3fff:fdd2' => false,
    '2001:4860:4860::8888' => true, '[::1]' => false, 'neni-ip' => false, '' => false,
] as $wpIp => $ocekavano) {
    over('StahovaniObrazku::verejnaIp ' . $wpIp, PhpRS\Core\StahovaniObrazku::verejnaIp((string) $wpIp), $ocekavano);
}
over('StahovaniObrazku: IP adresa místo domény se posuzuje stejně', [$wpStahovani->overenaIp('127.0.0.1'), $wpStahovani->overenaIp('[::1]'), $wpStahovani->overenaIp('93.184.216.34')], [null, null, '93.184.216.34']);
over('StahovaniObrazku: přesměrování na jinou doménu neprojde dalším kolem kontroly', $wpStahovani->povolenaAdresa(PhpRS\Core\StahovaniObrazku::cilPresmerovani('https://stary-web.example/a.png', 'https://utocnik.example/a.png')), false);
over('StahovaniObrazku: přesměrování //jinam a do vnitřní sítě neprojde', [$wpStahovani->povolenaAdresa(PhpRS\Core\StahovaniObrazku::cilPresmerovani('https://stary-web.example/a.png', '//utocnik.example/a.png')), $wpStahovani->povolenaAdresa(PhpRS\Core\StahovaniObrazku::cilPresmerovani('https://stary-web.example/a.png', 'http://169.254.169.254/'))], [false, false]);
over('StahovaniObrazku: relativní přesměrování zůstává na starém webu', [PhpRS\Core\StahovaniObrazku::cilPresmerovani('https://stary-web.example/u/a.png', '/jinde/b.png'), PhpRS\Core\StahovaniObrazku::cilPresmerovani('https://stary-web.example/u/a.png', 'b.png')], ['https://stary-web.example/jinde/b.png', 'https://stary-web.example/u/b.png']);
$wpPng = (string) base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
$wpSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="1" height="1"><script>alert(1)</script></svg>';
over('StahovaniObrazku::typObrazku: PNG podle hlavičky i obsahu', PhpRS\Core\StahovaniObrazku::typObrazku('image/png; charset=binary', $wpPng), 'image/png');
over('StahovaniObrazku::typObrazku: hlavička tvrdí obrázek, obsah je HTML', PhpRS\Core\StahovaniObrazku::typObrazku('image/jpeg', '<html><body>přihlášení</body></html>'), null);
over('StahovaniObrazku::typObrazku: obsah je obrázek, hlavička ne', PhpRS\Core\StahovaniObrazku::typObrazku('text/html', $wpPng), null);
over('StahovaniObrazku::typObrazku: SVG se odmítá vždy', [PhpRS\Core\StahovaniObrazku::typObrazku('image/svg+xml', $wpSvg), PhpRS\Core\StahovaniObrazku::typObrazku('image/png', $wpSvg)], [null, null]);
over('StahovaniObrazku::typObrazku: prázdná odpověď', PhpRS\Core\StahovaniObrazku::typObrazku('image/png', ''), null);
over('StahovaniObrazku: limity podle zadání (15 MB, 3 přesměrování, 5 s spojení, 20 s celkem)', [PhpRS\Core\StahovaniObrazku::MAX_BAJTU, PhpRS\Core\StahovaniObrazku::MAX_PRESMEROVANI, PhpRS\Core\StahovaniObrazku::CAS_SPOJENI, PhpRS\Core\StahovaniObrazku::CAS_CELKEM], [15 * 1024 * 1024, 3, 5, 20]);
$wpZdrojak = (string) file_get_contents(PHPRS_ROOT . '/system/src/Core/StahovaniObrazku.php');
over('StahovaniObrazku: přesměrování se nikdy nenásledují automaticky a nic se neposílá navíc', [substr_count($wpZdrojak, 'CURLOPT_FOLLOWLOCATION => false'), str_contains($wpZdrojak, "'follow_location' => 0"), (bool) preg_match('/CURLOPT_(COOKIE\w*|USERPWD|HTTPHEADER|HTTPAUTH)\b/', $wpZdrojak), str_contains($wpZdrojak, "'phpRS-import'")], [1, true, false, true]);

echo $chyb === 0 ? "  ok     jednotkové testy ({$celkem})\n" : "  NALEZENO CHYB: {$chyb} z {$celkem}\n";
exit($chyb === 0 ? 0 : 1);
