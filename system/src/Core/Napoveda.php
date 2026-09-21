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
            'aktualizace' => 'updates', 'presun-webu' => 'moving-your-site', 'provoz' => 'operations', 'posta' => 'mail', 'zalohy' => 'backups',
            'ulohy-na-pozadi' => 'background-tasks', 'stav-systemu' => 'system-status', 'bezpecnost' => 'security', 'reseni-potizi' => 'troubleshooting',
        ]],
        'de' => ['dokumentation', [
            'zaciname' => 'erste-schritte', 'pozadavky' => 'voraussetzungen', 'instalace' => 'installation', 'prvni-kroky' => 'nach-der-installation',
            'aktualizace' => 'aktualisierungen', 'presun-webu' => 'umzug', 'provoz' => 'betrieb', 'posta' => 'e-mail', 'zalohy' => 'backups',
            'ulohy-na-pozadi' => 'hintergrundaufgaben', 'stav-systemu' => 'systemstatus', 'bezpecnost' => 'sicherheit', 'reseni-potizi' => 'fehlerbehebung',
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
