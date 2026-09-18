<?php

declare(strict_types=1);

namespace PhpRS\Front;

use PhpRS\Admin\Moduly\Rubriky;
use PhpRS\Core\App;
use PhpRS\Core\Response;
use PhpRS\Core\View;

/**
 * Veřejná část webu.
 *
 *   /                    hlavní stránka
 *   /clanek/<seo-link>   celý článek
 *   /rubrika/<seo-link>  výpis rubriky
 *   /hledani?q=...       vyhledávání
 *   /rss.xml             RSS kanál
 *   /<alias>             stránkový alias
 *
 * Staré adresy phpRS 2 (view.php?cisloclanku=, search.php?rstema=) se přesměrují na nové.
 */
final class Kernel
{
    private readonly View $view;
    private readonly Clanky $clanky;

    public function __construct(private readonly App $app)
    {
        $layout = $app->settings()->get('layout');
        if (!preg_match('/^[a-z0-9_-]+$/i', $layout) || !is_dir(PHPRS_ROOT . '/layout/' . $layout)) {
            $layout = 'default';
        }
        // šablona se hledá nejdřív v layoutu webu, potom mezi systémovými - layout tak může přepsat cokoli
        $this->view = new View([PHPRS_ROOT . '/layout/' . $layout, PHPRS_SYSTEM . '/views/front']);
        $this->clanky = new Clanky($app->db(), $app->settings(), $app->request->basePath());
    }

