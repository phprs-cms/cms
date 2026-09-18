<?php
/**
 * phpRS 3 - administrace.
 * Adresy mají stejný tvar jako v phpRS 2: admin.php?modul=clanky&akce=edit&id=5
 */

declare(strict_types=1);

require __DIR__ . '/system/bootstrap.php';

$app = PhpRS\Core\App::boot();
(new PhpRS\Admin\Kernel($app))->handle()->send();
