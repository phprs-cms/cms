<?php

declare(strict_types=1);

namespace PhpRS\Front;

use PhpRS\Admin\Moduly\Rubriky;
use PhpRS\Core\App;
use PhpRS\Core\Response;
use PhpRS\Core\Rozsireni;
use PhpRS\Core\View;

/**
 * Veřejná část webu.
 *
 *   /                    hlavní stránka
 *   /clanek/<seo-link>   celý článek
 *   /rubrika/<seo-link>  výpis rubriky
 *   /hledani?q=...       vyhledávání
 *   /rss.xml             RSS kanál
 *   /stitek/<seo-link>   články se štítkem
 *   /autor/<id>          články autora
 *   /<adresa>            statická stránka
 *   robots.txt, sitemap.xml, llms.txt, feed.json... viz Seo
 */
final class Kernel
{
    private readonly View $view;
    private readonly Clanky $clanky;
    private readonly ?Ctenari $ctenari;

    public function __construct(private readonly App $app)
    {
        $layout = $app->settings()->get('layout');
        // náhled jiné šablony (?sablona=slozka) - jen přihlášenému administrátorovi, např. při tvorbě šablony přes Claude
        $nahled = $app->request->get('sablona');
        if ($nahled !== '' && preg_match('/^[a-z0-9_-]+$/i', $nahled) && is_file(PHPRS_ROOT . '/layout/' . $nahled . '/base.php') && $app->auth()->isAdmin()) {
            $layout = $nahled;
        }
        if (!preg_match('/^[a-z0-9_-]+$/i', $layout) || !is_dir(PHPRS_ROOT . '/layout/' . $layout)) {
            $layout = 'default';
        }
        // šablona se hledá nejdřív v layoutu webu, potom mezi systémovými - layout tak může přepsat cokoli
        $this->view = new View([PHPRS_ROOT . '/layout/' . $layout, PHPRS_SYSTEM . '/views/front']);
        $this->ctenari = Rozsireni::je($app->settings(), 'ctenari') ? new Ctenari($app) : null;
        $this->clanky = new Clanky($app->db(), $app->settings(), $app->request->basePath(), $this->ctenari === null ? null : $this->ctenari->zamkni(...));
    }

