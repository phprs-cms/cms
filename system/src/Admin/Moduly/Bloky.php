<?php

declare(strict_types=1);

namespace PhpRS\Admin\Moduly;

use PhpRS\Admin\Modul;
use PhpRS\Core\Response;

/**
 * Úprava bloků. Stránka webu má pevné místo pro obsah a kolem něj zóny, do kterých se skládají bloky.
 * Které zóny existují, určuje zvolené rozvržení stránky; pořadí bloků se mění přetažením myší.
 * Běžný blok nese vlastní HTML, systémový vykresluje systém (rubriky, novinky, vyhledávání...).
 */
final class Bloky extends Modul
{
    public const string IDENT = 'bloky';
    public const string NAZEV = 'Bloky a rozvržení';
    public const string NAZEV_RETRO = 'Úprava bloků';
    public const string SKUPINA = 'Vzhled';
    public const string IKONA = 'bloky';

    public const array ZONY = [
        'hlavicka' => 'Hlavička', 'leva' => 'Levý sloupec', 'nad' => 'Nad obsahem',
        'pod' => 'Pod obsahem', 'prava' => 'Pravý sloupec', 'paticka' => 'Patička',
    ];

    /** Rozvržení stránky: název, popis a zóny, které v něm existují. */
    public const array ROZVRZENI = [
        'tri' => ['3 sloupce', 'Klasické phpRS: bloky vlevo i vpravo, obsah uprostřed.', ['hlavicka', 'leva', 'nad', 'pod', 'prava', 'paticka']],
        'dva' => ['2 sloupce', 'Obsah a vpravo úzký sloupec s bloky.', ['hlavicka', 'nad', 'pod', 'prava', 'paticka']],
        'jeden' => ['1 sloupec', 'Úzký sloupec pro pohodlné čtení, bloky pod obsahem.', ['hlavicka', 'nad', 'pod', 'paticka']],
        'plna' => ['Plná šířka', 'Obsah přes celou šířku stránky, bloky pod obsahem.', ['hlavicka', 'nad', 'pod', 'paticka']],
    ];

    /** Kam se přesunou bloky ze zóny, která v novém rozvržení není. */
    private const array NAHRADNI_ZONA = ['dva' => ['leva' => 'prava'], 'jeden' => ['leva' => 'pod', 'prava' => 'pod'], 'plna' => ['leva' => 'pod', 'prava' => 'pod']];

    /** Systémové bloky: zkratky shodné s phpRS 2. */
    public const array SYSTEMOVE = [
        'rub' => 'Seznam rubrik',
        'nov' => 'Novinky',
        'hle' => 'Vyhledávání',
        'nej' => 'Nejčtenější články',
        'ank' => 'Anketa',
        'rek' => 'Reklama',
    ];

    /** Vzhled bloku; v databázi číslo 1-5 jako v phpRS 2 (rs_bloky.typ). */
    public const array VZHLEDY = [1 => 'Běžný', 2 => 'Podbarvený', 3 => 'Zvýrazněný nadpis', 4 => 'V rámečku', 5 => 'Bez nadpisu'];

    public const array KDE = [0 => 'na všech stránkách', 1 => 'jen na hlavní stránce', 2 => 'všude kromě hlavní stránky'];

    protected function akceVypis(): Response
    {
        $rozvrzeni = $this->rozvrzeni();
        $bloky = array_fill_keys(self::ROZVRZENI[$rozvrzeni][2], []);
        foreach ($this->db->all('SELECT * FROM {bloky} ORDER BY hodnost DESC, idb') as $blok) {
            $zona = isset($bloky[$blok['zona']]) ? $blok['zona'] : 'pod';
            $bloky[$zona][] = $blok;
        }

        return $this->view('vypis', 'Bloky a rozvržení', ['rozvrzeni' => $rozvrzeni, 'bloky' => $bloky]);
    }

    /** Změna rozvržení stránky; bloky ze zrušených zón se přesunou do nejbližší existující. */
    protected function akceRozvrzeni(): Response
    {
        $nove = $this->request->post('rozvrzeni');
        if (!$this->request->isPost() || !isset(self::ROZVRZENI[$nove])) {
            return $this->zpet();
        }
        foreach (self::NAHRADNI_ZONA[$nove] ?? [] as $z => $do) {
            // přesunuté bloky se zařadí za ty, které v cílové zóně už jsou
            $nejniz = (int) $this->db->value('SELECT COALESCE(MIN(hodnost), 1000) FROM {bloky} WHERE zona = ?', [$do]);
            $this->db->run('UPDATE {bloky} SET zona = ?, hodnost = GREATEST(0, ? - 10 - (1000 - LEAST(hodnost, 1000)) DIV 10) WHERE zona = ?', [$do, $nejniz, $z]);
        }
        $this->app->settings()->set('rozvrzeni', $nove);

        return $this->zpet('Rozvržení stránky: ' . self::ROZVRZENI[$nove][0] . '.');
    }

