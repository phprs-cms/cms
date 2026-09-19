<?php
/**
 * phpRS 3 - veřejná část webu.
 */

declare(strict_types=1);

require __DIR__ . '/system/bootstrap.php';

$app = PhpRS\Core\App::boot();
(new PhpRS\Front\Kernel($app))->handle()->send();

// po odeslání stránky: oznámení o právě vydaných (i naplánovaných) článcích a kontrola bezpečnostních aktualizací (nejvýše jednou za 12 hodin)
PhpRS\Core\Oznameni::naPozadi($app);
PhpRS\Core\Aktualizace::naPozadi($app);
