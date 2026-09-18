# phpRS 3

Redakční systém pro internetové časopisy a magazíny, napsaný od nuly pro PHP 8.4+ a MySQL 8 / MariaDB 10.6+.
Hlásí se k odkazu českého [phpRS](https://phprs.net/) (Jiří Lukáš, 2001–2007), jehož vývoj skončil:
přebírá jeho jednoduchost, zaměření na články a rubriky a české názvosloví v databázi.

**Není to nová verze starého phpRS a nejde na ni přejít.** Data ze starého phpRS 2 se nepřevádějí, staré adresy
se nepřesměrovávají a žádná stará funkce se kvůli kompatibilitě nedrží. Jedinou vzpomínkou je prostředí
administrace „phpRS retro" – pro zábavu přepínatelný vzhled původního systému (modré menu, šedá tlačítka,
Verdana) nad úplně stejnými funkcemi, jaké má moderní prostředí „phpRS 2026".

Co je uvnitř: PDO a připravené dotazy všude, `password_hash`, CSRF ochrana, InnoDB s cizími klíči, utf8mb4,
hezké adresy (`/clanek/titulek`), responzivní administrace i web, žádné globální proměnné, žádný framework.

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
RSS.

Plán (pořadí = priorita; stav k 18. 9. 2026):

- **M2 – moderní tvorba článků:** hotovo – Média se složkami a vazbou na články, WYSIWYG editor s automatickým
  ukládáním, dvousloupcový editor, stavy koncept / naplánováno / vydáno a vydání jedním klikem, připnutí,
  štítky, seriály, historie verzí, statické Stránky, redakční kalendář.
- **M3 – čtenáři:** hotovo – Komentáře (reakce, moderace, režim schvalování; antispam bez cookies a bez CAPTCHA:
  podepsaný časový zámek, honeypot, limit na IP, komentáře s odkazy čekají na schválení), hodnocení článků
  hvězdičkami, Ankety (blok Anketa, jeden hlas na čtenáře), vlastní Statistika bez cookies (návštěvy,
  zobrazení, nejčtenější články, zdroje návštěv; IP adresy se neukládají). Zbývá: anketa u konkrétního článku,
  upozornění redakce na nový komentář e-mailem.
- **M3b – Nastavení s podzáložkami:** hotovo – Základní · Vzhled · SEO a GEO · Měření · Soukromí a cookies · Stav systému.
  - *SEO:* robots.txt, přepínač indexování, sitemap.xml + Google News sitemap, schema.org (NewsArticle, FAQPage,
    BreadcrumbList, WebSite, Organization, Person), Open Graph / Twitter Cards, vlastní titulek, popis a noindex
    u článku, přesměrování 301 (modul Přesměrování + automaticky při změně adresy článku), IndexNow, ověření
    Search Console a Bing.
  - *GEO:* pravidla pro AI roboty, llms.txt, Markdown verze článku, blok „Ve zkratce", otázky a odpovědi (FAQ),
    stránky autorů, JSON Feed.
  - *Měření:* vestavěná statistika, GA4 (Consent Mode v2), Matomo, Plausible, vlastní kód do hlavičky.
  - *Soukromí a cookies:* vestavěná lišta / externí služba (Cookiebot) / nic; evidence souhlasů bez IP adres.
  - *Stav systému:* kontroly serveru, databáze, souborů, bezpečnosti a provozu, zkušební e-mail,
    bezpečnostní hlavičky, `/stav.json?token=…`. Kontrola „poslední záloha" přijde se Zálohou DB v M5.
