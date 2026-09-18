<?php

declare(strict_types=1);

namespace PhpRS\Admin\Moduly;

use PhpRS\Admin\Modul;
use PhpRS\Core\Obrazky;
use PhpRS\Core\Response;

/**
 * Galerie obrázků: nahrávání (i přetažením a přímo z editoru článku), popisky, mazání.
 * Obrázek se do článku vkládá z editoru, nebo značkou <obrazek id="N"> jako v phpRS 2.
 */
final class Galerie extends Modul
{
    public const string IDENT = 'intergal';
    public const string NAZEV = 'Galerie obrázků';

    /** Nahrávat musí umět každý, kdo píše články; cizí obrázky ale mění a maže jen admin. */
    public const bool PRO_VSECHNY = true;

    private const int NA_STRANKU = 40;

    protected function akceVypis(): Response
    {
        $strana = max(1, $this->request->getInt('strana', 1));
        $celkem = (int) $this->db->value('SELECT COUNT(*) FROM {imggal_obr}');

        return $this->view('vypis', 'Galerie obrázků', [
            'obrazky' => $this->nacti($strana, self::NA_STRANKU),
            'strana' => $strana,
            'stran' => max(1, (int) ceil($celkem / self::NA_STRANKU)),
            'limit' => ini_get('upload_max_filesize'),
        ]);
    }

    /** JSON seznam pro okno výběru obrázku v editoru. */
    protected function akceSeznam(): Response
    {
        return Response::json(['obrazky' => array_map($this->proJson(...), $this->nacti(max(1, $this->request->getInt('strana', 1)), 60))]);
    }

    /** Nahrání jednoho či více souborů; s parametrem format=json odpovídá editoru JSONem. */
    protected function akceNahraj(): Response
    {
        $json = $this->request->get('format') === 'json';
        $nahrane = [];
        $chyby = [];
        foreach ($this->soubory() as $file) {
            try {
                $data = Obrazky::uloz($file);
                $data['ido'] = $this->db->insert('imggal_obr', $data + ['vlastnik' => $this->app->auth()->id(), 'datum' => date('Y-m-d H:i:s')]);
                $nahrane[] = $this->proJson($data + ['popis' => '']);
            } catch (\RuntimeException $e) {
                $chyby[] = ($file['name'] ?? 'soubor') . ': ' . $e->getMessage();
            }
        }
        if ($nahrane === [] && $chyby === []) {
            $chyby[] = 'Nebyl vybrán žádný soubor.';
        }
        if ($json) {
            return Response::json(['obrazky' => $nahrane, 'chyby' => $chyby], $nahrane === [] ? 400 : 200);
        }
        foreach ($chyby as $chyba) {
            $this->app->session->flash('chyba', $chyba);
        }

        return $this->zpet($nahrane !== [] ? 'Nahráno obrázků: ' . count($nahrane) . '.' : '');
    }

    protected function akceUloz(): Response
    {
        if ($this->request->isPost() && $this->smiMenit($this->request->postInt('ido'))) {
            $this->db->update('imggal_obr', [
                'nazev' => mb_substr($this->request->post('nazev'), 0, 150),
                'popis' => mb_substr($this->request->post('popis'), 0, 500),
            ], ['ido' => $this->request->postInt('ido')]);
        }

        return $this->zpet('Popis obrázku byl uložen.');
    }

    protected function akceSmaz(): Response
    {
        if (!$this->request->isPost()) {
            return $this->zpet();
        }
        $smazano = 0;
        foreach ($this->request->postList('smaz') as $id) {
            $obr = $this->db->one('SELECT * FROM {imggal_obr} WHERE ido = ?', [(int) $id]);
            if ($obr !== null && $this->smiMenit((int) $obr['ido'])) {
                Obrazky::smaz($obr['obr_poloha'], $obr['nahl_poloha']);
                $smazano += $this->db->delete('imggal_obr', ['ido' => $obr['ido']]);
            }
        }

        return $this->zpet("Smazáno obrázků: {$smazano}.");
    }

    private function smiMenit(int $ido): bool
    {
        $vlastnik = $this->db->value('SELECT vlastnik FROM {imggal_obr} WHERE ido = ?', [$ido]);

        return $this->app->auth()->isAdmin() || (int) $vlastnik === $this->app->auth()->id();
    }

    /** @return list<array<string, mixed>> */
    private function nacti(int $strana, int $pocet): array
    {
        return $this->db->all('SELECT * FROM {imggal_obr} ORDER BY ido DESC LIMIT ? OFFSET ?', [$pocet, ($strana - 1) * $pocet]);
    }

    /** @param array<string, mixed> $o */
    private function proJson(array $o): array
    {
        return [
            'id' => (int) $o['ido'], 'nazev' => $o['nazev'], 'popis' => $o['popis'] ?? '',
            'url' => $this->app->url($o['obr_poloha']), 'nahled' => $this->app->url($o['nahl_poloha']),
            'sirka' => (int) $o['obr_width'], 'vyska' => (int) $o['obr_height'],
        ];
    }

    /** $_FILES['soubory'] (i vícenásobné) převedené na seznam jednotlivých souborů. */
    private function soubory(): array
    {
        $f = $_FILES['soubory'] ?? null;
        if (!is_array($f)) {
            return [];
        }
        if (!is_array($f['name'])) {
            return $f['error'] === UPLOAD_ERR_NO_FILE ? [] : [$f];
        }
        $soubory = [];
        foreach (array_keys($f['name']) as $i) {
            if ($f['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                $soubory[] = ['name' => $f['name'][$i], 'tmp_name' => $f['tmp_name'][$i], 'error' => $f['error'][$i], 'size' => $f['size'][$i]];
            }
        }

        return array_slice($soubory, 0, 30);
    }
}
