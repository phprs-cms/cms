<?php
/**
 * phpRS 3 - zavaděč systému.
 * Společný start pro index.php (web), admin.php (administrace) a install.php.
 */

declare(strict_types=1);

const PHPRS_VERSION = '3.0.0-dev';

/** Číslo poslední migrace v system/sql/migrace - web podle něj pozná, že má po aktualizaci upravit databázi (hlídá tools/test.sh). */
const PHPRS_VERZE_DB = 27;

define('PHPRS_ROOT', dirname(__DIR__));
define('PHPRS_SYSTEM', __DIR__);

if (PHP_VERSION_ID < 80400) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    exit('phpRS 3 vyžaduje PHP 8.4 nebo novější. Na serveru běží PHP ' . PHP_VERSION . '.');
}

mb_internal_encoding('UTF-8');
date_default_timezone_set('Europe/Prague');

// Vlastní PSR-4 autoloader: system/src/Core/Db.php = PhpRS\Core\Db.
// Composer není k běhu potřeba - web se dá nahrát přes FTP tak, jak je.
spl_autoload_register(static function (string $class): void {
    if (!str_starts_with($class, 'PhpRS\\')) {
        return;
    }
    $file = PHPRS_SYSTEM . '/src/' . str_replace('\\', '/', substr($class, 6)) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require PHPRS_SYSTEM . '/src/helpers.php';
