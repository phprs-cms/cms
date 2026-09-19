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

    /**
     * Vzhledy administrace: stejné HTML, jiný stylesheet (image/admin.css a image/admin-2026.css).
     * Retro je pocta původnímu phpRS (Jiří Lukáš, 2001-2007), jehož vývoj skončil - žádnou starou funkci nenese.
     */
    public const array PROSTREDI = ['retro' => 'phpRS retro', '2026' => 'phpRS 2026'];

    public function __construct(public readonly App $app)
    {
    }

    /** Prostředí přihlášeného uživatele; když si žádné nezvolil (a na přihlašovací stránce), platí výchozí z Konfigurace. */
    public function prostredi(): string
    {
        $volba = (string) ($this->app->auth()->user()['prostredi'] ?? '');
        if (!isset(self::PROSTREDI[$volba])) {
            $volba = $this->app->settings()->get('prostredi_admin');
        }

        return isset(self::PROSTREDI[$volba]) ? $volba : 'retro';
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

        if ($request->isPost()) {
            \PhpRS\Front\Cache::vymaz(); // každá změna v administraci zneplatní cache stránek webu
        }
        $akce = $request->get('akce');
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
        if ($akce === 'prostredi' && $request->isPost()) {
            if (isset(self::PROSTREDI[$request->post('prostredi')])) {
                $app->db()->update('user', ['prostredi' => $request->post('prostredi')], ['idu' => $app->auth()->id()]);
            }

            return Response::redirect($app->url('admin.php' . (preg_match('/^[a-z]+$/', $ident) ? '?modul=' . $ident : '')));
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
            'prostredi' => $this->prostredi(),
        ]), $status);
    }

    /**
     * Data úvodní obrazovky. Retro ukazuje jen logo jako originál, prostředí 2026 přehled redakce.
     *
     * @return array<string, mixed>
     */
    private function desktop(): array
    {
        $data = ['app' => $this->app, 'prostredi' => $this->prostredi(), 'moduly' => $this->moduly()];
        if ($data['prostredi'] === 'retro') {
            return $data;
        }
        $db = $this->app->db();
        $autori = $this->app->auth()->spravovaniAutori();
        $jen = $autori === null ? '' : ' AND autor IN (' . implode(',', $autori) . ')';

        return $data + [
            'pocty' => [
                'Vydané články' => (int) $db->value("SELECT COUNT(*) FROM {clanky} WHERE visible = 1 AND datum <= NOW(){$jen}"),
                'Naplánované' => (int) $db->value("SELECT COUNT(*) FROM {clanky} WHERE visible = 1 AND datum > NOW(){$jen}"),
                'Ke korektuře' => (int) $db->value("SELECT COUNT(*) FROM {clanky} WHERE visible = 0 AND stav_redakce = 'korektura'{$jen}"),
                'Koncepty' => (int) $db->value("SELECT COUNT(*) FROM {clanky} WHERE visible = 0 AND stav_redakce <> 'korektura'{$jen}"),
                ...(Rozsireni::je($this->app->settings(), 'komentare') ? ['Komentáře ke schválení' => (int) $db->value('SELECT COUNT(*) FROM {komentare} WHERE zobrazit = 0')] : []),
                'Přečtení celkem' => (int) $db->value("SELECT COALESCE(SUM(visit), 0) FROM {clanky} WHERE 1 = 1{$jen}"),
            ],
            'posledni' => $db->all(
                "SELECT c.idc, c.titulek, c.datum, c.visible, c.visit, t.nazev AS tema_jm
                 FROM {clanky} c JOIN {topic} t ON t.idt = c.tema WHERE 1 = 1" . str_replace('autor', 'c.autor', $jen) . "
                 ORDER BY COALESCE(c.zmeneno, c.datum) DESC LIMIT 6",
            ),
        ];
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
            'prostredi' => $this->prostredi(),
        ]), $chyba === null ? 200 : 401);
    }
}
