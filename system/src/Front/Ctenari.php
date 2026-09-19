<?php

declare(strict_types=1);

namespace PhpRS\Front;

use PhpRS\Core\Antispam;
use PhpRS\Core\App;
use PhpRS\Core\Posta;
use PhpRS\Core\Response;
use PhpRS\Core\View;

/**
 * Účty čtenářů a uzamčený obsah (rozšíření "ctenari").
 *
 *   /ctenar              přihlášení + registrace, po přihlášení Můj účet
 *   /ctenar/potvrdit/<token>, /ctenar/heslo/<token>
 *
 * Přihlášení drží podepsaná cookie phprs_ctenar (id.platnost.podpis) - bez PHP session, aby web zůstal
 * pro nepřihlášené cachovatelný. Cookie s předponou phprs_c cache stránek vypíná (Front\Cache).
 */
final class Ctenari
{
    public const string COOKIE = 'phprs_ctenar';
    public const string COOKIE_CTENO = 'phprs_cteno';
    public const array PRISTUP = [0 => 'Všichni', 1 => 'Jen přihlášení čtenáři', 2 => 'Jen předplatitelé'];
    private const int PLATNOST = 60 * 86400;

    /** @var array<string, mixed>|false|null */
    private array|false|null $ctenar = null;
    private bool $meri = false;

    /** @var array{precteno:int, limit:int, vycerpano:bool}|null */
    private ?array $stavZdarma = null;

    public function __construct(private readonly App $app)
    {
    }

    /** @return array<string, mixed>|null přihlášený čtenář */
    public function prihlaseny(): ?array
    {
        if ($this->ctenar === null) {
            $this->ctenar = false;
            $casti = explode('.', (string) ($_COOKIE[self::COOKIE] ?? ''));
            if (count($casti) === 3 && ctype_digit($casti[0]) && ctype_digit($casti[1]) && (int) $casti[1] > time()) {
                $ctenar = $this->app->db()->one('SELECT * FROM {ctenari} WHERE idct = ? AND potvrzen = 1', [(int) $casti[0]]);
                // v podpisu je i otisk hesla: změna hesla odhlásí všechna ostatní zařízení
                if ($ctenar !== null && hash_equals($this->podpis($casti[0] . '.' . $casti[1] . '.' . $ctenar['heslo']), $casti[2])) {
                    $this->ctenar = $ctenar;
                }
            }
        }

        return $this->ctenar ?: null;
    }

    public function jePredplatitel(): bool
    {
        $c = $this->prihlaseny();

        return $c !== null && $c['predplatne_do'] !== null && $c['predplatne_do'] >= date('Y-m-d');
    }

    /** Smí návštěvník číst celý článek? Redakce přihlášená v administraci vidí vše. */
    public function smiCist(array $clanek): bool
    {
        $smi = match (true) {
            (int) $clanek['pristup'] === 0, $this->app->auth()->user() !== null => true,
            (int) $clanek['pristup'] === 1 => $this->prihlaseny() !== null,
            default => $this->jePredplatitel(),
        };

        return $smi || ($this->meri && $this->zdarma((int) $clanek['idc']));
    }

    /** Měkký paywall se počítá jen na stránce článku, ne ve výpisech - Kernel ho zapíná kolem načtení článku. */
    public function meritClanek(bool $ano): void
    {
        $this->meri = $ano;
    }

    /** Kolik zamčených článků zdarma už čtenář tento měsíc otevřel a kolik jich má; null = měkký paywall je vypnutý nebo se nepoužil. */
    public function stavZdarma(): ?array
    {
        return $this->stavZdarma;
    }

    /**
     * Měkký paywall: pár zamčených článků měsíčně zdarma. Počítá podepsaná cookie phprs_cteno (měsíc + čísla článků);
     * kdo ji smaže, začíná znovu - to je u měkkého paywallu záměr, ne chyba. Vyhledávače cookie neposílají a vidí celý text.
     */
    private function zdarma(int $idc): bool
    {
        $limit = $this->app->settings()->int('paywall_zdarma');
        if ($limit <= 0) {
            return false;
        }
        $mesic = date('Ym');
        $prectene = [];
        $casti = explode('.', (string) ($_COOKIE[self::COOKIE_CTENO] ?? ''));
        if (count($casti) === 3 && $casti[0] === $mesic && hash_equals($this->podpis('cteno.' . $casti[0] . '.' . $casti[1]), $casti[2])) {
            $prectene = array_slice(array_map(intval(...), array_filter(explode('-', $casti[1]), ctype_digit(...))), 0, 50);
        }
        if (!in_array($idc, $prectene, true)) {
            if (count($prectene) >= $limit) {
                $this->stavZdarma = ['precteno' => count($prectene), 'limit' => $limit, 'vycerpano' => true];

                return false;
            }
            $prectene[] = $idc;
            $seznam = implode('-', $prectene);
            setcookie(self::COOKIE_CTENO, $mesic . '.' . $seznam . '.' . $this->podpis('cteno.' . $mesic . '.' . $seznam), [
                'expires' => time() + 40 * 86400, 'path' => $this->app->request->basePath() . '/', 'secure' => $this->app->request->isHttps(), 'httponly' => true, 'samesite' => 'Lax',
            ]);
        }
        $this->stavZdarma = ['precteno' => count($prectene), 'limit' => $limit, 'vycerpano' => false];

        return true;
    }

