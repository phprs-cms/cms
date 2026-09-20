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
- **Identita webu** (`Front\Identita`, modul `vzhled`): šablony berou hlavní barvu a písma z proměnných `--rs-akcent`,
  `--rs-pismo-titulky`, `--rs-pismo-text` s vlastní výchozí hodnotou (`--akcent: var(--rs-akcent, #326891)`). Nová šablona
  je musí použít také. Žádná externí písma – jen sady v `Identita::PISMA_*`.
- **Tři layouty webu** (`default`, `classic-newspaper`, `modern-magazine`): nová proměnná pro šablony
  nebo nový systémový blok se musí promítnout do všech tří. Žádná externí písma ani CDN.
- Identifikátory v kódu (metody `akce*`, proměnné domény, šablony) česky bez diakritiky; komentáře a
  texty česky s diakritikou. Jádro (`Core/`) má API anglicky.
- **Změna databáze = dva zápisy:** úplné schéma v `system/sql/schema.sql` (nové instalace) a migrace
  `system/sql/migrace/NNNN-popis.sql` (stávající weby; provede se sama při vstupu admina do administrace,
  číslo drží `rs_config.verze_db`).
- **Rozšíření jsou uzavřený systém** (`Core\Rozsireni::SEZNAM`): žádné cizí plug-iny, žádné veřejné API,
  žádné nahrávání kódu z administrace. Nová volitelná funkce = položka v `SEZNAM` + `ROZSIRENI` u modulu +
  kontrola `Rozsireni::je()` na webu. Jádro (články, média, rubriky, stránky, bloky, uživatelé, nastavení) vypnout nejde.
- **Vydání a aktualizace:** verze je `PHPRS_VERSION` v `system/bootstrap.php`; `php tools/vydani.php <verze> --url=…`
  vytvoří `dist/*.zip` a podepsaný `dist/aktualizace.json`. Soukromý klíč `tools/klice/vydavatel.key` NIKDY do gitu
  ani do balíčku; veřejný `system/aktualizace.pub` je součástí systému. `Core\Aktualizace::CHRANENE` = co se nepřepisuje.
  Až bude web projektu, doplnit `Aktualizace::VYCHOZI_URL`.
- **Nastavení** (`Moduly\Konfigurace`): nová volba = klíč v `Settings::DEFAULTS` + typ v `Konfigurace::POLE`
  (podle typu se hodnota čistí) + řádek `$pole(...)` v `views/admin/config/<zalozka>.php`.
- **Layouty musí vypsat `<?= $hlava ?>` před `</head>` a `<?= $pata ?>` před `</body>`** - tudy jde SEO,
  strukturovaná data, měřicí kódy a cookie lišta (`Front\Seo`). Měřicí skripty čekající na souhlas mají
  `type="text/plain" data-souhlas="analytika"` (+ `data-cookieconsent` pro Cookiebot).
- **Bloky se upravují vizuálně přímo ve stránce webu** (`/?upravit=1` → `views/front/vizual.php`, `image/vizual.js|css`):
  `Front\Bloky::zony(..., $upravit)` obalí zóny `.rs-zona[data-zona]` (display: contents – nesmí rozbít mřížku layoutu) a
  bloky `.rs-blok[data-blok]`; ukládá se přes JSON akce `Moduly\Bloky` (rychle_pridat, nastaveni_json, uloz_json, poradi).
  Nový typ bloku = `Bloky::SYSTEMOVE` + `Bloky::KATALOG` (název, popis jednou větou, ikona) + větev ve `Front\Bloky::systemovy()`
  + případná pole v panelu nastavení ve `vizual.js`. Nastavení bloku má být na pár polí – co jde odvodit, neptej se.
  Schéma bez JavaScriptu zůstává na `admin.php?modul=bloky&schema=1`.
- **Nikdy `window.confirm()`** – vestavěné prohlížeče ho potlačují. V administraci atribut `data-potvrdit="text"`
  na formuláři či tlačítku (řeší `admin.js`), ve vizuálním editoru vlastní dialog.
- **Cache stránek** (`Front\Cache`): cachuje se jen výstup `Front\Kernel::stranka()` pro nepřihlášené bez osobních
  cookies; cokoli, co se má lišit podle čtenáře, musí buď běžet v JS, nebo mít vlastní cookie `phprs_*`, která
  cache vypíná. Každý POST v administraci i formuláře čtenářů volají `Cache::vymaz()`.
- **Obrázky:** varianty `-1200`, `-nahled` a sourozenci `.webp` vznikají v `Core\Obrazky::uloz()`; `srcset` doplňuje
  `Front\Clanky::priprav()`. WebP se podává přes `.htaccess` (a `dev-router.php`), HTML se kvůli němu nemění.
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

- **Společné prvky článku** (fotogalerie, prohlížečka fotek, přehrávač, živá reportáž, hodnocení recenze, zámek, účet čtenáře,
  přepínač jazyků, Web Push) mají styl a skript v `image/web.css` a `image/web.js` – vkládá je `Front\Seo::hlava()`, takže fungují ve
  všech šablonách včetně cizích. `Front\TypyObsahu` vkládá hotové HTML přímo do textu článku; layouty se kvůli nim nemění.
- **Texty šablon webu jdou přes `t('Česky')`** (`Core\Jazyk`, slovníky `system/jazyky/<kód>.php`, klíčem je český text). Nový text
  v layoutu nebo `views/front/` = obalit `t()` + doplnit do slovníků en/sk/de. Administrace má vlastní slovníky `admin-<kód>.php`
  (přeložené jen hlavní obrazovky). Hodnoty formulářů (`value` skrytých polí a tlačítek s `name`) se NIKDY nepřekládají.
