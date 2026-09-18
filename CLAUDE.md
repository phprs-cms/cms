# phpRS 3

Duchovní nástupce českého CMS phpRS 2.8 pro magazíny. Čisté PHP 8.4+ bez frameworku a bez Composeru
(vlastní PSR-4 autoloader v `system/bootstrap.php`), MySQL přes PDO, serverové HTML + trocha vanilla JS.

## Zásady

- **Jednoduchost nad abstrakcí.** Kód má přečíst i člověk, který kdysi upravoval phpRS 2. Žádné DI
  kontejnery, ORM, build kroky ani npm. Nová závislost = potřeba silného důvodu.
- **Z phpRS držíme ducha, ne mechaniky.** Zůstává: jednoduchost, zaměření na magazíny, datový model a
  názvy tabulek/sloupců (`rs_clanky.titulek`, `rs_topic`… kvůli importu z 2.x), identifikátory modulů
  (`users, clanky, news, bloky, topic, config, intergal`), adresy `admin.php?modul=&akce=` a retro vzhled.
  Nezůstává ovládání z roku 2005: číselné priority, „typ 1–5", „Vydat článek Ano/Ne", volací linky ve
  formulářích apod. Uživatel (2026-09-18): nový člověk musí všemu rozumět bez znalosti phpRS. Staré
  hodnoty se dál ukládají do původních sloupců (priority, typ, visible), jen je UI pojmenovává lidsky
  (připnout, vzhled bloku, stav). Reference originálu: `../phprs-original-reference/`.
- **Vzhled prostředí 2026** vychází z referenčního screenshotu uživatele (čistý styl à la Clockhaus):
  seskupené menu s čárovými ikonami (`views/admin/ikony.php`), podklad #F7F7F8, bílé karty s jemnou linkou,
  výrazné nadpisy, avatar a přepínač světlý/tmavý vpravo nahoře. Retro je jen skin téhož HTML a ukazuje
  původní názvy modulů (`NAZEV_RETRO`).
- **Dvě prostředí administrace nad jedním HTML** (retro `image/admin.css`, 2026 `image/admin-2026.css`).
  Každá změna šablon administrace se musí zkontrolovat v obou; nové třídy doplnit do obou stylesheetů.
  Pozor: klíč pole `'2026'` je v PHP int – při porovnání přetypovat na string.
- **Tři layouty webu** (`default`, `classic-newspaper`, `modern-magazine`): nová proměnná pro šablony
  nebo nový systémový blok se musí promítnout do všech tří. Žádná externí písma ani CDN.
- Identifikátory v kódu (metody `akce*`, proměnné domény, šablony) česky bez diakritiky; komentáře a
  texty česky s diakritikou. Jádro (`Core/`) má API anglicky.
- **Změna databáze = dva zápisy:** úplné schéma v `system/sql/schema.sql` (nové instalace) a migrace
  `system/sql/migrace/NNNN-popis.sql` (stávající weby; provede se sama při vstupu admina do administrace,
  číslo drží `rs_config.verze_db`).
- Editor článků a galerie: `image/editor.js` + `image/editor.css` (barvy přes proměnné `--ed-*` z obou
  admin stylesheetů). Nahrané soubory jdou vždy přes `Core\Obrazky` (překódování GD, složka `media/`).
- Schéma se mění v `system/sql/schema.sql`; každá změna musí zůstat mapovatelná z phpRS 2.x (importér M4).
- Bezpečnost: jen připravené dotazy (`{tabulka}` doplní předponu), výstup přes `e()`, každý POST
  má CSRF (kontroluje `Admin\Kernel`), práva článků viz `Moduly\Clanky`.
- HTML článků a bloků je důvěryhodné (píší ho autoři), komentáře a vstupy čtenářů nikdy.

## Spuštění

`php -S localhost:8080 system/dev-router.php` (preview: konfigurace `phprs3`). MySQL: `mysql.server start`,
databáze `phprs3`, uživatel `phprs3` (údaje v `config.php`, není v gitu). Čistá instalace: smazat
`config.php`, `DROP` tabulek `rs_*`, otevřít `/install.php`.

Po změně: `find . -name '*.php' | xargs -n1 php -l` a projít dotčené stránky v prohlížeči / přes curl.
