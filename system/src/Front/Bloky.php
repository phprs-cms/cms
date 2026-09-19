<?php

declare(strict_types=1);

namespace PhpRS\Front;

use PhpRS\Admin\Moduly\Bloky as Nastaveni;
use PhpRS\Admin\Moduly\Rubriky;
use PhpRS\Core\App;
use PhpRS\Core\Rozsireni;
use PhpRS\Core\View;

/**
 * Zóny stránky a jejich bloky. Každý blok se vykreslí šablonou "blok" z layoutu;
 * obsah systémových bloků dodávají šablony blok_<zkratka> (blok_rub, blok_nov...).
 */
final class Bloky
{
    public function __construct(private readonly App $app, private readonly View $view)
    {
    }

    public function rozvrzeni(): string
    {
        $rozvrzeni = $this->app->settings()->get('rozvrzeni');

        return isset(Nastaveni::ROZVRZENI[$rozvrzeni]) ? $rozvrzeni : 'tri';
    }

    /**
     * @param int|null $rubrika rubrika právě zobrazené stránky (výpis rubriky nebo článek) - kvůli blokům "jen v rubrice"
     * @param bool $upravit režim vizuálního editoru: bloky a zóny dostanou značky, ukážou se i skryté bloky a prázdné zóny
     * @return array<string, string> zóna => HTML bloků; vždy všechny klíče, prázdná zóna = ''
     */
    public function zony(bool $hlavniStranka, ?int $rubrika = null, bool $upravit = false): array
    {
        $existujici = Nastaveni::ROZVRZENI[$this->rozvrzeni()][2];
        $html = array_fill_keys(array_keys(Nastaveni::ZONY), '');

        foreach ($this->app->db()->all('SELECT * FROM {bloky} ORDER BY hodnost DESC, idb') as $blok) {
            // proč se blok na této stránce čtenáři neukáže (prázdné = ukáže se)
            $duvod = match (true) {
                !$blok['zobrazit'] => 'blok je skrytý',
                (int) $blok['zobrazit_kde'] === 1 && !$hlavniStranka => 'jen na hlavní stránce',
                (int) $blok['zobrazit_kde'] === 2 && $hlavniStranka => 'všude kromě hlavní stránky',
                $blok['jen_rubrika'] !== null && (int) $blok['jen_rubrika'] !== $rubrika => 'jen ve vybrané rubrice',
                default => '',
            };
            if ($duvod !== '' && !$upravit) {
                continue;
            }
            $obsah = $duvod !== '' ? '' : ($blok['sys_funkce'] === '' ? $blok['obsah'] : $this->systemovy($blok['sys_funkce'], (string) $blok['data_sys'], (string) $blok['obsah']));
            if (trim($obsah) === '' && !$upravit) {
                continue;
            }
            // blok ze zóny, kterou zvolené rozvržení nemá, se ukáže pod obsahem
            $zona = in_array($blok['zona'], $existujici, true) ? $blok['zona'] : 'pod';
            $blokHtml = trim($obsah) === ''
                ? '<div class="rs-duch"><strong>' . e($blok['nazev']) . '</strong><br>' . e($duvod !== '' ? 'Teď se nezobrazuje: ' . $duvod . '.' : 'Zatím nemá co zobrazit.') . '</div>'
                : $this->view->render('blok', ['nadpis' => $blok['nazev'], 'obsah' => $obsah, 'typ' => (int) $blok['typ'], 'sys' => $blok['sys_funkce'], 'zona' => $zona]);
            if ($upravit) {
                $blokHtml = '<div class="rs-blok" data-blok="' . (int) $blok['idb'] . '" data-nazev="' . e($blok['nazev']) . '" draggable="true">' . $blokHtml . '</div>';
            }
            // "jen na mobilu / jen na počítači" řeší obal s třídou; styl je v hlavičce stránky (Front\Seo), layout ho nemusí znát
            $html[$zona] .= $blok['zarizeni'] === 'vse' || $upravit ? $blokHtml : '<div class="jen-' . e($blok['zarizeni']) . '">' . $blokHtml . '</div>';
        }
        if ($upravit) {
            foreach ($existujici as $zona) {
                // display: contents - obal nesmí rozbít mřížku, do které layout bloky skládá
                $html[$zona] = '<div class="rs-zona" data-zona="' . e($zona) . '" data-nazev="' . e(Nastaveni::ZONY[$zona]) . '">' . $html[$zona]
                    . '<button type="button" class="rs-pridat" data-zona="' . e($zona) . '">+ Přidat blok <small>' . e(Nastaveni::ZONY[$zona]) . '</small></button></div>';
            }
        }

        return $html;
    }

