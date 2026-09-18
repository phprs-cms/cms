# phpRS 3

Redakční systém pro magazíny, který se hlásí k odkazu českého phpRS (vývoj originálu skončil). Čisté PHP 8.4+ bez frameworku a bez Composeru
(vlastní PSR-4 autoloader v `system/bootstrap.php`), MySQL přes PDO, serverové HTML + trocha vanilla JS.

## Zásady

- **Jednoduchost nad abstrakcí.** Kód má přečíst i poučený laik, který si chce web upravit. Žádné DI
  kontejnery, ORM, build kroky ani npm. Nová závislost = potřeba silného důvodu.
- **Z phpRS držíme ducha, ne mechaniky – a žádnou kompatibilitu.** Uživatel (2026-09-18) zrušil import ze
  starého phpRS: nepřevádějí se data, nepřesměrovávají staré adresy, nedrží se žádný sloupec ani funkce „kvůli
  2.x". Zůstává jednoduchost, zaměření na magazíny, české názvy tabulek/sloupců (`rs_clanky.titulek`…) a
  identifikátory modulů, protože tak už systém je postavený. Ovládání musí být srozumitelné člověku, který
  phpRS nikdy neviděl. **Retro prostředí je jen vtipná pocta** – skin nad stejným HTML, nic víc; nepřidávej
  kvůli němu žádné chování. Původní zdroják a screenshoty (`../phprs-original-reference/`) slouží jen jako
  vizuální předloha retro skinu.
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
- **Nastavení** (`Moduly\Konfigurace`): nová volba = klíč v `Settings::DEFAULTS` + typ v `Konfigurace::POLE`
  (podle typu se hodnota čistí) + řádek `$pole(...)` v `views/admin/config/<zalozka>.php`.
- **Layouty musí vypsat `<?= $hlava ?>` před `</head>` a `<?= $pata ?>` před `</body>`** - tudy jde SEO,
  strukturovaná data, měřicí kódy a cookie lišta (`Front\Seo`). Měřicí skripty čekající na souhlas mají
  `type="text/plain" data-souhlas="analytika"` (+ `data-cookieconsent` pro Cookiebot).
- **Formuláře čtenářů** (komentáře, hodnocení, ankety – `Front\Interakce`) nemají session ani CSRF token;
  chrání je `Core\Antispam` (podepsaný čas, honeypot, limit na otisk IP). Cokoli od čtenáře se vypisuje
  jen přes `e()`. Do `rs_kontrola_ip` a statistik se nikdy neukládá IP adresa, jen otisk.
- Hotové kusy HTML pro šablony článku (`shrnuti_html`, `faq_html`, `hodnoceni_html`, `komentare_html`) mají
  výchozí šablony v `system/views/front/` – layout je jen vypíše, nebo si šablonu přepíše vlastní.
- Editor článků a galerie: `image/editor.js` + `image/editor.css` (barvy přes proměnné `--ed-*` z obou
  admin stylesheetů). Nahrané soubory jdou vždy přes `Core\Obrazky` (překódování GD, složka `media/`).
- Bezpečnost: jen připravené dotazy (`{tabulka}` doplní předponu), výstup přes `e()`, každý POST
  má CSRF (kontroluje `Admin\Kernel`), práva článků viz `Moduly\Clanky`.
- HTML článků a bloků je důvěryhodné (píší ho autoři), komentáře a vstupy čtenářů nikdy.

## Spuštění

`php -S localhost:8080 system/dev-router.php` (preview: konfigurace `phprs3`). MySQL: `mysql.server start`,
databáze `phprs3`, uživatel `phprs3` (údaje v `config.php`, není v gitu). Čistá instalace: smazat
`config.php`, `DROP` tabulek `rs_*`, otevřít `/install.php`.

Po změně: `find . -name '*.php' | xargs -n1 php -l` a projít dotčené stránky v prohlížeči / přes curl.
