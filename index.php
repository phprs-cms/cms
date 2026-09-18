<?php
/**
 * phpRS 3 - veřejná část webu.
 */

declare(strict_types=1);

require __DIR__ . '/system/bootstrap.php';

$app = PhpRS\Core\App::boot();
(new PhpRS\Front\Kernel($app))->handle()->send();
