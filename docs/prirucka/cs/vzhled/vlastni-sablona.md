# Vlastní šablona

Když vám nestačí barva, písma a logo z [Identity webu](identita-webu.md), vytvořte si vlastní šablonu. Je to složka s několika soubory PHP a jedním stylem. Stránka je určená tomu, kdo umí HTML a CSS a nebojí se jednoduchého PHP.

Vestavěné šablony `classic-newspaper`, `modern-magazine` a `minimal` neupravujte. Každá aktualizace je přepíše a **Stav systému** zásah do nich hlásí jako změněný soubor jádra. Vlastní šablona ve vlastní složce se při aktualizaci nepřepisuje nikdy.

## Postup

1. Zkopírujte složku vestavěné šablony, která je vašemu záměru nejblíž, pod novým názvem do `layout/`. Název složky: malá písmena, číslice a pomlčky, například `layout/muj-magazin/`.
2. V kopii `base.php` opravte cestu ke stylu – místo `layout/classic-newspaper/style.css` tam musí být `layout/muj-magazin/style.css`. Jinak by šablona dál načítala styl originálu.
3. V `info.php` změňte název a popis.
4. Prohlédněte si výsledek na adrese `/?sablona=muj-magazin`. Náhled funguje jen přihlášenému administrátorovi, čtenáři dál vidí původní šablonu.
5. Upravujte `style.css` a šablony. Průběžně kontrolujte hlavní stránku, rubriku, článek, stránku a vyhledávání – ve světlém i tmavém režimu a v šířce telefonu.
6. Hotovou šablonu zapněte ve **Vzhled → Identita webu**. Objeví se tam jako další karta.

Šablona se v nabídce ukáže, když její složka obsahuje soubor `base.php`.

## Soubory šablony

| Soubor | K čemu je |
|---|---|
| `info.php` | Vrací pole s klíči `nazev`, `popis` a `rozvrzeni` (`tri`, `dva`, `jeden` nebo `plna`) – rozvržení, které se nastaví při výběru šablony. |
| `base.php` | Kostra stránky: `<head>`, záhlaví, zóny bloků, obsah, zápatí. |
| `blok.php` | Obal jednoho bloku. |
| `cla_standard.php` | Článek ve třech režimech: ve výpisu (`nahled`, `kratky`) a celý (`cely`). |
| `style.css` | Vzhled. |

Šablona se hledá nejdřív ve vaší složce a teprve potom mezi systémovými v `system/views/front/`. Stejnojmenným souborem ve své složce tak můžete nahradit i výpis článků (`vypis.php`), stránku (`stranka.php`) nebo obsah systémového bloku (`blok_rub.php`, `blok_nej.php`…). Čím méně souborů přepíšete, tím méně práce budete mít po aktualizacích.

## Co šablona dostává

**`base.php`:**

| Proměnná | Obsah |
|---|---|
| `$web` | nastavení webu; jen metody `get('klic')`, `int('klic')`, `bool('klic')` |
| `$titulek` | titulek stránky; na hlavní stránce prázdný |
| `$meta` | pole `hlavni`, `popis`, `klicova_slova`, `obrazek`, `typ`, `noindex` |
| `$obsah` | hotové HTML obsahu (výpis, článek…) |
| `$zony` | HTML bloků: `hlavicka`, `leva`, `nad`, `pod`, `prava`, `paticka`; prázdná zóna je prázdný řetězec |
| `$rozvrzeni` | `tri`, `dva`, `jeden` nebo `plna` |
| `$rubriky`, `$stranky` | rubriky a stránky pro navigaci |
| `$url` | funkce, která z cesty udělá adresu: `$url('rubrika/sport')` |
| `$kanonicka` | kanonická adresa stránky |
| `$jazyk`, `$jazyky_html` | kód jazyka pro `<html lang>` a hotový přepínač jazyků |
| `$hlava`, `$pata` | značky systému do hlavičky a před konec stránky |

> `$hlava` musíte vypsat před `</head>` a `$pata` před `</body>`. Jde tudy SEO, strukturovaná data, měřicí kódy, cookie lišta, společné styly a skripty článku i vizuální editor bloků. Bez nich web nebude fungovat správně.

**`cla_standard.php`:** `$clanek` (sloupce článku a k nim `tema_jm`, `tema_seo`, `autor_jm`, u celého článku `stitky`), `$rezim`, `$poradi` (pořadí ve výpisu; 0 je otvírák), `$url`, `$souvisejici`. Hotové kusy HTML `shrnuti_html`, `faq_html`, `hodnoceni_html`, `komentare_html` a `reklama_html` jen vypište. Před text článku nevkládejte odstavec `<p>` – šablony dávají prvnímu odstavci iniciálu.

**`blok.php`:** `$nadpis`, `$obsah`, `$typ` (vzhled 1–5; 5 znamená bez nadpisu), `$sys` (zkratka systémového bloku) a `$zona`.

### Pomocné funkce

| Funkce | Co dělá |
|---|---|
| `e($text)` | ošetří text pro výpis do HTML; použijte ji na všechno, co není hotové HTML |
| `t('Text')` | přeloží text šablony do jazyka webu |
| `datum($d)`, `datum_slovy()` | datum ve formátu jazyka webu; datum slovy |
| `cislo($n)` | desetinné číslo s čárkou nebo tečkou podle jazyka |
| `slugify($text)`, `bez_diakritiky($text)` | převod textu do adresy; odstranění háčků a čárek |

