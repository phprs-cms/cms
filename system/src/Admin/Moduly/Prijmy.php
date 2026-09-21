<?php

declare(strict_types=1);

namespace PhpRS\Admin\Moduly;

use PhpRS\Admin\Modul;
use PhpRS\Core\Response;
use PhpRS\Core\Rozsireni;

/**
 * Příjmy: jedno místo, kde vydavatel vidí, z čeho web žije - předplatné, dobrovolná podpora, reklama a newsletter.
 * Žádná nová logika: každá karta říká, jestli je zdroj zapnutý, ukáže jedno číslo a vede tam, kde se nastavuje.
 */
final class Prijmy extends Modul
{
    public const string IDENT = 'prijmy';
    public const string NAZEV = 'Příjmy';
    public const string SKUPINA = 'Čtenáři';
    public const string IKONA = 'b-srdce';
    public const bool JEN_ADMIN = true;

    protected function akceVypis(): Response
    {
        $web = $this->app->settings();
        $je = static fn (string $rozsireni): bool => Rozsireni::je($web, $rozsireni);
        $url = fn (string $dotaz): string => $this->app->url('admin.php?' . $dotaz);
        $podpora = (int) $this->db->value("SELECT COUNT(*) FROM {bloky} WHERE sys_funkce = 'pod' AND zobrazit = 1");

        $karty = [
            [
                'nazev' => 'Předplatné', 'zapnuto' => $je('ctenari'),
                'cislo' => $je('ctenari') ? (int) $this->db->value('SELECT COUNT(*) FROM {ctenari} WHERE predplatne_do >= CURDATE()') : 0,
                'popisek' => 'čtenářů s platným předplatným',
                'text' => 'Zamčené články čtou jen předplatitelé. Předplatné zatím zapisujete ručně u čtenáře; kde ho čtenář získá, říká adresa v Nastavení → Základní.',
                'odkaz' => $je('ctenari') ? [$url('modul=ctenari'), 'Čtenáři'] : [$url('modul=rozsireni'), 'Zapnout v Rozšířeních'],
                'poznamka' => $je('ctenari') && $web->get('predplatne_url') === '' ? 'Není vyplněno, kde čtenář předplatné získá.' : '',
            ],
            [
                'nazev' => 'Dobrovolná podpora', 'zapnuto' => $podpora > 0,
                'cislo' => $podpora, 'popisek' => 'bloků „Podpořte nás“ na webu',
                'text' => 'Krátká výzva s tlačítkem na platbu nebo na stránku s číslem účtu. Blok přidáte ve vizuálním editoru rozvržení.',
                'odkaz' => [$url('modul=bloky'), 'Bloky a rozvržení'], 'poznamka' => '',
            ],
            [
                'nazev' => 'Reklama', 'zapnuto' => $je('reklama'),
                'cislo' => $je('reklama') ? (int) $this->db->value('SELECT COALESCE(SUM(zobrazeni), 0) FROM {reklama} WHERE aktivni = 1') : 0,
                'popisek' => 'zobrazení aktivních reklam',
                'text' => 'Vlastní reklamní pozice s cílením na rubriky, časovými kampaněmi a výkazem zobrazení a prokliků.',
                'odkaz' => $je('reklama') ? [$url('modul=reklama'), 'Reklama'] : [$url('modul=rozsireni'), 'Zapnout v Rozšířeních'],
                'poznamka' => $je('reklama') ? t('Aktivních reklam: %d, prokliků: %d.', (int) $this->db->value('SELECT COUNT(*) FROM {reklama} WHERE aktivni = 1'), (int) $this->db->value('SELECT COALESCE(SUM(kliky), 0) FROM {reklama} WHERE aktivni = 1')) : '',
            ],
            [
                'nazev' => 'Newsletter', 'zapnuto' => $je('newsletter'),
                'cislo' => $je('newsletter') ? (int) $this->db->value('SELECT COUNT(*) FROM {odberatele} WHERE potvrzen = 1') : 0,
                'popisek' => 'potvrzených odběratelů',
                'text' => 'Sám peníze nepřináší, ale vrací čtenáře na web – a s nimi předplatné, podporu i zobrazení reklam.',
                'odkaz' => $je('newsletter') ? [$url('modul=newsletter'), 'Newsletter'] : [$url('modul=rozsireni'), 'Zapnout v Rozšířeních'],
                'poznamka' => '',
            ],
        ];

        return $this->view('vypis', 'Příjmy', ['karty' => $karty]);
    }
}
