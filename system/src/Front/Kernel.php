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
        if ($path === '/hledani') {
            return $this->hledani();
        }
        if ($path === '/rss.xml') {
            return $this->rss();
        }

        $alias = $this->app->db()->one("SELECT * FROM {alias} WHERE alias = ? AND typ = 'clanek'", [ltrim($path, '/')]);
        if ($alias !== null) {
            $seo = $this->app->db()->value('SELECT seo_link FROM {clanky} WHERE idc = ?', [(int) $alias['hodnota']]);
            if ($seo !== null) {
                return $this->clanek((string) $seo);
            }
        }

        return $this->nenalezeno();
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

        $obsah = $this->view->render($this->sablonaClanku($clanek), [
            'clanek' => $clanek,
            'rezim' => 'cely',
            'poradi' => 0,
            'url' => $this->app->url(...),
            'souvisejici' => $this->clanky->zeSkupiny($clanek),
        ]);

        return $this->stranka($clanek['titulek'], $obsah, [
            'popis' => mb_strimwidth(trim(strip_tags($clanek['uvod'])), 0, 300, '…'),
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

        return Response::html($this->view->render('base', [
            'web' => $web,
            'titulek' => $titulek,
            'meta' => $meta + ['hlavni' => false, 'popis' => '', 'klicova_slova' => $web->get('klicova_slova'), 'obrazek' => '', 'typ' => 'website', 'noindex' => false],
            'obsah' => $obsah,
            'zony' => $bloky->zony(!empty($meta['hlavni'])),
            'rozvrzeni' => $bloky->rozvrzeni(),
            'rubriky' => Rubriky::strom($this->app->db(), true),
            'url' => $this->app->url(...),
            'kanonicka' => $this->app->request->origin() . $this->app->url(ltrim($this->app->request->path(), '/')),
        ]), $status);
    }
}
