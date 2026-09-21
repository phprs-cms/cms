# Zásady projektu

Tato stránka shrnuje pravidla, podle kterých se phpRS vyvíjí. Přispěvateli ušetří kolo připomínek u pull requestu. Úplné znění i s odůvodněním je v souboru `CLAUDE.md` v kořeni repozitáře; pravidla pro šablony vzhledu v `layout/CLAUDE.md`.

## Jednoduchost nad abstrakcí

Kód má přečíst i poučený laik, který si chce web upravit. Proto:

- žádné kontejnery závislostí, ORM, kroky sestavení ani npm,
- nová závislost jen se silným důvodem,
- jeden modul administrace je jedna třída, jedna obrazovka je jedna šablona,
- nastavení funkce má být na pár polí – co jde odvodit, na to se systém neptá.

Ovládání musí být srozumitelné člověku, který systém vidí poprvé. Administrace má jediný vzhled; každá změna jejích šablon se kontroluje ve světlém i tmavém režimu a v šířce telefonu.

## Rozšíření jsou uzavřená sada

Volitelné funkce jsou vyjmenované v `Core\Rozsireni::SEZNAM`. Všechna rozšíření jsou součástí balíčku a vznikají v projektu. Systém nemá cizí plug-iny, veřejné API pro zásuvné moduly ani nahrávání kódu z administrace – je to bezpečnostní rozhodnutí.

Nová volitelná funkce znamená:

1. položku v `Rozsireni::SEZNAM` (název, popis, výchozí stav),
2. konstantu `ROZSIRENI` u modulu administrace,
3. kontrolu `Rozsireni::je()` všude, kde se funkce projevuje na webu.

Jádro – články, média, rubriky, stránky, bloky, uživatelé, nastavení – vypnout nejde. Vypnuté rozšíření zmizí z nabídky i z webu, jeho data zůstávají.

## Změna databáze

Každá změna struktury se zapisuje na třech místech:

1. **`system/sql/schema.sql`** – úplné schéma pro nové instalace.
2. **`system/sql/migrace/NNNN-popis.sql`** – migrace pro stávající weby. Provede se sama při první návštěvě webu nebo administrace po aktualizaci; číslo poslední provedené drží nastavení `verze_db`.
3. **`PHPRS_VERZE_DB`** v `system/bootstrap.php` – zvýšit na číslo nové migrace. Hlídá to `tools/test.sh`.

Nový sloupec tabulky článků, který má být vidět ve výpisech, doplňte i do `Front\Clanky::SLOUPCE_VYPISU` – výpisy záměrně nenačítají dlouhé texty.

## Nové nastavení

Nová volba v Nastavení má tři části: klíč s výchozí hodnotou v `Settings::DEFAULTS`, typ v `Konfigurace::POLE` (podle typu se hodnota při uložení čistí) a řádek `$pole(...)` v šabloně `system/views/admin/config/<záložka>.php`. Tajné hodnoty (klíče, hesla) mají typ `tajne` a nikdy se nevypisují zpět do formuláře.

## Překlady

- Texty webu i administrace se obalují `t('Česky')`. Klíčem je český text.
- Text v šabloně webu nebo ve `system/views/front/` doplňte do slovníků `en.php`, `sk.php` a `de.php`; text administrace do `admin-en.php`, `admin-sk.php` a `admin-de.php`. E-maily čtenářům patří do slovníků webu.
- Hodnoty formulářů – `value` skrytých polí a tlačítek s atributem `name` – se nikdy nepřekládají.
- Texty vizuálního editoru bloků jsou v jazyce administrace přihlášeného, ne v jazyce zobrazené verze webu: v JavaScriptu `T()`, slovník `image/jazyky/admin-<kód>.js`.

Překlady nedoplňujte ručně. Použijte nástroj, který správně ošetří apostrofy:

```
tools/slovnik.py system/jazyky/admin-en.php < radky.txt
```

