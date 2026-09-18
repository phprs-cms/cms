<?php

declare(strict_types=1);

namespace PhpRS\Admin\Moduly;

use PhpRS\Admin\Kernel;
use PhpRS\Admin\Modul;
use PhpRS\Core\Auth;
use PhpRS\Core\Response;

/**
 * Editace autorů: účty administrace, práva k modulům, právo vydávat a vazby nadřízený - podřízený.
 */
final class Autori extends Modul
{
    public const string IDENT = 'users';
    public const string NAZEV = 'Uživatelé';
    public const string NAZEV_RETRO = 'Editace autorů';
    public const string SKUPINA = 'Správa';
    public const string IKONA = 'uzivatele';
    public const bool JEN_ADMIN = true;

    protected function akceVypis(): Response
    {
        return $this->view('vypis', 'Uživatelé', [
            'autori' => $this->db->all(
                'SELECT u.*, (SELECT COUNT(*) FROM {clanky} c WHERE c.autor = u.idu) AS pocet_clanku FROM {user} u ORDER BY u.user',
            ),
        ]);
    }

    protected function akceNovy(): Response
    {
        return $this->formular(['idu' => 0, 'user' => '', 'jmeno' => '', 'email' => '', 'url' => '', 'admin' => Auth::AUTOR, 'pravo_vydavat' => 0, 'blokovat' => 0]);
    }

    protected function akceEdit(): Response
    {
        $autor = $this->db->one('SELECT * FROM {user} WHERE idu = ?', [$this->request->getInt('id')]);

        return $autor === null ? $this->chyba('Autor neexistuje.', 404) : $this->formular($autor);
    }

    protected function akceUloz(): Response
    {
        if (!$this->request->isPost()) {
            return $this->zpet();
        }
        $r = $this->request;
        $id = $r->postInt('idu');
        $sam = $id === $this->app->auth()->id();
        $data = [
            'user' => $r->post('user'),
            'jmeno' => $r->post('jmeno'),
            'email' => $r->post('email'),
            'url' => $r->post('url'),
            'admin' => array_key_exists($r->postInt('admin'), Auth::TYPY) ? $r->postInt('admin') : Auth::AUTOR,
            'pravo_vydavat' => (int) $r->postBool('pravo_vydavat'),
            'blokovat' => (int) $r->postBool('blokovat'),
        ];
        if ($sam) {
            // admin si nesmí sám sobě vzít práva ani se zablokovat - zamkl by si administraci
            $data['admin'] = Auth::ADMIN;
            $data['blokovat'] = 0;
        }
        if (!$data['blokovat']) {
            $data['pocet_chyb'] = 0;
        }

        $chyby = [];
        if (!preg_match('/^[a-zA-Z0-9._-]{2,40}$/', $data['user'])) {
            $chyby['user'] = 'Přihlašovací jméno: 2-40 znaků, jen písmena bez diakritiky, číslice, tečka, pomlčka a podtržítko.';
        } elseif ($this->db->value('SELECT idu FROM {user} WHERE user = ? AND idu <> ?', [$data['user'], $id]) !== null) {
            $chyby['user'] = 'Toto přihlašovací jméno už používá jiný autor.';
        }
        if ($data['email'] !== '' && filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
            $chyby['email'] = 'E-mail nemá platný tvar.';
        }
        $heslo = $r->post('password');
        if ($heslo !== '' || $id === 0) {
            if (mb_strlen($heslo) < 10) {
                $chyby['password'] = 'Heslo musí mít alespoň 10 znaků.';
            } elseif ($heslo !== $r->post('password2')) {
                $chyby['password'] = 'Hesla se neshodují.';
            } else {
                $data['password'] = password_hash($heslo, PASSWORD_DEFAULT);
            }
        }
        if ($chyby !== []) {
            return $this->formular(['idu' => $id] + $data, $chyby);
        }

        $moduly = array_intersect($r->postList('moduly'), array_map(fn (string $c): string => $c::IDENT, Kernel::MODULY));
        $podrizeni = array_filter(array_map(intval(...), $r->postList('podrizeni')), fn (int $p): bool => $p > 0 && $p !== $id);

        $this->db->transaction(function () use (&$id, $data, $moduly, $podrizeni): void {
            if ($id > 0) {
                $this->db->update('user', $data, ['idu' => $id]);
            } else {
                $id = $this->db->insert('user', $data);
            }
            $this->db->delete('user_prava', ['fk_id_user' => $id]);
            foreach ($moduly as $ident) {
                $this->db->insert('user_prava', ['fk_id_user' => $id, 'ident_modulu' => $ident]);
            }
            $this->db->delete('vazby_prava', ['fk_id_nadrizeny' => $id]);
            foreach ($podrizeni as $p) {
                $this->db->insert('vazby_prava', ['fk_id_nadrizeny' => $id, 'fk_id_podrizeny' => $p]);
            }
        });

        return $this->zpet('Autor byl uložen.');
    }

    protected function akceSmaz(): Response
    {
        if (!$this->request->isPost()) {
            return $this->zpet();
        }
        $id = $this->request->postInt('idu');
        if ($id === $this->app->auth()->id()) {
            return $this->zpet('Nemůžete smazat sám sebe.', typ: 'chyba');
        }
        $this->db->delete('user', ['idu' => $id]);

        return $this->zpet('Autor byl smazán. Jeho články zůstaly zachovány bez autora.');
    }

    /**
     * @param array<string, mixed> $autor
     * @param array<string, string> $chyby
     */
    private function formular(array $autor, array $chyby = []): Response
    {
        $id = (int) $autor['idu'];
        $nastavitelne = [];
        foreach (Kernel::MODULY as $class) {
            if (!$class::JEN_ADMIN && !$class::PRO_VSECHNY) {
                $nastavitelne[$class::IDENT] = $class::NAZEV;
            }
        }

        return $this->view('formular', $id ? 'Úprava uživatele' : 'Nový uživatel', [
            'autor' => $autor,
            'chyby' => $chyby,
            'sam' => $id === $this->app->auth()->id(),
            'moduly' => $nastavitelne,
            'maModuly' => $this->request->isPost()
                ? $this->request->postList('moduly')
                : array_column($this->db->all('SELECT ident_modulu FROM {user_prava} WHERE fk_id_user = ?', [$id]), 'ident_modulu'),
            'ostatni' => $this->db->pairs('SELECT idu, IF(jmeno = \'\', user, CONCAT(jmeno, \' (\', user, \')\')) FROM {user} WHERE idu <> ? ORDER BY user', [$id]),
            'maPodrizene' => $this->request->isPost()
                ? array_map(intval(...), $this->request->postList('podrizeni'))
                : array_map(intval(...), array_column($this->db->all('SELECT fk_id_podrizeny FROM {vazby_prava} WHERE fk_id_nadrizeny = ?', [$id]), 'fk_id_podrizeny')),
        ]);
    }
}
