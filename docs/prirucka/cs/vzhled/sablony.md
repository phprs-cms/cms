# Šablony webu

Šablona určuje, jak web vypadá: záhlaví, výpis článků, podobu článku, patičku. Se systémem přicházejí tři šablony. Obsah, bloky ani nastavení na šabloně nezávisí, takže ji můžete kdykoli vyměnit.

## Tři vestavěné šablony

| Šablona | Pro koho je | Rozvržení po výběru |
|---|---|---|
| **Classic Newspaper** | Seriózní deník: patkové titulky, tenké linky, otvírák a sloupcová sazba. | 2 sloupce |
| **Modern Magazine** | Výrazný online magazín: černá lišta, velké titulky a fotografie, mřížka karet. | Plná šířka |
| **Minimal** | Osobní magazín, blog nebo newsletterový web: jeden úzký sloupec a klidná typografie. | 1 sloupec |

Nová instalace používá Classic Newspaper, pokud jste v instalátoru nevybrali jinou. Žádná ze šablon nestahuje písma ani skripty z cizích serverů.

## Přepnutí šablony

Šablonu mění administrátor.

1. Otevřete **Vzhled → Identita webu**.
2. V oddílu **Šablona** klepněte na kartu šablony.
3. Klepněte na **Uložit**. Výsledek si prohlédnete odkazem **Zobrazit web**.

Změna se projeví hned pro všechny čtenáře.

### Co přepnutí změní a co ne

Spolu se šablonou se nastaví rozvržení stránky, které k ní patří (viz tabulka výše). Doladíte ho v sekci **Bloky a rozvržení**. Bloky ze zóny, kterou nové rozvržení nemá, se přitom přesunou stejně jako při ruční změně rozvržení (viz [Rozvržení stránky](#rozvrzeni-stranky)).

Beze změny zůstává:

- všechen obsah – články, stránky, rubriky, média, komentáře,
- bloky a jejich nastavení,
- logo, ikona webu, hlavní barva, písma a tmavý režim z [Identity webu](identita-webu.md),
- šablony článku (Dlouhé čtení, Fotoreportáž, Rozhovor) a typy obsahu – fungují ve všech třech šablonách,
- adresy stránek, SEO, měření a cookie lišta.

Hlavní barva nastavená na **barva šablony** se s novou šablonou změní, protože každá šablona má vlastní výchozí barvu. Totéž platí pro písmo **Podle šablony**.

## Náhled jiné šablony

Administrátor si může šablonu prohlédnout, aniž ji zapne. Stačí být přihlášený do administrace a k adrese webu přidat parametr `sablona` s názvem složky šablony:

```
https://www.example.cz/?sablona=modern-magazine
https://www.example.cz/clanek/muj-clanek?sablona=minimal
```

Složky vestavěných šablon se jmenují `classic-newspaper`, `modern-magazine` a `minimal`. Stejně si prohlédnete i [vlastní šablonu](vlastni-sablona.md). Náhled vidíte jen vy; nepřihlášenému čtenáři ani redaktorovi se parametr neprojeví. Platí pro jednu stránku – po klepnutí na odkaz se vrátíte do nastavené šablony, parametr je tedy potřeba přidat znovu.

## Rozvržení stránky

Rozvržení říká, kolik sloupců stránka má a které zóny pro bloky v ní existují. Je společné pro celý web.

| Rozvržení | Popis | Zóny |
|---|---|---|
| **3 sloupce** | Bloky vlevo i vpravo, obsah uprostřed. | Hlavička, Levý sloupec, Nad obsahem, Pod obsahem, Pravý sloupec, Patička |
| **2 sloupce** | Obsah a vpravo úzký sloupec s bloky. | Hlavička, Nad obsahem, Pod obsahem, Pravý sloupec, Patička |
| **1 sloupec** | Úzký sloupec pro pohodlné čtení, bloky pod obsahem. | Hlavička, Nad obsahem, Pod obsahem, Patička |
| **Plná šířka** | Obsah přes celou šířku stránky, bloky pod obsahem. | Hlavička, Nad obsahem, Pod obsahem, Patička |

Rozvržení změníte ve vizuálním editoru bloků: **Vzhled → Bloky a rozvržení**, v horní liště tlačítka u popisku **Rozvržení:**. Postup je na stránce [Bloky a rozvržení](bloky-a-rozvrzeni.md).

Když nové rozvržení některý sloupec nemá, bloky z něj se přesunou:

- při přechodu na **2 sloupce** z levého sloupce do pravého,
- při přechodu na **1 sloupec** nebo **Plná šířka** z obou sloupců pod obsah.

Přesunuté bloky se zařadí za ty, které v cílové zóně už jsou.

### Která šablona které rozvržení podporuje

| Rozvržení | Classic Newspaper | Modern Magazine | Minimal |
|---|---|---|---|
| 3 sloupce | ano | ano | bez sloupců – vše v jednom sloupci |
| 2 sloupce | ano | ano | bez sloupců – vše v jednom sloupci |
| 1 sloupec | ano (obsah do 760 px) | ano (obsah do 820 px) | ano |
| Plná šířka | ano | ano | jeden úzký sloupec |

Minimal je vždy jednosloupcová. Zvolíte-li v ní rozvržení se sloupci, zóny **Levý sloupec** a **Pravý sloupec** existují dál, ale nevykreslí se vedle obsahu – jejich bloky se vypíšou ve stejném úzkém sloupci, oddělené linkou. Pro Minimal se proto hodí rozvržení **1 sloupec**, které se s ní nastaví samo.

V šablonách Classic Newspaper a Modern Magazine se postranní sloupce ukážou jen na širokém displeji. Na telefonu a užším tabletu se vše řadí pod sebe: nejdřív obsah, pod ním bloky z levého a pravého sloupce. Sloupec, ve kterém není žádný blok, místo nezabírá – obsah se roztáhne.

## Když složka šablony chybí

Pokud nastavená šablona ve složce `layout/` není (například jste ji smazali), web se vykreslí šablonou Classic Newspaper. V **Identitě webu** pak vyberte jinou.

## Související

- [Identita webu](identita-webu.md)
- [Bloky a rozvržení](bloky-a-rozvrzeni.md)
- [Vlastní šablona](vlastni-sablona.md)
- [Typy obsahu](../psani/typy-obsahu.md)
