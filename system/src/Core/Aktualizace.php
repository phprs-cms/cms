<?php

declare(strict_types=1);

namespace PhpRS\Core;

/**
 * Aktualizace systému z administrace.
 *
 * Zdroj je soubor aktualizace.json: {"verze","vydano","url","sha256","podpis","min_php","bezpecnostni","zmeny":[...]}.
 * Vydání označené "bezpecnostni": true se umí nainstalovat samo (Nastavení -> Zálohy a aktualizace).
 * Balíček (ZIP) se přijme jen tehdy, když sedí SHA-256 a podpis Ed25519 (Core\Podpis::zpravaBalicku) ověřený některým
 * z veřejných klíčů v system/aktualizace.pub (provozní + záložní, viz docs/VYDAVANI.md). Soukromý klíč má jen vydavatel (tools/vydani.php).
 * Nikdy se nepřepisuje config.php, media/, storage/, install.php ani layouty, které nejsou součástí balíčku.
 */
final class Aktualizace
{
    /** Výchozí zdroj aktualizací; doplní se, až poběží web projektu. Lze přepsat v Nastavení. */
    public const string VYCHOZI_URL = 'https://phprs.eu/aktualizace.json';

    private const array CHRANENE = ['config.php', 'install.php', 'media/', 'storage/', 'image/ukazka/', 'tools/', '.git/'];
    private const int MAX_BAJTU = 60 * 1024 * 1024;

    public function __construct(
        private readonly Settings $settings,
        private readonly string $koren = PHPRS_ROOT,
        private readonly string $klicSoubor = PHPRS_SYSTEM . '/aktualizace.pub',
    ) {
    }

    public function url(): string
    {
        return $this->settings->get('aktualizace_url') !== '' ? $this->settings->get('aktualizace_url') : self::VYCHOZI_URL;
    }

    /**
     * Stav aktualizací; výsledek dotazu se pamatuje 12 hodin.
     *
     * @return array{nastaveno:bool, aktualni:string, nova:?array<string, mixed>, chyba:?string, overeno:int}
     */
    public function stav(bool $vynutit = false): array
    {
        $stav = ['nastaveno' => $this->url() !== '', 'aktualni' => PHPRS_VERSION, 'nova' => null, 'chyba' => null, 'overeno' => 0];
        if (!$stav['nastaveno']) {
            return $stav;
        }
        $cache = json_decode($this->settings->get('aktualizace_cache'), true);
        if (!$vynutit && is_array($cache) && ($cache['url'] ?? '') === $this->url() && time() - (int) ($cache['overeno'] ?? 0) < 12 * 3600) {
            $manifest = $cache['manifest'] ?? null;
            $stav['chyba'] = $cache['chyba'] ?? null;
            $stav['overeno'] = (int) $cache['overeno'];
        } else {
            try {
                $manifest = $this->manifest();
            } catch (\RuntimeException $e) {
                $manifest = null;
                $stav['chyba'] = $e->getMessage();
            }
            $stav['overeno'] = time();
            $this->settings->set('aktualizace_cache', (string) json_encode(['url' => $this->url(), 'overeno' => time(), 'manifest' => $manifest, 'chyba' => $stav['chyba']], JSON_UNESCAPED_UNICODE));
        }
        if (is_array($manifest) && version_compare((string) $manifest['verze'], PHPRS_VERSION, '>')) {
            $stav['nova'] = $manifest;
        }

        return $stav;
    }

