<?php

declare(strict_types=1);

namespace PhpRS\Front;

use PhpRS\Core\Antispam;
use PhpRS\Core\App;
use PhpRS\Core\Response;
use PhpRS\Core\Rozsireni;
use PhpRS\Core\View;

/**
 * Zapojení čtenářů: komentáře, hodnocení článků hvězdičkami a ankety.
 * Všechny formuláře chrání Core\Antispam; opakované hlasování hlídá otisk IP adresy a cookie.
 */
final class Interakce
{
    private readonly Antispam $antispam;

    public function __construct(private readonly App $app, private readonly View $view)
    {
        $this->antispam = new Antispam($app->db(), $app->settings());
    }

    /* ---------- komentáře ---------- */

    /** @param array<string, mixed> $clanek */
    public function komentareHtml(array $clanek): string
    {
        if (!Rozsireni::je($this->app->settings(), 'komentare') || !$this->app->settings()->bool('povolit_komentare') || !$clanek['povolit_kom']) {
            return '';
        }
        $vse = $this->app->db()->all('SELECT * FROM {komentare} WHERE clanek = ? AND zobrazit = 1 ORDER BY datum, idk', [$clanek['idc']]);
        $koreny = [];
        $reakce = [];
        foreach ($vse as $k) {
            if ($k['reakce_na'] === null) {
                $koreny[] = $k;
            } else {
                $reakce[(int) $k['reakce_na']][] = $k;
            }
        }

        return $this->view->render('komentare', [
            'clanek' => $clanek, 'koreny' => $koreny, 'reakce' => $reakce, 'pocet' => count($vse),
            'akce' => $this->app->url('komentar'), 'pole' => $this->antispam->pole('komentar-' . $clanek['idc']),
            'zprava' => ['ok' => 'Děkujeme, komentář byl přidán.', 'ceka' => 'Děkujeme. Komentář se zobrazí po schválení redakcí.'][$this->app->request->get('komentar')] ?? '',
            'chyba' => $this->app->request->get('komentar') === 'chyba' ? (string) $this->app->session->get('komentar_chyba', 'Komentář se nepodařilo uložit.') : '',
        ]);
    }

    public function ulozKomentar(): Response
    {
        $r = $this->app->request;
        $db = $this->app->db();
        $clanek = $db->one('SELECT idc, seo_link, povolit_kom FROM {clanky} WHERE idc = ? AND visible = 1 AND datum <= NOW()', [$r->postInt('idc')]);
        if ($clanek === null || !$clanek['povolit_kom'] || !$this->app->settings()->bool('povolit_komentare')) {
            return Response::redirect($this->app->url(''));
        }
        $zpet = fn (string $stav): Response => Response::redirect($this->app->url('clanek/' . $clanek['seo_link'] . '?komentar=' . $stav . '#komentare'), 303);
        $chyba = function (string $text) use ($zpet): Response {
            $this->app->session->set('komentar_chyba', $text);

            return $zpet('chyba');
        };

        $duvod = $this->antispam->over($r, 'komentar-' . $clanek['idc']);
        if ($duvod === 'robot') {
            return $zpet('ok'); // robot se nedozví, že neuspěl
        }
        if ($duvod !== null) {
            return $chyba($duvod);
        }
        $od = mb_substr($r->post('od'), 0, 60);
        $obsah = mb_substr($r->post('obsah'), 0, 5000);
        $mail = mb_substr($r->post('od_mail'), 0, 190);
        if ($od === '' || mb_strlen($obsah) < 3) {
            return $chyba('Vyplňte jméno a text komentáře.');
        }
        if ($mail !== '' && filter_var($mail, FILTER_VALIDATE_EMAIL) === false) {
            return $chyba('E-mail nemá platný tvar.');
        }
        if ($this->antispam->pocet($r->ip(), 'komentar', 0, 10) >= 5) {
            return $chyba('Příliš mnoho komentářů za krátkou dobu. Zkuste to prosím později.');
        }
        $reakceNa = $db->value('SELECT idk FROM {komentare} WHERE idk = ? AND clanek = ? AND reakce_na IS NULL', [$r->postInt('reakce_na'), $clanek['idc']]);

        // podezřelý komentář (odkazy) čeká na schválení i v režimu "hned"
        $podezrely = preg_match_all('#https?://|www\.#i', $obsah) >= 2;
        $zobrazit = $this->app->settings()->get('komentare_rezim') === 'hned' && !$podezrely;
        $db->insert('komentare', [
            'clanek' => $clanek['idc'], 'reakce_na' => $reakceNa === null ? null : (int) $reakceNa, 'datum' => date('Y-m-d H:i:s'),
            'obsah' => $obsah, 'od' => $od, 'od_mail' => $mail, 'od_ip' => $r->ip(), 'zobrazit' => (int) $zobrazit,
        ]);
        $this->antispam->zapis($r->ip(), 'komentar', 0);
        self::prepocitej($db, (int) $clanek['idc']);
        Cache::vymaz(); // až po skutečném zápisu - odmítnutý spam nesmí držet cache studenou
        $this->upozorniRedakci($clanek, $od, $obsah, $zobrazit);

        return $zpet($zobrazit ? 'ok' : 'ceka');
    }

