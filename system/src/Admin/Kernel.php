<?php

declare(strict_types=1);

namespace PhpRS\Admin;

use PhpRS\Core\App;
use PhpRS\Core\Migrace;
use PhpRS\Core\Response;
use PhpRS\Core\Rozsireni;

/**
 * Administrace. Adresy: admin.php?modul=<ident>&akce=<akce>
 */
final class Kernel
{
    /**
     * Moduly v pořadí, v jakém jsou v menu (po skupinách Obsah, Čtenáři, Vzhled, Správa).
     * Identifikátory odpovídají phpRS 2 (users, clanky, news, bloky, topic, config...).
     *
     * @var list<class-string<Modul>>
     */
    public const array MODULY = [
        Moduly\Clanky::class,
        Moduly\Galerie::class,
        Moduly\Rubriky::class,
        Moduly\Stitky::class,
        Moduly\Stranky::class,
        Moduly\Novinky::class,
        Moduly\Komentare::class,
        Moduly\Ankety::class,
        Moduly\NewsletterAdmin::class,
        Moduly\CtenariAdmin::class,
        Moduly\Statistika::class,
        Moduly\Reklama::class,
        Moduly\Vzhled::class,
        Moduly\Bloky::class,
        Moduly\Autori::class,
        Moduly\Presmerovani::class,
        Moduly\ProtokolZmen::class,
        Moduly\Konfigurace::class,
    ];

    public function __construct(public readonly App $app)
    {
    }

    public function handle(): Response
    {
        $app = $this->app;
        $request = $app->request;

        if ($request->isPost() && !$app->session->csrfValid($request)) {
            return $this->page('Neplatný požadavek', $app->view->render('admin/chyba', [
                'text' => 'Platnost formuláře vypršela. Vraťte se zpět, obnovte stránku a odešlete jej znovu.',
            ]), 400);
        }

        // každá změna v administraci zneplatní cache stránek webu; průběžné požadavky editoru (zámek článku, rozepsaný
        // stav, asistent) web nemění - kdyby cache mazaly, při psaní článku by byla pořád studená
        if ($request->isPost() && !in_array($request->get('akce'), ['zamek', 'koncept', 'asistent'], true)) {
            \PhpRS\Front\Cache::vymaz();
        }
        $akce = $request->get('akce');
        // adresa webu: starší instalace ji ještě nemá - zapíše se podle adresy, na které pracuje přihlášený administrátor
        if ($app->settings()->get('adresa_webu') === '' && $app->auth()->isAdmin()) {
            $app->settings()->set('adresa_webu', $request->origin());
        }
        $request->setOrigin($app->settings()->get('adresa_webu'));
        // jazyk administrace: volba uživatele (Můj účet); přihlašovací stránka se řídí jazykem webu
        $jazyk = (string) ($app->auth()->user()['jazyk'] ?? '') ?: \PhpRS\Core\Jazyk::vychozi($app->settings());
        \PhpRS\Core\Jazyk::nastav(isset(\PhpRS\Core\Jazyk::ADMINISTRACE[$jazyk]) ? $jazyk : 'cs', 'admin-');
        if ($app->auth()->user() === null) {
            return $this->login();
        }
        if ($akce === 'logout' && $request->isPost()) {
            $app->auth()->logout();

            return Response::redirect($app->url('admin.php'));
        }

        // aktualizace struktury databáze po nahrání nové verze systému
        if ($app->auth()->isAdmin() && $app->settings()->int('verze_db') < Migrace::posledni()) {
            foreach (Migrace::proved($app->db(), $app->settings()) as $migrace) {
                $app->session->flash('info', 'Databáze byla aktualizována: ' . $migrace);
            }
        }

        if ($app->auth()->isAdmin()) {
            \PhpRS\Core\Zaloha::automaticka($app->db(), $app->settings());
        }

        $ident = $request->get('modul');
        if ($akce === 'ucet') {
            return (new Ucet($this))->handle();
        }
        if ($akce === 'pruvodce_skryt' && $request->isPost() && $app->auth()->isAdmin()) {
            $app->settings()->set('pruvodce_skryt', '1');

            return Response::redirect($app->url('admin.php'));
        }
        if ($ident === '') {
            $nova = $app->auth()->isAdmin() ? (new \PhpRS\Core\Aktualizace($app->settings()))->stav()['nova'] : null;
            if ($nova !== null) {
                $app->session->flash(!empty($nova['bezpecnostni']) ? 'chyba' : 'info', (!empty($nova['bezpecnostni']) ? 'Je k dispozici BEZPEČNOSTNÍ aktualizace ' : 'Je k dispozici nová verze ') . $nova['verze'] . ' – Nastavení → Zálohy a aktualizace.');
            }

            return $this->page('', $app->view->render('admin/desktop', $this->desktop()));
        }
        $class = $this->moduly()[$ident] ?? null;
        if ($class === null) {
            return $this->page('Chyba', $app->view->render('admin/chyba', ['text' => 'K tomuto modulu nemáte přístup.']), 403);
        }

        $odpoved = (new $class($this))->handle($akce === '' ? 'vypis' : $akce);
        if ($request->isPost() && $odpoved->status === 302 && $akce !== 'poradi' && $akce !== 'zamek') {
            // každá provedená změna v administraci jde do protokolu
            $popis = $request->post('titulek') ?: ($request->post('nazev') ?: ($request->post('user') ?: ($request->post('otazka') ?: $request->post('zalozka'))));
            Protokol::zapis($app, $ident, $akce, $popis);
        }

        return $odpoved;
    }