    private function systemovy(string $zkratka, string $data, string $obsah): string
    {
        $rozsireni = ['nov' => 'novinky', 'ank' => 'ankety', 'rek' => 'reklama', 'nws' => 'newsletter', 'cte' => 'ctenari'][$zkratka] ?? '';
        if (!Rozsireni::je($this->app->settings(), $rozsireni)) {
            return '';
        }
        $url = $this->app->url(...);
        $db = $this->app->db();
        $web = $this->app->settings();
        $clanky = new Clanky($db, $web, $this->app->request->basePath());
        $pocet = max(1, min(50, (int) $data ?: 5));

        return match ($zkratka) {
            'rub' => $this->view->render('blok_rub', ['rubriky' => Rubriky::strom($db, true), 'url' => $url]),
            'nov' => $this->view->render('blok_nov', [
                'novinky' => $db->all('SELECT * FROM {news} WHERE datum <= NOW() ORDER BY datum DESC, idn DESC LIMIT ?', [$web->int('pocet_novinek')]),
            ]),
            'hle' => $this->view->render('blok_hle', ['url' => $url, 'q' => $this->app->request->get('q')]),
            'nej' => $this->view->render('blok_nej', ['clanky' => $clanky->nejctenejsi($pocet), 'url' => $url]),
            'ank' => (new Interakce($this->app, $this->view))->anketaHtml(),
            'rek' => (new Reklama($this->app))->html($data !== '' ? $data : 'sloupec'),
            'cla' => (function () use ($clanky, $data, $url): string {
                [$idt, $kolik] = array_map(intval(...), explode(':', $data . ':5'));
                $seznam = $idt > 0 ? $clanky->zRubriky($idt, 1, max(1, min(20, $kolik)))[0] : $clanky->naHlavniStranku(1, max(1, min(20, $kolik)))[0];

                return $seznam === [] ? '' : $this->view->render('blok_cla', ['clanky' => $seznam, 'url' => $url]);
            })(),
            'otv' => ($otvirak = $clanky->naHlavniStranku(1, 1)[0][0] ?? null) === null ? '' : $this->view->render('blok_otv', ['clanek' => $otvirak, 'url' => $url]),
            'sti' => $this->view->render('blok_sti', ['url' => $url, 'stitky' => $db->all(
                'SELECT s.nazev, s.seo_link, COUNT(*) AS pocet FROM {stitky} s JOIN {clanky_stitky} cs ON cs.ids = s.ids JOIN {clanky} c ON c.idc = cs.idc
                 WHERE c.visible = 1 AND c.datum <= NOW() GROUP BY s.ids, s.nazev, s.seo_link ORDER BY pocet DESC, s.nazev LIMIT ?',
                [$pocet],
            )]),
            'arc' => $this->view->render('blok_arc', ['url' => $url, 'mesice' => $db->all(
                "SELECT DATE_FORMAT(datum, '%Y-%m') AS mesic, COUNT(*) AS pocet FROM {clanky} WHERE visible = 1 AND datum <= NOW() GROUP BY mesic ORDER BY mesic DESC LIMIT ?",
                [$pocet],
            )]),
            'aut' => $this->view->render('blok_aut', ['url' => $url, 'autori' => $db->all(
                "SELECT u.idu, IF(u.jmeno = '', u.user, u.jmeno) AS jmeno, COUNT(*) AS pocet FROM {user} u JOIN {clanky} c ON c.autor = u.idu
                 WHERE c.visible = 1 AND c.datum <= NOW() GROUP BY u.idu, jmeno ORDER BY pocet DESC LIMIT ?",
                [$pocet],
            )]),
            'men' => $this->view->render('blok_men', ['url' => $url, 'odkazy' => self::odkazy($obsah)]),
            'str' => $this->view->render('blok_men', ['url' => $url, 'odkazy' => array_map(
                fn (array $st): array => [$st['titulek'], $st['seo_link']],
                $db->all('SELECT titulek, seo_link FROM {stranky} WHERE zobrazit = 1 AND v_menu = 1 ORDER BY poradi, titulek'),
            )]),
            'soc' => $this->view->render('blok_men', ['url' => $url, 'odkazy' => array_values(array_filter(array_map(
                fn (string $klic, string $nazev): ?array => $web->get($klic) !== '' ? [$nazev, $web->get($klic)] : null,
                array_keys(\PhpRS\Admin\Moduly\Konfigurace::SITE),
                \PhpRS\Admin\Moduly\Konfigurace::SITE,
            )))]),
            'kon' => $web->get('email_webu') === '' && $web->get('text_paticky') === '' ? '' : '<p class="blok-kontakt">' . nl2br(e($web->get('text_paticky')))
                . ($web->get('email_webu') !== '' ? '<br><a href="mailto:' . e($web->get('email_webu')) . '">' . e($web->get('email_webu')) . '</a>' : '') . '</p>',
            'nws' => (new Newsletter($this->app, $this->view))->formularHtml(),
            // stránka může být z cache, proto blok nerozlišuje přihlášeného - /ctenar ukáže přihlášení, nebo účet
            'cte' => '<p class="blok-ctenar"><a class="rs-tl" href="' . e($url('ctenar')) . '">Přihlášení / Můj účet</a></p>',
            default => '',
        };
    }

    /**
     * Řádky "text | adresa" z bloku Menu.
     *
     * @return list<array{0:string, 1:string}>
     */
    private static function odkazy(string $text): array
    {
        $odkazy = [];
        foreach (preg_split('/\R/', $text) ?: [] as $radek) {
            [$popisek, $adresa] = array_map(trim(...), explode('|', $radek, 2) + [1 => '']);
            if ($popisek !== '' && $adresa !== '' && !preg_match('#^\s*(javascript|data|vbscript):#i', $adresa)) {
                $odkazy[] = [$popisek, $adresa];
            }
        }

        return $odkazy;
    }
}