    /** Počet zveřejněných komentářů článku (rs_clanky.kom). */
    /**
     * E-mail redakci o novém komentáři. Nejvýš jeden za 10 minut - při náporu (nebo spamu) stačí vědět, že je co schvalovat.
     *
     * @param array<string, mixed> $clanek
     */
    private function upozorniRedakci(array $clanek, string $od, string $obsah, bool $zverejnen): void
    {
        $web = $this->app->settings();
        $rezim = $web->get('upozorneni_komentare');
        if ($web->get('email_webu') === '' || $rezim === 'nic' || ($rezim === 'schvaleni' && $zverejnen) || time() - $web->int('upozorneni_cas') < 600) {
            return;
        }
        $web->set('upozorneni_cas', (string) time());
        $ceka = (int) $this->app->db()->value('SELECT COUNT(*) FROM {komentare} WHERE zobrazit = 0');
        \PhpRS\Core\Posta::odesli($web, $web->get('email_webu'), ($zverejnen ? 'Nový komentář' : 'Komentář čeká na schválení') . ' – ' . $web->get('nazev_webu'),
            "Článek: {$clanek['titulek']}\nOd: {$od}\n\n" . mb_strimwidth($obsah, 0, 600, '…') . "\n\n"
            . ($ceka > 0 ? "Ke schválení čeká komentářů: {$ceka}\n" : '')
            . 'Správa komentářů: ' . $this->app->request->origin() . $this->app->request->basePath() . "/admin.php?modul=comment\n\n"
            . "Další upozornění přijde nejdřív za 10 minut. Vypnete je v Nastavení → Základní.\n");
    }

    public static function prepocitej(\PhpRS\Core\Db $db, int $idc): void
    {
        $db->run('UPDATE {clanky} SET kom = (SELECT COUNT(*) FROM {komentare} WHERE clanek = ? AND zobrazit = 1) WHERE idc = ?', [$idc, $idc]);
    }

    /* ---------- hodnocení ---------- */

    /** @param array<string, mixed> $clanek */
    public function hodnoceniHtml(array $clanek): string
    {
        if (!Rozsireni::je($this->app->settings(), 'komentare') || !$this->app->settings()->bool('povolit_hodnoceni')) {
            return '';
        }

        return $this->view->render('hodnoceni', [
            'clanek' => $clanek, 'akce' => $this->app->url('hodnoceni'),
            'prumer' => $clanek['mn_hodnoceni'] > 0 ? $clanek['hodnoceni'] / $clanek['mn_hodnoceni'] : 0.0,
            'hlasoval' => isset($_COOKIE['phprs_h' . $clanek['idc']]) || $this->app->request->get('hodnoceni') !== '',
            'pole' => $this->antispam->pole('hodnoceni-' . $clanek['idc']),
        ]);
    }

