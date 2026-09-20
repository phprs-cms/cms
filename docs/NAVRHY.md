# Co v phpRS 3 ještě chybí – náměty po rešerši (2026-09-19)

Seřazeno podle přínosu pro redakci malého až středního magazínu. U každého bodu je odhad pracnosti:
**S** = do půl dne, **M** = den až dva, **L** = několik dní. Nic z toho není slíbené – je to zásobník k rozhodnutí.

## 1. Funkce, které redakce čeká a dnes je nenajde

| # | Námět | Proč | Pracnost |
|---|-------|------|----------|
| 1 | ✅ HOTOVO · **Tlačítka sdílení pod článkem** (Web Share API na mobilu, jinak odkazy na Facebook, X, LinkedIn, WhatsApp, e-mail, „kopírovat odkaz") | dnes nejde článek sdílet jinak než zkopírováním adresy; bez cizích skriptů, jen odkazy | S |
| 2 | ✅ HOTOVO · **Medailonek autora**: fotka, pár vět o autorovi, sociální sítě; rámeček pod článkem a hlavička stránky autora | účet má jen jméno a web; důvěryhodnost autora (E-E-A-T) je dnes pro vyhledávače i AI podstatná | M |
| 3 | ✅ HOTOVO · **Více autorů u článku** a „externí autor" bez účtu | společné texty, hosté, agenturní zprávy | M |
| 4 | ✅ HOTOVO · **Automatické související články** podle štítků a rubriky (dnes jen ručně přes seriál) | drží čtenáře na webu bez práce redakce | S |
| 5 | ✅ HOTOVO · **Hledání bez diakritiky a s našeptávačem** („nabrezi" najde „nábřeží"; fulltext nad indexem už běží) | čtenáři na mobilu diakritiku nepíšou | M |
| 6 | ✅ HOTOVO · **Obsah článku (osnova)** z mezititulků u dlouhých textů + kotvy u H2 | dlouhé čtení, odkazování na část článku | S |
| 7 | ◐ ČÁSTEČNĚ (video a podcast z adresy na samostatném řádku; příspěvky ze sítí zbývají) · **Vložení příspěvku ze sítí a videa vložením adresy** do textu (YouTube, Vimeo, X, Instagram, Mastodon – načtení až po kliknutí jako u přehrávače) | dnes jen ruční `<iframe>` v HTML režimu | M |
| 8 | ✅ HOTOVO · **Tabulky v editoru** (vložit tabulku, přidat řádek/sloupec, záhlaví) | čistič HTML je povoluje, ale editor je neumí vytvořit | M |
| 9 | ✅ HOTOVO · **Výběr odkazu na vlastní článek** v dialogu odkazu (hledání podle titulku) | interní prolinkování je základ SEO | S |
| 10 | **Ořez a otočení obrázku, bod zájmu** pro výřezy v otvíráku | dnes se fotka ořízne na střed | M |
| 11 | ✅ HOTOVO · **Přílohy ke stažení** (PDF, tabulky) v Médiích a v článku – nahrazuje zamýšlenou „Download sekci" | tiskové zprávy, dokumenty ke kauzám | M |
| 12 | ✅ HOTOVO · **Ukládání rozepsaného článku na server** (dnes jen v prohlížeči) a porovnání revizí (co se změnilo) | práce z více zařízení, korektury | M |
| 13 | ✅ HOTOVO · **Kurátorovaná hlavní stránka**: ruční pořadí prvních N článků přetažením (dnes jen „připnout") | editor dne si chce titulní stranu poskládat | M |
| 14 | ✅ HOTOVO · **Stránky témat**: štítek s popisem, obrázkem a vlastním úvodem („speciál") | volby, kauzy, festivaly | S |

## 2. Čtenáři, komunita, příjmy

| # | Námět | Proč | Pracnost |
|---|-------|------|----------|
| 15 | **Platební brána pro předplatné** (Stripe; pro ČR/SK Comgate nebo GoPay) – automatické prodloužení `predplatne_do` | dnes předplatné zapisuje administrátor ručně | L |
| 16 | ✅ HOTOVO · **Přihlášení odkazem z e-mailu** (bez hesla) a propojení účtu čtenáře s odběrem newsletteru | méně tření při registraci | M |
| 17 | ✅ HOTOVO · **Komentáře pod účtem čtenáře** (volitelně jen pro přihlášené), upozornění na odpověď e-mailem, tlačítko „nahlásit" | účty čtenářů už existují, komentáře o nich nevědí | M |
| 18 | ✅ HOTOVO · **Upozornění redakci** na nový komentář ke schválení a na nového předplatitele | dnes se to redakce dozví jen v administraci | S |
| 19 | ✅ HOTOVO (uložené články; historie čtení se záměrně nevede – soukromí) · **Uložené články a historie čtení** pro přihlášené | důvod se registrovat i u nezamčeného webu | M |
| 20 | ✅ HOTOVO (automatický výběr, naplánované odeslání, souhrnná statistika, šablona s logem a barvou) · **Newsletter: automatický týdenní výběr**, naplánované odeslání, statistika otevření a prokliků, šablona s logem a barvou webu | dnes jen ruční vydání bez měření | M–L |
| 21 | ✅ HOTOVO · **Dobrovolný příspěvek čtenářů** (blok „Podpořte nás" s QR platbou / odkazem) | nejjednodušší monetizace malých magazínů | S |
| 22 | ✅ HOTOVO (rubrika, zařízení, limit zobrazení, výkaz CSV) · **Reklama: cílení na rubriku a zařízení, limit zobrazení, výkaz pro inzerenta (PDF/CSV)** | dnes jen pozice a termín | M |

## 3. Správa, provoz, bezpečnost

| # | Námět | Proč | Pracnost |
|---|-------|------|----------|
| 23 | ✅ HOTOVO (obnova v administraci; kopie na FTP nebo do S3 – proti skutečnému úložišti zatím neověřeno) · **Obnova ze zálohy přímo v administraci** a zálohy mimo server (SFTP / S3 kompatibilní úložiště) | záloha na stejném disku nechrání před ztrátou hostingu | M |
| 24 | ✅ HOTOVO · **Adresa pro cron** (`/ulohy?token=…`) jako doplněk úloh spouštěných návštěvou | web s malou návštěvností vydá naplánovaný článek a pošle push se zpožděním | S |
| 25 | ✅ HOTOVO · **Fronta e-mailů** s opakováním a protokolem odeslaných zpráv | při výpadku SMTP se potvrzovací e-mail dnes ztratí | M |
| 26 | **Přihlášení klíčem (passkey / WebAuthn)** vedle TOTP; přehled přihlášených zařízení s možností odhlásit | pohodlnější a bezpečnější než kódy | M |
| 27 | **Oprávnění podle rubriky** a role „korektor" | větší redakce; dnes jen autor / redaktor / administrátor | M |
| 28 | **Import z WordPressu (WXR)** a **export celého webu** (JSON + média) | bez importu se na phpRS 3 stěhuje těžko; export = žádné uzamčení dat | L |
| 29 | ✅ HOTOVO · **Prohlížeč chyb** (`storage/log/chyby.log`) a **404 log s nabídkou přesměrování** ve Stavu systému | správce dnes musí na FTP | S |
| 30 | ✅ HOTOVO · **Kontrola nefunkčních odkazů** v článcích (na pozadí, po dávkách) | hygiena obsahu i SEO | M |
| 31 | ✅ HOTOVO · **Jednotkové testy** jádra (Totp, Antispam, Push/VAPID, cookie čtenáře, Migrace::prikazy, čistič adres) vedle kouřového testu | kouřový test nechytí regresi v kryptografii a parsování | M |
| 32 | ✅ HOTOVO · **Průvodce po instalaci** na přehledu: 5 kroků (logo a barva → rubriky → první článek → bloky → SMTP) a volba „nainstalovat ukázkový obsah" | prázdný web po instalaci působí mrtvě; nový uživatel neví kudy | S |

## 4. Jazykové verze – dotažení

| # | Námět | Pracnost |
|---|-------|----------|
| 33 | ✅ HOTOVO · Propojení překladů i u **stránek a rubrik** (hreflang, přepínač vede na protějšek) – dnes jen u článků | S |
| 34 | **Štítky, novinky, ankety a newsletter podle jazyka** (dnes společné) | M |
| 35 | Překlad **vizuálního editoru bloků** a hlášek skládaných z proměnných; němčina administrace | M |
| 36 | „Přeložit článek" asistentem: založí koncept v cílové rubrice s propojením na originál | S |

## 5. Vzhled webu

| # | Námět | Proč | Pracnost |
|---|-------|------|----------|
| 37 | ✅ HOTOVO (podle zařízení čtenáře; ruční přepínač na webu zbývá) · **Tmavý režim šablon** (`prefers-color-scheme` + přepínač) | administrace ho má, web ne | M |
| 38 | ✅ HOTOVO (dlouhé čtení, fotoreportáž, rozhovor – jako varianty nad standardní šablonou) · **Další šablony článku**: dlouhé čtení s fotkou přes celou šířku, fotoreportáž, rozhovor (otázka/odpověď), krátká zpráva | dnes jedna šablona „Standardní" na všechno | M |
| 39 | ✅ HOTOVO · **Mobilní menu** (hamburger s rubrikami, hledáním a jazyky) a lepkavá hlavička | na telefonu se pruh rubrik posouvá do strany bez nápovědy, že pokračuje | S–M |
| 40 | ✅ HOTOVO · **Ukazatel průběhu čtení**, odhad doby čtení u článku, „další článek" na konci | drobnosti, které dělají magazín magazínem | S |
| 41 | ✅ HOTOVO · **Tiskový styl** a čistý režim čtení | tisk článku dnes vytiskne i sloupce | S |
| 42 | **Rozmazaný náhled obrázků** (LQIP) a pevné poměry stran ve výpisech | méně poskakování stránky (CLS) | S |
| 43 | **Čtvrtá šablona „Minimal / blog"** pro osobní magazíny a newsletterové weby | tři stávající jsou novinové a magazínové | M |
| 44 | ✅ HOTOVO · Stránka 404 s hledáním a nejčtenějšími články; stránka „Děkujeme za registraci / odběr" ve stylu webu | S |

## 6. Vzhled administrace

| # | Námět | Pracnost |
|---|-------|----------|
| 45 | **Čárové ikony bloků** ve vizuálním editoru místo znaků (★ ☰ 🔔) – sjednotit se sadou v `views/admin/ikony.php` | S |
| 46 | **Prázdné stavy** se zřetelnou první akcí ve všech výpisech (část je hotová: Čtenáři) | S |
| 47 | ✅ HOTOVO (graf návštěvnosti, fronta ke korektuře a naplánované; stav rozesílek zbývá) · **Přehled (dashboard)**: graf návštěvnosti za 14 dní, fronta „ke korektuře", naplánované články, stav rozesílek | M |
| 48 | ✅ HOTOVO · **Hromadné akce ve výpisu článků** (změna rubriky, štítek, zamknout) a uložené filtry | S |
| 49 | **Klávesové zkratky a paleta příkazů** (Ctrl+K: „nový článek", „najít článek…", „nastavení pošty") | M |
| 50 | **Administrace na telefonu**: editor článku v jednom sloupci s plovoucím „Uložit", nahrání fotky z fotoaparátu | M |
| 51 | Kontrola nových obrazovek (Čtenáři, Živá reportáž, Pošta) v **retro prostředí** – fungují, ale nebyly vizuálně doladěné | S |

## Stav k 2026-09-20

Hotovo: 1, 2, 3, 4, 5, 6, 8, 9, 11, 12, 13, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 27, 29, 30, 31, 32, 33, 36, 37, 38, 39, 40, 41, 44, 45, 46, 47, 48; částečně 7 (video z adresy ano, příspěvky ze sítí ne).

K bodu 36: tlačítko „Přeložit asistentem“ je v editoru článku (Překlad článku); kostra HTML zůstává z originálu, model dodává jen texty úseků, výsledek je vždy koncept. Se skutečným Claude API zatím nevyzkoušeno – jen proti místnímu falešnému serveru.

K bodu 27: uživateli jde v pokročilých volbách zaškrtnout „Jen tyto rubriky“ (platí i pro podrubriky, v administraci i přes MCP). Samostatnou roli „korektor“ nepřidáváme – složí se z existujících voleb: autor, který „smí upravovat i články autorů“ a nemá právo vydávat.

Další v pořadí: platební brána (15),

## Doporučené pořadí

1. Rychlé výhry (vše **S**): sdílení (1), související články (4), osnova (6), upozornění redakci (18), cron adresa (24), průvodce po instalaci (32), mobilní menu (39).
2. Důvěryhodnost a obsah: medailonek autora (2), vkládání ze sítí (7), tabulky (8), přílohy (11).
3. Provozní jistota: obnova a vzdálené zálohy (23), fronta e-mailů (25), jednotkové testy (31).
4. Příjmy: platební brána (15), dobrovolný příspěvek (21), newsletter 2.0 (20).
5. Před veřejným vydáním: import z WordPressu (28).

Výsledky rešerše bezpečnosti a výkonu včetně toho, co zbývá: `docs/audity/2026-09-19-reserse-kodu.md`.
