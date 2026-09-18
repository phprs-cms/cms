<?php

declare(strict_types=1);

namespace PhpRS\Admin\Moduly;

use PhpRS\Admin\Modul;
use PhpRS\Core\Posta;
use PhpRS\Core\Response;

/**
 * Newsletter: vydání = předmět, krátký úvod a vybrané články. Rozesílá se po dávkách, aby to zvládl i sdílený hosting.
 */
final class NewsletterAdmin extends Modul
{
    public const string IDENT = 'newsletter';
    public const string NAZEV = 'Newsletter';
    public const string NAZEV_RETRO = 'Poštovní centrum';
    public const string SKUPINA = 'Čtenáři';
    public const string IKONA = 'newsletter';
    public const string ROZSIRENI = 'newsletter';

    private const int DAVKA = 40;

    protected function akceVypis(): Response
    {
        $posledni = (string) ($this->db->value('SELECT MAX(vytvoreno) FROM {newsletter} WHERE odeslano IS NOT NULL') ?? '2000-01-01');

        return $this->view('vypis', 'Newsletter', [
            'odberatelu' => (int) $this->db->value('SELECT COUNT(*) FROM {odberatele} WHERE potvrzen = 1'),
            'nepotvrzenych' => (int) $this->db->value('SELECT COUNT(*) FROM {odberatele} WHERE potvrzen = 0'),
            'vydani' => $this->db->all('SELECT * FROM {newsletter} ORDER BY idn DESC LIMIT 30'),
            'clanky' => $this->db->all('SELECT idc, titulek, datum, datum > ? AS novy FROM {clanky} WHERE visible = 1 AND datum <= NOW() AND typ_clanku = 1 ORDER BY datum DESC LIMIT 15', [$posledni]),
            'maEmail' => $this->app->settings()->get('email_webu') !== '',
            'maBlok' => $this->db->value("SELECT idb FROM {bloky} WHERE sys_funkce = 'nws'") !== null,
        ]);
    }

    /** Uloží vydání a buď ho pošle na zkoušku redakci, nebo zahájí rozesílku. */
    protected function akceUloz(): Response
    {
        if (!$this->request->isPost()) {
            return $this->zpet();
        }
        $r = $this->request;
        $clanky = array_slice(array_filter(array_map(intval(...), $r->postList('clanky'))), 0, 20);
        if ($r->post('predmet') === '' || $clanky === []) {
            return $this->zpet('Vyplňte předmět a vyberte alespoň jeden článek.', typ: 'chyba');
        }
        $idn = $this->db->insert('newsletter', ['predmet' => mb_substr($r->post('predmet'), 0, 200), 'uvod' => $r->post('uvod'), 'clanky' => implode(',', $clanky), 'vytvoreno' => date('Y-m-d H:i:s')]);
        if ($r->post('co') === 'zkouska') {
            $ok = $this->posli($idn, $this->app->settings()->get('email_webu'), 'zkouska');
            $this->db->delete('newsletter', ['idn' => $idn]);

            return $this->zpet($ok ? 'Zkušební zpráva odešla na e-mail redakce.' : 'Zkušební zprávu se nepodařilo odeslat – zkontrolujte E-mail redakce v Nastavení a poštu u hostingu.', typ: $ok ? 'ok' : 'chyba');
        }

        return Response::redirect($this->url('rozeslat', ['id' => $idn]));
    }

    /** Jedna dávka rozesílky; stránka se sama obnovuje, dokud nejsou obslouženi všichni odběratelé. */
    protected function akceRozeslat(): Response
    {
        $vydani = $this->db->one('SELECT * FROM {newsletter} WHERE idn = ?', [$this->request->getInt('id')]);
        if ($vydani === null) {
            return $this->chyba('Vydání neexistuje.', 404);
        }
        if ($vydani['odeslano'] === null && $this->request->isPost()) {
            $davka = $this->db->all('SELECT * FROM {odberatele} WHERE potvrzen = 1 AND ido > ? ORDER BY ido LIMIT ?', [(int) $vydani['posledni'], self::DAVKA]);
            foreach ($davka as $o) {
                $this->posli((int) $vydani['idn'], $o['email'], $o['token']);
                $this->db->run('UPDATE {newsletter} SET posledni = ?, pocet = pocet + 1 WHERE idn = ?', [$o['ido'], $vydani['idn']]);
            }
            if (count($davka) < self::DAVKA) {
                $this->db->update('newsletter', ['odeslano' => date('Y-m-d H:i:s')], ['idn' => $vydani['idn']]);
            }
            $vydani = $this->db->one('SELECT * FROM {newsletter} WHERE idn = ?', [$vydani['idn']]);
        }

        return $this->view('rozeslat', 'Rozesílka newsletteru', [
            'vydani' => $vydani,
            'zbyva' => (int) $this->db->value('SELECT COUNT(*) FROM {odberatele} WHERE potvrzen = 1 AND ido > ?', [(int) $vydani['posledni']]),
        ]);
    }

    protected function akceOdberatele(): Response
    {
        if ($this->request->get('format') === 'csv') {
            $csv = "email;prihlasen\n" . implode("\n", array_map(fn (array $o): string => $o['email'] . ';' . $o['prihlasen'], $this->db->all('SELECT email, prihlasen FROM {odberatele} WHERE potvrzen = 1 ORDER BY email')));

            return new Response($csv, 200, ['Content-Type' => 'text/csv; charset=utf-8', 'Content-Disposition' => 'attachment; filename="odberatele.csv"']);
        }

        return $this->view('odberatele', 'Odběratelé', ['odberatele' => $this->db->all('SELECT * FROM {odberatele} ORDER BY ido DESC LIMIT 500')]);
    }

    protected function akceSmazOdberatele(): Response
    {
        if ($this->request->isPost()) {
            $this->db->delete('odberatele', ['ido' => $this->request->postInt('ido')]);
        }

        return $this->zpet('Odběratel byl odstraněn.', 'odberatele');
    }

    private function posli(int $idn, string $email, string $token): bool
    {
        $web = $this->app->settings();
        $v = $this->db->one('SELECT * FROM {newsletter} WHERE idn = ?', [$idn]);
        $ids = array_filter(array_map(intval(...), explode(',', $v['clanky'])));
        $clanky = $ids === [] ? [] : $this->db->all('SELECT titulek, seo_link, uvod, obrazek FROM {clanky} WHERE idc IN (' . implode(',', $ids) . ') ORDER BY FIELD(idc, ' . implode(',', $ids) . ')');
        $koren = $this->app->request->origin() . $this->app->url('');
        $odhlasit = $koren . 'newsletter/odhlasit/' . $token;
        $html = $this->app->view->render('admin/newsletter/email', ['web' => $web, 'vydani' => $v, 'clanky' => $clanky, 'koren' => $koren, 'odhlasit' => $odhlasit]);
        $text = $v['uvod'] . "\n\n" . implode("\n\n", array_map(fn (array $c): string => $c['titulek'] . "\n" . trim(strip_tags($c['uvod'])) . "\n" . $koren . 'clanek/' . $c['seo_link'], $clanky)) . "\n\n--\nOdhlášení z odběru: {$odhlasit}\n";

        return Posta::odesli($web, $email, $v['predmet'], $text, $html, ['List-Unsubscribe' => "<{$odhlasit}>", 'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click']);
    }
}