- ~~**M4 – import z phpRS 2.x**~~ – zrušeno (18. 9. 2026): phpRS 3 je samostatný systém bez cesty ze starého phpRS.
- **M5 – rozšíření a provoz:** hotovo (kromě položek „Později").
  - *Rozšíření:* uzavřený systém. Všechna rozšíření jsou součástí balíčku a píše je tým phpRS; administrátor je jen
    zapíná a vypíná (Nastavení → Rozšíření). Cizí plug-iny ani veřejné API pro ně neexistují – cílem je systém,
    kde je vše připravené. Jádro (články, média, rubriky, stránky, bloky, uživatelé, nastavení) vypnout nejde.
  - *Reklamní systém:* bannery (obrázek / kód), pozice (blok Reklama, pod článkem), plánování od–do, váha,
    strop zobrazení, počítání zobrazení a prokliků, označení „Reklama", `ads.txt`.
  - *Zálohy:* záloha databáze jedním klikem i automaticky jednou týdně, stažení, kontrola stáří ve Stavu systému.
  - *Aktualizace:* administrace čte podepsaný soubor `aktualizace.json` (hostovaný na webu projektu / GitHub Pages),
    balíček je ZIP v GitHub Releases. Ověřuje se SHA-256 a podpis Ed25519 (veřejný klíč je součástí systému,
    soukromý má jen vydavatel). Před aktualizací se zálohuje databáze; nepřepisuje se `config.php`, `media/`,
    `storage/` ani vlastní layouty; migrace databáze proběhnou samy. Vydání připravuje `tools/vydani.php`.
  - Později: Download sekce (nízká priorita), levely a registrace čtenářů, slovenština a angličtina administrace.
- **M6 – Bloky 2.0:** hotovo – zóny, rozvržení, přetahování myší, pojmenované vzhledy, typy bloků (otvírák, články
  z rubriky, nejčtenější, rubriky, štítky, archiv, autoři, menu, stránky, vyhledávání, novinky, anketa, sociální sítě,
  kontakt, reklama, vlastní HTML), viditelnost podle rubriky a zařízení, archiv po měsících (`/archiv/2026-09`).
- **M7 – napojení na Claude:** hotovo – rozšíření „Napojení na Claude" (výchozí vypnuto) = MCP server na `/mcp`
  (Streamable HTTP, JSON-RPC). Token si uživatel vytvoří v nabídce Můj účet; Claude jedná s jeho právy. Nástroje:
  články (seznam, čtení, založení jako koncept, úprava s historií verzí, vydání jen s právem vydávat), rubriky, média,
  bloky a – jen pro administrátora – šablony webu: kopie šablony, čtení a ukládání souborů (kontrola syntaxe PHP,
  vestavěné šablony jsou jen ke čtení), náhled `?sablona=…`, aktivace. Každý zásah jde do Protokolu změn.
  Zbývá: asistent přímo v editoru (návrh titulků, perexu, korektura) – vyžaduje API klíč správce.

### M-X – schválené náměty

Hotovo:

- **Bezpečnost účtů:** dvoufázové přihlášení (TOTP, záložní kódy), stránka Můj účet, protokol změn, zámek proti
  souběžné úpravě článku.
- **Výkon:** cache celých stránek pro nepřihlášené, WebP varianty a `srcset`, lazy-loading.
- **Redakční workflow:** stavy koncept → ke korektuře → schváleno → vydáno, interní poznámka u článku, označení
  „Aktualizováno".
- **Newsletter** (rozšíření): přihlášení s potvrzením e-mailem, blok Newsletter, vydání = předmět + úvod + vybrané
  články, zkušební odeslání, rozesílka po dávkách, odhlášení jedním klikem, export odběratelů.
- **Distribuce:** webhook po vydání článku (Make, Zapier, IFTTT, n8n → sdílení na sociální sítě, Slack…).
- **Veřejné API** (rozšíření): `/api/clanky`, `/api/clanky/<adresa>`, `/api/rubriky`.
- **Právo:** žádost čtenáře o osobní údaje – export a výmaz podle e-mailu (komentáře, newsletter).
- **Provoz:** režim údržby, záloha médií do ZIP, úlohy spouštěné návštěvou (aktualizace, zálohy).
- **Jednoduché ovládání:** vizuální editor bloků ve stránce webu, volby jako karty, pokročilá nastavení schovaná.

Zbývá (větší celky):

- **Registrace čtenářů a uzamčený obsah:** článek jen pro přihlášené či předplatitele, měkký paywall.
- **Typy obsahu:** fotogalerie v článku s prohlížečkou, živá reportáž, podcast/video s přehrávačem, recenze s hodnocením.
- **Vícejazyčnost** webu a **více webů z jedné instalace**.
- **Web Push**, webhook i pro články naplánované do budoucna (dnes se volá při ručním vydání).
- **Kontrola přístupnosti obsahu** v editoru (alt texty, hierarchie nadpisů).
- **Asistent v editoru** (návrh titulků a perexu, korektura) – navazuje na M7.
- Slovenština a angličtina administrace, Download sekce.

## Distribuce a podpora projektu

- **Zdarma, open source (GPL).** Systém se nabízí bez poplatků a bez placených verzí.
- **Web projektu:** co systém umí, živé demo, stažení, dokumentace, seznam změn, „Podpořte vývoj". Web poběží
  na phpRS 3 jako referenční instalace a hostuje i `aktualizace.json`.
- **Dobrovolný příspěvek:** QR platba na účet, GitHub Sponsors, případně Ko-fi / Stripe. V administraci jen
  nenápadný odkaz „Podpořit vývoj" u Stavu systému (lze skrýt) – žádné naléhavé výzvy ani omezené funkce.
- **Vydávání:** zdrojový kód a Releases na GitHubu; instalace se aktualizují z administrace (viz M5).

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
