<?php

declare(strict_types=1);

namespace PhpRS\Front;

use PhpRS\Core\App;

/**
 * SEO, GEO, měření a souhlasy: robots.txt, sitemap.xml, llms.txt, Markdown verze článku,
 * značky do <head> (ověření, strukturovaná data, měřicí kódy) a cookie lišta před </body>.
 * Vše se řídí Nastavením; layouty jen vypíší proměnné $hlava a $pata.
 */
final class Seo
{
    /** Roboti AI služeb, kterých se týká přepínač v Nastavení. */
    private const array AI_ROBOTI = ['GPTBot', 'OAI-SearchBot', 'ChatGPT-User', 'ClaudeBot', 'Claude-User', 'anthropic-ai', 'PerplexityBot', 'Perplexity-User', 'Google-Extended', 'Applebot-Extended', 'CCBot', 'Bytespider', 'Amazonbot', 'meta-externalagent', 'cohere-ai'];

    private readonly string $web;

    public function __construct(private readonly App $app)
    {
        $this->web = $app->request->origin() . $app->url('');
    }

    public function robotsTxt(): string
    {
        $s = $this->app->settings();
        if (!$s->bool('indexovani')) {
            return "# Indexování webu je vypnuté v Nastavení.\nUser-agent: *\nDisallow: /\n";
        }
        $radky = ['User-agent: *', 'Disallow: /admin.php', 'Disallow: /hledani', 'Disallow: /*?nahled=', ''];
        if ($s->get('ai_crawlery') === 'zakazat') {
            foreach (self::AI_ROBOTI as $robot) {
                $radky[] = 'User-agent: ' . $robot;
            }
            array_push($radky, 'Disallow: /', '');
        }
        if (trim($s->get('robots_extra')) !== '') {
            array_push($radky, trim($s->get('robots_extra')), '');
        }
        $radky[] = 'Sitemap: ' . $this->web . 'sitemap.xml';

        return implode("\n", $radky) . "\n";
    }

    public function sitemapXml(): string
    {
        $db = $this->app->db();
        $url = fn (string $cesta, ?string $zmena = null, string $priorita = '0.5'): string => '<url><loc>' . e($this->web . $cesta) . '</loc>'
            . ($zmena !== null ? '<lastmod>' . date('c', strtotime($zmena)) . '</lastmod>' : '') . '<priority>' . $priorita . '</priority></url>';

        $xml = [$url('', (string) $db->value('SELECT MAX(COALESCE(zmeneno, datum)) FROM {clanky} WHERE visible = 1 AND datum <= NOW()') ?: null, '1.0')];
        foreach ($db->all('SELECT seo_link FROM {topic} WHERE zobrazit = 1') as $r) {
            $xml[] = $url('rubrika/' . $r['seo_link'], null, '0.6');
        }
        foreach ($db->all('SELECT seo_link, zmeneno FROM {stranky} WHERE zobrazit = 1') as $r) {
            $xml[] = $url($r['seo_link'], $r['zmeneno'], '0.4');
        }
        foreach ($db->all('SELECT seo_link, COALESCE(zmeneno, datum) AS zmena FROM {clanky} WHERE visible = 1 AND datum <= NOW() AND typ_clanku = 1 ORDER BY datum DESC LIMIT 45000') as $r) {
            $xml[] = $url('clanek/' . $r['seo_link'], $r['zmena'], '0.8');
        }

        return '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n" . implode("\n", $xml) . "\n</urlset>\n";
    }

