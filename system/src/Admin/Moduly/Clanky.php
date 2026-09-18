<?php

declare(strict_types=1);

namespace PhpRS\Admin\Moduly;

use PhpRS\Admin\Modul;
use PhpRS\Core\Response;

/**
 * Editace článků.
 *
 * Pravidla převzatá z phpRS 2:
 *  - autor vidí a edituje články své a svých podřízených, admin všechny,
 *  - vydaný článek smí měnit jen ten, kdo má "právo vydávat",
 *  - bez práva vydávat nejde nastavit "Vydat článek: Ano" (článek čeká na redaktora).
 */
final class Clanky extends Modul
{
    public const string IDENT = 'clanky';
    public const string NAZEV = 'Editace článků';

    private const int NA_STRANKU = 20;

    protected function akceVypis(): Response
    {
        $auth = $this->app->auth();
        $where = ['1 = 1'];
        $params = [];

        $autori = $auth->spravovaniAutori();
        if ($this->request->get('moje') === '1') {
            $autori = [$auth->id()];
        }
        if ($autori !== null) {
            $where[] = 'c.autor IN (' . implode(',', $autori) . ')';
        }
        if (($tema = $this->request->getInt('tema')) > 0) {
            $where[] = 'c.tema = ?';
            $params[] = $tema;
        }
        if (($hledat = $this->request->get('hledat')) !== '') {
            $where[] = 'c.titulek LIKE ?';
            $params[] = '%' . addcslashes($hledat, '%_\\') . '%';
        }
        $cond = implode(' AND ', $where);

        $celkem = (int) $this->db->value("SELECT COUNT(*) FROM {clanky} c WHERE {$cond}", $params);
        $strana = max(1, $this->request->getInt('strana', 1));
        $clanky = $this->db->all(
            "SELECT c.idc, c.link, c.seo_link, c.titulek, c.datum, c.visible, c.visit, c.kom, c.priority,
                    t.nazev AS tema_jm, u.jmeno AS autor_jm, u.user AS autor_login
             FROM {clanky} c
             JOIN {topic} t ON t.idt = c.tema
             LEFT JOIN {user} u ON u.idu = c.autor
             WHERE {$cond}
             ORDER BY c.datum DESC, c.idc DESC
             LIMIT ? OFFSET ?",
            [...$params, self::NA_STRANKU, ($strana - 1) * self::NA_STRANKU],
        );

        return $this->view('vypis', 'Výpis článků', [
            'clanky' => $clanky,
            'celkem' => $celkem,
            'strana' => $strana,
            'stran' => max(1, (int) ceil($celkem / self::NA_STRANKU)),
            'rubriky' => Rubriky::strom($this->db),
            'filtr' => ['tema' => $tema, 'hledat' => $hledat, 'moje' => $this->request->get('moje')],
        ]);
    }

    protected function akceNovy(): Response
    {
        if (Rubriky::strom($this->db) === []) {
            return $this->chyba('Nejprve založte alespoň jednu rubriku (Úprava rubrik).');
        }

        return $this->formular([
            'idc' => 0, 'link' => '', 'seo_link' => '', 'titulek' => '', 'uvod' => '', 'text' => '', 'obrazek' => '',
            'tema' => 0, 'autor' => $this->app->auth()->id(), 'datum' => date('Y-m-d H:i:s'), 'datum_pl' => null,
            'visible' => 0, 'zobr_na_indexu' => 1, 'priority' => 0, 'typ_clanku' => 1, 'sablona' => null,
            'zdroj' => '', 't_slova' => '', 'znacky' => 1, 'povolit_kom' => 1,
        ]);
    }

    protected function akceEdit(): Response
    {
        $clanek = $this->nacti($this->request->getInt('id'));
        if ($clanek === null) {
            return $this->chyba('Článek neexistuje nebo k němu nemáte přístup.', 404);
        }

        return $this->formular($clanek);
    }

