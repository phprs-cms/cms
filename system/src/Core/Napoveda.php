<?php

declare(strict_types=1);

namespace PhpRS\Core;

/**
 * Odkazy z administrace do příručky na webu projektu (https://phprs.eu).
 *
 * Příručka existuje česky, anglicky a německy; slovenská administrace vede na českou. Stránky se zadávají
 * cestou souboru v docs/prirucka (např. 'provoz/posta'), adresy se překládají stejně jako na webu -
 * tabulka ADRESY musí odpovídat klíči "adresy" v docs/prirucka/osnova.json (hlídá to tools/testy.php).
 */
final class Napoveda
{
    public const string WEB = 'https://phprs.eu';

    /** @var array<string, array{0:string, 1:array<string,string>}> jazyk => [složka dokumentace, překlad částí cesty] */
    public const array ADRESY = [
        'cs' => ['dokumentace', []],
        'en' => ['docs', [
            'zaciname' => 'getting-started', 'pozadavky' => 'requirements', 'instalace' => 'installation', 'prvni-kroky' => 'first-steps',
            'aktualizace' => 'updates', 'presun-webu' => 'moving-your-site', 'import-z-wordpressu' => 'importing-from-wordpress', 'provoz' => 'operations', 'posta' => 'mail', 'zalohy' => 'backups',
            'ulohy-na-pozadi' => 'background-tasks', 'stav-systemu' => 'system-status', 'bezpecnost' => 'security', 'reseni-potizi' => 'troubleshooting',
            'psani' => 'writing', 'editor' => 'editor', 'obrazky-a-galerie' => 'images-and-galleries', 'vkladani-obsahu' => 'embedding-content',
            'typy-obsahu' => 'content-types', 'rubriky-stitky-serialy' => 'sections-tags-series', 'planovani-a-revize' => 'scheduling-and-revisions',
            'ai-asistent' => 'ai-assistant', 'redakce' => 'newsroom', 'role-a-opravneni' => 'roles-and-permissions',
            'predavka-a-korektura' => 'handoff-and-proofreading', 'titulni-strana-a-kalendar' => 'front-page-and-calendar', 'komentare' => 'comments',
            'ucet-a-prihlaseni' => 'account-and-sign-in',
            'vzhled' => 'appearance', 'sablony' => 'templates', 'identita-webu' => 'site-identity', 'bloky-a-rozvrzeni' => 'blocks-and-layout',
            'uprava-na-webu' => 'editing-on-the-site', 'vlastni-sablona' => 'custom-template', 'ctenari-a-prijmy' => 'readers-and-revenue',
            'ucty-ctenaru' => 'reader-accounts', 'zamceny-obsah' => 'locked-content', 'newsletter' => 'newsletter', 'web-push' => 'web-push',
            'reklama' => 'advertising', 'podpora-a-prijmy' => 'support-and-revenue', 'jazykove-verze' => 'language-versions',
            'jazyky-webu' => 'site-languages', 'preklad-obsahu' => 'translating-content', 'seo-a-ai' => 'seo-and-ai', 'seo' => 'seo',
            'ai-vyhledavace' => 'ai-search-engines', 'napojeni-na-claude' => 'connecting-claude', 'mereni-a-soukromi' => 'analytics-and-privacy',
            'pro-vyvojare' => 'for-developers', 'struktura-projektu' => 'project-structure', 'zasady' => 'principles',
            'testy-a-vydavani' => 'tests-and-releases', 'jak-prispet' => 'contributing',
        ]],
        'de' => ['dokumentation', [
            'zaciname' => 'erste-schritte', 'pozadavky' => 'voraussetzungen', 'instalace' => 'installation', 'prvni-kroky' => 'nach-der-installation',
            'aktualizace' => 'aktualisierungen', 'presun-webu' => 'umzug', 'import-z-wordpressu' => 'import-aus-wordpress', 'provoz' => 'betrieb', 'posta' => 'e-mail', 'zalohy' => 'backups',
            'ulohy-na-pozadi' => 'hintergrundaufgaben', 'stav-systemu' => 'systemstatus', 'bezpecnost' => 'sicherheit', 'reseni-potizi' => 'fehlerbehebung',
            'psani' => 'schreiben', 'editor' => 'editor', 'obrazky-a-galerie' => 'bilder-und-galerien', 'vkladani-obsahu' => 'inhalte-einbetten',
            'typy-obsahu' => 'inhaltstypen', 'rubriky-stitky-serialy' => 'rubriken-schlagwoerter-serien', 'planovani-a-revize' => 'planung-und-versionen',
            'ai-asistent' => 'ki-assistent', 'redakce' => 'redaktion', 'role-a-opravneni' => 'rollen-und-rechte',
            'predavka-a-korektura' => 'uebergabe-und-korrektur', 'titulni-strana-a-kalendar' => 'titelseite-und-kalender', 'komentare' => 'kommentare',
            'ucet-a-prihlaseni' => 'konto-und-anmeldung',
            'vzhled' => 'design', 'sablony' => 'vorlagen', 'identita-webu' => 'website-identitaet', 'bloky-a-rozvrzeni' => 'bloecke-und-layout',
            'uprava-na-webu' => 'bearbeiten-auf-der-website', 'vlastni-sablona' => 'eigene-vorlage', 'ctenari-a-prijmy' => 'leser-und-einnahmen',
            'ucty-ctenaru' => 'leserkonten', 'zamceny-obsah' => 'gesperrte-inhalte', 'newsletter' => 'newsletter', 'web-push' => 'web-push',
            'reklama' => 'werbung', 'podpora-a-prijmy' => 'unterstuetzung-und-einnahmen', 'jazykove-verze' => 'sprachversionen',
            'jazyky-webu' => 'sprachen-der-website', 'preklad-obsahu' => 'inhalte-uebersetzen', 'seo-a-ai' => 'seo-und-ki', 'seo' => 'seo',
            'ai-vyhledavace' => 'ki-suchmaschinen', 'napojeni-na-claude' => 'anbindung-an-claude', 'mereni-a-soukromi' => 'messung-und-datenschutz',
            'pro-vyvojare' => 'fuer-entwickler', 'struktura-projektu' => 'projektstruktur', 'zasady' => 'grundsaetze',
            'testy-a-vydavani' => 'tests-und-releases', 'jak-prispet' => 'mitwirken',
        ]],
    ];

    /** Adresa stránky příručky v jazyce administrace; prázdná cesta = úvod příručky. */
    public static function url(string $cesta = '', ?string $jazyk = null): string
    {
        $jazyk ??= Jazyk::kod();
        $jazyk = isset(self::ADRESY[$jazyk]) ? $jazyk : ($jazyk === 'sk' ? 'cs' : 'en');
        [$slozka, $preklad] = self::ADRESY[$jazyk];
        $casti = array_map(static fn (string $c): string => $preklad[$c] ?? $c, array_filter(explode('/', $cesta), static fn (string $c): bool => $c !== ''));

        return self::WEB . "/{$jazyk}/{$slozka}/" . ($casti === [] ? '' : implode('/', $casti) . '/');
    }

    /** Hotový odkaz „Nápověda: …“ pro šablony administrace. */
    public static function odkaz(string $cesta, string $text): string
    {
        return '<a class="napoveda-odkaz" href="' . e(self::url($cesta)) . '" target="_blank" rel="noopener">' . e(t('Nápověda')) . ': ' . e(t($text)) . '</a>';
    }
}