## Šablony článku

Článek může mít šablonu **Dlouhé čtení**, **Fotoreportáž** nebo **Rozhovor**. Jsou to varianty: `cla_standard.php` dá prvku `<article>` třídu `sablona-dlouhe-cteni`, `sablona-fotoreportaz` nebo `sablona-rozhovor` a vzhled dodá `image/web.css`. Vaše šablona musí tuto třídu vypsat také – v kopii vestavěné šablony to už je.

Chcete-li některé variantě dát úplně jiné HTML, přidejte do složky soubor `cla_dlouhe-cteni.php`, `cla_fotoreportaz.php` nebo `cla_rozhovor.php`. Když existuje, použije se místo `cla_standard.php`.

## Společné styly a jejich přepsání

Soubor `image/web.css` se načítá ve všech šablonách, a to až po vašem `style.css`. Obsahuje dvě skupiny pravidel:

- **Základní vzhled společných prvků** – štítky článku, Ve zkratce, otázky a odpovědi, hodnocení, komentáře, anketa, reklama, typy bloků. Pravidla jsou zapsaná v `:where()`, mají tedy nulovou váhu. Přepíše je jakékoli pravidlo ve vašem `style.css`. Nepoužívejte `!important`.
- **Prvky s třídou `rs-…`** – fotogalerie, přehrávač, zámek článku, účet čtenáře, přepínač jazyků. Mají váhu jedné třídy; přepíšete je selektorem o třídu silnějším, například `.clanek-text .rs-zamek`.

Do `style.css` pište jen to, co má vypadat jinak. `image/web.css` neupravujte, aktualizace ho přepíše.

Další pravidla:

- Barvu a písma berte z proměnných `--rs-akcent`, `--rs-pismo-titulky` a `--rs-pismo-text` s vlastní výchozí hodnotou, například `--akcent: var(--rs-akcent, #326891)`. Jen tak bude fungovat Identita webu.
- Na konec `style.css` patří tmavý režim: `@media (prefers-color-scheme: dark) { :root[data-tmavy] { … } }`. Atribut `data-tmavy` dává `base.php` prvku `<html>` podle nastavení. Barvy proto pište přes proměnné.
- Žádná externí písma ani skripty z CDN.
- Rozměry obrázků doplňuje systém do hotového HTML sám. U obrázků s pevnou výškou v CSS počítejte s atributem `height`.

## Povolený zápis PHP

Šablona je prezentační vrstva: vypisuje data, která dostala. Soubory PHP ukládané přes napojení na Claude kontroluje `Core\SablonaKontrola` a soubor, který pravidla poruší, se neuloží. Kontrola je povolovací – co není výslovně dovoleno, neprojde. Vestavěné šablony jí procházejí, takže jejich kopie jdou dál upravovat. Stejných pravidel se držte i při ruční práci.

| Povolené | Zakázané |
|---|---|
| výpis `<?= e($x) ?>`, `if`, `foreach`, `for`, `while`, `match` | `include`, `require`, `eval`, zpětné apostrofy |
| uzávěry: `$f = fn ($x) => …`, `$f = function () { … }` | pojmenované funkce, `class`, `new`, `namespace`, import tříd přes `use` |
| `$url('…')` a vlastní uzávěry | volání jiné proměnné jako funkce, `$$x`, `${…}` v řetězci |
| `$web->get()`, `->int()`, `->bool()` | jiné metody objektů, volání tříd `Trida::metoda()` |
| funkce pro text, čísla, datum a pole (`count`, `implode`, `mb_substr`, `number_format`, `date`, `array_map`, `preg_replace`…) | každá jiná funkce: soubory, síť, procesy, databáze, reflexe |
| zpětné volání jako uzávěr nebo `trim(...)` | název funkce v řetězci (`'trim'`) |
| – | `$_GET`, `$_POST`, `$_COOKIE`, `$_SERVER`, `$_SESSION`, `$GLOBALS`, `$this`, `$app`, `$db`, `try`, `throw`, `exit`, `global`, `goto`, `clone` |

Důvod je bezpečnost. Šablona běží při každém zobrazení stránky se stejnými právy jako systém. Kdyby směla číst soubory nebo volat síť, stačila by jedna podvržená šablona k ovládnutí webu. Úplný seznam povolených funkcí je v `system/src/Core/SablonaKontrola.php`.

Styl (`.css`) se kontroluje jen na zastaralé spustitelné konstrukce `expression(` a `behavior:`. Jeden soubor smí mít nejvýše 300 kB.

## Šablona s pomocí Claude

Se zapnutým rozšířením **Napojení na Claude** může šablonu vytvořit a upravovat Claude přes MCP – s účtem administrátora. Má k tomu nástroje pro zkopírování vestavěné šablony, čtení a uložení souboru a přepnutí webu na šablonu. Zapisovat smí jen do složky vlastní šablony, jen soubory `.php` a `.css`, a každý soubor PHP projde kontrolou popsanou výše. Po každém uložení dostane adresu náhledu `/?sablona=…`, kterou si otevřete v prohlížeči.

Nastavení popisuje stránka [Napojení na Claude](../seo-a-ai/napojeni-na-claude.md). Pravidla pro Claude pracujícího přímo se soubory jsou v `layout/CLAUDE.md`.

## Související

- [Šablony webu](sablony.md)
- [Identita webu](identita-webu.md)
- [Aktualizace](../zaciname/aktualizace.md)
- [Stav systému](../provoz/stav-systemu.md)
