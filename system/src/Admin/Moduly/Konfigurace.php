<?php

declare(strict_types=1);

namespace PhpRS\Admin\Moduly;

use PhpRS\Admin\Kernel;
use PhpRS\Admin\Modul;
use PhpRS\Core\Response;
use PhpRS\Core\Stav;
use PhpRS\Front\Layouty;

/**
 * Nastavení webu (tabulka rs_config) rozdělené do záložek.
 * Každá záložka má šablonu views/admin/config/<zalozka>.php a seznam polí s typem - podle něj se hodnoty čistí.
 */
final class Konfigurace extends Modul
{
    public const string IDENT = 'config';
    public const string NAZEV = 'Nastavení';
    public const string NAZEV_RETRO = 'Konfigurace';
    public const string SKUPINA = 'Správa';
    public const string IKONA = 'nastaveni';
    public const bool JEN_ADMIN = true;

    public const array ZALOZKY = [
        'zakladni' => 'Základní', 'vzhled' => 'Vzhled', 'seo' => 'SEO a GEO',
        'mereni' => 'Měření', 'cookies' => 'Soukromí a cookies', 'stav' => 'Stav systému',
    ];

    public const array SITE = ['soc_facebook' => 'Facebook', 'soc_instagram' => 'Instagram', 'soc_x' => 'X (Twitter)', 'soc_youtube' => 'YouTube', 'soc_linkedin' => 'LinkedIn'];

    /**
     * Pole jednotlivých záložek: klíč v rs_config => typ.
     * text | radky (víceřádkový text) | kod (HTML/JS - zadává jen administrátor) | url | email | ano | cislo:min:max | vyber:a|b | vzor:/regex/
     */
    private const array POLE = [
        'zakladni' => [
            'nazev_webu' => 'text', 'popis_webu' => 'radky', 'klicova_slova' => 'text', 'email_webu' => 'email', 'text_paticky' => 'text',
            'soc_facebook' => 'url', 'soc_instagram' => 'url', 'soc_x' => 'url', 'soc_youtube' => 'url', 'soc_linkedin' => 'url',
            'pocet_clanku' => 'cislo:1:100', 'pocet_novinek' => 'cislo:0:50', 'hlidat_platnost' => 'ano', 'povolit_komentare' => 'ano',
        ],
        'vzhled' => ['logo_webu' => 'text', 'prostredi_admin' => 'vyber:retro|2026'],
        'seo' => [
            'indexovani' => 'ano', 'schema_org' => 'ano', 'og_obrazek' => 'text', 'overeni_google' => 'vzor:/^[A-Za-z0-9_-]{0,100}$/',
            'overeni_bing' => 'vzor:/^[A-Za-z0-9]{0,64}$/', 'robots_extra' => 'radky', 'ai_crawlery' => 'vyber:povolit|zakazat', 'llms_txt' => 'ano', 'markdown_clanky' => 'ano', 'indexnow' => 'ano',
        ],
        'mereni' => [
            'ga4_id' => 'vzor:/^(G-[A-Z0-9]{4,20})?$/', 'matomo_url' => 'url', 'matomo_id' => 'cislo:0:99999',
            'plausible_domena' => 'vzor:/^([a-z0-9.-]{3,100})?$/', 'kod_hlava' => 'kod',
        ],
        'cookies' => ['cookies_rezim' => 'vyber:zadna|vestavena|externi', 'cookies_externi_kod' => 'kod', 'cookies_text' => 'radky', 'cookies_zasady_url' => 'text', 'kod_marketing' => 'kod', 'cookies_evidence' => 'ano'],
        'stav' => ['stav_token' => 'vzor:/^[A-Za-z0-9]{0,64}$/'],
    ];

    protected function akceVypis(): Response
    {
        $zalozka = $this->zalozka($this->request->get('zalozka'));
        $nastaveni = $this->app->settings();
        $hodnoty = [];
        foreach (array_keys(self::POLE[$zalozka]) as $klic) {
            $hodnoty[$klic] = $nastaveni->get($klic);
        }

        return $this->view('vypis', 'Nastavení', [
            'zalozka' => $zalozka,
            'hodnoty' => $hodnoty + ['layout' => $nastaveni->get('layout')],
            'layouty' => Layouty::seznam(),
            'prostredi' => Kernel::PROSTREDI,
            'kontroly' => $zalozka === 'stav' ? Stav::kontroly($this->app) : [],
            'adresaWebu' => $this->app->request->origin() . $this->app->url(''),
            'souhlasy' => $zalozka === 'cookies' ? $this->db->all("SELECT kategorie, COUNT(*) AS pocet FROM {souhlasy} WHERE cas > NOW() - INTERVAL 30 DAY GROUP BY kategorie ORDER BY pocet DESC") : [],
        ]);
    }

