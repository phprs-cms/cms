# phpRS 3

Duchovní nástupce českého CMS phpRS 2.8 pro magazíny. Čisté PHP 8.4+ bez frameworku a bez Composeru
(vlastní PSR-4 autoloader v `system/bootstrap.php`), MySQL přes PDO, serverové HTML + trocha vanilla JS.

## Zásady

- **Jednoduchost nad abstrakcí.** Kód má přečíst i člověk, který kdysi upravoval phpRS 2. Žádné DI
  kontejnery, ORM, build kroky ani npm. Nová závislost = potřeba silného důvodu.
- **Věrnost originálu.** Názvy tabulek/sloupců (`rs_clanky.titulek`, `rs_topic`…), identifikátory modulů
  (`users, clanky, news, bloky, topic, config`…), adresy `admin.php?modul=&akce=`, české popisky
  („Editace článků", „Zpět na hlavní stránku sekce", „Vymaž všechny označené…") a vzhled administrace
  (`image/admin.css` – barvy a třídy z 2.8) se drží phpRS 2. Před návrhem nového modulu se podívej,
  jak vypadal v originále: `../phprs-original-reference/` (zdroják 2.8.1a, dokumentace, screenshoty).
- Identifikátory v kódu (metody `akce*`, proměnné domény, šablony) česky bez diakritiky; komentáře a
  texty česky s diakritikou. Jádro (`Core/`) má API anglicky.
- Schéma se mění v `system/sql/schema.sql`; každá změna musí zůstat mapovatelná z phpRS 2.x (importér M4).
- Bezpečnost: jen připravené dotazy (`{tabulka}` doplní předponu), výstup přes `e()`, každý POST
  má CSRF (kontroluje `Admin\Kernel`), práva článků viz `Moduly\Clanky`.
- HTML článků a bloků je důvěryhodné (píší ho autoři), komentáře a vstupy čtenářů nikdy.

## Spuštění

`php -S localhost:8080 system/dev-router.php` (preview: konfigurace `phprs3`). MySQL: `mysql.server start`,
databáze `phprs3`, uživatel `phprs3` (údaje v `config.php`, není v gitu). Čistá instalace: smazat
`config.php`, `DROP` tabulek `rs_*`, otevřít `/install.php`.

Po změně: `find . -name '*.php' | xargs -n1 php -l` a projít dotčené stránky v prohlížeči / přes curl.
