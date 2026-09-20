<?php

declare(strict_types=1);

namespace PhpRS\Install;

use PhpRS\Core\Auth;
use PhpRS\Core\Db;
use PhpRS\Core\Migrace;
use PhpRS\Core\Request;
use PhpRS\Core\Response;
use PhpRS\Core\View;
use PhpRS\Front\Layouty;

/**
 * Webový instalátor: ověří server, založí tabulky, prvního admina a zapíše config.php.
 */
final class Installer
{
    private readonly Request $request;
    private readonly View $view;

    public function __construct()
    {
        $this->request = Request::fromGlobals();
        $this->view = new View([PHPRS_SYSTEM . '/views']);
    }

    public function handle(): Response
    {
        if (is_file(PHPRS_ROOT . '/config.php')) {
            return $this->stranka('hotovo', ['jizNainstalovano' => true]);
        }

        $pozadavky = $this->pozadavky();
        $data = [
            'db_host' => 'localhost', 'db_port' => '3306', 'db_name' => '', 'db_user' => '', 'db_password' => '', 'db_prefix' => 'rs_',
            'nazev_webu' => 'Můj magazín', 'user' => 'admin', 'jmeno' => '', 'email' => '',
            'prostredi' => '2026', 'layout' => 'default',
        ];
        $chyby = [];

        if ($this->request->isPost() && !in_array(false, array_column($pozadavky, 'ok'), true)) {
            foreach (array_keys($data) as $klic) {
                // heslo k databázi se neořezává - může obsahovat mezery
                $data[$klic] = $klic === 'db_password' ? (string) ($_POST[$klic] ?? '') : $this->request->post($klic);
            }
            $chyby = $this->instaluj($data, (string) ($_POST['password'] ?? ''), (string) ($_POST['password2'] ?? ''));
            if ($chyby === []) {
                return $this->stranka('hotovo', ['jizNainstalovano' => false]);
            }
        }

        return $this->stranka('formular', ['pozadavky' => $pozadavky, 'data' => $data, 'chyby' => $chyby, 'layouty' => Layouty::seznam()]);
    }

    /** @return list<array{nazev:string, ok:bool, info:string}> */
    private function pozadavky(): array
    {
        $zapis = fn (string $cesta): bool => is_writable(PHPRS_ROOT . $cesta);

        return [
            ['nazev' => 'PHP 8.4 nebo novější', 'ok' => PHP_VERSION_ID >= 80400, 'info' => 'běží ' . PHP_VERSION],
            ['nazev' => 'Rozšíření pdo_mysql', 'ok' => extension_loaded('pdo_mysql'), 'info' => 'připojení k databázi MySQL / MariaDB'],
            ['nazev' => 'Rozšíření mbstring', 'ok' => extension_loaded('mbstring'), 'info' => 'práce s češtinou'],
            ['nazev' => 'Zápis do kořenové složky', 'ok' => $zapis(''), 'info' => 'kvůli vytvoření config.php'],
            ['nazev' => 'Zápis do složky storage/', 'ok' => $zapis('/storage/log') && $zapis('/storage/cache'), 'info' => 'logy a cache'],
        ];
    }

    /**
     * @param array<string, string> $d
     * @return array<string, string> chyby; prázdné pole = nainstalováno
     */
    private function instaluj(array $d, string $heslo, string $heslo2): array
    {
        $chyby = [];
        if (!preg_match('/^[a-z][a-z0-9_]{0,15}$/', $d['db_prefix'])) {
            $chyby['db_prefix'] = 'Předpona: malá písmena, číslice a podtržítko, nejvýše 16 znaků (např. rs_).';
        }
        if ($d['db_name'] === '' || $d['db_user'] === '') {
            $chyby['db_name'] = 'Vyplňte název databáze a uživatele.';
        }
        if (!preg_match('/^[a-zA-Z0-9._-]{2,40}$/', $d['user'])) {
            $chyby['user'] = 'Přihlašovací jméno: 2-40 znaků, písmena bez diakritiky, číslice, tečka, pomlčka, podtržítko.';
        }
        if (mb_strlen($heslo) < 10) {
            $chyby['password'] = 'Heslo musí mít alespoň 10 znaků.';
        } elseif ($heslo !== $heslo2) {
            $chyby['password'] = 'Hesla se neshodují.';
        }
        if ($d['email'] !== '' && filter_var($d['email'], FILTER_VALIDATE_EMAIL) === false) {
            $chyby['email'] = 'E-mail nemá platný tvar.';
        }
        if (!in_array($d['prostredi'], ['retro', '2026'], true)) {
            $d['prostredi'] = 'retro';
        }
        if (!isset(Layouty::seznam()[$d['layout']])) {
            $d['layout'] = 'default';
        }
        if ($chyby !== []) {
            return $chyby;
        }

        $config = [
            'db' => [
                'host' => $d['db_host'] !== '' ? $d['db_host'] : 'localhost',
                'port' => (int) $d['db_port'] ?: 3306,
                'name' => $d['db_name'],
                'user' => $d['db_user'],
                'password' => $d['db_password'],
                'prefix' => $d['db_prefix'],
            ],
            'debug' => false,
        ];

        try {
            $db = Db::fromConfig($config['db']);
            $db->pdo();
        } catch (\PDOException $e) {
            return ['db_name' => 'K databázi se nepodařilo připojit: ' . $e->getMessage()];
        }
        $existuje = $db->value(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?',
            [$d['db_prefix'] . 'user'],
        );
        if ((int) $existuje > 0) {
            return ['db_prefix' => 'V databázi už tabulky s touto předponou existují. Zvolte jinou předponu, nebo je nejprve odstraňte.'];
        }

        try {
            foreach (Migrace::prikazy((string) file_get_contents(PHPRS_SYSTEM . '/sql/schema.sql'), $d['db_prefix']) as $sql) {
                $db->pdo()->exec($sql);
            }
            $this->vychoziData($db, $d, $heslo);
        } catch (\PDOException $e) {
            return ['db_name' => 'Vytvoření tabulek selhalo: ' . $e->getMessage()];
        }

        $obsah = "<?php\n/**\n * phpRS 3 - konfigurace vytvořená instalátorem " . date('j. n. Y') . ".\n */\n\nreturn " . var_export($config, true) . ";\n";
        if (file_put_contents(PHPRS_ROOT . '/config.php', $obsah, LOCK_EX) === false) {
            return ['db_name' => 'Tabulky jsou vytvořeny, ale nepodařilo se zapsat config.php. Zkontrolujte práva k zápisu.'];
        }

        return [];
    }

