<?php
/**
 * phpRS 3 - instalace. Po úspěšné instalaci tento soubor ze serveru smažte.
 */

declare(strict_types=1);

require __DIR__ . '/system/bootstrap.php';

(new PhpRS\Install\Installer())->handle()->send();
