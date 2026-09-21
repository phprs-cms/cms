# Testy a vydávání

Projekt má dvě sady testů, průběžnou kontrolu na GitHubu a podepsaná vydání. Tato stránka říká, co spustit před odesláním změny a jak se hotová změna dostane k uživatelům.

## Jednotkové testy

```
php tools/testy.php
```

Testy běží bez frameworku a bez databáze, během okamžiku. Hlídají logiku, kterou průchod webem nepozná: kryptografii, parsování a převody textu. Patří mezi ně například:

- převody textu, hledání, dělení SQL migrací na příkazy,
- TOTP, přihlašovací klíče (WebAuthn) proti softwarovému autentikátoru, podpis Web Push, podpis pro zálohy do S3,
- povolený zápis PHP v šablonách (`Core\SablonaKontrola`) včetně toho, že jím projdou všechny vestavěné šablony,
- překlad článku asistentem: kostra HTML zůstává z originálu,
- ověřování podpisů vydání: víc klíčů, výměna a odvolání klíče,
- úplnost slovníků instalátoru a ukázkového obsahu ve všech jazycích,
- shoda tabulky adres v `Core\Napoveda` s `docs/prirucka/osnova.json`,
- statické kontroly: administrace neobsahuje inline skripty ani obsluhy událostí; žádný skript nehledá prvek `[data-…]`, který nikde nevzniká.

Nový test je další volání `over('popis', $skutecne, $ocekavane)`. Ke každé změně v kryptografii a parsování přidejte test včetně záporného případu – vstupu, který projít nesmí.

## Kouřový test

```
tools/test.sh
```

Potřebuje běžící MySQL nebo MariaDB a příkazy `mysql` a `curl`. Postup testu:

1. zkontroluje syntaxi všech souborů PHP,
2. ověří, že `PHPRS_VERZE_DB` odpovídá číslu poslední migrace,
3. spustí jednotkové testy,
4. zkopíruje projekt do dočasné složky, založí čistou databázi a provede instalaci; ověří, že se instalátor po sobě smazal,
5. projde web: hlavní stránku, článek, rubriku, hledání, kanály, mapy webu, `robots.txt`, `llms.txt`, čistou verzi článku, stránku 404 a to, že `system/` a `config.php` nejsou z webu přístupné,
6. vyzkouší všechny tři vestavěné šablony i náhradní šablonu při chybějící složce,
7. přihlásí se do administrace a projde její obrazovky včetně oprávnění podle rubriky a úpravy článku přímo na webu.

Odpověď se považuje za chybnou, i když obsahuje text `Warning:`, `Notice:`, `Deprecated:` nebo `Fatal error`.

Připojení k databázi se bere z proměnných prostředí:

| Proměnná | Výchozí |
|---|---|
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_NAME` | `phprs3_test` |
| `DB_USER` | `root` |
| `DB_PASS` | prázdné |
| `PORT` | `8099` – port dočasného webového serveru |

> Databázi `DB_NAME` test **smaže a vytvoří znovu**. Nikdy nezadávejte databázi, na které vám záleží.

Po změně šablon, dialogů editoru nebo stylů testy nestačí. Projděte dotčené stránky v prohlížeči – ve světlém i tmavém režimu a v šířce telefonu.

## Kontrola na GitHubu

Workflow **Kontrola** běží při každé změně a jednou týdně i bez změn:

- kouřový test na PHP 8.4 a 8.5,
- Semgrep s bezpečnostními pravidly pro PHP a JavaScript,
- Gitleaks – v repozitáři nesmí být klíče ani hesla,
- kontrola, že v gitu není soukromý klíč vydavatele.

Dependabot sleduje jen GitHub Actions. Jiné závislosti projekt nemá.

## Denní bezpečnostní kontrola

Workflow **Denní kontrola** běží každou noc. Nic nevydává ani nepodepisuje; jen včas upozorní, že je potřeba jednat:

| Kontrola | Co odhalí |
|---|---|
| Kanál aktualizací | Manifest aktualizací na webu projektu není podepsaný klíčem vydavatele, balíček neodpovídá otisku nebo nese cizí veřejný klíč – tedy podvržení nebo poškození toho, co si instalace stahují. |
| Testy na podporovaných verzích PHP a na připravované verzi | Změnu v PHP, která systém rozbije, dřív než dorazí na hostingy. Připravovaná verze smí selhat. |
| Statická analýza s denně čerstvými pravidly | Nově popsané zranitelné vzory v kódu. Nálezy vidí jen správci repozitáře. |
| Web projektu a demo zvenku | Chybějící bezpečnostní hlavičky, přístupný `config.php`, `system/`, `storage/` nebo `.git/`. |

Když něco selže, založí se v repozitáři úkol s odkazem na běh kontroly.

## Jak vzniká vydání

Číslo verze je konstanta `PHPRS_VERSION` v `system/bootstrap.php`. Vydání sestavuje skript `tools/vydani.php`. Vytvoří balíček ZIP a manifest aktualizace, který nese číslo verze, otisk balíčku, popis změn a příznak, zda jde o běžné, nebo bezpečnostní vydání.

Co je podepsané:

- řetězec složený z verze, otisku SHA-256 balíčku a druhu vydání. Příznak bezpečnostního vydání je tedy krytý podpisem – kdo by ovládl jen web s manifestem, nemůže běžné vydání prohlásit za bezpečnostní a vynutit jeho automatickou instalaci,
- seznam souborů jádra `system/soubory.json`. Podle něj **Stav systému** hlásí změněné, chybějící a přidané soubory a aktualizace uklízí soubory, které nové vydání už neobsahuje.

Podpisy jsou Ed25519 a ověřuje je jen `Core\Podpis`. Veřejné klíče jsou v `system/aktualizace.pub`, na řádku jeden. Platí podpis kterýmkoli z nich; díky tomu lze provozní klíč vyměnit a existuje záložní klíč pro případ jeho ztráty. Soubor je součástí balíčku, takže nové klíče se do instalací dostanou aktualizací a odvolané z nich zmizí.

Dvě pravidla, která se nemění:

- **Podepisuje se lokálně, ne v CI.** V CI by klíčem mohl podepisovat každý, kdo smí měnit workflow. Workflow **Vydání** po označení verze tagem jen ověří, že verze v kódu odpovídá tagu, spustí testy a založí koncept vydání.
- **Soukromé klíče nikdy nepatří do gitu ani do balíčku.** Hlídá to `.gitignore` i kontrola na GitHubu.

Příznak bezpečnostního vydání je vyhrazen skutečným bezpečnostním opravám. Taková vydání si instalace při výchozím nastavení nainstalují samy a správce dostane e-mail. Jak aktualizace vypadá z pohledu správce webu, popisuje stránka [Aktualizace](../zaciname/aktualizace.md).

Úplný postup pro vydavatele – založení a výměna klíčů, postup při ztrátě nebo úniku klíče, vydání záplaty – je v `docs/VYDAVANI.md`. Po vydání verze 3.0.0 se opravy dělají na hlavní větvi a přenášejí do větve udržované řady, ze které vycházejí verze 3.0.x; nové funkce jdou jen do hlavní větve.

## Související

- [Zásady projektu](zasady.md)
- [Jak přispět](jak-prispet.md)
- [Aktualizace](../zaciname/aktualizace.md)
- [Stav systému](../provoz/stav-systemu.md)