    /** llms.txt - průvodce webem pro jazykové modely (https://llmstxt.org). */
    public function llmsTxt(): string
    {
        $s = $this->app->settings();
        $db = $this->app->db();
        $md = $s->bool('markdown_clanky') ? '.md' : '';
        $radky = ['# ' . $s->get('nazev_webu'), ''];
        if ($s->get('popis_webu') !== '') {
            array_push($radky, '> ' . str_replace("\n", ' ', $s->get('popis_webu')), '');
        }
        $radky[] = '## Rubriky';
        foreach ($db->all('SELECT nazev, seo_link, popis FROM {topic} WHERE zobrazit = 1 ORDER BY hodnost DESC, nazev') as $r) {
            $popis = trim(strip_tags($r['popis']));
            $radky[] = '- [' . $r['nazev'] . '](' . $this->web . 'rubrika/' . $r['seo_link'] . ')' . ($popis !== '' ? ': ' . $popis : '');
        }
        array_push($radky, '', '## Nejnovější články');
        foreach ($db->all('SELECT titulek, seo_link, uvod FROM {clanky} WHERE visible = 1 AND datum <= NOW() AND typ_clanku = 1 ORDER BY datum DESC LIMIT 30') as $c) {
            $radky[] = '- [' . $c['titulek'] . '](' . $this->web . 'clanek/' . $c['seo_link'] . $md . '): ' . mb_strimwidth(trim(strip_tags($c['uvod'])), 0, 200, '…');
        }

        return implode("\n", $radky) . "\n";
    }

    /** @param array<string, mixed> $clanek */
    public function clanekMarkdown(array $clanek): string
    {
        $hlava = ['# ' . $clanek['titulek'], ''];
        $hlava[] = '- Autor: ' . ($clanek['autor_jm'] ?? $this->app->settings()->get('nazev_webu'));
        $hlava[] = '- Vydáno: ' . date('Y-m-d', strtotime($clanek['datum'])) . ($clanek['zmeneno'] ? ', aktualizováno: ' . date('Y-m-d', strtotime($clanek['zmeneno'])) : '');
        $hlava[] = '- Rubrika: ' . $clanek['tema_jm'];
        $hlava[] = '- Zdroj: ' . $this->web . 'clanek/' . $clanek['seo_link'];

        return implode("\n", $hlava) . "\n\n" . self::htmlNaMarkdown($clanek['uvod']) . "\n\n" . self::htmlNaMarkdown($clanek['text']) . "\n";
    }

    /**
     * Značky před </head>.
     *
     * @param array<string, mixed> $meta     meta údaje stránky (typ, popis, obrazek, noindex...)
     * @param array<string, mixed>|null $clanek celý článek, jde-li o stránku článku
     */
    public function hlava(string $titulek, array $meta, ?array $clanek): string
    {
        $s = $this->app->settings();
        $h = [];
        if ($s->get('cookies_rezim') === 'externi' && trim($s->get('cookies_externi_kod')) !== '') {
            $h[] = $s->get('cookies_externi_kod');
        }
        if (!$s->bool('indexovani')) {
            $h[] = '<meta name="robots" content="noindex, nofollow">';
        }
        if ($s->get('overeni_google') !== '') {
            $h[] = '<meta name="google-site-verification" content="' . e($s->get('overeni_google')) . '">';
        }
        if ($s->get('overeni_bing') !== '') {
            $h[] = '<meta name="msvalidate.01" content="' . e($s->get('overeni_bing')) . '">';
        }
        if (($meta['obrazek'] ?? '') === '' && $s->get('og_obrazek') !== '') {
            $h[] = '<meta property="og:image" content="' . e($this->absolutni($s->get('og_obrazek'))) . '">';
        }
        if (($meta['popis'] ?? '') !== '') {
            $h[] = '<meta property="og:description" content="' . e($meta['popis']) . '">';
        }
        $h[] = '<meta name="twitter:card" content="' . (($meta['obrazek'] ?? '') !== '' || $s->get('og_obrazek') !== '' ? 'summary_large_image' : 'summary') . '">';
        if ($clanek !== null && $s->bool('markdown_clanky')) {
            $h[] = '<link rel="alternate" type="text/markdown" href="' . e($this->web . 'clanek/' . $clanek['seo_link'] . '.md') . '">';
        }
        if ($s->bool('schema_org')) {
            $h[] = '<script type="application/ld+json">' . json_encode($this->strukturovanaData($titulek, $meta, $clanek), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) . '</script>';
        }
        $h[] = $this->mereni();
        if (trim($s->get('kod_hlava')) !== '') {
            $h[] = $s->get('kod_hlava');
        }

        return implode("\n", array_filter($h)) . "\n";
    }

