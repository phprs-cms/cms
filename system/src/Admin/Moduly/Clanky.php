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
    public const string NAZEV = 'Články';
    public const string NAZEV_RETRO = 'Editace článků';
    public const string SKUPINA = 'Obsah';
    public const string IKONA = 'clanek';

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
        $stav = $this->request->get('stav');
        $podminkyStavu = [
            'vydane' => 'c.visible = 1 AND c.datum <= NOW()',
            'plan' => 'c.visible = 1 AND c.datum > NOW()',
            'koncepty' => 'c.visible = 0',
        ];
        if (isset($podminkyStavu[$stav])) {
            $where[] = $podminkyStavu[$stav];
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

        return $this->view('vypis', 'Články', [
            'clanky' => $clanky,
            'celkem' => $celkem,
            'strana' => $strana,
            'stran' => max(1, (int) ceil($celkem / self::NA_STRANKU)),
            'rubriky' => Rubriky::strom($this->db),
            'filtr' => ['tema' => $tema, 'hledat' => $hledat, 'moje' => $this->request->get('moje'), 'stav' => isset($podminkyStavu[$stav]) ? $stav : ''],
            'smiVydavat' => $auth->smiVydavat(),
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
            'zdroj' => '', 't_slova' => '', 'povolit_kom' => 1, 'skupina_cl' => null,
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
            'visible' => (int) ($r->post('stav') === 'vydany' && $auth->smiVydavat()),
            'zobr_na_indexu' => (int) $r->postBool('zobr_na_indexu'),
            // připnutý článek = priorita > 0 (phpRS 2 řadil hlavní stránku podle čísla priority)
            'priority' => $r->postBool('pripnout') ? max(100, (int) ($puvodni['priority'] ?? 0)) : 0,
            'typ_clanku' => $r->postBool('kratky') ? 2 : 1,
            'sablona' => $r->postInt('sablona') ?: null,
            'zdroj' => $r->post('zdroj'),
            't_slova' => $r->post('t_slova'),
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
        $data['skupina_cl'] = $this->serial($r->postInt('skupina_cl'), $r->post('serial_novy'));
        if ($id > 0) {
            if ([$puvodni['titulek'], $puvodni['uvod'], $puvodni['text']] !== [$data['titulek'], $data['uvod'], $data['text']]) {
                $this->ulozRevizi($puvodni);
            }
            $this->db->update('clanky', $data, ['idc' => $id]);
        } else {
            $id = $this->db->transaction(function () use ($data): int {
                return $this->db->insert('clanky', $data + ['link' => $this->novyLink($data['datum'])]);
            });
        }

        Galerie::zapisPouziti($this->db, $id, $data['obrazek'], $data['uvod'], $data['text']);
        $this->ulozStitky($id, $r->post('stitky'));

        $hlaska = 'Článek byl uložen.';
        if (!$auth->smiVydavat()) {
            $hlaska .= ' Na webu se objeví, až ho vydá redaktor.';
        }

        return $r->post('po_ulozeni') === 'zustat'
            ? $this->zpet($hlaska, 'edit', ['id' => $id])
            : $this->zpet($hlaska);
    }

    /** Vydání konceptu jedním kliknutím z přehledu (dřív modul Redaktor). */
    protected function akceVydat(): Response
    {
        $clanek = $this->nacti($this->request->postInt('idc'));
        if (!$this->request->isPost() || $clanek === null || !$this->app->auth()->smiVydavat()) {
            return $this->zpet('Článek nelze vydat.', typ: 'chyba');
        }
        $this->db->update('clanky', ['visible' => 1], ['idc' => $clanek['idc']]);

        return $this->zpet(strtotime($clanek['datum']) > time() ? 'Článek je naplánován na ' . datum($clanek['datum'], true) . '.' : 'Článek byl vydán.', '', ['stav' => 'koncepty']);
    }

    /** Načte do editoru starší verzi článku; uloží se až odesláním formuláře. */
    protected function akceRevize(): Response
    {
        $clanek = $this->nacti($this->request->getInt('id'));
        $revize = $clanek === null ? null : $this->db->one('SELECT * FROM {clanky_revize} WHERE idr = ? AND idc = ?', [$this->request->getInt('idr'), $clanek['idc']]);
        if ($revize === null) {
            return $this->chyba('Verze článku neexistuje.', 404);
        }
        $this->app->session->flash('info', 'V editoru je verze z ' . datum($revize['datum'], true) . '. Platit začne, až článek uložíte.');

        return $this->formular(['titulek' => $revize['titulek'], 'uvod' => $revize['uvod'], 'text' => $revize['text']] + $clanek);
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

        return $this->view('formular', $clanek['idc'] ? 'Úprava článku' : 'Nový článek', [
            'clanek' => $clanek,
            'chyby' => $chyby,
            'rubriky' => Rubriky::strom($this->db),
            'autori' => $autori,
            'sablony' => $this->db->pairs('SELECT ids, nazev_cla_sab FROM {cla_sab} ORDER BY ids'),
            'smiVydavat' => $auth->smiVydavat(),
            'serialy' => $this->db->pairs('SELECT ids, nazev_skup FROM {skup_cl} ORDER BY nazev_skup'),
            'stitky' => $this->request->isPost() ? $this->request->post('stitky') : implode(', ', array_column(
                $this->db->all('SELECT s.nazev FROM {stitky} s JOIN {clanky_stitky} cs ON cs.ids = s.ids WHERE cs.idc = ? ORDER BY s.nazev', [(int) $clanek['idc']]),
                'nazev',
            )),
            'vsechnyStitky' => array_column($this->db->all('SELECT nazev FROM {stitky} ORDER BY nazev LIMIT 500'), 'nazev'),
            'revize' => $this->db->all(
                "SELECT r.idr, r.datum, r.titulek, IF(u.jmeno = '' OR u.jmeno IS NULL, u.user, u.jmeno) AS kdo_jm
                 FROM {clanky_revize} r LEFT JOIN {user} u ON u.idu = r.kdo WHERE r.idc = ? ORDER BY r.idr DESC",
                [(int) $clanek['idc']],
            ),
        ]);
    }

    /** Seriál (v phpRS 2 "skupina souvisejících článků"): vybraný, nebo nově založený podle názvu. */
    private function serial(int $ids, string $novy): ?int
    {
        $novy = mb_substr($novy, 0, 150);
        if ($novy !== '') {
            $existujici = $this->db->value('SELECT ids FROM {skup_cl} WHERE nazev_skup = ?', [$novy]);

            return $existujici !== null ? (int) $existujici : $this->db->insert('skup_cl', ['nazev_skup' => $novy]);
        }

        return $this->db->value('SELECT ids FROM {skup_cl} WHERE ids = ?', [$ids]) !== null ? $ids : null;
    }

    /** Uloží předchozí podobu článku; drží se posledních 20 verzí. */
    private function ulozRevizi(array $puvodni): void
    {
        $this->db->insert('clanky_revize', [
            'idc' => $puvodni['idc'], 'datum' => $puvodni['zmeneno'] ?? $puvodni['datum'], 'kdo' => $this->app->auth()->id(),
            'titulek' => $puvodni['titulek'], 'uvod' => $puvodni['uvod'], 'text' => $puvodni['text'],
        ]);
        $hranice = $this->db->value('SELECT idr FROM {clanky_revize} WHERE idc = ? ORDER BY idr DESC LIMIT 1 OFFSET 20', [$puvodni['idc']]);
        if ($hranice !== null) {
            $this->db->run('DELETE FROM {clanky_revize} WHERE idc = ? AND idr <= ?', [$puvodni['idc'], $hranice]);
        }
    }

    /** Štítky zapsané čárkami; neznámé se založí. */
    private function ulozStitky(int $idc, string $vstup): void
    {
        $this->db->delete('clanky_stitky', ['idc' => $idc]);
        $nazvy = array_unique(array_filter(array_map(fn (string $n): string => mb_substr(trim($n), 0, 80), explode(',', $vstup))));
        foreach (array_slice($nazvy, 0, 20) as $nazev) {
            $seo = slugify($nazev, 90);
            $ids = $this->db->value('SELECT ids FROM {stitky} WHERE seo_link = ?', [$seo]);
            $ids = $ids !== null ? (int) $ids : $this->db->insert('stitky', ['nazev' => $nazev, 'seo_link' => $seo]);
            $this->db->run('INSERT IGNORE INTO {clanky_stitky} (idc, ids) VALUES (?, ?)', [$idc, $ids]);
        }
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
