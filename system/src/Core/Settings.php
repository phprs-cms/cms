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
        'layout' => 'default',
        'pocet_clanku' => '7',        // článků na hlavní stránce
        'pocet_novinek' => '3',
        'hlidat_platnost' => '1',     // po datu stažení článek zmizí z hlavní stránky
        'povolit_komentare' => '1',
        'aktivni_anketa' => '0',
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
