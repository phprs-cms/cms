<?php

declare(strict_types=1);

namespace PhpRS\Admin\Moduly;

use PhpRS\Admin\Modul;
use PhpRS\Core\Response;

/**
 * Stránky: samostatný obsah mimo rubriky (O nás, Kontakt, Zásady ochrany soukromí...).
 * Stránka má adresu /<seo_link>.
 */
final class Stranky extends Modul
{
    public const string IDENT = 'stranky';
    public const string NAZEV = 'Stránky';
    public const string NAZEV_RETRO = 'Stránky';
    public const string SKUPINA = 'Obsah';
    public const string IKONA = 'stranky';

    /** Adresy, které patří systému a stránka je mít nemůže. */
    private const array VYHRAZENE = ['clanek', 'rubrika', 'stitek', 'archiv', 'autor', 'mcp', 'api', 'newsletter', 'hledani', 'admin', 'install', 'media', 'image', 'layout', 'system', 'storage', 'plugins', 'rss', 'sitemap', 'robots', 'llms', 'stav'];

    protected function akceVypis(): Response
    {
        return $this->view('vypis', 'Stránky', ['stranky' => $this->db->all('SELECT * FROM {stranky} ORDER BY poradi, titulek')]);
    }

    protected function akceNovy(): Response
    {
        return $this->formular(['ids' => 0, 'seo_link' => '', 'titulek' => '', 'popis' => '', 'text' => '', 'zobrazit' => 1, 'v_menu' => 1, 'poradi' => 100]);
    }

    protected function akceEdit(): Response
    {
        $stranka = $this->db->one('SELECT * FROM {stranky} WHERE ids = ?', [$this->request->getInt('id')]);

        return $stranka === null ? $this->chyba('Stránka neexistuje.', 404) : $this->formular($stranka);
    }

    protected function akceUloz(): Response
    {
        if (!$this->request->isPost()) {
            return $this->zpet();
        }
        $r = $this->request;
        $id = $r->postInt('ids');
        $data = [
            'titulek' => mb_substr($r->post('titulek'), 0, 200),
            'seo_link' => slugify($r->post('seo_link') !== '' ? $r->post('seo_link') : $r->post('titulek'), 110),
            'popis' => mb_substr($r->post('popis'), 0, 300),
            'text' => $r->post('text'),
            'zobrazit' => (int) $r->postBool('zobrazit'),
            'v_menu' => (int) $r->postBool('v_menu'),
            'poradi' => max(0, min(65535, $r->postInt('poradi', 100))),
            'zmeneno' => date('Y-m-d H:i:s'),
            'jazyk' => \PhpRS\Core\Jazyk::sloupec($this->app->settings(), $r->post('jazyk')),
        ];
        $chyby = [];
        if ($data['titulek'] === '') {
            $chyby['titulek'] = 'Vyplňte název stránky.';
        }
        if (in_array($data['seo_link'], self::VYHRAZENE, true) || isset(\PhpRS\Core\Jazyk::DOSTUPNE[$data['seo_link']])) {
            $chyby['seo_link'] = 'Tuto adresu používá systém, zvolte jinou.';
        } elseif ($this->db->value('SELECT ids FROM {stranky} WHERE seo_link = ? AND ids <> ?', [$data['seo_link'], $id]) !== null) {
            $chyby['seo_link'] = 'Stránka s touto adresou už existuje.';
        }
        if ($chyby !== []) {
            return $this->formular(['ids' => $id] + $data, $chyby);
        }
        if ($id > 0) {
            $this->db->update('stranky', $data, ['ids' => $id]);
        } else {
            $this->db->insert('stranky', $data);
        }

        return $this->zpet('Stránka byla uložena.');
    }

    protected function akceSmaz(): Response
    {
        if ($this->request->isPost()) {
            $this->db->delete('stranky', ['ids' => $this->request->postInt('ids')]);
        }

        return $this->zpet('Stránka byla smazána.');
    }

    /**
     * @param array<string, mixed> $stranka
     * @param array<string, string> $chyby
     */
    private function formular(array $stranka, array $chyby = []): Response
    {
        return $this->view('formular', $stranka['ids'] ? 'Úprava stránky' : 'Nová stránka', ['stranka' => $stranka, 'chyby' => $chyby]);
    }
}
