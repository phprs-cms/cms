<?php

declare(strict_types=1);

namespace PhpRS\Front;

use PhpRS\Core\App;
use PhpRS\Core\Response;
use PhpRS\Core\Rozsireni;

/**
 * Cache celých stránek pro nepřihlášené čtenáře (soubory ve storage/cache/stranky, platnost 5 minut).
 * Stránka z cache stojí jeden dotaz do databáze místo desítek. Jakákoli změna v administraci i nový
 * komentář cache smaže. Necachuje se nic osobního: přihlášená redakce, čtenář, který už hlasoval, náhledy.
 */
final class Cache
{
    private const string SLOZKA = PHPRS_ROOT . '/storage/cache/stranky';
    private const int PLATNOST = 300;

    public static function nacti(App $app): ?Response
    {
        $soubor = self::soubor($app);
        if ($soubor === null || !is_file($soubor) || filemtime($soubor) < time() - self::PLATNOST) {
            return null;
        }
        [$hlavicka, $html] = explode("\n", (string) file_get_contents($soubor), 2) + [1 => ''];
        $meta = json_decode($hlavicka, true);
        if (!is_array($meta) || $html === '') {
            return null;
        }
        Statistika::zaznamenej($app, $meta['idc'] ?? null);
        if (!empty($meta['idc'])) {
            $app->db()->run('UPDATE {clanky} SET visit = visit + 1 WHERE idc = ?', [(int) $meta['idc']]);
        }

        return new Response($html, 200, ['Content-Type' => 'text/html; charset=utf-8', 'X-Cache' => 'phprs']);
    }

    public static function uloz(App $app, string $html, ?int $idc): void
    {
        $soubor = self::soubor($app);
        if ($soubor === null) {
            return;
        }
        if (!is_dir(self::SLOZKA)) {
            @mkdir(self::SLOZKA, 0775, true);
        }
        @file_put_contents($soubor, json_encode(['idc' => $idc]) . "\n" . $html, LOCK_EX);
    }

    public static function vymaz(): void
    {
        foreach (glob(self::SLOZKA . '/*.html') ?: [] as $soubor) {
            @unlink($soubor);
        }
    }

    /** Soubor cache pro tento požadavek, nebo null, když se cachovat nemá. */
    private static function soubor(App $app): ?string
    {
        $r = $app->request;
        $s = $app->settings();
        if (!$s->bool('cache_stranek') || $r->isPost() || Rozsireni::je($s, 'reklama')) {
            return null; // reklamy se střídají a počítají při každém zobrazení
        }
        if (array_diff(array_keys($_GET), ['strana']) !== []) {
            return null;
        }
        foreach (array_keys($_COOKIE) as $cookie) {
            if ($cookie === 'phprs3' || str_starts_with((string) $cookie, 'phprs_h') || str_starts_with((string) $cookie, 'phprs_a')) {
                return null;
            }
        }

        return self::SLOZKA . '/' . md5($r->origin() . '|' . $r->path() . '|' . $r->getInt('strana', 1) . '|' . $s->get('layout') . '|' . $s->get('rozvrzeni')) . '.html';
    }
}
