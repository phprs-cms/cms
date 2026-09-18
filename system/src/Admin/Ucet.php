<?php

declare(strict_types=1);

namespace PhpRS\Admin;

use PhpRS\Core\Response;
use PhpRS\Core\Rozsireni;
use PhpRS\Core\Totp;

/**
 * Můj účet: vlastní jméno a e-mail, změna hesla, dvoufázové přihlášení. Dostupné každému přihlášenému.
 */
final class Ucet
{
    public function __construct(private readonly Kernel $kernel)
    {
    }

    public function handle(): Response
    {
        $app = $this->kernel->app;
        $r = $app->request;
        $db = $app->db();
        $user = $app->auth()->user();
        $data = ['zalozniKody' => [], 'noveTajemstvi' => '', 'novyToken' => ''];

        if ($r->isPost()) {
            $hlaska = null;
            switch ($r->postInt('smaz_token') > 0 ? 'token_smaz' : $r->post('co')) {
                case 'profil':
                    if ($r->post('email') !== '' && filter_var($r->post('email'), FILTER_VALIDATE_EMAIL) === false) {
                        $hlaska = ['chyba', 'E-mail nemá platný tvar.'];
                        break;
                    }
                    $db->update('user', ['jmeno' => mb_substr($r->post('jmeno'), 0, 100), 'email' => mb_substr($r->post('email'), 0, 190), 'url' => mb_substr($r->post('url'), 0, 255)], ['idu' => $user['idu']]);
                    $hlaska = ['ok', 'Údaje byly uloženy.'];
                    break;
                case 'heslo':
                    $nove = (string) ($_POST['nove'] ?? '');
                    $hlaska = match (true) {
                        !password_verify((string) ($_POST['soucasne'] ?? ''), $user['password']) => ['chyba', 'Současné heslo není správné.'],
                        mb_strlen($nove) < 10 => ['chyba', 'Nové heslo musí mít alespoň 10 znaků.'],
                        $nove !== (string) ($_POST['nove2'] ?? '') => ['chyba', 'Nová hesla se neshodují.'],
                        default => null,
                    };
                    if ($hlaska === null) {
                        $db->update('user', ['password' => password_hash($nove, PASSWORD_DEFAULT)], ['idu' => $user['idu']]);
                        $hlaska = ['ok', 'Heslo bylo změněno.'];
                    }
                    break;
                case 'token_novy':
                    if (!Rozsireni::je($app->settings(), 'claude')) {
                        break;
                    }
                    $token = 'phprs_' . bin2hex(random_bytes(24));
                    $db->insert('api_tokeny', ['idu' => $user['idu'], 'nazev' => mb_substr($r->post('nazev') ?: 'Claude', 0, 100), 'otisk' => hash('sha256', $token), 'vytvoren' => date('Y-m-d H:i:s')]);
                    Protokol::zapis($app, 'ucet', 'vytvořen token pro Claude');
                    // token se ukazuje jen teď - proto bez přesměrování
                    return $this->stranka(['novyToken' => $token] + $data);
                case 'token_smaz':
                    $db->delete('api_tokeny', ['idt' => $r->postInt('smaz_token'), 'idu' => $user['idu']]);
                    $hlaska = ['ok', 'Token byl zrušen.'];
                    break;
                case 'totp_start':
                    $app->session->set('totp_nove', Totp::noveTajemstvi());
                    break;
                case 'totp_potvrd':
                    $tajemstvi = (string) $app->session->get('totp_nove', '');
                    if ($tajemstvi === '' || !Totp::over($tajemstvi, $r->post('kod'))) {
                        $hlaska = ['chyba', 'Kód nesouhlasí. Zkontrolujte čas v telefonu a zkuste to znovu.'];
                        break;
                    }
                    [$kody, $json] = Totp::zalozniKody();
                    $db->update('user', ['totp_tajemstvi' => $tajemstvi, 'totp_zalozni' => $json], ['idu' => $user['idu']]);
                    $app->session->remove('totp_nove');
                    Protokol::zapis($app, 'ucet', 'zapnuto dvoufázové přihlášení');
                    // záložní kódy se ukazují jen teď - proto bez přesměrování
                    return $this->stranka(['zalozniKody' => $kody] + $data);
                case 'totp_vypni':
                    if (!password_verify((string) ($_POST['soucasne'] ?? ''), $user['password'])) {
                        $hlaska = ['chyba', 'Pro vypnutí zadejte správné heslo.'];
                        break;
                    }
                    $db->update('user', ['totp_tajemstvi' => '', 'totp_zalozni' => null], ['idu' => $user['idu']]);
                    Protokol::zapis($app, 'ucet', 'vypnuto dvoufázové přihlášení');
                    $hlaska = ['ok', 'Dvoufázové přihlášení je vypnuté.'];
                    break;
            }
            if ($hlaska !== null) {
                $app->session->flash(...$hlaska);

                return Response::redirect($app->url('admin.php?akce=ucet'));
            }
        }

        return $this->stranka(['noveTajemstvi' => (string) $app->session->get('totp_nove', '')] + $data);
    }

    /** @param array<string, mixed> $data */
    private function stranka(array $data): Response
    {
        $app = $this->kernel->app;
        $user = $app->db()->one('SELECT * FROM {user} WHERE idu = ?', [$app->auth()->id()]);

        return $this->kernel->page('Můj účet', $app->view->render('admin/ucet', $data + [
            'app' => $app, 'user' => $user, 'csrf' => $app->session->csrfField(),
            'uri' => $data['noveTajemstvi'] !== '' ? Totp::uri($data['noveTajemstvi'], $user['user'], $app->settings()->get('nazev_webu')) : '',
            'zbyvaKodu' => count((array) json_decode((string) $user['totp_zalozni'], true)),
            'claude' => Rozsireni::je($app->settings(), 'claude'),
            'tokeny' => $app->db()->all('SELECT * FROM {api_tokeny} WHERE idu = ? ORDER BY idt DESC', [$user['idu']]),
            'adresaMcp' => $app->request->origin() . $app->url('mcp'),
        ]));
    }
}
