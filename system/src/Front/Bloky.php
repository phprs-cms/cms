<?php

declare(strict_types=1);

namespace PhpRS\Front;

use PhpRS\Admin\Moduly\Rubriky;
use PhpRS\Core\App;
use PhpRS\Core\View;

/**
 * Sloupce a bloky webu. Každý blok se vykreslí šablonou "blok" z layoutu;
 * obsah systémových bloků dodávají šablony blok_<zkratka> (blok_rub, blok_nov...).
 */
final class Bloky
{
    public function __construct(private readonly App $app, private readonly View $view)
    {
    }

    /**
     * @param string $hlavniObsah HTML, které se vloží do Hlavního bloku
     * @return list<array{ids:int, html:string, hlavni:bool}> sloupce zleva doprava
     */
    public function sloupce(string $hlavniObsah, bool $hlavniStranka): array
    {
        $db = $this->app->db();
        $kde = $hlavniStranka ? 'b.zobrazit_kde IN (0, 1)' : 'b.zobrazit_kde IN (0, 2)';
        $bloky = $db->all(
            "SELECT b.* FROM {bloky} b JOIN {sloupce} s ON s.ids = b.id_sloupec
             WHERE b.zobrazit = 1 AND s.zobrazit = 1 AND {$kde}
             ORDER BY b.id_sloupec, b.hodnost DESC, b.idb",
        );

        $sloupce = [];
        foreach ($bloky as $blok) {
            $ids = (int) $blok['id_sloupec'];
            $sloupce[$ids] ??= ['ids' => $ids, 'html' => '', 'hlavni' => false];
            if ($blok['sys_funkce'] === 'hlb') {
                $sloupce[$ids]['html'] .= $hlavniObsah;
                $sloupce[$ids]['hlavni'] = true;
                continue;
            }
            $obsah = $blok['sys_funkce'] === '' ? $blok['obsah'] : $this->systemovy($blok['sys_funkce']);
            if (trim($obsah) !== '') {
                $sloupce[$ids]['html'] .= $this->view->render('blok', ['nadpis' => $blok['nazev'], 'obsah' => $obsah, 'typ' => (int) $blok['typ'], 'sys' => $blok['sys_funkce']]);
            }
        }
        if (!in_array(true, array_column($sloupce, 'hlavni'), true)) {
            // web bez Hlavního bloku by nezobrazil žádný obsah - raději ho přidat jako poslední sloupec
            $sloupce[] = ['ids' => 0, 'html' => $hlavniObsah, 'hlavni' => true];
        }

        return array_values($sloupce);
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
            default => '',
        };
    }
}