    protected function akceUloz(): Response
    {
        if (!$this->request->isPost()) {
            return $this->zpet();
        }
        $auth = $this->app->auth();
        $r = $this->request;
        $id = $r->postInt('idc');

        $puvodni = null;
        if ($id > 0) {
            $puvodni = $this->nacti($id);
            if ($puvodni === null) {
                return $this->chyba('Článek neexistuje nebo k němu nemáte přístup.', 404);
            }
            if ($puvodni['visible'] && !$auth->smiVydavat()) {
                return $this->chyba('Vydaný článek může upravit jen uživatel s právem vydávat.', 403);
            }
        }

        $data = [
            'titulek' => $r->post('titulek'),
            'seo_link' => slugify($r->post('seo_link') !== '' ? $r->post('seo_link') : $r->post('titulek'), 150),
            'uvod' => $r->post('uvod'),
            'text' => $r->post('text'),
            'obrazek' => $r->post('obrazek'),
            'tema' => $r->postInt('tema'),
            'autor' => $r->postInt('autor'),
            'datum' => self::datumZFormulare($r->post('datum')) ?? date('Y-m-d H:i:s'),
            'datum_pl' => self::datumZFormulare($r->post('datum_pl')),
            'visible' => (int) ($r->postBool('visible') && $auth->smiVydavat()),
            'zobr_na_indexu' => (int) $r->postBool('zobr_na_indexu'),
            'priority' => max(0, min(255, $r->postInt('priority'))),
            'typ_clanku' => $r->postInt('typ_clanku') === 2 ? 2 : 1,
            'sablona' => $r->postInt('sablona') ?: null,
            'zdroj' => $r->post('zdroj'),
            't_slova' => $r->post('t_slova'),
            'znacky' => (int) $r->postBool('znacky'),
            'povolit_kom' => (int) $r->postBool('povolit_kom'),
            'zmeneno' => date('Y-m-d H:i:s'),
        ];

        $chyby = [];
        if ($data['titulek'] === '') {
            $chyby['titulek'] = 'Vyplňte titulek článku.';
        }
        if ($this->db->value('SELECT idt FROM {topic} WHERE idt = ?', [$data['tema']]) === null) {
            $chyby['tema'] = 'Vyberte rubriku.';
        }
        $povoleniAutori = $auth->spravovaniAutori();
        if ($povoleniAutori !== null && !in_array($data['autor'], $povoleniAutori, true)) {
            $data['autor'] = $auth->id();
        }
        if ($this->db->value('SELECT idu FROM {user} WHERE idu = ?', [$data['autor']]) === null) {
            $chyby['autor'] = 'Vyberte autora.';
        }
        if ($chyby !== []) {
            return $this->formular(['idc' => $id, 'link' => $puvodni['link'] ?? ''] + $data, $chyby);
        }

        $data['seo_link'] = $this->volnySeoLink($data['seo_link'], $id);
        if ($id > 0) {
            $this->db->update('clanky', $data, ['idc' => $id]);
        } else {
            $id = $this->db->transaction(function () use ($data): int {
                return $this->db->insert('clanky', $data + ['link' => $this->novyLink($data['datum'])]);
            });
        }

        $hlaska = 'Článek byl uložen.';
        if ($r->postBool('visible') && !$auth->smiVydavat()) {
            $hlaska .= ' Nemáte právo vydávat - článek čeká na vydání redaktorem.';
        }

        return $r->post('po_ulozeni') === 'zustat'
            ? $this->zpet($hlaska, 'edit', ['id' => $id])
            : $this->zpet($hlaska);
    }

    protected function akceSmaz(): Response
    {
        if (!$this->request->isPost()) {
            return $this->zpet();
        }
        $smazano = 0;
        foreach ($this->request->postList('smaz') as $id) {
            $clanek = $this->nacti((int) $id);
            if ($clanek === null || ($clanek['visible'] && !$this->app->auth()->smiVydavat())) {
                continue;
            }
            $smazano += $this->db->delete('clanky', ['idc' => $clanek['idc']]);
        }

        return $this->zpet("Smazáno článků: {$smazano}.");
    }

    /**
     * @param array<string, mixed> $clanek
     * @param array<string, string> $chyby
     */
    private function formular(array $clanek, array $chyby = []): Response
    {
        $auth = $this->app->auth();
        $povoleni = $auth->spravovaniAutori();
        $autori = $povoleni === null
            ? $this->db->pairs('SELECT idu, IF(jmeno = \'\', user, jmeno) FROM {user} ORDER BY 2')
            : $this->db->pairs('SELECT idu, IF(jmeno = \'\', user, jmeno) FROM {user} WHERE idu IN (' . implode(',', $povoleni) . ') ORDER BY 2');

        return $this->view('formular', $clanek['idc'] ? 'Úprava článku' : 'Přidání nového článku', [
            'clanek' => $clanek,
            'chyby' => $chyby,
            'rubriky' => Rubriky::strom($this->db),
            'autori' => $autori,
            'sablony' => $this->db->pairs('SELECT ids, nazev_cla_sab FROM {cla_sab} ORDER BY ids'),
            'smiVydavat' => $auth->smiVydavat(),
        ]);
    }

    /** Načte článek, jen pokud ho přihlášený uživatel smí spravovat. */
    private function nacti(int $id): ?array
    {
        $clanek = $this->db->one('SELECT * FROM {clanky} WHERE idc = ?', [$id]);
        $autori = $this->app->auth()->spravovaniAutori();
        if ($clanek === null || ($autori !== null && !in_array((int) $clanek['autor'], $autori, true))) {
            return null;
        }

        return $clanek;
    }

    /** Volací link ve tvaru RRRRMMDDNN jako v phpRS 2 (první článek dne končí 01). */
    private function novyLink(string $datum): int
    {
        $den = (int) date('Ymd', strtotime($datum)) * 100;
        $posledni = (int) $this->db->value('SELECT MAX(link) FROM {clanky} WHERE link BETWEEN ? AND ? FOR UPDATE', [$den, $den + 99]);
        $link = $posledni > 0 ? $posledni + 1 : $den + 1;
        if ($link > $den + 99) {
            // víc než 99 článků za den: pokračuje se za nejvyšším existujícím číslem
            $link = (int) $this->db->value('SELECT MAX(link) FROM {clanky}') + 1;
        }

        return $link;
    }

    private function volnySeoLink(string $seo, int $idc): string
    {
        $kandidat = $seo;
        for ($i = 2; $this->db->value('SELECT idc FROM {clanky} WHERE seo_link = ? AND idc <> ?', [$kandidat, $idc]) !== null; $i++) {
            $kandidat = $seo . '-' . $i;
        }

        return $kandidat;
    }

    /** Hodnota z <input type="datetime-local"> -> DATETIME; prázdné nebo neplatné = null. */
    private static function datumZFormulare(string $value): ?string
    {
        if ($value === '') {
            return null;
        }
        $dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', substr($value, 0, 16));

        return $dt === false ? null : $dt->format('Y-m-d H:i:00');
    }
}