    /**
     * Zamčenému článku nechá jen ukázku textu. Volá se z Front\Clanky::priprav(), takže celý text
     * neunikne ani přes RSS, feed.json, API nebo /clanek/<adresa>.md.
     *
     * @param array<string, mixed> $clanek
     * @return array<string, mixed>
     */
    public function zamkni(array $clanek): array
    {
        $clanek['zamceno'] = !$this->smiCist($clanek);
        if ($clanek['zamceno']) {
            $ukazka = $this->ukazka($clanek['text']);
            $clanek['text'] = $ukazka === '' ? '' : '<div class="rs-ukazka">' . $ukazka . '</div>';
            $clanek['faq'] = '';
        }

        return $clanek;
    }

    /** Ukázka zamčeného článku: prvních pár odstavců textu. */
    public function ukazka(string $html): string
    {
        $pocet = max(0, $this->app->settings()->int('zamek_odstavcu'));
        if ($pocet === 0 || !preg_match_all('#<p\b[^>]*>.*?</p>#is', $html, $m)) {
            return '';
        }

        return implode("\n", array_slice($m[0], 0, $pocet));
    }

    /** Výzva pod ukázkou zamčeného článku. */
    public function zamekHtml(array $clanek, View $view): string
    {
        return $view->render('zamek', [
            'predplatne' => (int) $clanek['pristup'] === 2,
            'prihlasen' => $this->prihlaseny() !== null,
            'text' => $this->app->settings()->get('zamek_text'),
            'ucet' => $this->app->url('ctenar') . '?zpet=' . rawurlencode('clanek/' . $clanek['seo_link']),
            'registrace' => $this->app->settings()->bool('ctenari_registrace'),
            'zdarma' => $this->stavZdarma,
        ]);
    }

    /**
     * Obsluha adres /ctenar…
     *
     * @return Response|array{0:string, 1:string} hotová odpověď (přesměrování), nebo [titulek, HTML obsahu stránky]
     */
    public function handle(string $path, View $view): Response|array
    {
        $r = $this->app->request;
        if (preg_match('#^/ctenar/potvrdit/([a-f0-9]{32})$#', $path, $m)) {
            $ctenar = $this->app->db()->one('SELECT * FROM {ctenari} WHERE token = ?', [$m[1]]);
            if ($ctenar === null) {
                return $this->zprava($view, 'Odkaz neplatí', 'Účet už je nejspíš potvrzený – zkuste se přihlásit.');
            }
            $this->app->db()->update('ctenari', ['potvrzen' => 1, 'token' => bin2hex(random_bytes(16))], ['idct' => $ctenar['idct']]);
            $this->prihlas((int) $ctenar['idct']);

            return Response::redirect($this->app->url('ctenar') . '?stav=vitejte', 303);
        }
        if (preg_match('#^/ctenar/heslo/([a-f0-9]{32})$#', $path, $m)) {
            return $this->noveHeslo($m[1], $view);
        }
        if ($path !== '/ctenar') {
            return $this->zprava($view, 'Stránka nenalezena', 'Tahle adresa neexistuje.');
        }
        if ($r->isPost()) {
            Cache::vymaz();

            return match ($r->post('akce')) {
                'registrace' => $this->registrace(),
                'prihlaseni' => $this->prihlaseni(),
                'zapomenute' => $this->zapomenute(),
                'odhlasit' => $this->odhlas(),
                'ucet' => $this->ulozUcet(),
                'smazat' => $this->smazUcet(),
                default => Response::redirect($this->app->url('ctenar'), 303),
            };
        }

        $ctenar = $this->prihlaseny();
        $antispam = new Antispam($this->app->db(), $this->app->settings());

        return [t($ctenar === null ? 'Přihlášení čtenáře' : 'Můj účet'), $view->render('ctenar', [
            'ctenar' => $ctenar,
            'predplatitel' => $this->jePredplatitel(),
            'akce' => $this->app->url('ctenar'),
            'zpet' => $this->zpet($r->get('zpet')),
            'stav' => $r->get('stav'),
            'pole' => $ctenar === null ? $antispam->pole('ctenar') : '',
            'podpis' => $ctenar === null ? '' : $this->podpis('formular.' . $ctenar['idct']),
            'registrace' => $this->app->settings()->bool('ctenari_registrace'),
            'url' => $this->app->url(...),
        ])];
    }