Každý řádek vstupu má tvar `česky|překlad`. Existující klíče nástroj přeskočí. Po změně slovníků spusťte testy – úplnost slovníků instalátoru a ukázkového obsahu hlídá `tools/testy.php`.

## Bezpečnost

- **Databáze:** jen připravené dotazy; `{tabulka}` doplní předponu.
- **Výstup:** všechno přes `e()`. HTML článků a bloků je důvěryhodné, protože ho píší autoři. Komentáře a jiné vstupy čtenářů nikdy.
- **Administrace:** každý POST má token CSRF; kontroluje ho `Admin\Kernel`.
- **Formuláře čtenářů** nemají relaci ani token CSRF. Chrání je `Core\Antispam`: podepsaný čas, skryté pole a limit na otisk IP adresy. IP adresa se neukládá, jen její otisk.
- **Nahrávání:** obrázky vždy přes `Core\Obrazky` (překódování), přílohy přes `Core\Soubory` s povoleným seznamem přípon. HTML, SVG ani skripty nikdy.
- **Pošta** jde vždy přes `Core\Posta::odesli()`, nikdy přímo funkcí `mail()`.
- **Adresa webu** se bere z nastavení přes `$app->request->origin()`, nikdy z hlavičky `Host`.
- **Napojení na Claude** smí měnit jen obsah a vlastní šablony. Žádný nástroj nesmí zapsat mimo `layout/<vlastní>/`, spustit kód ani dotaz. Novou funkci do povolovacího seznamu `Core\SablonaKontrola` přidejte jen tehdy, když nepracuje se soubory, sítí, procesy, zpětným voláním ani reflexí.

### Content-Security-Policy v administraci

Administrace posílá hlavičku `script-src 'self'`. Důsledek pro šablony administrace:

- žádné inline `<script>`,
- žádné atributy `onclick=`, `onchange=` a podobné,
- chování patří do `image/admin.js` a váže se přes atributy `data-…` (například `data-odeslat-pri-zmene`, `data-ukaz-heslo`).

Hlídá to statická kontrola v `tools/testy.php`. Stejná kontrola ověřuje, že žádný skript nehledá prvek `[data-…]`, který nikde nevzniká.

### Nikdy window.confirm

Vestavěné prohlížeče aplikací dialog `window.confirm()` potlačují, takže potvrzení by tiše neproběhlo. V administraci dejte formuláři nebo tlačítku atribut `data-potvrdit="text otázky"` – obslouží ho `admin.js`. Vizuální editor bloků má vlastní dialog.

## Web a šablony

- Nová proměnná pro šablony nebo nový systémový blok se musí promítnout do všech tří vestavěných šablon.
- Šablona musí vypsat `$hlava` před `</head>` a `$pata` před `</body>`.
- Co se nevypisuje, nemá styl ani skript. Do `image/web.css` a `style.css` šablon nepatří selektor, který nikde nevzniká.
- Společný vzhled prvků je na konci `image/web.css` v `:where()` s nulovou vahou. Šablona nese jen to, co se liší.
- Žádná externí písma ani CDN.
- Každý dotaz na webu, který vypisuje obsah, filtruje podle jazykové verze.
- Cokoli čte články jinudy než přes `Front\Clanky`, musí samo respektovat zámek obsahu.
- Cachuje se jen výstup pro nepřihlášené bez osobních cookies. Co se má lišit podle čtenáře, běží v JavaScriptu, nebo má vlastní cookie `phprs_*`, která cache vypíná.

## Příručka

Při změně chování nebo popisku v administraci upravte i příručku v `docs/prirucka/cs/`. Čeština je zdroj, angličtina a němčina jsou překlady se stejnými názvy souborů. Pořadí stránek a překlad adres drží `docs/prirucka/osnova.json`; tabulka v `Core\Napoveda` mu musí odpovídat.

## Související

- [Struktura projektu](struktura-projektu.md)
- [Testy a vydávání](testy-a-vydavani.md)
- [Jak přispět](jak-prispet.md)
