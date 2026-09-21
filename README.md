<p><picture><source media="(prefers-color-scheme: dark)" srcset="image/phprs-logo-tmavy.svg"><img src="image/phprs-logo.svg" alt="phpRS" height="48"></picture></p>

# phpRS 3

Redakční systém pro internetové časopisy a magazíny, napsaný od nuly pro PHP 8.4+ a MySQL 8 / MariaDB 10.6+.
Hlásí se k odkazu českého [phpRS](https://phprs.net/) (Jiří Lukáš, 2001–2007), jehož vývoj skončil:
přebírá jeho jednoduchost, zaměření na články a rubriky a české názvosloví v databázi.

**Není to nová verze starého phpRS a nejde na ni přejít.** Data ze starého phpRS 2 se nepřevádějí, staré adresy
se nepřesměrovávají a žádná stará funkce se kvůli kompatibilitě nedrží.

Co je uvnitř: PDO a připravené dotazy všude, `password_hash`, CSRF ochrana, InnoDB s cizími klíči, utf8mb4,
hezké adresy (`/clanek/titulek`), responzivní administrace i web, žádné globální proměnné, žádný framework.

## Instalace

1. Nahrajte obsah složky na hosting (FTP stačí, Composer ani příkazová řádka nejsou potřeba).
2. Založte prázdnou databázi.
3. Otevřete `https://vas-web.cz/install.php` a vyplňte formulář.
4. Smažte `install.php`.

Apache používá přiložené soubory `.htaccess`. Nginx je nečte – použijte hotovou ukázku `system/nginx.priklad.conf`
(zákazy přístupu, média bez spouštění skriptů, WebP, hezké adresy) a po nasazení ověřte, že `/config.php` a `/storage/log/chyby.log` vracejí 403.

## Vývoj

```bash
php -S localhost:8080 system/dev-router.php
```

## Struktura

```
index.php, admin.php, install.php   vstupní body
config.php                          vytvoří instalátor
image/                              CSS, JS a logo administrace
layout/<název>/                     vzhled webu: base.php, blok.php, cla_*.php, style.css
media/RRRR/MM/                      nahrané obrázky (galerie)
plugins/                            plug-iny (připravuje se)
storage/                            logy a cache, z webu nepřístupné
system/src/Core/                    jádro: App, Db, Request, Response, Session, View, Auth, Settings
system/src/Admin/Moduly/            moduly administrace - jeden modul = jedna třída
system/src/Front/                   veřejná část webu
system/views/                       šablony administrace, instalátoru a výchozí šablony webu
system/sql/schema.sql               struktura databáze
```

Nový modul administrace: třída v `system/src/Admin/Moduly/` dědící z `Modul` (konstanty `IDENT`,
`NAZEV`, metody `akceVypis()`, `akceEdit()`…), šablony ve `system/views/admin/<ident>/` a zápis do
`Kernel::MODULY`.

### Šablony webu (layouty)

| složka | název | vzhled |
| --- | --- | --- |
| `layout/classic-newspaper` | Classic Newspaper | seriózní deník – patkové titulky, tenké linky, otvírák, pravý sloupec |
| `layout/modern-magazine` | Modern Magazine | výrazný magazín – černá lišta, hero článek, mřížka karet, pás bloků dole |
| `layout/minimal` | Minimal | osobní magazín, blog, newsletterový web – jeden úzký sloupec, klidná typografie, čistý seznam článků |

Layout = `base.php` (stránka), `blok.php` (jeden blok), `cla_*.php` (šablony článku s režimy
náhled / krátký / celý; `$poradi === 0` je první článek titulní strany), `style.css` a `info.php`
(název a popis). Vybírá se při instalaci a ve Vzhled → Identita webu. Vlastní layout: zkopírujte některou složku pod
jiným názvem a v `base.php` opravte odkaz na `style.css` na novou složku. Základní vzhled společných prvků (komentáře, anketa,
hodnocení, typy bloků…) je v `image/web.css` s nulovou vahou – `style.css` šablony nese jen to, co se liší. Layout může přepsat i kteroukoli šablonu ze `system/views/front/` (výpis, systémové bloky, RSS).
Layouty nepoužívají externí písma ani skripty (GDPR, rychlost).


## Údržba a bezpečnost (pro vydavatele)

- **Žádné cizí knihovny za běhu** – není co hlídat kvůli zranitelnostem závislostí; Dependabot sleduje jen GitHub Actions.
- **Každá změna:** `.github/workflows/kontrola.yml` – kouřový test `tools/test.sh` (čistá instalace + průchod webem
  a administrací) na PHP 8.4 a 8.5, Semgrep (bezpečnostní pravidla), Gitleaks (klíče a hesla v repozitáři).
  Běží i každé pondělí bez změn. Lokálně: `tools/test.sh` (potřebuje MySQL; databázi `phprs3_test` smaže a vytvoří).
- **Vydání:** zvýšit `PHPRS_VERSION`, commit, `git tag -a v3.0.1 -m "- oprava …"` a push tagu →
  `.github/workflows/vydani.yml` sestaví ZIP, podepíše `aktualizace.json` (tajemství `PHPRS_KLIC` v prostředí
  `vydani` s povinným schválením), založí Release a zveřejní manifest na GitHub Pages. Ručně totéž umí
  `php tools/vydani.php`.
- **Bezpečnostní oprava:** do zprávy tagu přidat `[bezpecnostni]`. Instalace se po novinkách dívají dvakrát denně,
  bezpečnostní verzi si nainstalují samy (lze vypnout), správce dostane e-mail. Postup hlášení chyb: `SECURITY.md`.

## Licence

GNU GPL verze 2 nebo novější – stejně jako původní phpRS. Text licence je v souboru `LICENSE`.
