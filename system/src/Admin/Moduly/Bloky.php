<?php

declare(strict_types=1);

namespace PhpRS\Admin\Moduly;

use PhpRS\Admin\Modul;
use PhpRS\Core\Response;

/**
 * Úprava bloků. Web se skládá ze sloupců a v nich z bloků seřazených podle priority.
 * Běžný blok nese vlastní HTML; systémový blok vykresluje systém (rubriky, novinky,
 * anketa...) a "Hlavní blok" je místo, kam se vypisuje vlastní obsah stránky.
 */
final class Bloky extends Modul
{
    public const string IDENT = 'bloky';
    public const string NAZEV = 'Úprava bloků';

    /** Systémové bloky: zkratky shodné s phpRS 2. */
    public const array SYSTEMOVE = [
        'hlb' => 'Hlavní blok (obsah stránky)',
        'rub' => 'Seznam rubrik',
        'nov' => 'Novinky',
        'hle' => 'Vyhledávání',
        'nej' => 'Nejčtenější články',
    ];

    public const array KDE = [0 => 'všude', 1 => 'jen na hlavní stránce', 2 => 'všude mimo hlavní stránku'];

    protected function akceVypis(): Response
    {
        $bloky = [];
        foreach ($this->db->all('SELECT * FROM {bloky} ORDER BY hodnost DESC, idb') as $blok) {
            $bloky[(int) $blok['id_sloupec']][] = $blok;
        }

        return $this->view('vypis', 'Úprava bloků', [
            'sloupce' => $this->db->all('SELECT * FROM {sloupce} ORDER BY ids'),
            'bloky' => $bloky,
        ]);
    }

    protected function akceNovy(): Response
    {
        $sys = $this->request->get('sys');

        return $this->formular([
            'idb' => 0, 'nazev' => self::SYSTEMOVE[$sys] ?? '', 'obsah' => '', 'typ' => 1, 'hodnost' => 100,
            'sys_funkce' => isset(self::SYSTEMOVE[$sys]) ? $sys : '', 'zobrazit' => 1, 'zobrazit_kde' => 0,
            'id_sloupec' => $this->request->getInt('sloupec'),
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
        $data = [
            'nazev' => $r->post('nazev'),
            'obsah' => $r->post('obsah'),
            'typ' => max(1, min(5, $r->postInt('typ', 1))),
            'hodnost' => max(0, min(65535, $r->postInt('hodnost', 100))),
            'sys_funkce' => isset(self::SYSTEMOVE[$sys]) ? $sys : '',
            'zobrazit' => (int) $r->postBool('zobrazit'),
            'zobrazit_kde' => array_key_exists($r->postInt('zobrazit_kde'), self::KDE) ? $r->postInt('zobrazit_kde') : 0,
            'id_sloupec' => $r->postInt('id_sloupec'),
        ];

        $chyby = [];
        if ($data['nazev'] === '') {
            $chyby['nazev'] = 'Vyplňte název bloku.';
        }
        if ($this->db->value('SELECT ids FROM {sloupce} WHERE ids = ?', [$data['id_sloupec']]) === null) {
            $chyby['id_sloupec'] = 'Vyberte sloupec.';
        }
        if ($data['sys_funkce'] === 'hlb'
            && $this->db->value("SELECT idb FROM {bloky} WHERE sys_funkce = 'hlb' AND idb <> ?", [$id]) !== null) {
            $chyby['sys_funkce'] = 'Hlavní blok může být na webu jen jeden.';
        }
        if ($chyby !== []) {
            return $this->formular(['idb' => $id] + $data, $chyby);
        }

        if ($id > 0) {
            $this->db->update('bloky', $data, ['idb' => $id]);
        } else {
            $this->db->insert('bloky', $data);
        }

        return $this->zpet('Blok byl uložen.');
    }

    protected function akceSmaz(): Response
    {
        if (!$this->request->isPost()) {
            return $this->zpet();
        }
        $blok = $this->db->one('SELECT * FROM {bloky} WHERE idb = ?', [$this->request->postInt('idb')]);
        if ($blok !== null && $blok['sys_funkce'] === 'hlb') {
            return $this->zpet('Hlavní blok nelze smazat - bez něj by se na webu nezobrazoval obsah stránek.', typ: 'chyba');
        }
        if ($blok !== null) {
            $this->db->delete('bloky', ['idb' => $blok['idb']]);
        }

        return $this->zpet('Blok byl smazán.');
    }

    /**
     * @param array<string, mixed> $blok
     * @param array<string, string> $chyby
     */
    private function formular(array $blok, array $chyby = []): Response
    {
        return $this->view('formular', $blok['idb'] ? 'Úprava bloku' : 'Přidání nového bloku', [
            'blok' => $blok,
            'chyby' => $chyby,
            'sloupce' => $this->db->pairs("SELECT ids, IF(nazev = '', CONCAT('sloupec ', ids), nazev) FROM {sloupce} ORDER BY ids"),
        ]);
    }
}