    private function registrace(): Response
    {
        $r = $this->app->request;
        $db = $this->app->db();
        $email = mb_strtolower(trim(mb_substr($r->post('email'), 0, 190)));
        if (!$this->app->settings()->bool('ctenari_registrace')) {
            return $this->na('zavreno');
        }
        if (($chyba = $this->overFormular()) !== null) {
            return $chyba;
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || mb_strlen($r->post('heslo')) < 8) {
            return $this->na('udaje');
        }
        $web = $this->app->settings();
        $existujici = $db->one('SELECT * FROM {ctenari} WHERE email = ?', [$email]);
        if ($existujici !== null && $existujici['potvrzen']) {
            // odpověď webu nesmí prozradit, že e-mail už účet má - majitel se to dozví e-mailem
            Posta::odesli($web, $email, 'Účet už máte – ' . $web->get('nazev_webu'),
                "Dobrý den,\n\nna webu {$web->get('nazev_webu')} se někdo pokusil znovu zaregistrovat váš e-mail. Účet už máte – stačí se přihlásit:\n"
                . $r->origin() . $this->app->url('ctenar') . "\n\nPokud jste zapomněli heslo, na stejné stránce si necháte poslat odkaz pro nové.\n");

            return $this->na('poslano');
        }
        $token = bin2hex(random_bytes(16));
        $data = ['jmeno' => mb_substr(trim($r->post('jmeno')), 0, 80), 'heslo' => password_hash($r->post('heslo'), PASSWORD_DEFAULT), 'token' => $token];
        if ($existujici === null) {
            $db->insert('ctenari', $data + ['email' => $email, 'vytvoren' => date('Y-m-d H:i:s')]);
        } else {
            $db->update('ctenari', $data, ['idct' => $existujici['idct']]);
        }
        Posta::odesli($web, $email, 'Potvrďte registraci – ' . $web->get('nazev_webu'),
            "Dobrý den,\n\nregistraci na webu {$web->get('nazev_webu')} dokončíte kliknutím na tento odkaz:\n"
            . $r->origin() . $this->app->url('ctenar/potvrdit/' . $token) . "\n\nPokud jste se neregistrovali, e-mail ignorujte.\n");

        return $this->na('poslano');
    }

    private function prihlaseni(): Response
    {
        $r = $this->app->request;
        if (($chyba = $this->overFormular()) !== null) {
            return $chyba;
        }
        $ctenar = $this->app->db()->one('SELECT * FROM {ctenari} WHERE email = ? AND potvrzen = 1', [mb_strtolower(trim($r->post('email')))]);
        // hash se ověřuje i pro neexistující účet, aby doba odpovědi neprozradila, které e-maily jsou registrované
        $hash = $ctenar['heslo'] ?? '$2y$12$.rGL5BCVuy.khmu9ByuEeuXls/1.SsuwX7g78BdiBRuq./37G0q1q';
        if (!password_verify($r->post('heslo'), $hash) || $ctenar === null) {
            return $this->na('spatne');
        }
        $this->prihlas((int) $ctenar['idct']);
        $zpet = $this->zpet($r->post('zpet'));

        return Response::redirect($zpet !== '' ? $this->app->url($zpet) : $this->app->url('ctenar'), 303);
    }

    private function zapomenute(): Response
    {
        $r = $this->app->request;
        if (($chyba = $this->overFormular()) !== null) {
            return $chyba;
        }
        $ctenar = $this->app->db()->one('SELECT * FROM {ctenari} WHERE email = ? AND potvrzen = 1', [mb_strtolower(trim($r->post('email')))]);
        if ($ctenar !== null) {
            $token = bin2hex(random_bytes(16));
            $this->app->db()->update('ctenari', ['token' => $token, 'token_cas' => date('Y-m-d H:i:s')], ['idct' => $ctenar['idct']]);
            $web = $this->app->settings();
            Posta::odesli($web, $ctenar['email'], 'Nové heslo – ' . $web->get('nazev_webu'),
                "Dobrý den,\n\nnové heslo k účtu na webu {$web->get('nazev_webu')} si nastavíte tady (odkaz platí 2 hodiny):\n"
                . $r->origin() . $this->app->url('ctenar/heslo/' . $token) . "\n\nPokud jste o nové heslo nežádali, e-mail ignorujte.\n");
        }

        return $this->na('heslo-poslano');
    }

