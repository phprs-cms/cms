<?php
/**
 * phpRS 3 - příprava vydání (spouští vydavatel na svém počítači, na web se nenahrává).
 *
 *   php tools/vydani.php 3.0.1 --url=https://github.com/<ucet>/<repo>/releases/download/v3.0.1/phprs-3.0.1.zip \
 *       --zmena="Oprava ..." --zmena="Nové ..." [--bezpecnostni]
 *
 * --bezpecnostni označí vydání jako bezpečnostní opravu: instalace se na ně aktualizují samy a správce dostane e-mail.
 * Soukromý klíč lze místo souboru předat proměnnou prostředí PHPRS_KLIC (base64) - pro vydávání z GitHub Actions.
 *
 * Vytvoří dist/phprs-<verze>.zip (soubory sledované gitem) a dist/aktualizace.json podepsaný soukromým klíčem.
 * Klíče: tools/klice/vydavatel.key (SOUKROMÝ - nikdy do gitu, zálohujte si ho) a system/aktualizace.pub (veřejný, součást systému).
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit('Jen z příkazové řádky.');
}
$koren = dirname(__DIR__);
$verze = $argv[1] ?? '';
$volby = ['url' => '', 'zmeny' => [], 'bezpecnostni' => false];
foreach (array_slice($argv, 2) as $arg) {
    if (str_starts_with($arg, '--url=')) {
        $volby['url'] = substr($arg, 6);
    } elseif ($arg === '--bezpecnostni') {
        $volby['bezpecnostni'] = true;
    } elseif (str_starts_with($arg, '--zmena=')) {
        $volby['zmeny'][] = substr($arg, 8);
    }
}
if (!preg_match('/^\d+\.\d+\.\d+([.-][0-9A-Za-z.-]+)?$/', $verze)) {
    exit("Použití: php tools/vydani.php <verze> --url=<adresa ZIPu> [--zmena=\"...\"]\n");
}
if (!str_contains((string) file_get_contents($koren . '/system/bootstrap.php'), "const PHPRS_VERSION = '{$verze}';")) {
    exit("V system/bootstrap.php není PHPRS_VERSION = '{$verze}'. Nejprve zvyšte verzi a změnu commitněte.\n");
}

// --- klíče
$soukromy = $koren . '/tools/klice/vydavatel.key';
$verejny = $koren . '/system/aktualizace.pub';
if (getenv('PHPRS_KLIC') === false && !is_file($soukromy)) {
    @mkdir(dirname($soukromy), 0700, true);
    $par = sodium_crypto_sign_keypair();
    file_put_contents($soukromy, base64_encode(sodium_crypto_sign_secretkey($par)) . "\n");
    chmod($soukromy, 0600);
    file_put_contents($verejny, base64_encode(sodium_crypto_sign_publickey($par)) . "\n");
    echo "Vytvořen nový pár klíčů. SOUKROMÝ klíč {$soukromy} si bezpečně zálohujte; veřejný system/aktualizace.pub commitněte.\n";
    exit("Spusťte příkaz znovu, až bude veřejný klíč v gitu (musí být součástí balíčku).\n");
}
$sk = base64_decode(trim(getenv('PHPRS_KLIC') !== false ? (string) getenv('PHPRS_KLIC') : (string) file_get_contents($soukromy)), true);
if ($sk === false || strlen($sk) !== SODIUM_CRYPTO_SIGN_SECRETKEYBYTES || base64_encode(sodium_crypto_sign_publickey_from_secretkey($sk)) !== trim((string) file_get_contents($verejny))) {
    exit("Soukromý klíč neodpovídá veřejnému klíči system/aktualizace.pub.\n");
}

// --- balíček ze souborů sledovaných gitem
$soubory = array_filter(explode("\n", (string) shell_exec('cd ' . escapeshellarg($koren) . ' && git ls-files')));
$vynechat = ['tools/', 'docs/', '.github/', '.claude/', 'CLAUDE.md', '.gitignore']; // kořenový CLAUDE.md je pro vývoj; layout/CLAUDE.md (pravidla šablon) do balíčku patří
@mkdir($koren . '/dist');
$zipSoubor = $koren . "/dist/phprs-{$verze}.zip";
@unlink($zipSoubor);
$zip = new ZipArchive();
$zip->open($zipSoubor, ZipArchive::CREATE);
$otisky = [];
foreach ($soubory as $soubor) {
    foreach ($vynechat as $v) {
        if ($soubor === $v || str_starts_with($soubor, $v)) {
            continue 2;
        }
    }
    $zip->addFile($koren . '/' . $soubor, $soubor);
    // seznam souborů jádra s otisky: instalace podle něj pozná změněné, chybějící a přidané soubory (Core\Integrita)
    // bez uživatelských složek a bez install.php (aktualizace ho nepřepisuje a správce ho po instalaci může smazat)
    if (!preg_match('#^(media|storage)/|^install\.php$#', $soubor)) {
        $otisky[$soubor] = hash_file('sha256', $koren . '/' . $soubor);
    }
}
require_once $koren . '/system/src/Core/Integrita.php';
ksort($otisky);
$zip->addFromString('system/soubory.json', json_encode([
    'verze' => $verze, 'soubory' => $otisky,
    'podpis' => base64_encode(sodium_crypto_sign_detached(PhpRS\Core\Integrita::kPodpisu($verze, $otisky), $sk)),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
$zip->close();

$sha = hash_file('sha256', $zipSoubor);
$manifest = [
    'verze' => $verze, 'vydano' => date('Y-m-d'), 'url' => $volby['url'], 'sha256' => $sha,
    'podpis' => base64_encode(sodium_crypto_sign_detached($verze . '|' . $sha, $sk)),
    'min_php' => '8.4', 'bezpecnostni' => $volby['bezpecnostni'], 'zmeny' => $volby['zmeny'],
];
file_put_contents($koren . '/dist/aktualizace.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
echo "Hotovo: dist/phprs-{$verze}.zip (" . round(filesize($zipSoubor) / 1024) . " kB) a dist/aktualizace.json\n";
echo $volby['url'] === '' ? "POZOR: nezadali jste --url, doplňte adresu ZIPu do dist/aktualizace.json PŘED podpisem (spusťte znovu s --url).\n" : "1) ZIP nahrajte na {$volby['url']}\n2) aktualizace.json nahrajte na web projektu.\n";
