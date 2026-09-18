<?php

declare(strict_types=1);

namespace PhpRS\Admin\Moduly;

use PhpRS\Admin\Modul;
use PhpRS\Core\Response;

/**
 * Reklamní systém: bannery (obrázek s odkazem) a reklamní kódy na pozicích webu.
 * Pozice "sloupec", "hlavicka" a "paticka" se na web umístí blokem Reklama (Bloky a rozvržení),
 * pozice "pod-clankem" se vypisuje pod každým článkem sama.
 */
final class Reklama extends Modul
{
    public const string IDENT = 'reklama';
    public const string NAZEV = 'Reklama';
    public const string NAZEV_RETRO = 'Reklamní systém';
    public const string SKUPINA = 'Čtenáři';
    public const string IKONA = 'reklama';
    public const string ROZSIRENI = 'reklama';

    public const array POZICE = [
        'sloupec' => 'Sloupec (čtverec, např. 300×250)',
        'hlavicka' => 'Hlavička (široký pruh, např. 970×210)',
        'pod-clankem' => 'Pod článkem (zobrazuje se automaticky)',
        'paticka' => 'Patička (široký pruh)',
    ];

    protected function akceVypis(): Response
    {
        return $this->view('vypis', 'Reklama', [
            'reklamy' => $this->db->all('SELECT * FROM {reklama} ORDER BY aktivni DESC, pozice, idr DESC'),
            'adsTxt' => $this->app->settings()->get('ads_txt'),
        ]);
    }

    protected function akceNovy(): Response
    {
        return $this->formular(['idr' => 0, 'nazev' => '', 'pozice' => 'sloupec', 'typ' => 'obrazek', 'obrazek' => '', 'cil_url' => '', 'kod' => '',
            'platna_od' => null, 'platna_do' => null, 'aktivni' => 1, 'vaha' => 1, 'max_zobrazeni' => null]);
    }

    protected function akceEdit(): Response
    {
        $reklama = $this->db->one('SELECT * FROM {reklama} WHERE idr = ?', [$this->request->getInt('id')]);

        return $reklama === null ? $this->chyba('Reklama neexistuje.', 404) : $this->formular($reklama);
    }

    protected function akceUloz(): Response
    {
        if (!$this->request->isPost()) {
            return $this->zpet();
        }
        $r = $this->request;
        $id = $r->postInt('idr');
        $datum = fn (string $v): ?string => ($dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', substr($v, 0, 16))) ? $dt->format('Y-m-d H:i:00') : null;
        $data = [
            'nazev' => mb_substr($r->post('nazev'), 0, 150),
            'pozice' => isset(self::POZICE[$r->post('pozice')]) ? $r->post('pozice') : 'sloupec',
            'typ' => $r->post('typ') === 'kod' ? 'kod' : 'obrazek',
            'obrazek' => mb_substr($r->post('obrazek'), 0, 255),
            'cil_url' => mb_substr($r->post('cil_url'), 0, 500),
            'kod' => (string) ($_POST['kod'] ?? ''),
            'platna_od' => $datum($r->post('platna_od')),
            'platna_do' => $datum($r->post('platna_do')),
            'aktivni' => (int) $r->postBool('aktivni'),
            'vaha' => max(1, min(10, $r->postInt('vaha', 1))),
            'max_zobrazeni' => $r->postInt('max_zobrazeni') > 0 ? $r->postInt('max_zobrazeni') : null,
        ];
        $chyby = [];
        if ($data['nazev'] === '') {
            $chyby['nazev'] = 'Vyplňte název.';
        }
        if ($data['typ'] === 'obrazek' && ($data['obrazek'] === '' || !preg_match('#^https?://#i', $data['cil_url']) || !filter_var($data['cil_url'], FILTER_VALIDATE_URL))) {
            $chyby['obrazek'] = 'Banner potřebuje obrázek a cílovou adresu začínající https://.';
        }
        if ($data['typ'] === 'kod' && trim($data['kod']) === '') {
            $chyby['kod'] = 'Vložte reklamní kód.';
        }
        if ($chyby !== []) {
            return $this->formular(['idr' => $id] + $data, $chyby);
        }
        if ($id > 0) {
            $this->db->update('reklama', $data, ['idr' => $id]);
        } else {
            $this->db->insert('reklama', $data);
        }

        return $this->zpet('Reklama byla uložena.');
    }

    protected function akceSmaz(): Response
    {
        if ($this->request->isPost()) {
            $this->db->delete('reklama', ['idr' => $this->request->postInt('idr')]);
        }

        return $this->zpet('Reklama byla smazána.');
    }

    protected function akceAdsTxt(): Response
    {
        if ($this->request->isPost()) {
            $this->app->settings()->set('ads_txt', mb_substr($this->request->post('ads_txt'), 0, 20000));
        }

        return $this->zpet('Soubor ads.txt byl uložen.');
    }

    /**
     * @param array<string, mixed> $reklama
     * @param array<string, string> $chyby
     */
    private function formular(array $reklama, array $chyby = []): Response
    {
        return $this->view('formular', $reklama['idr'] ? 'Úprava reklamy' : 'Nová reklama', ['reklama' => $reklama, 'chyby' => $chyby]);
    }
}