    /** Uložení pořadí po přetažení: JSON {"zona": [idb, idb...], ...}. */
    protected function akcePoradi(): Response
    {
        $poradi = json_decode($this->request->post('poradi'), true);
        if (!$this->request->isPost() || !is_array($poradi)) {
            return Response::json(['ok' => false], 400);
        }
        $povolene = self::ROZVRZENI[$this->rozvrzeni()][2];
        $this->db->transaction(function () use ($poradi, $povolene): void {
            foreach ($poradi as $zona => $ids) {
                if (!in_array($zona, $povolene, true) || !is_array($ids)) {
                    continue;
                }
                foreach (array_values($ids) as $i => $idb) {
                    $this->db->update('bloky', ['zona' => $zona, 'hodnost' => 1000 - $i * 10], ['idb' => (int) $idb]);
                }
            }
        });

        return Response::json(['ok' => true]);
    }

    protected function akceNovy(): Response
    {
        $sys = $this->request->get('sys');
        $zona = $this->request->get('zona');

        return $this->formular([
            'idb' => 0, 'nazev' => self::SYSTEMOVE[$sys] ?? '', 'obsah' => '', 'typ' => 1,
            'sys_funkce' => isset(self::SYSTEMOVE[$sys]) ? $sys : '', 'zobrazit' => 1, 'zobrazit_kde' => 0, 'data_sys' => $sys === 'rek' ? 'sloupec' : '',
            'zona' => isset(self::ZONY[$zona]) ? $zona : 'prava',
        ]);
    }

    protected function akceEdit(): Response
    {
        $blok = $this->db->one('SELECT * FROM {bloky} WHERE idb = ?', [$this->request->getInt('id')]);

        return $blok === null ? $this->chyba('Blok neexistuje.', 404) : $this->formular($blok);
    }

    protected function akceUloz(): Response
    {
        if (!$this->request->isPost()) {
            return $this->zpet();
        }
        $r = $this->request;
        $id = $r->postInt('idb');
        $sys = $r->post('sys_funkce');
        $povolene = self::ROZVRZENI[$this->rozvrzeni()][2];
        $data = [
            'nazev' => $r->post('nazev'),
            'obsah' => $r->post('obsah'),
            'typ' => isset(self::VZHLEDY[$r->postInt('typ')]) ? $r->postInt('typ') : 1,
            'sys_funkce' => isset(self::SYSTEMOVE[$sys]) ? $sys : '',
            'zobrazit' => (int) $r->postBool('zobrazit'),
            'zobrazit_kde' => isset(self::KDE[$r->postInt('zobrazit_kde')]) ? $r->postInt('zobrazit_kde') : 0,
            'zona' => in_array($r->post('zona'), $povolene, true) ? $r->post('zona') : end($povolene),
            'data_sys' => isset(Reklama::POZICE[$r->post('data_sys')]) ? $r->post('data_sys') : '',
        ];
        if ($data['nazev'] === '') {
            return $this->formular(['idb' => $id] + $data, ['nazev' => 'Vyplňte název bloku.']);
        }

        if ($id > 0) {
            $this->db->update('bloky', $data, ['idb' => $id]);
        } else {
            // nový blok se zařadí na konec své zóny
            $nejniz = (int) $this->db->value('SELECT COALESCE(MIN(hodnost), 1010) FROM {bloky} WHERE zona = ?', [$data['zona']]);
            $this->db->insert('bloky', $data + ['hodnost' => max(0, $nejniz - 10)]);
        }

        return $this->zpet('Blok byl uložen.');
    }

    protected function akceSmaz(): Response
    {
        if ($this->request->isPost()) {
            $this->db->delete('bloky', ['idb' => $this->request->postInt('idb')]);
        }

        return $this->zpet('Blok byl smazán.');
    }

    private function rozvrzeni(): string
    {
        $rozvrzeni = $this->app->settings()->get('rozvrzeni');

        return isset(self::ROZVRZENI[$rozvrzeni]) ? $rozvrzeni : 'tri';
    }

    /**
     * @param array<string, mixed> $blok
     * @param array<string, string> $chyby
     */
    private function formular(array $blok, array $chyby = []): Response
    {
        return $this->view('formular', $blok['idb'] ? 'Úprava bloku' : 'Nový blok', [
            'blok' => $blok,
            'chyby' => $chyby,
            'zony' => array_intersect_key(self::ZONY, array_flip(self::ROZVRZENI[$this->rozvrzeni()][2])),
        ]);
    }
}