    /** Cookie lišta vestavěného řešení a marketingové kódy; vkládá se před </body>. */
    public function pata(): string
    {
        $s = $this->app->settings();
        $rezim = $s->get('cookies_rezim');
        $marketing = trim($s->get('kod_marketing'));
        $html = '';
        if ($marketing !== '') {
            // kód čeká na souhlas: prohlížeč značku <template> nevykonává, lišta ji po souhlasu rozbalí
            $html .= $rezim === 'zadna' ? $marketing : '<template data-souhlas="marketing">' . $marketing . '</template>';
        }
        if ($rezim !== 'vestavena' || (!$this->meriSCookies() && $marketing === '')) {
            return $html;
        }
        $view = new \PhpRS\Core\View([PHPRS_SYSTEM . '/views/front']);

        return $html . $view->render('cookies', [
            'text' => $s->get('cookies_text'),
            'zasady' => $s->get('cookies_zasady_url'),
            'analytika' => $this->meriSCookies(),
            'marketing' => $marketing !== '',
        ]);
    }

    private function meriSCookies(): bool
    {
        $s = $this->app->settings();

        return $s->get('ga4_id') !== '' || ($s->get('matomo_url') !== '' && $s->int('matomo_id') > 0);
    }

    private function mereni(): string
    {
        $s = $this->app->settings();
        // se souhlasem: skript je "text/plain", dokud ho lišta (vestavěná i Cookiebot) nepovolí
        $ceka = $s->get('cookies_rezim') !== 'zadna';
        $atr = $ceka ? ' type="text/plain" data-souhlas="analytika" data-cookieconsent="statistics"' : '';
        $kod = '';
        if ($s->get('ga4_id') !== '') {
            $id = $s->get('ga4_id');
            $kod .= "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}"
                . ($ceka ? "gtag('consent','default',{ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied',analytics_storage:'denied',wait_for_update:500});" : '')
                . "gtag('js',new Date());gtag('config','{$id}');</script>\n"
                . "<script async{$atr} src=\"https://www.googletagmanager.com/gtag/js?id={$id}\"></script>\n";
        }
        if ($s->get('matomo_url') !== '' && $s->int('matomo_id') > 0) {
            $adresa = json_encode(rtrim($s->get('matomo_url'), '/') . '/', JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
            $kod .= "<script{$atr}>var _paq=window._paq=window._paq||[];_paq.push(['trackPageView']);_paq.push(['enableLinkTracking']);(function(){var u={$adresa};_paq.push(['setTrackerUrl',u+'matomo.php']);_paq.push(['setSiteId','{$s->int('matomo_id')}']);var d=document,g=d.createElement('script'),s=d.getElementsByTagName('script')[0];g.async=true;g.src=u+'matomo.js';s.parentNode.insertBefore(g,s);})();</script>\n";
        }
        if ($s->get('plausible_domena') !== '') {
            $kod .= '<script defer data-domain="' . e($s->get('plausible_domena')) . '" src="https://plausible.io/js/script.js"></script>' . "\n";
        }

        return $kod;
    }

    /**
     * @param array<string, mixed> $meta
     * @param array<string, mixed>|null $clanek
     * @return array<string, mixed>
     */
    private function strukturovanaData(string $titulek, array $meta, ?array $clanek): array
    {
        $s = $this->app->settings();
        $vydavatel = array_filter([
            '@type' => 'Organization',
            'name' => $s->get('nazev_webu'),
            'url' => $this->web,
            'logo' => $s->get('logo_webu') !== '' ? $this->absolutni($s->get('logo_webu')) : null,
            'sameAs' => array_values(array_filter(array_map($s->get(...), ['soc_facebook', 'soc_instagram', 'soc_x', 'soc_youtube', 'soc_linkedin']))) ?: null,
        ]);
        if ($clanek === null) {
            return ['@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => $s->get('nazev_webu'), 'url' => $this->web,
                'description' => $s->get('popis_webu'), 'inLanguage' => 'cs', 'publisher' => $vydavatel,
                'potentialAction' => ['@type' => 'SearchAction', 'target' => $this->web . 'hledani?q={q}', 'query-input' => 'required name=q']];
        }

        return ['@context' => 'https://schema.org', '@graph' => [
            array_filter([
                '@type' => 'NewsArticle',
                'headline' => mb_substr($clanek['titulek'], 0, 110),
                'description' => $meta['popis'] ?? '',
                'image' => $clanek['obrazek'] !== '' ? [$this->absolutni($clanek['obrazek'])] : null,
                'datePublished' => date('c', strtotime($clanek['datum'])),
                'dateModified' => date('c', strtotime($clanek['zmeneno'] ?? $clanek['datum'])),
                'author' => $clanek['autor_jm'] !== null ? ['@type' => 'Person', 'name' => $clanek['autor_jm']] : $vydavatel,
                'publisher' => $vydavatel,
                'articleSection' => $clanek['tema_jm'],
                'keywords' => implode(', ', array_column($clanek['stitky'] ?? [], 'nazev')) ?: null,
                'mainEntityOfPage' => $this->web . 'clanek/' . $clanek['seo_link'],
                'inLanguage' => 'cs',
            ]),
            ['@type' => 'BreadcrumbList', 'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => $s->get('nazev_webu'), 'item' => $this->web],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $clanek['tema_jm'], 'item' => $this->web . 'rubrika/' . $clanek['tema_seo']],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $clanek['titulek']],
            ]],
        ]];
    }

