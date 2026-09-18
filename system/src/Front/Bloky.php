<?php

declare(strict_types=1);

namespace PhpRS\Front;

use PhpRS\Admin\Moduly\Bloky as Nastaveni;
use PhpRS\Admin\Moduly\Rubriky;
use PhpRS\Core\App;
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

    /** @return array<string, string> zóna => HTML bloků; vždy všechny klíče, prázdná zóna = '' */
    public function zony(bool $hlavniStranka): array
    {
        $existujici = Nastaveni::ROZVRZENI[$this->rozvrzeni()][2];
        $html = array_fill_keys(array_keys(Nastaveni::ZONY), '');
        $kde = $hlavniStranka ? 'zobrazit_kde IN (0, 1)' : 'zobrazit_kde IN (0, 2)';

        foreach ($this->app->db()->all("SELECT * FROM {bloky} WHERE zobrazit = 1 AND {$kde} ORDER BY hodnost DESC, idb") as $blok) {
            $obsah = $blok['sys_funkce'] === '' ? $blok['obsah'] : $this->systemovy($blok['sys_funkce']);
            if (trim($obsah) === '') {
                continue;
            }
            // blok ze zóny, kterou zvolené rozvržení nemá, se ukáže pod obsahem
            $zona = in_array($blok['zona'], $existujici, true) ? $blok['zona'] : 'pod';
            $html[$zona] .= $this->view->render('blok', ['nadpis' => $blok['nazev'], 'obsah' => $obsah, 'typ' => (int) $blok['typ'], 'sys' => $blok['sys_funkce'], 'zona' => $zona]);
        }

        return $html;
    }

    private function systemovy(string $zkratka): string
    {
        $url = $this->app->url(...);
        $db = $this->app->db();

        return match ($zkratka) {
            'rub' => $this->view->render('blok_rub', ['rubriky' => Rubriky::strom($db, true), 'url' => $url]),
            'nov' => $this->view->render('blok_nov', [
                'novinky' => $db->all('SELECT * FROM {news} WHERE datum <= NOW() ORDER BY datum DESC, idn DESC LIMIT ?', [$this->app->settings()->int('pocet_novinek')]),
            ]),
            'hle' => $this->view->render('blok_hle', ['url' => $url, 'q' => $this->app->request->get('q')]),
            'nej' => $this->view->render('blok_nej', ['clanky' => (new Clanky($db, $this->app->settings()))->nejctenejsi(5), 'url' => $url]),
            'ank' => (new Interakce($this->app, $this->view))->anketaHtml(),
            default => '',
        };
    }
}
