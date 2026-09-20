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
  Asistent přímo v editoru je hotový jako samostatné rozšíření (viz M-X).

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
- **Identita webu** (Vzhled → Identita webu): šablona, logo, ikona webu, hlavní barva (vzorník + kontrola čitelnosti)
  a písma titulků a textu s živou ukázkou; propisuje se do všech tří šablon přes `--rs-akcent`, `--rs-pismo-titulky`,
  `--rs-pismo-text`. Jen systémová písma – nic se nestahuje z cizích serverů.
- **Jednoduché ovládání:** vizuální editor bloků ve stránce webu, volby jako karty, pokročilá nastavení schovaná.

- **Fotogalerie v článku:** tlačítko „galerie" v editoru (výběr více fotek z Médií), mřížka na webu a prohlížečka fotek přes celou
  obrazovku (klávesnice, tažení prstem); otevírá i jednotlivé obrázky v textu. Společné prvky článku jsou v `image/web.css|js`.
- **Čtenáři a zamčený obsah** (rozšíření): registrace s potvrzením e-mailem, přihlášení podepsanou cookie (bez session, web zůstává
  cachovatelný), zapomenuté heslo, smazání účtu; článek „jen pro přihlášené" nebo „jen pro předplatitele" s ukázkou prvních odstavců
  a výzvou; předplatné zapisuje administrátor ručně (modul Čtenáři, export CSV); zamčený text neunikne přes RSS, API ani `.md`;
  strukturovaná data `isAccessibleForFree`. Platební brána záměrně není.
- **AI asistent v editoru** (rozšíření): návrhy titulků, perexu, shrnutí „Ve zkratce", SEO popisu a štítků, korektura s výběrem oprav
- **Překlad článku asistentem:** v editoru (Překlad článku) založí koncept v rubrice cílového jazyka, propojený s originálem; formátování, obrázky a galerie zůstávají z originálu. Firemní proxy: `define('PHPRS_AI_URL', '…')` v `config.php`.
- **Vložení ze sítí adresou:** adresa příspěvku (X, Instagram, Facebook, TikTok, Mastodon) nebo videa na samostatném řádku se na webu promění ve vložený rámec, který se načte až po kliknutí čtenáře.
- **Jazykové verze do hloubky:** vlastní jazyk mají i novinky, ankety a newsletter – odběratel dostává vydání v jazyce verze webu, na které se přihlásil, a e-mail je celý v tomto jazyce.
- **Paleta příkazů:** Ctrl/⌘+K v administraci – skok do sekce, rychlé akce (nový článek, nastavení pošty…) a hledání článku k úpravě, bez diakritiky.
  a popisy obrázků (vidí obrázek). Klíč Claude API a model zadá správce v Nastavení → Rozšíření; klíč se nikdy nevypisuje zpět.
  Asistent jen navrhuje, nic neukládá; limit 60 dotazů za hodinu na uživatele.
- **Kontrola přístupnosti obsahu** v editoru: obrázky bez popisu (doplní se přímo v panelu), přeskočené úrovně mezititulků, nic neříkající
  odkazy, tabulky bez záhlaví, rámce bez názvu, titulek verzálkami.
- **Web Push** (rozšíření): oznámení o novém článku bez cizí služby a bez knihoven – VAPID (ES256) přes OpenSSL, zpráva bez obsahu,
  `sw.js` si titulek stáhne z `/push.json`; blok Oznámení, `manifest.webmanifest` (iOS), rozesílka po dávkách, zaniklé odběry se mažou samy.
- **Oznámení o vydání i pro naplánované články:** webhook, IndexNow a Web Push odchází, jakmile čas vydání nastane (`Core\Oznameni`,
  kontrola po návštěvách nejvýš jednou za minutu) – platí i pro články vydané přes Claude (MCP).
- **Jazyk webu a jazykové verze** (rozšíření): jazyk webu (cs, sk, en, de) přeloží texty šablon přes `t()` a slovníky `system/jazyky/`;
  další verze běží na `/en/`, `/de/`… a mají své rubriky, články a stránky. Jazyk se volí u rubriky, článek ho převezme; překlad se
  propojí s originálem (přepínač jazyků vede přímo na překlad, `hreflang`, `og:locale`, `inLanguage`); bloky jdou omezit na jazyk;
  společná mapa webu, RSS/llms.txt/podcast pro každou verzi zvlášť.
- **Typy obsahu:** živá reportáž (průběžné zápisy, čtenářům se načítají samy, `LiveBlogPosting`), přehrávač zvuku a videa (soubor,
  YouTube, Vimeo, Spotify – cizí přehrávač se načte až po kliknutí), podcastový kanál `/podcast.xml`, recenze s hodnocením v % (`Review`).
