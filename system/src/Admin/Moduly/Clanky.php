<?php

declare(strict_types=1);

namespace PhpRS\Admin\Moduly;

use PhpRS\Admin\Modul;
use PhpRS\Core\Response;

/**
 * Editace článků.
 *
 * Pravidla:
 *  - autor vidí a edituje články své a svých podřízených, redaktor a administrátor všechny,
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
            'koncepty' => "c.visible = 0 AND c.stav_redakce = ''",
            'korektura' => "c.visible = 0 AND c.stav_redakce = 'korektura'",
            'schvaleno' => "c.visible = 0 AND c.stav_redakce = 'schvaleno'",
        ];
        if (isset($podminkyStavu[$stav])) {
            $where[] = $podminkyStavu[$stav];
        }
        $cond = implode(' AND ', $where);

        $celkem = (int) $this->db->value("SELECT COUNT(*) FROM {clanky} c WHERE {$cond}", $params);
        $strana = max(1, $this->request->getInt('strana', 1));
        $clanky = $this->db->all(
            "SELECT c.idc, c.stav_redakce, c.seo_link, c.titulek, c.datum, c.visible, c.visit, c.kom, c.priority,
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
            'idc' => 0, 'seo_link' => '', 'titulek' => '', 'uvod' => '', 'text' => '', 'obrazek' => '',
            'tema' => 0, 'autor' => $this->app->auth()->id(), 'datum' => date('Y-m-d H:i:s'), 'datum_pl' => null,
            'visible' => 0, 'zobr_na_indexu' => 1, 'priority' => 0, 'typ_clanku' => 1, 'sablona' => null,
            'zdroj' => '', 't_slova' => '', 'povolit_kom' => 1, 'skupina_cl' => null,
            'seo_titulek' => '', 'seo_popis' => '', 'noindex' => 0, 'pristup' => 0, 'shrnuti' => '', 'faq' => '', 'stav_redakce' => '', 'poznamka' => '',
        ]);
    }

    protected function akceEdit(): Response
    {
        $clanek = $this->nacti($this->request->getInt('id'));
        if ($clanek === null) {
            return $this->chyba('Článek neexistuje nebo k němu nemáte přístup.', 404);
        }
        // zámek proti souběžné úpravě: platí 3 minuty od posledního "jsem tu" z editoru
        $ja = $this->app->auth()->id();
        if ($clanek['zamek_kdo'] !== null && (int) $clanek['zamek_kdo'] !== $ja && strtotime((string) $clanek['zamek_cas']) > time() - 180) {
            $kdo = $this->db->value("SELECT IF(jmeno = '', user, jmeno) FROM {user} WHERE idu = ?", [$clanek['zamek_kdo']]);
            $this->app->session->flash('chyba', "Článek má právě otevřený {$kdo}. Když ho uložíte oba, přepíšete si navzájem změny – domluvte se, kdo bude pokračovat.");
        } else {
            $this->db->update('clanky', ['zamek_kdo' => $ja, 'zamek_cas' => date('Y-m-d H:i:s')], ['idc' => $clanek['idc']]);
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
            'stav_redakce' => in_array($r->post('stav'), ['korektura', 'schvaleno'], true) && ($r->post('stav') !== 'schvaleno' || $auth->smiVydavat()) ? $r->post('stav') : '',
            'poznamka' => $r->post('poznamka'),
            'zobr_na_indexu' => (int) $r->postBool('zobr_na_indexu'),
            // připnutý článek = priorita > 0; hlavní stránka řadí podle priority a pak podle data
            'priority' => $r->postBool('pripnout') ? max(100, (int) ($puvodni['priority'] ?? 0)) : 0,
            'typ_clanku' => $r->postBool('kratky') ? 2 : 1,
            'sablona' => $r->postInt('sablona') ?: null,
            'zdroj' => $r->post('zdroj'),
            't_slova' => $r->post('t_slova'),
            'povolit_kom' => (int) $r->postBool('povolit_kom'),
            'seo_titulek' => mb_substr($r->post('seo_titulek'), 0, 255),
            'seo_popis' => mb_substr($r->post('seo_popis'), 0, 320),
            'noindex' => (int) $r->postBool('noindex'),
            'pristup' => \PhpRS\Core\Rozsireni::je($this->app->settings(), 'ctenari') ? min(2, max(0, $r->postInt('pristup'))) : (int) ($puvodni['pristup'] ?? 0),
            'shrnuti' => $r->post('shrnuti'),
            'faq' => $r->post('faq'),
            'zmeneno' => date('Y-m-d H:i:s'),
            'zamek_kdo' => null, // uložením se článek uvolní
            'zamek_cas' => null,
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
            return $this->formular(['idc' => $id] + $data, $chyby);
        }

        if ($r->postBool('oznacit_aktualizaci') && $data['visible']) {
            $data['aktualizovano'] = date('Y-m-d H:i:s');
        }
        $data['seo_link'] = $this->volnySeoLink($data['seo_link'], $id);
        $data['skupina_cl'] = $this->serial($r->postInt('skupina_cl'), $r->post('serial_novy'));
        if ($id > 0) {
            if ([$puvodni['titulek'], $puvodni['uvod'], $puvodni['text']] !== [$data['titulek'], $data['uvod'], $data['text']]) {
                $this->ulozRevizi($puvodni);
            }
            $this->db->update('clanky', $data, ['idc' => $id]);
            if ($puvodni['seo_link'] !== $data['seo_link'] && $puvodni['visible']) {
                // vydaný článek změnil adresu: stará se přesměruje, aby odkazy a vyhledávače nepřišly o stránku
                Presmerovani::pridej($this->db, 'clanek/' . $puvodni['seo_link'], 'clanek/' . $data['seo_link']);
            }
        } else {
            $id = $this->db->insert('clanky', $data);
        }

        Galerie::zapisPouziti($this->db, $id, $data['obrazek'], $data['uvod'], $data['text']);
        $this->ulozStitky($id, $r->post('stitky'));
        if ($data['visible'] && empty($puvodni['visible'])) {
            \PhpRS\Core\Webhook::clanekVydan($this->app, $id);
        }
        if ($data['visible'] && !$data['noindex'] && strtotime($data['datum']) <= time()) {
            (new \PhpRS\Front\Seo($this->app))->indexNow('clanek/' . $data['seo_link']);
        }

        $hlaska = 'Článek byl uložen.';
        if (!$auth->smiVydavat()) {
            $hlaska .= ' Na webu se objeví, až ho vydá redaktor.';
        }

        return $r->post('po_ulozeni') === 'zustat'
            ? $this->zpet($hlaska, 'edit', ['id' => $id])
            : $this->zpet($hlaska);
    }

    /** "Jsem tu" z otevřeného editoru - prodlužuje zámek článku. */
    protected function akceZamek(): Response
    {
        $clanek = $this->request->isPost() ? $this->nacti($this->request->postInt('idc')) : null;
        $ja = $this->app->auth()->id();
        if ($clanek !== null && ($clanek['zamek_kdo'] === null || (int) $clanek['zamek_kdo'] === $ja || strtotime((string) $clanek['zamek_cas']) <= time() - 180)) {
            $this->db->update('clanky', ['zamek_kdo' => $ja, 'zamek_cas' => date('Y-m-d H:i:s')], ['idc' => $clanek['idc']]);
        }

        return Response::json(['ok' => $clanek !== null]);
    }

    /**
     * AI asistent: návrh k rozepsanému článku (titulky, perex, shrnutí, SEO popis, štítky, korektura, popis obrázku).
     * Pracuje s textem z formuláře, nic neukládá - o použití návrhu rozhoduje redaktor.
     */
    protected function akceAsistent(): Response
    {
        $asistent = new \PhpRS\Core\Asistent($this->app->settings());
        if (!$this->request->isPost() || !$asistent->pripraven()) {
            return Response::json(['chyba' => 'AI asistent není zapnutý nebo chybí klíč (Nastavení → Rozšíření).'], 400);
        }
        // pojistka proti nechtěné útratě: nejvýš 60 dotazů za hodinu na uživatele
        $ja = $this->app->auth()->id();
        if ((int) $this->db->value("SELECT COUNT(*) FROM {protokol} WHERE kdo = ? AND modul = 'asistent' AND cas > NOW() - INTERVAL 1 HOUR", [$ja]) >= 60) {
            return Response::json(['chyba' => 'Za poslední hodinu jste asistenta použili 60×. Zkuste to prosím později.'], 429);
        }
        $ukol = $this->request->post('ukol');
        $obrazek = null;
        if ($ukol === 'alt') {
            // jen soubory z media/: cesta se skládá z ověřených částí adresy
            $obrazek = preg_match('#media/(\d{4}/\d{2}/[A-Za-z0-9._-]+\.(?:jpe?g|png|webp|gif))$#', (string) parse_url($this->request->post('obrazek'), PHP_URL_PATH), $m) ? PHPRS_ROOT . '/media/' . $m[1] : null;
            $mensi = $obrazek === null ? null : preg_replace('/\.(\w+)$/', '-1200.$1', $obrazek);
            $obrazek = $mensi !== null && is_file($mensi) ? $mensi : $obrazek;
        }
        try {
            $vysledek = $asistent->navrhni($ukol, [
                'titulek' => $this->request->post('titulek'),
                'uvod' => $this->request->post('uvod'),
                'text' => $this->request->post('text'),
                'stitky_webu' => $ukol === 'stitky' ? array_column($this->db->all('SELECT nazev FROM {stitky} ORDER BY nazev LIMIT 300'), 'nazev') : [],
            ], $obrazek);
        } catch (\RuntimeException $e) {
            return Response::json(['chyba' => $e->getMessage()], 502);
        }
        \PhpRS\Admin\Protokol::zapis($this->app, 'asistent', $ukol, mb_substr($this->request->post('titulek'), 0, 80));

        return Response::json($vysledek);
    }

    /** Redakční kalendář: články podle data vydání v měsíční mřížce. */
    protected function akceKalendar(): Response
    {
        $mesic = preg_match('/^\d{4}-\d{2}$/', $this->request->get('mesic')) ? $this->request->get('mesic') : date('Y-m');
        $od = new \DateTimeImmutable($mesic . '-01');
        $autori = $this->app->auth()->spravovaniAutori();
        $clanky = $this->db->all(
            'SELECT idc, titulek, datum, visible FROM {clanky} WHERE datum >= ? AND datum < ?'
            . ($autori !== null ? ' AND autor IN (' . implode(',', $autori) . ')' : '') . ' ORDER BY datum',
            [$od->format('Y-m-d'), $od->modify('+1 month')->format('Y-m-d')],
        );
        $dny = [];
        foreach ($clanky as $c) {
            $dny[(int) date('j', strtotime($c['datum']))][] = $c;
        }

        return $this->view('kalendar', 'Redakční kalendář', ['od' => $od, 'dny' => $dny]);
    }

    /** Vydání konceptu jedním kliknutím z přehledu (dřív modul Redaktor). */
    protected function akceVydat(): Response
    {
        $clanek = $this->nacti($this->request->postInt('idc'));
        if (!$this->request->isPost() || $clanek === null || !$this->app->auth()->smiVydavat()) {
            return $this->zpet('Článek nelze vydat.', typ: 'chyba');
        }
        $this->db->update('clanky', ['visible' => 1, 'stav_redakce' => ''], ['idc' => $clanek['idc']]);
        \PhpRS\Core\Webhook::clanekVydan($this->app, (int) $clanek['idc']);

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
            'ctenari' => \PhpRS\Core\Rozsireni::je($this->app->settings(), 'ctenari'),
            'asistent' => (new \PhpRS\Core\Asistent($this->app->settings()))->pripraven(),
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

    /** Seriál: vybraný, nebo nově založený podle názvu. */
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
