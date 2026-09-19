<?php
/**
 * Vizuální editor bloků - vkládá se před </body> místo běžné patičky, když přihlášený uživatel otevře web s ?upravit=1.
 *
 * @var PhpRS\Core\App $app
 * @var string $rozvrzeni
 */
use PhpRS\Admin\Moduly\Bloky;
use PhpRS\Admin\Moduly\Reklama;
use PhpRS\Admin\Moduly\Rubriky;
use PhpRS\Core\Rozsireni;

$vypnute = array_keys(array_filter(['nov' => 'novinky', 'ank' => 'ankety', 'rek' => 'reklama', 'nws' => 'newsletter', 'cte' => 'ctenari', 'psh' => 'push'], fn (string $r): bool => !Rozsireni::je($app->settings(), $r)));
$katalog = [];
foreach (Bloky::KATALOG as $skupina => $typy) {
    foreach ($typy as $typ => [$nazev, $popis, $ikona]) {
        if (!in_array($typ, $vypnute, true)) {
            $katalog[$skupina][] = ['typ' => (string) $typ, 'nazev' => $nazev, 'popis' => $popis, 'ikona' => $ikona];
        }
    }
}
$nastaveni = [
    'admin' => $app->url('admin.php'),
    'csrf' => $app->session->csrfToken(),
    'rozvrzeni' => $rozvrzeni,
    'rozvrzeniVolby' => array_map(fn (array $r): array => ['nazev' => $r[0], 'popis' => $r[1]], Bloky::ROZVRZENI),
    'katalog' => $katalog,
    'rubriky' => array_map(fn (array $r): array => ['id' => (int) $r['idt'], 'nazev' => str_repeat('– ', $r['uroven']) . $r['nazev']], Rubriky::strom($app->db())),
    'pozice' => array_diff_key(Reklama::POZICE, ['pod-clankem' => 1]),
    'kde' => Bloky::KDE,
    'zarizeni' => Bloky::ZARIZENI,
];
?>
<input type="hidden" name="_csrf" value="<?= e($app->session->csrfToken()) ?>">
<link rel="stylesheet" href="<?= e($app->url('image/editor.css')) ?>?v=<?= e(PHPRS_VERSION) ?>">
<link rel="stylesheet" href="<?= e($app->url('image/vizual.css')) ?>?v=<?= e(PHPRS_VERSION) ?>">
<script id="rs-nastaveni" type="application/json"><?= json_encode($nastaveni, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<script src="<?= e($app->url('image/editor.js')) ?>?v=<?= e(PHPRS_VERSION) ?>" data-admin-url="<?= e($app->url('admin.php')) ?>"></script>
<script src="<?= e($app->url('image/vizual.js')) ?>?v=<?= e(PHPRS_VERSION) ?>"></script>