    /**
     * Moduly dostupné přihlášenému uživateli.
     *
     * @return array<string, class-string<Modul>> ident => třída
     */
    public function moduly(): array
    {
        $auth = $this->app->auth();
        $moduly = [];
        foreach (self::MODULY as $class) {
            if (!Rozsireni::je($this->app->settings(), $class::ROZSIRENI)) {
                continue;
            }
            $povolen = $class::JEN_ADMIN ? $auth->isAdmin() : $auth->maModul($class::IDENT, $class::PRO_VSECHNY);
            if ($povolen) {
                $moduly[$class::IDENT] = $class;
            }
        }

        return $moduly;
    }

    /** Obalí obsah společným rámcem administrace (menu, login proužek, hlášky). */
    public function page(string $nadpis, string $obsah, int $status = 200): Response
    {
        $app = $this->app;

        return Response::html($app->view->render('admin/layout', [
            'app' => $app,
            'nadpis' => t($nadpis),
            'obsah' => $obsah,
            'moduly' => $app->auth()->user() !== null ? $this->moduly() : [],
            'aktivni' => $app->request->get('modul'),
            'user' => $app->auth()->user(),
            'hlasky' => $app->session->takeFlashes(),
        ]), $status);
    }

    /**
     * Data úvodní obrazovky: přehled redakce.
     *
     * @return array<string, mixed>
     */
    private function desktop(): array
    {
        $data = ['app' => $this->app, 'moduly' => $this->moduly()];
        $db = $this->app->db();
        $jen = $this->app->auth()->articleScope();      // pro dotazy bez aliasu
        $jenC = $this->app->auth()->articleScope('c.');  // pro dotazy s aliasem c

        return $data + [
            'pruvodce' => $this->pruvodce(),
            // návštěvnost za 14 dní (vlastní měření bez cookies) a fronta práce redakce
            'navstevnost' => Rozsireni::je($this->app->settings(), 'statistika') && isset($this->moduly()['stat'])
                ? $db->all('SELECT den, navstevy, zobrazeni FROM {stat_dny} WHERE den > CURDATE() - INTERVAL 14 DAY ORDER BY den') : [],
            'fronta' => $db->all(
                "SELECT c.idc, c.titulek, c.datum, c.visible, c.stav_redakce, IF(u.jmeno = '' OR u.jmeno IS NULL, u.user, u.jmeno) AS autor_jm
                 FROM {clanky} c LEFT JOIN {user} u ON u.idu = c.autor
                 WHERE ((c.visible = 0 AND c.stav_redakce IN ('korektura', 'schvaleno')) OR (c.visible = 1 AND c.datum > NOW()))" . $jenC . "
                 ORDER BY c.visible, c.datum LIMIT 8",
            ),
            'pocty' => [
                'Vydané články' => (int) $db->value("SELECT COUNT(*) FROM {clanky} WHERE visible = 1 AND datum <= NOW(){$jen}"),
                'Naplánované' => (int) $db->value("SELECT COUNT(*) FROM {clanky} WHERE visible = 1 AND datum > NOW(){$jen}"),
                'Ke korektuře' => (int) $db->value("SELECT COUNT(*) FROM {clanky} WHERE visible = 0 AND stav_redakce = 'korektura'{$jen}"),
                'Koncepty' => (int) $db->value("SELECT COUNT(*) FROM {clanky} WHERE visible = 0 AND stav_redakce <> 'korektura'{$jen}"),
                ...(Rozsireni::je($this->app->settings(), 'komentare') && isset($this->moduly()['comment']) ? ['Komentáře ke schválení' => (int) $db->value('SELECT COUNT(*) FROM {komentare} WHERE zobrazit = 0')] : []),
                'Přečtení celkem' => (int) $db->value("SELECT COALESCE(SUM(visit), 0) FROM {clanky} WHERE 1 = 1{$jen}"),
            ],
            'posledni' => $db->all(
                "SELECT c.idc, c.titulek, c.datum, c.visible, c.visit, t.nazev AS tema_jm
                 FROM {clanky} c JOIN {topic} t ON t.idt = c.tema WHERE 1 = 1" . $jenC . "
                 ORDER BY COALESCE(c.zmeneno, c.datum) DESC LIMIT 6",
            ),
        ];
    }

