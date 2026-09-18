# phpRS 3

Redakční systém pro internetové časopisy a magazíny. Duchovní nástupce českého
[phpRS](https://phprs.net/) (Jiří Lukáš, 2001–2007; komunitní verze do 2.8.3a) napsaný
od nuly pro PHP 8.4+ a MySQL 8 / MariaDB 10.6+.

Co zůstává z originálu: jednoduchost, zaměření na články a rubriky, administrace
`admin.php?modul=clanky&akce=edit`, tabulky `rs_*` s českými názvy sloupců, web složený ze
sloupců a bloků, šablony článků se třemi režimy (náhled / krátký / celý), autoři s právem
vydávat a vazbami nadřízený–podřízený. A vzhled administrace – modré menu, šedá tlačítka, Verdana.

Co je nové: PDO a připravené dotazy všude, `password_hash`, CSRF ochrana, InnoDB s cizími
klíči, utf8mb4, hezké adresy (`/clanek/titulek`), responzivní administrace i web, žádné globální proměnné.

## Instalace

1. Nahrajte obsah složky na hosting (FTP stačí, Composer ani příkazová řádka nejsou potřeba).
2. Založte prázdnou databázi.
3. Otevřete `https://vas-web.cz/install.php` a vyplňte formulář.
4. Smažte `install.php`.

Apache používá přiložený `.htaccess`. Pro nginx: vše, co není soubor, směrujte na `index.php`
a zakažte přístup do `system/`, `storage/` a ke `config.php`.

## Vývoj

```bash
php -S localhost:8080 system/dev-router.php
```

## Struktura

```
index.php, admin.php, install.php   vstupní body (view.php a search.php jen přesměrují staré adresy phpRS 2)
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
| `layout/default` | phpRS | původní rozvržení: tři sloupce, bloky po stranách |
| `layout/classic-newspaper` | Classic Newspaper | seriózní deník – patkové titulky, tenké linky, otvírák, pravý sloupec |
| `layout/modern-magazine` | Modern Magazine | výrazný magazín – černá lišta, hero článek, mřížka karet, pás bloků dole |

Layout = `base.php` (stránka), `blok.php` (jeden blok), `cla_*.php` (šablony článku s režimy
náhled / krátký / celý; `$poradi === 0` je první článek titulní strany), `style.css` a `info.php`
(název a popis). Vybírá se při instalaci a v Konfiguraci. Vlastní layout: zkopírujte některou složku pod
jiným názvem. Layout může přepsat i kteroukoli šablonu ze `system/views/front/` (výpis, systémové bloky, RSS).
Layouty nepoužívají externí písma ani skripty (GDPR, rychlost).

### Prostředí administrace

Administrace má dva vzhledy nad **stejným HTML**: `phpRS retro` (`image/admin.css`, podoba phpRS 2.8) a
`phpRS 2026` (`image/admin-2026.css`, moderní, responzivní, světlý i tmavý režim). Výchozí se volí při
instalaci a v Konfiguraci, každý autor si přepíná sám v horní liště (`rs_user.prostredi`). Nové šablony
administrace proto pište jen s existujícími třídami (`.formular .radek`, `table.vypis`, `.tl`,
`a.navigace`…) a bez vložených barev – pak fungují v obou prostředích.

## Stav

Hotovo (milník 1): instalátor s volbou prostředí a šablony, dvě prostředí administrace, tři šablony webu, přihlášení, Editace autorů (práva, vazby), Editace článků, Editace
novinek, Úprava bloků, Úprava rubrik, Konfigurace; web: hlavní stránka, článek, rubrika, vyhledávání,
RSS, přesměrování starých adres phpRS 2.

Plán (pořadí = priorita; stav k 18. 9. 2026):

- **M2 – moderní tvorba článků:** hotovo – Média se složkami a vazbou na články, WYSIWYG editor s automatickým
  ukládáním, dvousloupcový editor, stavy koncept / naplánováno / vydáno a vydání jedním klikem, připnutí,
  štítky, seriály, historie verzí (20 posledních), statické Stránky. Zbývá: redakční kalendář.
- **M3 – čtenáři:** Komentáře (moderace, antispam bez cookies: honeypot + časový zámek), Ankety, hodnocení
  článků, vlastní Statistika bez cookies (návštěvy, nejčtenější, zdroje).
- **M3b – Nastavení s podzáložkami:** hotovo – Základní · Vzhled · SEO a GEO · Měření · Soukromí a cookies · Stav systému.
  - *SEO:* robots.txt, přepínač indexování, sitemap.xml, schema.org (NewsArticle, BreadcrumbList, WebSite,
    Organization), Open Graph / Twitter Cards, výchozí obrázek pro sdílení, ověření Search Console a Bing.
    Zbývá: noindex u rubriky/článku, vlastní titulek a popis článku, správa přesměrování 301 (+ automaticky
    při změně adresy), Google News sitemap, IndexNow.
  - *GEO:* pravidla pro AI roboty v robots.txt, llms.txt, Markdown verze článku (`/clanek/….md`).
    Zbývá: blok „Ve zkratce" a FAQ se schema.org, `Person` stránky autorů, JSON Feed.
  - *Měření:* GA4 (Consent Mode v2), Matomo, Plausible, vlastní kód do hlavičky.
  - *Soukromí a cookies:* vestavěná lišta (nezbytné / analytické / marketingové, skripty až po souhlasu,
    odvolání souhlasu), nebo externí služba (Cookiebot a kompatibilní), nebo nic. Zbývá: evidence souhlasů.
  - *Stav systému:* kontroly serveru, databáze, souborů, bezpečnosti a provozu + `/stav.json?token=…`.
    Zbývá: test odeslání e-mailu, bezpečnostní hlavičky, poslední záloha.
- **M4 – import z phpRS 2.x:** převod `rs_*` tabulek včetně kódování win-1250 / ISO-8859-2 → UTF-8,
  zachování `link` (staré adresy fungují dál), obrázků galerie a hesel (přehashování při prvním přihlášení).
- **M5 – rozšíření:** plug-iny (položka menu + systémový blok + háčky), **Reklamní systém** (pozice,
  kampaně, počítání zobrazení a prokliků, plánování, ads.txt), Download sekce (nízká priorita), levely a
  registrace čtenářů, Záloha DB, Správa modulů, slovenština a angličtina administrace.
  Weblinky se nepřebírají – v dnešním webu nemají místo.
- **M6 – Bloky 2.0:** ~~zóny (hlavička, sloupce, nad/pod obsahem, patička), rozvržení 3/2/1 sloupec/plná šířka,
  přetahování myší, pojmenované vzhledy~~ (hotovo); zbývají nové typy
  bloků: menu (vlastní odkazy), článek/články z rubriky, otvírák, karusel, štítky, autoři, newsletter
  (formulář), sociální sítě, kontakt, HTML/embed, reklamní pozice, „Ve zkratce", kalendář/archiv;
  viditelnost podle rubriky a zařízení.
- **M7 – napojení na Claude (AI):** vestavěný **MCP server** (`/mcp` s tokeny a právy autora), přes který
  Claude umí číst a psát články, rubriky, bloky a **vytvářet a upravovat layouty** (soubory šablony v
  izolované složce, náhled před aktivací, vrácení zpět); v editoru pak asistent: návrh titulků a perexu,
  korektura, shrnutí „Ve zkratce", SEO popis, alt texty obrázků, štítky. Klíč k API zadává správce v
  Konfiguraci; bez něj systém funguje beze změny.

### M-X – schválené náměty (uživatel 18. 9. 2026 odsouhlasil všechny; pořadí se určí průběžně)

- **Newsletter:** sběr odběratelů (double opt-in), odeslání výběru článků; navazuje na původní „Poštovní centrum".
- **Placený / uzamčený obsah:** navazuje na levely – článek jen pro přihlášené či předplatitele, měkký paywall.
- **Dvoufázové přihlášení (TOTP)** pro redakci, protokol změn (kdo co kdy upravil), vynucení silných hesel.
- **Výkon:** cache celých stránek pro nepřihlášené, WebP/AVIF a `srcset` u obrázků, lazy-loading, kritické CSS.
- **Redakční workflow:** stavy koncept → ke korektuře → schváleno → vydáno, interní poznámky u článku,
  zámek proti souběžné úpravě, redakční kalendář.
- **Typy obsahu:** fotogalerie v článku, živá reportáž (online přenos po minutách), podcast/video s přehrávačem,
  rozhovor, recenze s hodnocením, opravy a aktualizace článku („Aktualizováno").
- **Distribuce:** automatické sdílení (Mastodon/Bluesky/Facebook/X přes webhooky), JSON Feed, Web Push,
  AMP se nepřebírá.
- **Vícejazyčnost** webu a **více webů z jedné instalace** (předpona tabulek už to umožňuje).
- **Právo a přístupnost:** cookie lišta jen když je co odsouhlasit, export/výmaz osobních údajů (GDPR),
  kontrola přístupnosti obsahu (alt texty, hierarchie nadpisů) přímo v editoru.
- **Provoz:** automatické zálohy (DB + média) do ZIP / na vzdálené úložiště, aktualizace systému jedním
  klikem s kontrolou podpisu, režim údržby, cron bez cronu (úlohy spouštěné návštěvou).
- **API:** veřejné čtecí REST/JSON API pro mobilní aplikaci nebo headless použití.

## Licence

GNU GPL verze 2 nebo novější – stejně jako původní phpRS. Text licence je v souboru `LICENSE`.