- **Jazykové verze:** sloupec `jazyk` ('' = výchozí jazyk webu) mají rubriky, stránky a články (článek ho přebírá z rubriky při uložení);
  `Jazyk::sloupecWebu()` je hodnota pro dotazy webu. `App::url()` přidává předponu `/en/` jen adresám bez přípony – soubory, `api/`,
  `mcp`, `push/` jsou společné. Každý nový dotaz na webu, který vypisuje obsah, musí filtrovat podle jazyka.
- **Zamčený obsah** řeší `Front\Ctenari::zamkni()` volané z `Front\Clanky::priprav()` – cokoli čte články jinudy, musí zámek
  respektovat samo. Čtenáři nemají session: podepsaná cookie `phprs_ctenar` (v podpisu je otisk hesla), formuláře přes `Antispam`.
- **Oznámení o vydání** (webhook, IndexNow, Web Push) odchází jen přes `Core\Oznameni::zpracuj()` a sloupec `rs_clanky.oznameno`;
  nevolej `Webhook::clanekVydan()` přímo. Web Push: adresa odběru smí vést jen na služby v `Push::SLUZBY` (ochrana proti SSRF).
- **AI asistent** (`Core\Asistent`): klíč `ai_klic` je typ `tajne` – do HTML jde jen jeho konec. Odpověď modelu je nedůvěryhodný vstup
  (jen řetězce bez HTML). Modely: `Asistent::MODELY`.

- **Adresa webu je nastavení `adresa_webu`, ne hlavička Host.** Absolutní adresy (e-maily, RSS, mapa webu, webhook, push) ber vždy
  z `$app->request->origin()` – oba kernely do něj po startu dosadí nastavenou adresu. Nikdy nečti `HTTP_HOST` přímo.
- **Výpisy článků nenačítají dlouhé texty** (`Front\Clanky::SLOUPCE_VYPISU`): nový sloupec `rs_clanky`, který má být vidět ve výpisu,
  doplň i tam. Nová migrace = zvýšit `PHPRS_VERZE_DB` v `system/bootstrap.php` (hlídá `tools/test.sh`).
- **`Auth::user()` nesmí na webu založit session** anonymnímu návštěvníkovi (vypnula by cache). `Cache::vymaz()` volej až po skutečném zápisu.
- **Pošta** jde vždy přes `Core\Posta::odesli()` (SMTP nebo mail() podle Nastavení → Pošta), nikdy přímo `mail()`.
- Čtenář se registruje bez hesla; heslo nastavuje až z odkazu v e-mailu (`/ctenar/heslo/<token>`) – neměnit zpět na heslo v prvním kroku.

- **Hledání** jde přes sloupec `rs_clanky.hledani` (`Core\Hledani`): kdo ukládá článek jinudy než přes administraci nebo MCP,
  musí zavolat `Hledani::indexuj()`. **Nahrávání**: obrázky `Core\Obrazky`, přílohy `Core\Soubory` (whitelist přípon – HTML, SVG ani
  skripty nikdy). **E-maily** mají frontu (`rs_posta`, `Posta::zpracujFrontu()` z úloh na pozadí); jednorázové zprávy `doFronty: false`.
- **Testy:** logiku bez databáze (kryptografie, parsování, převody textu) pokryj v `tools/testy.php`; průchod webem hlídá `tools/test.sh`.

- **Tmavý režim webu:** šablona má na konci `style.css` blok `@media (prefers-color-scheme: dark) { :root[data-tmavy] { … } }` a `base.php`
  dává `<html data-tmavy>` podle nastavení `tmavy_rezim`. Barvy v šablonách proto piš přes proměnné, ne natvrdo.

- **Šablony článku** (Dlouhé čtení, Fotoreportáž, Rozhovor) jsou varianty: `cla_standard.php` dává `<article>` třídu
  `sablona-<soubor>` a vzhled je v `image/web.css`. Nová šablona layoutu musí tu třídu vypsat také. Co vkládáš před text článku,
  nesmí být `<p>` – šablony dávají prvnímu odstavci iniciálu.

- **Hranice napojení na Claude (rozhodnutí uživatele 2026-09-21, bezpečí na prvním místě):** přes MCP se mění jen obsah a VLASTNÍ
  šablony; žádný nástroj nesmí umět zapsat mimo `layout/<vlastní>/`, spustit kód ani dotaz. PHP šablony ukládané přes MCP musí projít
  `Core\SablonaKontrola` (povolovací seznam funkcí a konstrukcí; vestavěné šablony jím projít musí – hlídá `tools/testy.php`).
  Novou funkci do seznamu přidej jen tehdy, když nepracuje se soubory, sítí, procesy, zpětným voláním ani reflexí. Pravidla pro
  Claude pracujícího se soubory jsou v `layout/CLAUDE.md` (je součástí balíčku). Vydání nese podepsaný seznam souborů
  `system/soubory.json`; `Core\Integrita` podle něj ve Stavu systému hlásí změněné, chybějící a přidané soubory jádra.

- **Překlady doplňuj nástrojem** `tools/slovnik.py <slovník> < řádky „česky|překlad“` – správně escapuje apostrofy (ruční skládání PHP
  řetězců už dvakrát rozbilo anglický slovník). Po každé změně slovníků spusť `tools/test.sh`.

## Spuštění

`php -S localhost:8080 system/dev-router.php` (preview: konfigurace `phprs3`). MySQL: `mysql.server start`,
databáze `phprs3`, uživatel `phprs3` (údaje v `config.php`, není v gitu). Čistá instalace: smazat
`config.php`, `DROP` tabulek `rs_*`, otevřít `/install.php`.

Po změně: `tools/test.sh` (lint, jednotkové testy, čistá instalace a průchod webem; potřebuje MySQL), případně jen `php tools/testy.php`, a projít dotčené stránky v prohlížeči / přes curl.