    public function handle(): Response
    {
        $request = $this->app->request;
        if (($stare = $this->stareAdresy()) !== null) {
            return $stare;
        }

        $path = $request->path();
        if ($path === '/' || $path === '/index.php') {
            return $this->hlavniStranka();
        }
        if (preg_match('#^/clanek/([a-z0-9-]+)$#', $path, $m)) {
            return $this->clanek($m[1]);
        }
        if (preg_match('#^/rubrika/([a-z0-9-]+)$#', $path, $m)) {
            return $this->rubrika($m[1]);
        }
        if (preg_match('#^/stitek/([a-z0-9-]+)$#', $path, $m)) {
            return $this->stitek($m[1]);
        }
        if ($path === '/hledani') {
            return $this->hledani();
        }
        if ($path === '/rss.xml') {
            return $this->rss();
        }
        $seo = new Seo($this->app);
        if ($path === '/robots.txt') {
            return new Response($seo->robotsTxt(), 200, ['Content-Type' => 'text/plain; charset=utf-8']);
        }
        if ($path === '/sitemap.xml') {
            return new Response($seo->sitemapXml(), 200, ['Content-Type' => 'application/xml; charset=utf-8']);
        }
        if ($path === '/sitemap-news.xml') {
            return new Response($seo->sitemapNewsXml(), 200, ['Content-Type' => 'application/xml; charset=utf-8']);
        }
        if ($path === '/feed.json') {
            [$clanky] = $this->clanky->naHlavniStranku(1, 20);

            return new Response(json_encode($seo->jsonFeed($clanky), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 200, ['Content-Type' => 'application/feed+json; charset=utf-8']);
        }
        $klicIndexNow = $this->app->settings()->get('indexnow_klic');
        if ($klicIndexNow !== '' && $path === '/' . $klicIndexNow . '.txt') {
            return new Response($klicIndexNow, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
        }
        if ($path === '/souhlas' && $request->isPost()) {
            // evidence souhlasu s cookies: bez IP adresy, jen náhodný identifikátor z cookie návštěvníka
            $kategorie = implode(',', array_intersect(explode(',', $request->post('kategorie')), ['analytika', 'marketing'])) ?: 'nic';
            if ($this->app->settings()->bool('cookies_evidence') && preg_match('/^[a-f0-9]{32}$/', $request->post('id'))) {
                $this->app->db()->insert('souhlasy', ['id_souhlasu' => $request->post('id'), 'cas' => date('Y-m-d H:i:s'), 'kategorie' => $kategorie]);
            }

            return new Response('', 204);
        }
        if (preg_match('#^/autor/(\d+)$#', $path, $m)) {
            return $this->autor((int) $m[1]);
        }
        if ($path === '/llms.txt' && $this->app->settings()->bool('llms_txt')) {
            return new Response($seo->llmsTxt(), 200, ['Content-Type' => 'text/plain; charset=utf-8']);
        }
        if (preg_match('#^/clanek/([a-z0-9-]+)\.md$#', $path, $m) && $this->app->settings()->bool('markdown_clanky')) {
            $clanek = $this->clanky->podleSeo($m[1]);

            return $clanek === null || (int) $clanek['typ_clanku'] === 2
                ? $this->nenalezeno()
                : new Response($seo->clanekMarkdown($clanek), 200, ['Content-Type' => 'text/markdown; charset=utf-8', 'X-Robots-Tag' => 'noindex']);
        }
        if ($path === '/stav.json') {
            $token = $this->app->settings()->get('stav_token');
            if ($token === '' || !hash_equals($token, $request->get('token'))) {
                return Response::json(['chyba' => 'Neplatný token.'], 403);
            }
            $kontroly = \PhpRS\Core\Stav::kontroly($this->app);

            return Response::json(['stav' => \PhpRS\Core\Stav::souhrn($kontroly), 'verze' => PHPRS_VERSION, 'cas' => date('c'), 'kontroly' => $kontroly]);
        }

        $alias = $this->app->db()->one("SELECT * FROM {alias} WHERE alias = ? AND typ = 'clanek'", [ltrim($path, '/')]);
        if ($alias !== null) {
            $seo = $this->app->db()->value('SELECT seo_link FROM {clanky} WHERE idc = ?', [(int) $alias['hodnota']]);
            if ($seo !== null) {
                return $this->clanek((string) $seo);
            }
        }

        $stranka = $this->app->db()->one('SELECT * FROM {stranky} WHERE seo_link = ? AND zobrazit = 1', [ltrim($path, '/')]);
        if ($stranka !== null) {
            return $this->stranka($stranka['titulek'], $this->view->render('stranka', ['stranka' => $stranka]), ['popis' => $stranka['popis']]);
        }

        return $this->nenalezeno();
    }

    private function autor(int $idu): Response
    {
        $autor = $this->app->db()->one("SELECT idu, IF(jmeno = '', user, jmeno) AS jmeno, url FROM {user} WHERE idu = ? AND blokovat = 0", [$idu]);
        if ($autor === null) {
            return $this->nenalezeno();
        }
        $strana = max(1, $this->app->request->getInt('strana', 1));
        [$clanky, $celkem] = $this->clanky->odAutora($idu, $strana);
        $hlavicka = ['nazev' => $autor['jmeno'], 'popis' => $autor['url'] !== '' ? '<p><a href="' . e($autor['url']) . '" rel="me noopener">' . e($autor['url']) . '</a></p>' : ''];

        return $this->stranka($autor['jmeno'], $this->view->render('vypis', ['rubrika' => $hlavicka] + $this->proVypis($clanky, $celkem, $strana, 'autor/' . $idu)), ['popis' => 'Články autora ' . $autor['jmeno']]);
    }

    private function stitek(string $seo): Response
    {
        $stitek = $this->app->db()->one('SELECT * FROM {stitky} WHERE seo_link = ?', [$seo]);
        if ($stitek === null) {
            return $this->nenalezeno();
        }
        $strana = max(1, $this->app->request->getInt('strana', 1));
        [$clanky, $celkem] = $this->clanky->seStitkem((int) $stitek['ids'], $strana);
        $hlavicka = ['nazev' => '#' . $stitek['nazev'], 'popis' => ''];

        return $this->stranka('Štítek ' . $stitek['nazev'], $this->view->render('vypis', ['rubrika' => $hlavicka] + $this->proVypis($clanky, $celkem, $strana, 'stitek/' . $seo)));
    }

    private function stareAdresy(): ?Response
    {
        $request = $this->app->request;
        $db = $this->app->db();

        $cislo = $request->getInt('cisloclanku');
        if ($cislo > 0) {
            $seo = $db->value('SELECT seo_link FROM {clanky} WHERE link = ?', [$cislo]);

            return $seo !== null ? Response::redirect($this->app->url('clanek/' . $seo), 301) : $this->nenalezeno();
        }
        if ($request->script() === 'search.php') {
            $seo = $db->value('SELECT seo_link FROM {topic} WHERE idt = ?', [$request->getInt('rstema')]);
            if ($seo !== null) {
                return Response::redirect($this->app->url('rubrika/' . $seo), 301);
            }
            $text = $request->get('rstext');

            return Response::redirect($this->app->url('hledani' . ($text !== '' && $text !== 'all-phpRS-all' ? '?q=' . rawurlencode($text) : '')), 301);
        }

        return null;
    }

    private function hlavniStranka(): Response
    {
        $strana = max(1, $this->app->request->getInt('strana', 1));
        [$clanky, $celkem] = $this->clanky->naHlavniStranku($strana);

        return $this->stranka('', $this->view->render('vypis', $this->proVypis($clanky, $celkem, $strana, '')), [
            'hlavni' => true,
            'popis' => $this->app->settings()->get('popis_webu'),
        ]);
    }

    private function rubrika(string $seo): Response
    {
        $rubrika = $this->app->db()->one('SELECT * FROM {topic} WHERE seo_link = ?', [$seo]);
        if ($rubrika === null) {
            return $this->nenalezeno();
        }
        $strana = max(1, $this->app->request->getInt('strana', 1));
        [$clanky, $celkem] = $this->clanky->zRubriky((int) $rubrika['idt'], $strana);

        return $this->stranka(
            $rubrika['nazev'],
            $this->view->render('vypis', ['rubrika' => $rubrika] + $this->proVypis($clanky, $celkem, $strana, 'rubrika/' . $seo)),
            ['popis' => strip_tags($rubrika['popis'])],
        );
    }

    private function clanek(string $seo): Response
    {
        $nahled = $this->app->request->get('nahled') === '1' && $this->app->auth()->user() !== null;
        $clanek = $this->clanky->podleSeo($seo, $nahled);
        if ($clanek === null || ((int) $clanek['typ_clanku'] === 2 && !$nahled)) {
            return $this->nenalezeno();
        }
        if (!$nahled) {
            $this->app->db()->run('UPDATE {clanky} SET visit = visit + 1 WHERE idc = ?', [$clanek['idc']]);
        }

        $casti = new View([PHPRS_SYSTEM . '/views/front']);
        $clanek['shrnuti_html'] = $casti->render('shrnuti', ['body' => array_values(array_filter(array_map(trim(...), preg_split('/\R/', (string) $clanek['shrnuti']) ?: [])))]);
        $clanek['faq_html'] = $casti->render('faq', ['faq' => Seo::faq($clanek['faq'])]);
        $clanek['stitky'] = $this->app->db()->all('SELECT s.nazev, s.seo_link FROM {stitky} s JOIN {clanky_stitky} cs ON cs.ids = s.ids WHERE cs.idc = ? ORDER BY s.nazev', [$clanek['idc']]);

        $obsah = $this->view->render($this->sablonaClanku($clanek), [
            'clanek' => $clanek,
            'rezim' => 'cely',
            'poradi' => 0,
            'url' => $this->app->url(...),
            'souvisejici' => $this->clanky->zeSkupiny($clanek),
        ]);

        return $this->stranka($clanek['seo_titulek'] !== '' ? $clanek['seo_titulek'] : $clanek['titulek'], $obsah, [
            'clanek' => $clanek,
            'popis' => $clanek['seo_popis'] !== '' ? $clanek['seo_popis'] : mb_strimwidth(trim(strip_tags($clanek['uvod'])), 0, 300, '…'),
            'klicova_slova' => $clanek['t_slova'],
            'obrazek' => $clanek['obrazek'],
            'typ' => 'article',
        ]);
    }

    private function hledani(): Response
    {
        $q = mb_substr($this->app->request->get('q'), 0, 100);
        $strana = max(1, $this->app->request->getInt('strana', 1));
        [$clanky, $celkem] = mb_strlen($q) >= 3 ? $this->clanky->hledej($q, $strana) : [[], 0];

        return $this->stranka(
            'Vyhledávání',
            $this->view->render('vypis', ['hledano' => $q] + $this->proVypis($clanky, $celkem, $strana, 'hledani', ['q' => $q])),
            ['noindex' => true],
        );
    }

    private function rss(): Response
    {
        [$clanky] = $this->clanky->naHlavniStranku(1, 20);
        $xml = $this->view->render('rss', [
            'web' => $this->app->settings(),
            'clanky' => $clanky,
            'adresa' => $this->app->request->origin() . $this->app->url(''),
        ]);

        return new Response($xml, 200, ['Content-Type' => 'application/rss+xml; charset=utf-8']);
    }

    private function nenalezeno(): Response
    {
        // než web odpoví 404, zkusí přesměrování ze staré adresy (ruční i po změně adresy článku)
        $cil = $this->app->db()->one('SELECT * FROM {presmerovani} WHERE z_adresy = ?', [trim($this->app->request->path(), '/')]);
        if ($cil !== null) {
            $this->app->db()->run('UPDATE {presmerovani} SET pocet = pocet + 1 WHERE idp = ?', [$cil['idp']]);

            return Response::redirect(preg_match('#^https?://#i', $cil['na_adresu']) ? $cil['na_adresu'] : $this->app->url($cil['na_adresu']), 301);
        }

        return $this->stranka('Stránka nenalezena', $this->view->render('nenalezeno', ['url' => $this->app->url(...)]), ['noindex' => true], 404);
    }

    /**
     * @param list<array<string, mixed>> $clanky
     * @param array<string, string> $parametry další parametry stránkovacích odkazů
     * @return array<string, mixed>
     */
    private function proVypis(array $clanky, int $celkem, int $strana, string $cesta, array $parametry = []): array
    {
        // "poradi" (od nuly) dovoluje layoutu vysázet první článek výpisu jinak - jako otvírák
        $nahledy = array_map(fn (array $clanek, int $poradi): string => $this->view->render($this->sablonaClanku($clanek), [
            'clanek' => $clanek,
            'rezim' => (int) $clanek['typ_clanku'] === 2 ? 'kratky' : 'nahled',
            'poradi' => $strana === 1 ? $poradi : $poradi + 1000,
            'url' => $this->app->url(...),
            'souvisejici' => [],
        ]), $clanky, array_keys($clanky));

        return [
            'nahledy' => $nahledy,
            'celkem' => $celkem,
            'strana' => $strana,
            'stran' => max(1, (int) ceil($celkem / $this->clanky->naStranku())),
            'strankaUrl' => fn (int $s): string => $this->app->url($cesta) . (($query = http_build_query($parametry + ($s > 1 ? ['strana' => $s] : []))) !== '' ? '?' . $query : ''),
            'rubrika' => null,
            'hledano' => null,
            'hlavni' => $cesta === '',
            'url' => $this->app->url(...),
        ];
    }

    /** Šablona článku: cla_<soubor>.php z layoutu; když chybí, použije se standardní. */
    private function sablonaClanku(array $clanek): string
    {
        $soubor = 'cla_' . ($clanek['sablona_soubor'] ?? 'standard');

        return preg_match('/^cla_[a-z0-9_-]+$/', $soubor) && $this->view->exists($soubor) ? $soubor : 'cla_standard';
    }

    /**
     * Složí stránku: obsah vloží do Hlavního bloku, okolo vykreslí sloupce s bloky a vše obalí layoutem.
     *
     * @param array<string, mixed> $meta
     */
    private function stranka(string $titulek, string $obsah, array $meta = [], int $status = 200): Response
    {
        $web = $this->app->settings();
        $bloky = new Bloky($this->app, $this->view);
        $seo = new Seo($this->app);
        $clanek = $meta['clanek'] ?? null;
        unset($meta['clanek']);

        return Response::html($this->view->render('base', [
            'web' => $web,
            'titulek' => $titulek,
            'meta' => $meta + ['hlavni' => false, 'popis' => '', 'klicova_slova' => $web->get('klicova_slova'), 'obrazek' => '', 'typ' => 'website', 'noindex' => false],
            'obsah' => $obsah,
            'zony' => $bloky->zony(!empty($meta['hlavni'])),
            'rozvrzeni' => $bloky->rozvrzeni(),
            'hlava' => $seo->hlava($titulek, $meta, $clanek),
            'pata' => $seo->pata(),
            'rubriky' => Rubriky::strom($this->app->db(), true),
            'stranky' => $this->app->db()->all('SELECT titulek, seo_link FROM {stranky} WHERE zobrazit = 1 AND v_menu = 1 ORDER BY poradi, titulek'),
            'url' => $this->app->url(...),
            'kanonicka' => $this->app->request->origin() . $this->app->url(ltrim($this->app->request->path(), '/')),
        ]), $status);
    }
}