    private function absolutni(string $adresa): string
    {
        if (preg_match('#^https?://#i', $adresa)) {
            return $adresa;
        }

        return str_starts_with($adresa, '/') ? $this->app->request->origin() . $adresa : $this->web . $adresa;
    }

    /** Jednoduchý převod HTML článku na Markdown - nadpisy, odstavce, seznamy, odkazy, citace, obrázky. */
    private static function htmlNaMarkdown(string $html): string
    {
        $md = preg_replace('/\s+/', ' ', $html) ?? $html;
        $nahrady = [
            '#<h2[^>]*>(.*?)</h2>#i' => "\n\n## $1\n\n", '#<h3[^>]*>(.*?)</h3>#i' => "\n\n### $1\n\n", '#<h4[^>]*>(.*?)</h4>#i' => "\n\n#### $1\n\n",
            '#<(strong|b)>(.*?)</\1>#i' => '**$2**', '#<(em|i)>(.*?)</\1>#i' => '*$2*',
            '#<a [^>]*href="([^"]*)"[^>]*>(.*?)</a>#i' => '[$2]($1)',
            '#<img [^>]*src="([^"]*)"[^>]*alt="([^"]*)"[^>]*>#i' => '![$2]($1)', '#<img [^>]*src="([^"]*)"[^>]*>#i' => '![]($1)',
            '#<figcaption[^>]*>(.*?)</figcaption>#i' => "\n*$1*\n",
            '#<li[^>]*>(.*?)</li>#i' => "\n- $1", '#</(ul|ol)>#i' => "\n\n",
            '#<blockquote[^>]*>(.*?)</blockquote>#i' => "\n\n> $1\n\n",
            '#<br\s*/?>#i' => "\n", '#</p>#i' => "\n\n", '#<hr[^>]*>#i' => "\n\n---\n\n",
        ];
        $md = preg_replace(array_keys($nahrady), array_values($nahrady), $md) ?? $md;
        $md = html_entity_decode(strip_tags($md), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $md = preg_replace(['/[ \t]+\n/', '/\n{3,}/', '/^[ \t]+/m'], ["\n", "\n\n", ''], $md) ?? $md;

        return trim($md);
    }
}