    /**
     * Údržba na pozadí: jednou za 12 hodin ověří novou verzi; bezpečnostní vydání nainstaluje samo (je-li to
     * povoleno), jinak administrátora upozorní e-mailem. Volá se po odeslání stránky, takže čtenáře nezdržuje.
     */
    public static function naPozadi(App $app): void
    {
        $s = $app->settings();
        $a = new self($s);
        if ($a->url() === '') {
            return;
        }
        $cache = json_decode($s->get('aktualizace_cache'), true);
        if (is_array($cache) && time() - (int) ($cache['overeno'] ?? 0) < 12 * 3600) {
            return;
        }
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        ignore_user_abort(true);
        $nova = $a->stav(true)['nova'];
        if ($nova === null || empty($nova['bezpecnostni']) || $s->get('aktualizace_pokus') === $nova['verze']) {
            return;
        }
        $s->set('aktualizace_pokus', (string) $nova['verze']); // každá verze se zkouší a oznamuje jen jednou
        $vysledek = 'Je k dispozici bezpečnostní aktualizace ' . $nova['verze'] . '. Nainstalujte ji v administraci: Nastavení → Zálohy a aktualizace.';
        if ($s->bool('aktualizace_auto')) {
            try {
                Zaloha::vytvor($app->db(), 'predaktualizaci');
                $a->nainstaluj();
                $vysledek = 'Bezpečnostní aktualizace ' . $nova['verze'] . ' byla nainstalována automaticky. Před instalací vznikla záloha databáze.';
            } catch (\Throwable $e) {
                $vysledek .= ' Automatická instalace se nezdařila: ' . $e->getMessage();
            }
        }
        $komu = $s->get('email_webu');
        if ($komu !== '') {
            Posta::odesli($s, $komu, 'phpRS: bezpečnostní aktualizace ' . $nova['verze'], $vysledek . "\n\nZměny:\n- " . implode("\n- ", $nova['zmeny']) . "\n\n" . $s->get('nazev_webu'));
        }
    }

    /** @return string nainstalovaná verze */
    public function nainstaluj(): string
    {
        if (!class_exists(\ZipArchive::class) || !function_exists('sodium_crypto_sign_verify_detached')) {
            throw new \RuntimeException('Server nemá rozšíření zip nebo sodium - aktualizujte ručně nahráním souborů přes FTP.');
        }
        $m = $this->manifest();
        if (!version_compare((string) $m['verze'], PHPRS_VERSION, '>')) {
            throw new \RuntimeException('Žádná novější verze není k dispozici.');
        }
        if (version_compare(PHP_VERSION, (string) ($m['min_php'] ?? '8.4'), '<')) {
            throw new \RuntimeException('Nová verze vyžaduje PHP ' . $m['min_php'] . ', na serveru běží ' . PHP_VERSION . '.');
        }
        if (!is_writable($this->koren) || !is_writable($this->koren . '/system')) {
            throw new \RuntimeException('Soubory systému nejsou zapisovatelné - aktualizujte ručně přes FTP.');
        }
        if (Podpis::klice($this->klicSoubor) === []) {
            throw new \RuntimeException('Chybí veřejný klíč vydavatele (system/aktualizace.pub), balíček nelze ověřit.');
        }

        $pracovni = PHPRS_ROOT . '/storage/cache/aktualizace-' . bin2hex(random_bytes(4));
        $zip = $pracovni . '.zip';
        try {
            $this->stahni((string) $m['url'], $zip);
            $sha = hash_file('sha256', $zip);
            if (!hash_equals(strtolower((string) $m['sha256']), $sha)) {
                throw new \RuntimeException('Kontrolní součet balíčku nesouhlasí.');
            }
            // podpis kryje i příznak bezpečnostního vydání: kdo by ovládl jen web s manifestem, nesmí běžné vydání prohlásit za bezpečnostní
            if (!Podpis::plati(Podpis::zpravaBalicku((string) $m['verze'], $sha, !empty($m['bezpecnostni'])), (string) $m['podpis'], $this->klicSoubor)) {
                throw new \RuntimeException('Podpis balíčku není platný - balíček nepochází od vydavatele phpRS.');
            }
            $soubory = $this->rozbal($zip, $pracovni);
            touch(PHPRS_ROOT . '/storage/udrzba.lock');
            foreach ($soubory as $relativni) {
                $cil = $this->koren . '/' . $relativni;
                if (!is_dir(dirname($cil)) && !mkdir(dirname($cil), 0775, true)) {
                    throw new \RuntimeException('Nelze vytvořit složku ' . dirname($relativni) . '.');
                }
                if (!copy($pracovni . '/' . $relativni, $cil)) {
                    throw new \RuntimeException('Nelze zapsat soubor ' . $relativni . '.');
                }
            }
        } finally {
            @unlink(PHPRS_ROOT . '/storage/udrzba.lock');
            @unlink($zip);
            self::smazSlozku($pracovni);
        }
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
        $this->settings->set('aktualizace_cache', '');

        return (string) $m['verze'];
    }