    protected function akceUloz(): Response
    {
        $zalozka = $this->zalozka($this->request->post('zalozka'));
        if (!$this->request->isPost()) {
            return $this->zpet();
        }
        $nastaveni = $this->app->settings();
        $chyby = [];
        foreach (self::POLE[$zalozka] as $klic => $typ) {
            // "kod" se neořezává ani jinak neupravuje - je to HTML/JS vložené administrátorem
            $hodnota = $typ === 'kod' ? (string) ($_POST[$klic] ?? '') : $this->request->post($klic);
            $cista = self::vycisti($typ, $hodnota, $this->request->postBool($klic));
            if ($cista === null) {
                $chyby[] = $klic;
                continue;
            }
            $nastaveni->set($klic, $cista);
        }
        if ($zalozka === 'vzhled') {
            $layouty = Layouty::seznam();
            $layout = $this->request->post('layout');
            if (isset($layouty[$layout]) && $layout !== $nastaveni->get('layout')) {
                // nová šablona webu přináší i rozvržení stránky, které jí sluší; změnit ho lze v Blocích
                $nastaveni->set('layout', $layout);
                $nastaveni->set('rozvrzeni', $layouty[$layout]['rozvrzeni']);
            }
        }
        if ($zalozka === 'seo' && $nastaveni->bool('indexnow') && $nastaveni->get('indexnow_klic') === '') {
            $nastaveni->set('indexnow_klic', bin2hex(random_bytes(16)));
        }
        if ($this->request->postBool('novy_token')) {
            $nastaveni->set('stav_token', bin2hex(random_bytes(16)));
        }

        return $chyby === []
            ? $this->zpet('Nastavení bylo uloženo.', '', ['zalozka' => $zalozka])
            : $this->zpet('Některé hodnoty nemají platný tvar a nebyly uloženy: ' . implode(', ', $chyby) . '.', '', ['zalozka' => $zalozka], 'chyba');
    }

    /** Zkušební e-mail na adresu redakce - ověří, že server umí odesílat poštu. */
    protected function akceTestPosty(): Response
    {
        $komu = $this->app->settings()->get('email_webu');
        if (!$this->request->isPost() || $komu === '') {
            return $this->zpet('Nejprve vyplňte E-mail redakce v záložce Základní.', '', ['zalozka' => 'stav'], 'chyba');
        }
        $web = $this->app->settings()->get('nazev_webu');
        $ok = function_exists('mail') && @mail(
            $komu,
            '=?UTF-8?B?' . base64_encode('Zkušební zpráva z ' . $web) . '?=',
            "Dobrý den,\n\ntato zpráva potvrzuje, že web {$web} umí odesílat e-maily.\n\nphpRS " . PHPRS_VERSION,
            "Content-Type: text/plain; charset=utf-8\r\nFrom: {$komu}",
        );

        return $this->zpet(
            $ok ? "Zpráva byla předána k odeslání na {$komu}. Pokud nedorazí, zkontrolujte spam a nastavení pošty u hostingu." : 'Server zprávu odmítl odeslat (funkce mail() selhala).',
            '',
            ['zalozka' => 'stav'],
            $ok ? 'ok' : 'chyba',
        );
    }

    private function zalozka(string $zalozka): string
    {
        return isset(self::ZALOZKY[$zalozka]) ? $zalozka : 'zakladni';
    }

    /** @return string|null vyčištěná hodnota, null = neplatná */
    private static function vycisti(string $typ, string $hodnota, bool $zaskrtnuto): ?string
    {
        [$druh, $parametr] = explode(':', $typ, 2) + [1 => ''];

        return match ($druh) {
            'ano' => $zaskrtnuto ? '1' : '0',
            'text' => mb_substr(str_replace(["\r", "\n"], ' ', $hodnota), 0, 500),
            'radky' => mb_substr($hodnota, 0, 5000),
            'kod' => mb_substr($hodnota, 0, 20000),
            'email' => $hodnota === '' || filter_var($hodnota, FILTER_VALIDATE_EMAIL) ? $hodnota : null,
            'url' => $hodnota === '' || (preg_match('#^https?://#i', $hodnota) && filter_var($hodnota, FILTER_VALIDATE_URL)) ? rtrim($hodnota) : null,
            'cislo' => (function () use ($hodnota, $parametr): string {
                [$min, $max] = array_map(intval(...), explode(':', $parametr));

                return (string) max($min, min($max, (int) $hodnota));
            })(),
            'vyber' => in_array($hodnota, explode('|', $parametr), true) ? $hodnota : null,
            'vzor' => preg_match($parametr, $hodnota) ? $hodnota : null,
            default => null,
        };
    }
}
