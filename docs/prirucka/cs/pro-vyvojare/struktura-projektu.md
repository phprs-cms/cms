# Struktura projektu

phpRS je napsaný v čistém PHP 8.4+ nad MySQL nebo MariaDB. Nemá framework, Composer ani krok sestavení. Stránky skládá server, v prohlížeči běží jen malé množství obyčejného JavaScriptu. Tato stránka je mapa pro toho, kdo chce kód číst, ladit nebo do něj přispět.

## Proč bez frameworku, Composeru a npm

Je to záměr, ne dluh:

- **Instalace přes FTP.** Systém má běžet na obyčejném sdíleném hostingu. Nahrajete soubory, otevřete `install.php` a je hotovo. Příkazová řádka není potřeba.
- **Čitelnost.** Kód má přečíst i poučený laik, který si chce web upravit. Žádný kontejner závislostí, ORM ani generovaný kód – co se děje, je vidět v jednom souboru.
- **Bezpečnost a údržba.** Za běhu se nepoužívá žádná knihovna třetí strany. Není co hlídat kvůli zranitelnostem v závislostech a aktualizace je jeden podepsaný balíček.
- **Žádné sestavování.** CSS a JavaScript se píší tak, jak se posílají do prohlížeče.

Nová závislost potřebuje silný důvod. Věci, na které se jinde bere knihovna (TOTP, WebAuthn, Web Push, podpis pro S3, SMTP), jsou tu napsané v několika stech řádcích a pokryté testy.

## Vstupní body

| Soubor | K čemu je |
|---|---|
| `index.php` | veřejný web; předá řízení `Front\Kernel` |
| `admin.php` | administrace; posílá bezpečnostní hlavičky včetně Content-Security-Policy a předá řízení `Admin\Kernel` |
| `install.php` | instalátor; po dokončení se smaže sám (ve vývojové kopii se složkou `.git` ne) |
| `sw.js` | service worker pro oznámení Web Push |
| `system/bootstrap.php` | konstanty `PHPRS_VERSION` a `PHPRS_VERZE_DB`, autoloader PSR-4 pro jmenný prostor `PhpRS\`, pomocné funkce |
| `system/dev-router.php` | směrovač pro vestavěný server PHP při vývoji |

Hezké adresy na Apache zajišťuje `.htaccess`. Pro nginx je v `system/nginx.priklad.conf` hotová ukázka – viz [nginx](../provoz/nginx.md).

## Složky

| Složka | Obsah |
|---|---|
| `system/src/Core/` | jádro: `App`, `Db`, `Request`, `Response`, `Session`, `View`, `Auth`, `Settings` a služby (pošta, obrázky, zálohy, aktualizace, podpisy, Web Push, AI asistent, jazyky, rozšíření…) |
| `system/src/Admin/` | `Kernel` administrace, základní třída `Modul`, účet uživatele, obnova hesla, protokol změn |
| `system/src/Admin/Moduly/` | moduly administrace – jeden modul je jedna třída |
| `system/src/Front/` | veřejný web: směrování (`Kernel`), články, bloky, SEO, čtenáři, newsletter, reklama, cache, statistika, API |
| `system/src/Mcp/` | MCP server pro napojení na Claude: `Server` a `Nastroje` |
| `system/src/Install/` | instalátor |
| `system/views/admin/` | šablony administrace; složka podle identifikátoru modulu |
| `system/views/front/` | výchozí šablony webu, které šablona vzhledu smí přepsat |
| `system/views/install/` | šablony instalátoru |
| `system/jazyky/` | slovníky: web (`en.php`, `sk.php`, `de.php`), administrace (`admin-*.php`), instalátor (`install-*.php`) |
| `system/sql/` | `schema.sql` pro nové instalace a `migrace/NNNN-popis.sql` pro stávající |
| `system/demo/` | ukázkový obsah v češtině, angličtině a němčině |
| `layout/` | šablony vzhledu webu; vestavěné `classic-newspaper`, `modern-magazine`, `minimal` |
| `image/` | CSS a JavaScript administrace, editoru, vizuálního editoru bloků a společné `web.css` a `web.js` pro web; písmo administrace v `image/pisma/` |
| `media/` | nahrané soubory, řazené do složek `RRRR/MM/` |
| `storage/` | cache, logy, zálohy; z webu nepřístupné |
| `tools/` | testy, nástroj na slovníky, skript pro vydání |
| `docs/` | tato příručka (`docs/prirucka/`) a postup vydávání |

`config.php` s přístupem k databázi vytvoří instalátor. Do repozitáře nepatří; vzor je `config.sample.php`.

## Jak proteče požadavek

**Web.** `Front\Kernel` nastaví adresu webu a časové pásmo, případně provede čekající migrace, rozpozná jazykovou verzi z předpony adresy (`/en/…`) a vybere šablonu vzhledu. Pak zkusí cache stránek, a když neuspěje, podle cesty zavolá obsluhu (článek, rubrika, stránka, kanály, účet čtenáře…). Výsledek obalí `base.php` šablony. Šablona se hledá nejdřív ve složce šablony vzhledu, potom v `system/views/front/`.

**Administrace.** `Admin\Kernel` ověří přihlášení a u každého požadavku POST token CSRF. Podle parametru `modul` najde třídu v seznamu `Kernel::MODULY`, ověří rozšíření a oprávnění a zavolá metodu `akce<Název>()` – `?modul=clanky&akce=edit` vede na `Moduly\Clanky::akceEdit()`.

### Nový modul administrace

1. Třída v `system/src/Admin/Moduly/` dědící z `Modul`. Konstanty `IDENT`, `NAZEV`, `SKUPINA` (skupina v nabídce), `IKONA`; podle potřeby `ROZSIRENI` (klíč rozšíření) a `JEN_ADMIN`.
2. Metody `akceVypis()`, `akceEdit()`, `akceUloz()`… vracejí `Response`.
3. Šablony v `system/views/admin/<ident>/`.
4. Zápis třídy do `Admin\Kernel::MODULY`.

## Pojmenování

Doména systému je česká a kód to odráží:

- **Česky bez diakritiky:** tabulky a sloupce (`rs_clanky.titulek`, `rs_topic`), metody modulů (`akceUloz`), proměnné domény (`$clanek`, `$rubrika`), šablony (`vypis.php`, `formular.php`), klíče nastavení (`nazev_webu`).
- **Česky s diakritikou:** komentáře, texty pro uživatele, zprávy commitů.
- **Anglicky:** API jádra v `Core/` (`Request::post()`, `Db::all()`, `Settings::get()`).

Tabulky mají předponu (výchozí `rs_`). V dotazech se píše `{clanky}` a předponu doplní `Db`.

Texty pro uživatele se obalují funkcí `t('Česky')`. Klíčem slovníku je český text; co ve slovníku chybí, zobrazí se česky.

## Spuštění při vývoji

```
php -S localhost:8080 system/dev-router.php
```

Potřebujete PHP 8.4+ s rozšířeními `pdo_mysql`, `mbstring` a `gd` a běžící MySQL nebo MariaDB. Čistou instalaci vyvoláte smazáním `config.php`, odstraněním tabulek `rs_*` a otevřením `/install.php`.

## Související

- [Zásady projektu](zasady.md)
- [Testy a vydávání](testy-a-vydavani.md)
- [Vlastní šablona](../vzhled/vlastni-sablona.md)
- [Požadavky](../zaciname/pozadavky.md)