    public function handle(): Response
    {
        $request = $this->app->request;
        if ($this->app->settings()->bool('udrzba') && $request->path() !== '/mcp' && $this->app->auth()->user() === null) {
            return new Response('<!doctype html><html lang="cs"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>' . e($this->app->settings()->get('nazev_webu')) . '</title>'
                . '<body style="font:18px/1.5 system-ui,sans-serif;display:grid;place-items:center;min-height:90vh;margin:0;padding:24px;text-align:center"><div><h1 style="font-size:28px">' . e($this->app->settings()->get('nazev_webu'))
                . '</h1><p>' . e($this->app->settings()->get('udrzba_text')) . '</p></div>', 503, ['Content-Type' => 'text/html; charset=utf-8', 'Retry-After' => '3600']);
        }
        if (($zCache = Cache::nacti($this->app)) !== null) {
            return $zCache;
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
        if (str_starts_with($path, '/newsletter') && Rozsireni::je($this->app->settings(), 'newsletter')) {
            $newsletter = new Newsletter($this->app, $this->view);
            if ($path === '/newsletter' && $request->isPost()) {
                return $newsletter->prihlas();
            }
            if (preg_match('#^/newsletter/(potvrdit|odhlasit)/([a-f0-9]{32})$#', $path, $m)) {
                [$nadpis, $text] = $m[1] === 'potvrdit' ? $newsletter->potvrd($m[2]) : $newsletter->odhlas($m[2]);

                return $this->stranka($nadpis, $this->view->render('zprava', ['nadpis' => $nadpis, 'text' => $text, 'url' => $this->app->url(...)]), ['noindex' => true]);
            }
        }
        if ($this->ctenari !== null && ($path === '/ctenar' || str_starts_with($path, '/ctenar/'))) {
            $vysledek = $this->ctenari->handle($path, $this->view);

            return $vysledek instanceof Response ? $vysledek : $this->stranka($vysledek[0], $vysledek[1], ['noindex' => true]);
        }
        if (str_starts_with($path, '/push') || $path === '/manifest.webmanifest') {
            $odpoved = $this->push($path);
            if ($odpoved !== null) {
                return $odpoved;
            }
        }
        if (str_starts_with($path, '/api/') && Rozsireni::je($this->app->settings(), 'api')) {
            return (new Api($this->app, $this->clanky))->handle($path);
        }
        if ($path === '/mcp') {
            return (new \PhpRS\Mcp\Server($this->app))->handle();
        }
        if (preg_match('#^/r/(\d+)$#', $path, $m)) {
            return (new Reklama($this->app))->proklik((int) $m[1]);
        }
        if ($path === '/ads.txt' && trim($this->app->settings()->get('ads_txt')) !== '') {
            return new Response($this->app->settings()->get('ads_txt') . "\n", 200, ['Content-Type' => 'text/plain; charset=utf-8']);
        }
        if ($request->isPost() && in_array($path, ['/komentar', '/hodnoceni', '/anketa'], true)) {
            $interakce = new Interakce($this->app, $this->view);
            Cache::vymaz();

            return match ($path) {
                '/komentar' => $interakce->ulozKomentar(),
                '/hodnoceni' => $interakce->ulozHodnoceni(),
                default => $interakce->ulozHlas(),
            };
        }
        if (preg_match('#^/archiv/(\d{4}-\d{2})$#', $path, $m)) {
            $strana = max(1, $request->getInt('strana', 1));
            [$clanky, $celkem] = $this->clanky->zMesice($m[1], $strana);
            $mesice = [1 => 'leden', 'únor', 'březen', 'duben', 'květen', 'červen', 'červenec', 'srpen', 'září', 'říjen', 'listopad', 'prosinec'];
            $nadpis = 'Archiv: ' . $mesice[(int) substr($m[1], 5)] . ' ' . substr($m[1], 0, 4);

            return $celkem === 0 ? $this->nenalezeno() : $this->stranka($nadpis, $this->view->render('vypis', ['rubrika' => ['nazev' => $nadpis, 'popis' => '']] + $this->proVypis($clanky, $celkem, $strana, 'archiv/' . $m[1])));
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

        $stranka = $this->app->db()->one('SELECT * FROM {stranky} WHERE seo_link = ? AND zobrazit = 1', [ltrim($path, '/')]);
        if ($stranka !== null) {
            return $this->stranka($stranka['titulek'], $this->view->render('stranka', ['stranka' => $stranka]), ['popis' => $stranka['popis']]);
        }

        return $this->nenalezeno();
    }

    /** Web Push: obsah posledního oznámení, přihlášení a zrušení odběru, manifest webové aplikace (kvůli iOS). */
    private function push(string $path): ?Response
    {
        $web = $this->app->settings();
        $push = new \PhpRS\Core\Push($this->app->db(), $web);
        if (!$push->zapnuto()) {
            return null;
        }
        $koren = $this->app->request->origin() . $this->app->url('');
        $ikona = $web->get('favicon') === '' ? '' : (preg_match('#^https?://#i', $web->get('favicon')) ? $web->get('favicon') : rtrim($koren, '/') . '/' . ltrim($web->get('favicon'), '/'));
        if ($path === '/push.json') {
            return Response::json($push->zprava() + ['ikona' => $ikona]);
        }
        if ($path === '/manifest.webmanifest') {
            return new Response((string) json_encode([
                'name' => $web->get('nazev_webu'), 'short_name' => mb_substr($web->get('nazev_webu'), 0, 12), 'start_url' => $this->app->url(''), 'scope' => $this->app->url(''),
                'display' => 'standalone', 'background_color' => '#ffffff', 'theme_color' => $web->get('brand_akcent') ?: '#ffffff',
                'icons' => $ikona === '' ? [] : [['src' => $ikona, 'sizes' => '256x256', 'purpose' => 'any']],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 200, ['Content-Type' => 'application/manifest+json; charset=utf-8']);
        }
        if ($this->app->request->isPost() && in_array($path, ['/push/odber', '/push/zrusit'], true)) {
            $data = json_decode((string) file_get_contents('php://input', false, null, 0, 4000), true);
            $endpoint = is_array($data) && is_string($data['endpoint'] ?? null) ? $data['endpoint'] : '';
            $antispam = new \PhpRS\Core\Antispam($this->app->db(), $web);
            if ($path === '/push/zrusit') {
                $push->odhlas($endpoint);

                return Response::json(['ok' => true]);
            }
            if ($antispam->pocet($this->app->request->ip(), 'push', 0, 60) >= 10 || !$push->prihlas($endpoint, (string) ($data['keys']['p256dh'] ?? ''), (string) ($data['keys']['auth'] ?? ''))) {
                return Response::json(['ok' => false], 400);
            }
            $antispam->zapis($this->app->request->ip(), 'push', 0);

            return Response::json(['ok' => true]);
        }

        return null;
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
            ['popis' => strip_tags($rubrika['popis']), 'rubrika' => (int) $rubrika['idt']],
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
        if (!empty($clanek['zamceno'])) {
            $clanek['text'] .= $this->ctenari->zamekHtml($clanek, $this->view);
        }
        $interakce = new Interakce($this->app, new View([PHPRS_SYSTEM . '/views/front']));
        $clanek['reklama_html'] = (new Reklama($this->app))->html('pod-clankem');
        $clanek['hodnoceni_html'] = $nahled ? '' : $interakce->hodnoceniHtml($clanek);
        $clanek['komentare_html'] = $nahled ? '' : $interakce->komentareHtml($clanek);
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
        $cil = Rozsireni::je($this->app->settings(), 'presmerovani')
            ? $this->app->db()->one('SELECT * FROM {presmerovani} WHERE z_adresy = ?', [trim($this->app->request->path(), '/')])
            : null;
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
        $meta['rubrika'] ??= null;
        // vizuální editor bloků: ?upravit=1 pro přihlášeného uživatele s přístupem k blokům
        $upravit = $this->app->request->get('upravit') === '1' && $this->app->auth()->maModul('bloky');
        unset($meta['clanek']);
        if ($status === 200 && empty($meta['noindex']) && !$upravit) {
            Statistika::zaznamenej($this->app, $clanek === null ? null : (int) $clanek['idc']);
        }

        $html = $this->view->render('base', [
            'web' => $web,
            'titulek' => $titulek,
            'meta' => $meta + ['hlavni' => false, 'popis' => '', 'klicova_slova' => $web->get('klicova_slova'), 'obrazek' => '', 'typ' => 'website', 'noindex' => false],
            'obsah' => $obsah,
            'zony' => $bloky->zony(!empty($meta['hlavni']), $clanek !== null ? (int) $clanek['tema'] : ($meta['rubrika'] ?? null), $upravit),
            'rozvrzeni' => $bloky->rozvrzeni(),
            'hlava' => $seo->hlava($titulek, $meta, $clanek),
            'pata' => $upravit ? $this->view->render('vizual', ['app' => $this->app, 'rozvrzeni' => $bloky->rozvrzeni()]) : $seo->pata(),
            'rubriky' => Rubriky::strom($this->app->db(), true),
            'stranky' => $this->app->db()->all('SELECT titulek, seo_link FROM {stranky} WHERE zobrazit = 1 AND v_menu = 1 ORDER BY poradi, titulek'),
            'url' => $this->app->url(...),
            'kanonicka' => $this->app->request->origin() . $this->app->url(ltrim($this->app->request->path(), '/')),
        ]);
        if ($status === 200 && empty($meta['noindex']) && $this->app->request->get('nahled') === '') {
            Cache::uloz($this->app, $html, $clanek === null ? null : (int) $clanek['idc']);
        }

        return Response::html($html, $status);
    }
}
