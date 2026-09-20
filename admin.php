<?php
/**
 * phpRS 3 - administrace.
 * Adresy mají stejný tvar jako v phpRS 2: admin.php?modul=clanky&akce=edit&id=5
 */

declare(strict_types=1);

require __DIR__ . '/system/bootstrap.php';

$app = PhpRS\Core\App::boot();
$odpoved = (new PhpRS\Admin\Kernel($app))->handle();
// administrace: nic z ní nepatří do mezipaměti prohlížeče ani proxy a smí spouštět jen vlastní skripty (žádné inline, žádné cizí)
(new PhpRS\Core\Response($odpoved->body, $odpoved->status, $odpoved->headers + [
    'Cache-Control' => 'no-store, private',
    'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob: https:; media-src 'self' https:; "
        . "frame-src 'self' https:; connect-src 'self'; font-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'",
] + ($app->request->isHttps() ? ['Strict-Transport-Security' => 'max-age=15552000'] : [])))->send();