    public function ulozHodnoceni(): Response
    {
        $r = $this->app->request;
        $db = $this->app->db();
        $clanek = $db->one('SELECT idc, seo_link FROM {clanky} WHERE idc = ? AND visible = 1 AND datum <= NOW()', [$r->postInt('idc')]);
        $znamka = $r->postInt('znamka');
        if ($clanek === null) {
            return Response::redirect($this->app->url(''));
        }
        $idc = (int) $clanek['idc'];
        $smi = $this->app->settings()->bool('povolit_hodnoceni') && $znamka >= 1 && $znamka <= 5
            && $this->antispam->over($r, 'hodnoceni-' . $idc) === null
            && !isset($_COOKIE['phprs_h' . $idc]) && $this->antispam->pocet($r->ip(), 'hodnoceni', $idc, 60 * 24 * 30) === 0;
        if ($smi) {
            $db->run('UPDATE {clanky} SET hodnoceni = hodnoceni + ?, mn_hodnoceni = mn_hodnoceni + 1 WHERE idc = ?', [$znamka, $idc]);
            Cache::vymaz();
            $this->antispam->zapis($r->ip(), 'hodnoceni', $idc);
        }
        $odpoved = Response::redirect($this->app->url('clanek/' . $clanek['seo_link'] . '?hodnoceni=' . ($smi ? 'ok' : 'uz') . '#hodnoceni'), 303);
        setcookie('phprs_h' . $idc, '1', ['expires' => time() + 86400 * 30, 'path' => '/', 'samesite' => 'Lax', 'httponly' => true]);

        return $odpoved;
    }

    /* ---------- ankety ---------- */

    /** Systémový blok Anketa: aktivní anketa z Nastavení / modulu Ankety. */
    public function anketaHtml(): string
    {
        $db = $this->app->db();
        $anketa = $db->one('SELECT * FROM {ankety} WHERE ida = ? AND zobrazit = 1', [$this->app->settings()->int('aktivni_anketa')]);
        if ($anketa === null) {
            return '';
        }
        $odpovedi = $db->all('SELECT * FROM {odpovedi} WHERE anketa = ? ORDER BY poradi, ido', [$anketa['ida']]);

        return $this->view->render('blok_ank', [
            'anketa' => $anketa, 'odpovedi' => $odpovedi, 'celkem' => (int) array_sum(array_column($odpovedi, 'pocitadlo')),
            'hlasoval' => $anketa['uzavrena'] || isset($_COOKIE['phprs_a' . $anketa['ida']]),
            'akce' => $this->app->url('anketa'), 'pole' => $this->antispam->pole('anketa-' . $anketa['ida']),
            'zpet' => $this->app->request->path(),
        ]);
    }

    public function ulozHlas(): Response
    {
        $r = $this->app->request;
        $db = $this->app->db();
        $ida = $r->postInt('ida');
        $zpet = preg_match('#^/[a-z0-9/_-]*$#i', $r->post('zpet')) ? ltrim($r->post('zpet'), '/') : '';
        $odpoved = $db->one('SELECT o.ido FROM {odpovedi} o JOIN {ankety} a ON a.ida = o.anketa WHERE o.ido = ? AND a.ida = ? AND a.zobrazit = 1 AND a.uzavrena = 0', [$r->postInt('ido'), $ida]);
        if ($odpoved !== null && $this->antispam->over($r, 'anketa-' . $ida) === null
            && !isset($_COOKIE['phprs_a' . $ida]) && $this->antispam->pocet($r->ip(), 'anketa', $ida, 60 * 24 * 30) === 0) {
            $db->run('UPDATE {odpovedi} SET pocitadlo = pocitadlo + 1 WHERE ido = ?', [$odpoved['ido']]);
            Cache::vymaz();
            $this->antispam->zapis($r->ip(), 'anketa', $ida);
        }
        setcookie('phprs_a' . $ida, '1', ['expires' => time() + 86400 * 30, 'path' => '/', 'samesite' => 'Lax', 'httponly' => true]);

        return Response::redirect($this->app->url($zpet), 303);
    }
}
