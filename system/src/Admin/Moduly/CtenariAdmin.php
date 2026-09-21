<?php

declare(strict_types=1);

namespace PhpRS\Admin\Moduly;

use PhpRS\Admin\Modul;
use PhpRS\Core\Response;

/**
 * Registrovaní čtenáři: přehled, zápis předplatného, odstranění účtu, export.
 * Platební brána v systému záměrně není - předplatné (datum "do") zapisuje administrátor ručně.
 */
final class CtenariAdmin extends Modul
{
    public const string IDENT = 'ctenari';
    public const string NAZEV = 'Čtenáři';
    public const string SKUPINA = 'Čtenáři';
    public const string IKONA = 'ctenari';
    public const string ROZSIRENI = 'ctenari';
    public const bool JEN_ADMIN = true;

    protected function akceVypis(): Response
    {
        $q = mb_substr($this->request->get('q'), 0, 100);
        $kde = $q === '' ? '1 = 1' : '(email LIKE ? OR jmeno LIKE ?)';
        $like = '%' . addcslashes($q, '%_\\') . '%';
        if ($this->request->get('format') === 'csv') {
            // hodnoty od čtenářů: bez konců řádků a bez možnosti spustit vzorec v tabulkovém programu (= + - @)
            $bunka = fn (string $v): string => '"' . str_replace('"', '""', preg_replace('/^[=+\-@\t]/', "'$0", str_replace(["\r", "\n"], ' ', $v)) ?? '') . '"';
            $radky = array_map(fn (array $c): string => implode(';', [$bunka($c['email']), $bunka($c['jmeno']), $c['vytvoren'], (string) $c['predplatne_do']]), $this->db->all('SELECT * FROM {ctenari} WHERE potvrzen = 1 ORDER BY email'));

            return new Response("email;jmeno;registrace;predplatne_do\n" . implode("\n", $radky), 200, ['Content-Type' => 'text/csv; charset=utf-8', 'Content-Disposition' => 'attachment; filename="ctenari.csv"']);
        }

        return $this->view('vypis', 'Čtenáři', [
            'q' => $q,
            'ctenari' => $this->db->all("SELECT * FROM {ctenari} WHERE {$kde} ORDER BY idct DESC LIMIT 300", $q === '' ? [] : [$like, $like]),
            'pocty' => [
                'Registrovaní' => (int) $this->db->value('SELECT COUNT(*) FROM {ctenari} WHERE potvrzen = 1'),
                'Předplatitelé' => (int) $this->db->value('SELECT COUNT(*) FROM {ctenari} WHERE potvrzen = 1 AND predplatne_do >= CURDATE()'),
                'Zamčené články' => (int) $this->db->value('SELECT COUNT(*) FROM {clanky} WHERE pristup > 0'),
            ],
        ]);
    }

    /** Předplatné: o kolik měsíců prodloužit (od dneška, nebo od konce běžícího), případně konkrétní datum či zrušení. */
    protected function akcePredplatne(): Response
    {
        $ctenar = $this->db->one('SELECT * FROM {ctenari} WHERE idct = ?', [$this->request->postInt('idct')]);
        if (!$this->request->isPost() || $ctenar === null) {
            return $this->zpet();
        }
        $volba = $this->request->post('volba');
        $do = match (true) {
            $volba === 'zrusit' => null,
            in_array($volba, ['1', '3', '12'], true) => date('Y-m-d', strtotime('+' . $volba . ' month', max(time(), (int) strtotime((string) $ctenar['predplatne_do'])))),
            (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->request->post('datum')) => $this->request->post('datum'),
            default => $ctenar['predplatne_do'],
        };
        $this->db->update('ctenari', ['predplatne_do' => $do], ['idct' => $ctenar['idct']]);

        return $this->zpet($do === null ? 'Předplatné bylo zrušeno.' : 'Předplatné platí do ' . datum($do) . '.');
    }

    protected function akceSmaz(): Response
    {
        if ($this->request->isPost()) {
            $this->db->delete('ctenari', ['idct' => $this->request->postInt('idct')]);
        }

        return $this->zpet('Účet čtenáře byl smazán.');
    }
}
