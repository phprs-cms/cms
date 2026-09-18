<?php

declare(strict_types=1);

namespace PhpRS\Core;

/**
 * Stav systému (health check): sada rychlých kontrol serveru, databáze, bezpečnosti a provozu.
 * Výsledek se zobrazuje v Nastavení a je dostupný i jako JSON pro monitoring (/stav.json?token=...).
 */
final class Stav
{
    /**
     * @return list<array{skupina:string, nazev:string, stav:string, info:string}> stav: ok | varovani | chyba
     */
    public static function kontroly(App $app): array
    {
        $k = [];
        $pridej = function (string $skupina, string $nazev, bool|string $stav, string $info) use (&$k): void {
            $k[] = ['skupina' => $skupina, 'nazev' => $nazev, 'stav' => is_bool($stav) ? ($stav ? 'ok' : 'chyba') : $stav, 'info' => $info];
        };
        $db = $app->db();
        $web = $app->settings();

        // --- server
        $pridej('Server', 'Verze PHP', PHP_VERSION_ID >= 80400, PHP_VERSION . (PHP_VERSION_ID >= 80400 ? '' : ' - systém vyžaduje 8.4 nebo novější'));
        foreach (['pdo_mysql' => 'databáze', 'mbstring' => 'čeština', 'gd' => 'zpracování obrázků'] as $ext => $ucel) {
            $pridej('Server', "Rozšíření {$ext}", extension_loaded($ext), $ucel . (extension_loaded($ext) ? '' : ' - chybí'));
        }
        foreach (['exif' => 'správné otočení fotek z mobilu', 'intl' => 'řazení podle češtiny', 'curl' => 'oznamování novinek vyhledávačům'] as $ext => $ucel) {
            $pridej('Server', "Rozšíření {$ext}", extension_loaded($ext) ? 'ok' : 'varovani', $ucel . (extension_loaded($ext) ? '' : ' - doporučeno doinstalovat'));
        }
        $pridej('Server', 'Limit nahrávaných souborů', self::bajty((string) ini_get('upload_max_filesize')) >= 8 * 1024 * 1024 ? 'ok' : 'varovani', 'upload_max_filesize = ' . ini_get('upload_max_filesize') . ', post_max_size = ' . ini_get('post_max_size'));
        $volno = @disk_free_space(PHPRS_ROOT);
        if ($volno !== false) {
            $pridej('Server', 'Volné místo na disku', $volno > 200 * 1024 * 1024 ? 'ok' : 'varovani', self::velikost((int) $volno));
        }

        // --- databáze
        $pridej('Databáze', 'Server', 'ok', (string) $db->value('SELECT VERSION()'));
        $cekajici = Migrace::posledni() - max(1, $web->int('verze_db'));
        $pridej('Databáze', 'Struktura databáze', $cekajici <= 0, $cekajici <= 0 ? 'aktuální (verze ' . $web->int('verze_db') . ')' : "čeká {$cekajici} aktualizací - proběhnou při příštím načtení administrace");
        $velikost = (int) $db->value('SELECT COALESCE(SUM(data_length + index_length), 0) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name LIKE ?', [addcslashes($db->prefix, '_%') . '%']);
        $pridej('Databáze', 'Velikost', 'ok', self::velikost($velikost) . ', článků: ' . (int) $db->value('SELECT COUNT(*) FROM {clanky}'));

        // --- soubory a bezpečnost
        foreach (['media' => 'nahrané obrázky', 'storage/log' => 'záznam chyb', 'storage/cache' => 'dočasná data'] as $slozka => $ucel) {
            $ok = is_dir(PHPRS_ROOT . '/' . $slozka) ? is_writable(PHPRS_ROOT . '/' . $slozka) : is_writable(PHPRS_ROOT);
            $pridej('Soubory', "Zápis do {$slozka}/", $ok, $ucel . ($ok ? '' : ' - nastavte práva k zápisu'));
        }
        $pridej('Bezpečnost', 'Instalátor', !is_file(PHPRS_ROOT . '/install.php') ? 'ok' : 'varovani', is_file(PHPRS_ROOT . '/install.php') ? 'soubor install.php je stále na serveru - smažte ho' : 'install.php je odstraněn');
        $pridej('Bezpečnost', 'HTTPS', $app->request->isHttps() ? 'ok' : 'varovani', $app->request->isHttps() ? 'web běží na šifrovaném spojení' : 'web neběží na HTTPS - přihlašovací údaje putují nešifrovaně');
        $pridej('Bezpečnost', 'Ladicí režim', !$app->debug(), $app->debug() ? 'v config.php je debug = true; na ostrém webu vypněte' : 'vypnutý');
        $pridej('Bezpečnost', 'Bezpečnostní hlavičky', 'ok', 'systém odesílá X-Content-Type-Options, Referrer-Policy a X-Frame-Options');
        $slabi = (int) $db->value('SELECT COUNT(*) FROM {user} WHERE blokovat = 1');
        $pridej('Bezpečnost', 'Zablokované účty', $slabi === 0 ? 'ok' : 'varovani', $slabi === 0 ? 'žádné' : "{$slabi} - po opakovaně chybném hesle; odblokujete je v Uživatelích");

        // --- provoz
        $log = PHPRS_ROOT . '/storage/log/chyby.log';
        $chyb = 0;
        if (is_file($log)) {
            $od = date('c', time() - 86400);
            foreach (array_slice(file($log, FILE_IGNORE_NEW_LINES) ?: [], -500) as $radek) {
                $chyb += (int) (substr($radek, 1, 25) >= $od);
            }
        }
        $pridej('Provoz', 'Chyby za posledních 24 hodin', $chyb === 0 ? 'ok' : 'varovani', $chyb === 0 ? 'žádné' : "{$chyb} - podrobnosti v storage/log/chyby.log");
        $pridej('Provoz', 'Indexování vyhledávači', $web->bool('indexovani') ? 'ok' : 'varovani', $web->bool('indexovani') ? 'povoleno' : 'zakázáno v záložce SEO a GEO - web se neobjeví ve vyhledávání');
        $media = 0;
        if (is_dir(PHPRS_ROOT . '/media')) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(PHPRS_ROOT . '/media', \FilesystemIterator::SKIP_DOTS)) as $soubor) {
                $media += $soubor->getSize();
            }
        }
        $pridej('Provoz', 'Velikost médií', 'ok', self::velikost($media));
        $pridej('Provoz', 'Odesílání pošty', function_exists('mail') ? 'ok' : 'varovani', function_exists('mail') ? 'funkce mail() je dostupná' : 'funkce mail() je na serveru vypnutá');

        return $k;
    }

    /** Souhrn pro monitoring: nejhorší nalezený stav. */
    public static function souhrn(array $kontroly): string
    {
        $stavy = array_column($kontroly, 'stav');

        return in_array('chyba', $stavy, true) ? 'chyba' : (in_array('varovani', $stavy, true) ? 'varovani' : 'ok');
    }

    private static function velikost(int $bajtu): string
    {
        foreach (['B', 'kB', 'MB', 'GB', 'TB'] as $jednotka) {
            if ($bajtu < 1024 || $jednotka === 'TB') {
                return number_format($bajtu, $jednotka === 'B' ? 0 : 1, ',', ' ') . ' ' . $jednotka;
            }
            $bajtu /= 1024;
        }

        return '';
    }

    private static function bajty(string $ini): int
    {
        $cislo = (int) $ini;

        return match (strtoupper(substr(trim($ini), -1))) {
            'G' => $cislo * 1024 ** 3, 'M' => $cislo * 1024 ** 2, 'K' => $cislo * 1024, default => $cislo,
        };
    }
}