    /**
     * První kroky po instalaci: co už je hotové, se pozná z dat. Vidí je jen administrátor, dokud je neskryje nebo nesplní.
     *
     * @return list<array{nazev:string, popis:string, url:string, hotovo:bool}>
     */
    private function pruvodce(): array
    {
        $app = $this->app;
        $s = $app->settings();
        if (!$app->auth()->isAdmin() || $s->bool('pruvodce_skryt')) {
            return [];
        }
        $db = $app->db();
        $kroky = [
            ['Dejte webu tvář', 'Šablona, logo, hlavní barva a písmo.', 'admin.php?modul=vzhled', $s->get('logo_webu') !== '' || $s->get('brand_akcent') !== ''],
            ['Založte rubriky', 'Třeba Zprávy, Kultura, Sport – články se do nich řadí.', 'admin.php?modul=topic', (int) $db->value('SELECT COUNT(*) FROM {topic}') >= 2],
            ['Napište první článek', 'Uvítací článek pak můžete smazat.', 'admin.php?modul=clanky&akce=novy', (int) $db->value("SELECT COUNT(*) FROM {clanky} WHERE seo_link <> 'vitejte-v-phprs-3'") >= 1],
            ['Poskládejte si stránku', 'Bloky přidáváte a přesouváte přímo na webu.', '?upravit=1', (int) $db->value('SELECT COUNT(*) FROM {bloky}') !== 0 && (int) $db->value("SELECT COUNT(*) FROM {protokol} WHERE modul = 'bloky'") > 0],
            ['Nastavte e-mail redakce a poštu', 'Kam chodí upozornění a odkud web odesílá e-maily.', 'admin.php?modul=config&zalozka=posta', $s->get('email_webu') !== '' && ($s->get('posta_rezim') === 'smtp' || $s->get('posta_od') !== '')],
        ];
        $vysledek = array_map(fn (array $k): array => ['nazev' => $k[0], 'popis' => $k[1], 'url' => $app->url($k[2]), 'hotovo' => (bool) $k[3]], $kroky);

        return array_filter($vysledek, fn (array $k): bool => !$k['hotovo']) === [] ? [] : $vysledek;
    }

    private function login(): Response
    {
        $app = $this->app;
        $chyba = null;
        if ($app->request->isPost()) {
            $druhyKrok = $app->request->post('kod') !== '' || $app->request->post('krok') === 'kod';
            $chyba = $druhyKrok
                ? $app->auth()->overKod($app->request->post('kod'), $app->request->ip())
                : $app->auth()->login($app->request->post('user'), $app->request->post('password'), $app->request->ip());
            if ($chyba === null && $app->auth()->user() !== null) {
                Protokol::zapis($app, 'prihlaseni', 'login', $druhyKrok ? 'dvoufázově' : '');

                return Response::redirect($app->url('admin.php'));
            }
            if ($chyba !== null && !$druhyKrok) {
                Protokol::zapis($app, 'prihlaseni', 'neuspech', 'účet: ' . mb_substr($app->request->post('user'), 0, 40));
            }
        }

        return Response::html($app->view->render('admin/login', [
            'app' => $app,
            'chyba' => $chyba,
            'login' => $app->request->post('user'),
            'kod' => $app->auth()->cekaNaKod(),
        ]), $chyba === null ? 200 : 401);
    }
}