- **Jazyk administrace** podle uživatele (Můj účet): čeština, slovenština, angličtina. Texty všech obrazovek jdou přes `t()` (slovníky
  `system/jazyky/admin-*.php`), texty skriptů přes `T()` (`image/jazyky/admin-*.js`); hlášky skládané z proměnných a vizuální editor bloků
  zůstávají česky.
- **Pošta:** odesílání přes vlastní SMTP server (STARTTLS/SSL, přihlášení, opakované použití spojení při rozesílce) nebo funkcí `mail()`;
  Nastavení → Pošta, adresa odesílatele, Reply-To, zkušební e-mail s čitelnou chybou.
- **Měkký paywall:** N zamčených článků měsíčně zdarma pro kohokoli (podepsaná cookie, počítá se jen na stránce článku).
- **Článek pro čtenáře:** odkazy pro sdílení (bez cizích skriptů, na telefonu systémové sdílení), osnova z mezititulků s kotvami,
  automatické související články podle štítků a rubriky, medailonek autora (pozice, fotka, pár vět – Můj účet), tabulky v editoru,
  přehrávač z adresy videa vložené na samostatný řádek, mobilní menu rubrik.
- **Hledání bez diakritiky** („nabrezi" najde „nábřeží") nad vlastním fulltextovým indexem; zamčené články indexují jen titulek a perex.
- **Přílohy ke stažení** v Médiích (PDF, dokumenty, tabulky, ZIP, zvuk, video) – do článku se vkládají jako odkaz s typem a velikostí.
- **Pošta s frontou:** nepovedené odeslání se opakuje (5 min až 12 h), v Nastavení → Pošta je přehled posledních zpráv.
- **Zálohy:** obnova databáze ze zálohy přímo v administraci, před obnovou vždy vznikne pojistná záloha.
- **Správa:** prohlížeč záznamu chyb (Stav systému), přehled adres 404 s nabídkou přesměrování, blok „Podpořte nás",
  doba čtení a ukazatel průběhu, tiskový styl, stránka 404 s hledáním a nejčtenějšími články.
- **Redakční nástroje:** Titulní strana (ruční pořadí článků nahoře na hlavní stránce přetažením), Štítky a témata (přejmenování,
  sloučení, stránka tématu s úvodem a obrázkem), porovnání verzí článku („co se změnilo"), dialog odkazu s hledáním vlastních článků,
  přehled s grafem návštěvnosti za 14 dní a frontou práce.
- **Newsletter:** automatický výběr nových článků (týdně/denně), naplánované odeslání na pozadí, souhrnná statistika otevření
  a prokliků bez sledování jednotlivců, šablona s logem a barvou webu.
- **Šablony článku:** Standardní, Dlouhé čtení, Fotoreportáž, Rozhovor – fungují ve všech třech layoutech.
- **Tmavý režim webu** ve všech třech šablonách podle zařízení čtenáře (Vzhled → Identita webu, výchozí vypnuto).
- **Čtenáři a diskuse:** přihlášení odkazem z e-mailu (bez hesla), odběr newsletteru při registraci, komentáře pod účtem čtenáře
  (volitelně jen pro přihlášené), upozornění na odpověď e-mailem, nahlášení komentáře (po třech nahlášeních čeká na redakci).
- **Oprávnění podle rubriky:** uživateli jde vymezit rubriky (včetně podrubrik), ve kterých smí psát a upravovat – platí v administraci i přes napojení na Claude.
- **Autoři:** spoluautoři článku a externí autor (host, agentura); stránka autora ukazuje i spoluautorské články.
- **Zálohy mimo server:** každá nová záloha databáze se může sama nahrát na FTP(S) nebo do úložiště kompatibilního s S3.
- **Hranice úprav:** přes napojení na Claude jde měnit jen obsah a vlastní šablony. Šablona je prezentační vrstva – PHP soubor,
  který by sahal na soubory, databázi, síť nebo kód systému, se neuloží (`Core\SablonaKontrola`). Vydání nese podepsaný seznam
  souborů a Stav systému hlásí zásahy do jádra; pravidla pro Claude Code jsou v `layout/CLAUDE.md`.
- **Testy:** `php tools/testy.php` – jednotkové testy jádra bez databáze (součást `tools/test.sh`).
- **Redakce a provoz:** e-mail redakci o komentářích (nejvýš jednou za 10 minut), adresa pro cron `/ulohy?token=…` (Stav systému),
  „První kroky" po instalaci na přehledu administrace.
- **Provoz:** migrace databáze se provedou i při první návštěvě webu (se zámkem), takže automatická aktualizace web nerozbije.

Zbývá:

- Platební brána pro předplatné, šifrovaný obsah Web Push zpráv, Download sekce (nízká priorita).
- **Více webů z jedné instalace se dělat nebude** (rozhodnutí 2026-09-19) – jedna instalace = jeden web.
- Další náměty po rešerši celého systému: `docs/NAVRHY.md`.

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