    /** @return array<string, mixed> */
    private function manifest(): array
    {
        $json = $this->http($this->url(), 200 * 1024, 6); // krátký limit: kontrola běží po odeslání stránky, ale ne každý server ji umí oddělit
        $m = json_decode($json, true);
        if (!is_array($m) || !isset($m['verze'], $m['url'], $m['sha256'], $m['podpis']) || !preg_match('/^\d+\.\d+\.\d+([.-][0-9A-Za-z.-]+)?$/', (string) $m['verze'])) {
            throw new \RuntimeException('Soubor s informací o aktualizaci nemá platný tvar.');
        }
        $m['zmeny'] = array_values(array_filter(array_map(fn ($z): string => mb_substr((string) $z, 0, 300), (array) ($m['zmeny'] ?? []))));

        return $m;
    }

    private function stahni(string $url, string $cil): void
    {
        file_put_contents($cil, $this->http($url, self::MAX_BAJTU));
    }

    private function http(string $url, int $maxBajtu, int $limitSekund = 30): string
    {
        $host = (string) parse_url($url, PHP_URL_HOST);
        $mistni = in_array($host, ['localhost', '127.0.0.1'], true);
        if (!preg_match('#^https://#i', $url) && !($mistni && preg_match('#^http://#i', $url))) {
            throw new \RuntimeException('Zdroj aktualizací musí být na adrese https://.');
        }
        $data = @file_get_contents($url, false, stream_context_create(['http' => ['timeout' => $limitSekund, 'follow_location' => 1, 'max_redirects' => 5, 'header' => "User-Agent: phpRS/" . PHPRS_VERSION . "\r\n"]]), 0, $maxBajtu + 1);
        if ($data === false || $data === '') {
            throw new \RuntimeException('Zdroj aktualizací není dostupný (' . $host . ').');
        }
        if (strlen($data) > $maxBajtu) {
            throw new \RuntimeException('Stahovaný soubor je nečekaně velký.');
        }

        return $data;
    }

    /**
     * Rozbalí balíček do pracovní složky a vrátí seznam souborů k přepsání (bez chráněných cest).
     *
     * @return list<string>
     */
    private function rozbal(string $zipSoubor, string $kam): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipSoubor) !== true) {
            throw new \RuntimeException('Balíček nelze otevřít.');
        }
        $jmena = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $jmena[] = (string) $zip->getNameIndex($i);
        }
        // balíček může mít všechno v jedné složce navrch (jak to dělá GitHub) - ta se odřízne
        $prvni = array_unique(array_map(fn (string $j): string => explode('/', $j, 2)[0], $jmena));
        $predpona = count($prvni) === 1 && !in_array('index.php', $jmena, true) ? $prvni[0] . '/' : '';

        $soubory = [];
        foreach ($jmena as $i => $jmeno) {
            $relativni = substr($jmeno, strlen($predpona));
            if ($relativni === '' || str_ends_with($relativni, '/')) {
                continue;
            }
            if (str_contains($relativni, '..') || str_starts_with($relativni, '/') || str_contains($relativni, "\0") || str_contains($relativni, '\\')) {
                throw new \RuntimeException('Balíček obsahuje nebezpečnou cestu.');
            }
            foreach (self::CHRANENE as $chranena) {
                if ($relativni === $chranena || (str_ends_with($chranena, '/') && str_starts_with($relativni, $chranena))) {
                    continue 2;
                }
            }
            $cil = $kam . '/' . $relativni;
            if (!is_dir(dirname($cil))) {
                mkdir(dirname($cil), 0775, true);
            }
            file_put_contents($cil, $zip->getFromIndex($i));
            $soubory[] = $relativni;
        }
        $zip->close();
        if (!in_array('system/bootstrap.php', $soubory, true) || !in_array('index.php', $soubory, true)) {
            throw new \RuntimeException('Balíček neobsahuje phpRS.');
        }

        return $soubory;
    }

    private static function smazSlozku(string $slozka): void
    {
        if (!is_dir($slozka)) {
            return;
        }
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($slozka, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $polozka) {
            $polozka->isDir() ? @rmdir($polozka->getPathname()) : @unlink($polozka->getPathname());
        }
        @rmdir($slozka);
    }
}
