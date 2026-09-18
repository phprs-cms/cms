<?php

declare(strict_types=1);

namespace PhpRS\Core;

/**
 * Nastavení webu z tabulky rs_config (promenna => hodnota), stejně jako v phpRS 2.
 */
final class Settings
{
    /** Výchozí hodnoty; zároveň seznam všech známých proměnných. */
    public const array DEFAULTS = [
        'nazev_webu' => 'Můj magazín',
        'popis_webu' => '',
        'klicova_slova' => '',
        'email_webu' => '',
        'logo_webu' => '',            // obrázek místo textového názvu v záhlaví
        'text_paticky' => '',
        'soc_facebook' => '',
        'soc_instagram' => '',
        'soc_x' => '',
        'soc_youtube' => '',
        'soc_linkedin' => '',
        'layout' => 'default',
        'rozvrzeni' => 'tri',         // tri | dva | jeden | plna (Úprava bloků)
        'prostredi_admin' => 'retro', // výchozí vzhled administrace: retro | 2026
        'pocet_clanku' => '7',        // článků na hlavní stránce
        'pocet_novinek' => '3',
        'hlidat_platnost' => '1',     // po datu stažení článek zmizí z hlavní stránky
        'povolit_komentare' => '1',
        'cache_stranek' => '1',       // cache celých stránek pro nepřihlášené čtenáře (5 minut)
        'komentare_rezim' => 'hned',  // hned | schvalovat (komentář čeká na schválení)
        'povolit_hodnoceni' => '1',
        'statistika' => '1',          // vlastní měření návštěvnosti bez cookies
        'tajny_klic' => '',           // vznikne sám; podepisuje formuláře čtenářů a solí otisky statistiky
        'aktivni_anketa' => '0',
        // SEO a GEO
        'indexovani' => '1',          // 0 = celý web noindex + Disallow v robots.txt
        'schema_org' => '1',          // strukturovaná data JSON-LD
        'og_obrazek' => '',           // výchozí obrázek pro sdílení
        'overeni_google' => '',
        'overeni_bing' => '',
        'robots_extra' => '',
        'ai_crawlery' => 'povolit',   // povolit | zakazat (GPTBot, ClaudeBot, PerplexityBot...)
        'llms_txt' => '1',
        'markdown_clanky' => '1',     // /clanek/<adresa>.md
        'indexnow' => '0',            // po vydání článku oznámit adresu vyhledávačům (Bing, Seznam, Yandex)
        'indexnow_klic' => '',
        // měření
        'ga4_id' => '',
        'matomo_url' => '',
        'matomo_id' => '0',
        'plausible_domena' => '',
        'kod_hlava' => '',
        // soukromí a cookies
        'cookies_rezim' => 'vestavena', // zadna | vestavena | externi
        'cookies_externi_kod' => '',
        'cookies_text' => 'Používáme cookies k měření návštěvnosti. Pomáhají nám zjistit, co čtenáře zajímá.',
        'cookies_zasady_url' => '',
        'kod_marketing' => '',
        'cookies_evidence' => '1',    // zapisovat udělené souhlasy (doklad pro případnou kontrolu)
        'stav_token' => '',
        'zalohy_auto' => '1',         // týdenní automatická záloha databáze
        'aktualizace_url' => '',      // adresa souboru aktualizace.json; prázdné = výchozí zdroj projektu
        'aktualizace_cache' => '',
        'aktualizace_auto' => '1',    // bezpečnostní vydání instalovat automaticky
        'aktualizace_pokus' => '',    // verze, kterou už údržba na pozadí zkoušela / oznámila
        'rozsireni' => '',            // zapnutá rozšíření (Core\Rozsireni); prázdné = výchozí sada
        'ads_txt' => '',
        'verze_db' => '1',            // číslo poslední provedené migrace (system/sql/migrace)
    ];

    /** @var array<string, string>|null */
    private ?array $values = null;

    public function __construct(private readonly Db $db)
    {
    }

    public function get(string $key): string
    {
        $this->values ??= $this->db->pairs('SELECT promenna, hodnota FROM {config}');

        return $this->values[$key] ?? self::DEFAULTS[$key] ?? '';
    }

    public function int(string $key): int
    {
        return (int) $this->get($key);
    }

    public function bool(string $key): bool
    {
        return $this->get($key) === '1';
    }

    public function set(string $key, string $value): void
    {
        $this->db->run(
            'INSERT INTO {config} (promenna, hodnota) VALUES (?, ?) ON DUPLICATE KEY UPDATE hodnota = VALUES(hodnota)',
            [$key, $value],
        );
        if ($this->values !== null) {
            $this->values[$key] = $value;
        }
    }
}