    /** @param array<string, string> $d */
    private function vychoziData(Db $db, array $d, string $heslo): void
    {
        $db->transaction(function (Db $db) use ($d, $heslo): void {
            $admin = $db->insert('user', [
                'user' => $d['user'],
                'password' => password_hash($heslo, PASSWORD_DEFAULT),
                'jmeno' => $d['jmeno'],
                'email' => $d['email'],
                'admin' => Auth::ADMIN,
                'pravo_vydavat' => 1,
                'prostredi' => $d['prostredi'],
            ]);

            \PhpRS\Core\Hledani::dopln($db);
            $nastaveni = ['nazev_webu' => $d['nazev_webu'], 'adresa_webu' => $this->request->origin(), 'email_webu' => $d['email'], 'layout' => $d['layout'], 'rozvrzeni' => Layouty::seznam()[$d['layout']]['rozvrzeni'], 'prostredi_admin' => $d['prostredi'], 'verze_db' => (string) Migrace::posledni()];
            foreach ($nastaveni as $klic => $hodnota) {
                $db->insert('config', ['promenna' => $klic, 'hodnota' => $hodnota]);
            }
            $db->insert('levely', ['nazev_levelu' => 'Základní', 'hodnota' => 0, 'zakladni' => 1]);
            $sablona = $db->insert('cla_sab', ['nazev_cla_sab' => 'Standardní', 'soubor_cla_sab' => 'standard']);

            $bloky = [['leva', 'Rubriky', 'rub', 200], ['leva', 'Vyhledávání', 'hle', 100], ['prava', 'Novinky', 'nov', 200], ['prava', 'Nejčtenější články', 'nej', 100]];
            foreach ($bloky as [$zona, $nazev, $sys, $hodnost]) {
                $db->insert('bloky', ['nazev' => $nazev, 'obsah' => '', 'sys_funkce' => $sys, 'hodnost' => $hodnost, 'zona' => $zona]);
            }

            $rubrika = $db->insert('topic', ['nazev' => 'Aktuality', 'seo_link' => 'aktuality', 'popis' => '']);
            $db->insert('news', ['titulek' => 'Web běží na phpRS 3', 'informace' => 'Instalace proběhla úspěšně.', 'datum' => date('Y-m-d H:i:s')]);
            $db->insert('clanky', [
                'seo_link' => 'vitejte-v-phprs-3',
                'titulek' => 'Vítejte v phpRS 3',
                'uvod' => '<p>Redakční systém je nainstalován a připraven. Tento článek můžete v administraci upravit nebo smazat.</p>',
                'text' => '<p>Do administrace se dostanete na adrese <code>admin.php</code>. Začněte <strong>Úpravou rubrik</strong>, '
                    . 'potom pište v <strong>Editaci článků</strong>. Rozložení webu do sloupců a bloků najdete v <strong>Úpravě bloků</strong>.</p>',
                'tema' => $rubrika,
                'autor' => $admin,
                'datum' => date('Y-m-d H:i:s'),
                'visible' => 1,
                'sablona' => $sablona,
            ]);
        });
    }

    /** @param array<string, mixed> $data */
    private function stranka(string $sablona, array $data): Response
    {
        return Response::html($this->view->render('install/' . $sablona, $data + ['base' => $this->request->basePath()]));
    }
}