    /** @return Response|array{0:string, 1:string} */
    private function noveHeslo(string $token, View $view): Response|array
    {
        $db = $this->app->db();
        $ctenar = $db->one('SELECT * FROM {ctenari} WHERE token = ? AND token_cas > NOW() - INTERVAL 2 HOUR', [$token]);
        if ($ctenar === null) {
            return $this->zprava($view, 'Odkaz už neplatí', 'Nechte si prosím poslat nový odkaz ze stránky přihlášení.');
        }
        $r = $this->app->request;
        if ($r->isPost() && mb_strlen($r->post('heslo')) >= 8) {
            $db->update('ctenari', ['heslo' => password_hash($r->post('heslo'), PASSWORD_DEFAULT), 'token' => bin2hex(random_bytes(16)), 'token_cas' => null], ['idct' => $ctenar['idct']]);
            $this->prihlas((int) $ctenar['idct']);

            return Response::redirect($this->app->url('ctenar') . '?stav=heslo-zmeneno', 303);
        }

        return [t('Nové heslo'), $view->render('ctenar_heslo', ['akce' => $this->app->url('ctenar/heslo/' . $token), 'chyba' => $r->isPost()])];
    }

    private function ulozUcet(): Response
    {
        $r = $this->app->request;
        $ctenar = $this->overPrihlaseneho();
        if ($ctenar === null) {
            return $this->na('');
        }
        $data = ['jmeno' => mb_substr(trim($r->post('jmeno')), 0, 80)];
        if ($r->post('heslo') !== '') {
            if (mb_strlen($r->post('heslo')) < 8 || !password_verify($r->post('heslo_stare'), $ctenar['heslo'])) {
                return $this->na('heslo-chyba');
            }
            $data['heslo'] = password_hash($r->post('heslo'), PASSWORD_DEFAULT);
        }
        $this->app->db()->update('ctenari', $data, ['idct' => $ctenar['idct']]);
        if (isset($data['heslo'])) {
            $this->prihlas((int) $ctenar['idct']);
        }

        return $this->na('ulozeno');
    }

    private function smazUcet(): Response
    {
        $ctenar = $this->overPrihlaseneho();
        if ($ctenar === null || !password_verify($this->app->request->post('heslo'), $ctenar['heslo'])) {
            return $this->na('heslo-chyba');
        }
        $this->app->db()->delete('ctenari', ['idct' => $ctenar['idct']]);
        $this->cookie('', 1);

        return $this->na('smazano');
    }

    private function odhlas(): Response
    {
        $this->cookie('', 1);

        return Response::redirect($this->app->url(''), 303);
    }

    /** Formuláře přihlášeného čtenáře chrání podpis vázaný na jeho účet (náhrada CSRF tokenu bez session). */
    private function overPrihlaseneho(): ?array
    {
        $ctenar = $this->prihlaseny();

        return $ctenar !== null && hash_equals($this->podpis('formular.' . $ctenar['idct']), $this->app->request->post('podpis')) ? $ctenar : null;
    }

    private function overFormular(): ?Response
    {
        $r = $this->app->request;
        $antispam = new Antispam($this->app->db(), $this->app->settings());
        $duvod = $antispam->over($r, 'ctenar');
        if ($duvod !== null || $antispam->pocet($r->ip(), 'ctenar', 0, 15) >= 10) {
            return $this->na($duvod === 'robot' ? 'poslano' : 'pomalu');
        }
        $antispam->zapis($r->ip(), 'ctenar', 0);

        return null;
    }

    private function prihlas(int $id): void
    {
        $platnost = time() + self::PLATNOST;
        $heslo = (string) $this->app->db()->value('SELECT heslo FROM {ctenari} WHERE idct = ?', [$id]);
        $this->cookie($id . '.' . $platnost . '.' . $this->podpis($id . '.' . $platnost . '.' . $heslo), $platnost);
        $this->app->db()->update('ctenari', ['naposledy' => date('Y-m-d H:i:s')], ['idct' => $id]);
    }

    private function cookie(string $hodnota, int $platnost): void
    {
        setcookie(self::COOKIE, $hodnota, [
            'expires' => $platnost,
            'path' => $this->app->request->basePath() . '/',
            'secure' => $this->app->request->isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    private function podpis(string $data): string
    {
        return hash_hmac('sha256', 'ctenar|' . $data, (new Antispam($this->app->db(), $this->app->settings()))->klic());
    }

    /** Návrat po přihlášení jen na vlastní web. */
    private function zpet(string $cesta): string
    {
        return preg_match('#^[a-z0-9][a-z0-9/_-]*$#i', $cesta) ? $cesta : '';
    }

    private function na(string $stav): Response
    {
        $zpet = $this->zpet($this->app->request->post('zpet'));

        return Response::redirect($this->app->url('ctenar') . '?' . http_build_query(array_filter(['stav' => $stav, 'zpet' => $zpet])), 303);
    }

    /** @return array{0:string, 1:string} */
    private function zprava(View $view, string $nadpis, string $text): array
    {
        return [$nadpis, $view->render('zprava', ['nadpis' => $nadpis, 'text' => $text, 'url' => $this->app->url(...)])];
    }
}
