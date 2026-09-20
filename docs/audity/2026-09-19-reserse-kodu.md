# Rešerše kódu 2026-09-19: bezpečnost a výkon

Dvě nezávislé čtecí revize celého kódu (bezpečnost, výkon) po dokončení M-X. Kritický nález žádný.

## Bezpečnost – opraveno

| Závažnost | Nález | Oprava |
|---|---|---|
| vysoká | Absolutní adresy se skládaly z hlavičky `Host` → podvržením šel do e-mailu s novým heslem, webhooku a oznámení dostat cizí doménu | nastavení `adresa_webu` (zapíše instalátor / první přihlášení administrátora), `Request::setOrigin()` v obou kernelech |
| střední | Živá reportáž a adresa média zamčeného článku unikaly přes JSON-LD | `liveBlogUpdate` a `associatedMedia` se u zamčeného článku nevypisují |
| střední | 10 chybných hesel trvale zablokovalo účet (šlo vyřadit i jediného administrátora); přihlašovací jméno bylo vidět jako jméno autora | dočasný zámek 15 min (`rs_user.zamceno_do`), na webu jen vyplněné jméno, slepý hash se stejnou cenou |
| střední | Potvrzovací odkaz registrace fungoval jako přihlášení bez časového omezení; heslo šlo „přednastavit" k cizí adrese | registrace bez hesla – heslo se nastavuje až z odkazu v e-mailu (3 dny), jeden společný postup s obnovou hesla (2 hodiny) |
| střední | `/?strana=N` za koncem výpisu vracelo 200 a plnilo cache soubory | 404 + občasný úklid prošlých souborů cache |
| nízká | Zápisy živé reportáže obcházely právo vydávat; CSV export čtenářů dovoloval vzorce; hledání prozrazovalo text zamčených článků; příjemce e-mailu s řídicími znaky; API `Cache-Control: public` i pro přihlášeného čtenáře; IP adresa u přihlášení do administrace v čitelné podobě; evidence souhlasů bez limitu | vše opraveno |
| – | `tools/` (soukromý klíč vydavatele!), `docs/`, `dist/` a PHP soubory šablon byly dosažitelné z webu | zákaz v `.htaccess`, `dev-router.php` a vlastní `.htaccess` ve složkách |

Prověřeno a v pořádku: podpisy cookie čtenáře a paywallu, ochrana návratové adresy, whitelist služeb Web Push (SSRF), cesta k obrázku u asistenta, výstup modelu jen jako text, DOM v `web.js`/`editor.js`, typy nastavení `tajne`/`seznam`, routování jazyků, cesty záloh, podpis aktualizací, nahrávání souborů, CSRF administrace, tokeny MCP.

## Bezpečnost – zbývá (doporučení)

- ✔ HOTOVO 2026-09-20: CSP pro `admin.php` (bez `'unsafe-inline'` u skriptů – inline skripty a obsluhy událostí přesunuty do souborů, hlídá `tools/testy.php`), HSTS na HTTPS, `Cache-Control: no-store` pro administraci a `/ctenar`.
- ✔ HOTOVO 2026-09-20: limit chybných pokusů i na účet – krok TOTP počítá chyby do `pocet_chyb` a zamyká účet na 15 minut; přihlášení čtenáře má limit 10 chybných hesel za 15 minut na e-mail (i neexistující, aby hláška neprozradila registrované adresy). Obojí ověřeno naživo.
- ✔ HOTOVO 2026-09-20: session nese otisk hesla – změna hesla (vlastní i administrátorem) ukončí ostatní přihlášení účtu; Můj účet při změně hesla nabízí zrušení tokenů napojení (výchozí ano). Tokeny dál nemají časovou platnost.
- ✔ HOTOVO 2026-09-20: ukázková pravidla pro nginx v `system/nginx.priklad.conf` (zákazy, média bez spouštění PHP, WebP, hezké adresy, hlavička Authorization). Instalátor jednorázovým souborem zatím podmíněn není.
- `X-Forwarded-Proto`: VĚDOMĚ PONECHÁNO (2026-09-20). Weby za proxy (Cloudflare, sdílené hostingy) na hlavičce závisí; povinná volba v `config.php` by jim po aktualizaci rozbila napojení na Claude (MCP vyžaduje HTTPS). Podvržená hlavička ovlivní jen požadavek toho, kdo ji podvrhl (příznak secure u jeho cookie, HSTS).
- GIF při nahrání překódovat po snímcích. Zámek kolem automatické aktualizace.

## Výkon – opraveno

- **Anonymní návštěvník dostával PHP session** (kvůli dotazu „je přihlášená redakce?" u zamčených článků) → `Cache-Control: no-store` a vypnutá cache stránek. `Auth::user()` teď bez cookie session vůbec nezakládá.
- Komentář/hodnocení/hlas mazaly cache ještě před ověřením antispamem → maže se až po skutečném zápisu.
- Hledání: čtyři `LIKE '%…%'` včetně celého textu → fulltextový index `ft_clanky` (+ `LIKE` jen na titulek), limit 30 hledání za minutu.
- Cookie měkkého paywallu vypínala cache celého webu na 40 dní → cache vypíná jen cookie přihlášeného čtenáře.
- `glob()` migrací při každém požadavku → konstanta `PHPRS_VERZE_DB` (hlídá ji `tools/test.sh`).
- Výpisy načítaly i `text`, shrnutí a FAQ každého článku a počítaly `COUNT(*)`, i když se nestránkuje → `Clanky::SLOUPCE_VYPISU`, počet jen u stránkovaných výpisů.
- Indexy (migrace 0019): hlavní stránka s jazykem, `oznameno`, `datum`; archiv přes rozsah dat místo `DATE_FORMAT()`.
- Přepínač jazyků se počítal dvakrát; naplánovaný článek se na hlavní stránce z cache objevil až po 5 minutách (teď `Cache::vymaz()` při oznámení); souběh při vzniku klíčů Web Push (zámek).
- `.htaccess`: dlouhá platnost statických souborů (adresy nesou `?v=`), komprese, `sw.js` bez cache.

## Výkon – zbývá

- ✔ HOTOVO 2026-09-20: krátká souborová cache (`Front\Cache::text()`, 5 minut, maže ji každá změna v administraci) pro `rss.xml`, `sitemap.xml`, `sitemap-news.xml`, `podcast.xml`, `feed.json`, `llms.txt`.
- `Rubriky::strom()` a dotaz na stránky v menu běží dvakrát za požadavek (layout + blok) – předat jednou.
- `Obrazky::srcset()` volá `getimagesize()` při každém vykreslení – rozměry variant ukládat při nahrání.
- Ochrana proti souběžnému přegenerování téže stránky cache (stampede).
- ◐ rozměry obrázků doplňuje `Front\ObrazkyHtml` (2026-09-20); `loading="lazy"` a `decoding="async"` ve výpisech bloků zbývá.

Měření z revize: stránka z cache = 1 SELECT (nastavení) + 2–3 zápisy statistiky; bez cache cca 19 dotazů (hlavní stránka) a 26–28 (článek).
