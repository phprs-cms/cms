# Reklama

Reklamní systém zobrazuje na webu vlastní bannery a kódy reklamních sítí. Reklamu přidáte jednou a systém ji střídá na zvolené pozici, počítá zobrazení i prokliky, hlídá termín kampaně a limit zobrazení. Každá reklama je na webu označena slovem „Reklama“.

## Zapnutí

V hlavní nabídce otevřete **Rozšíření**, zaškrtněte **Reklamní systém** a uložte. V nabídce přibude **Čtenáři → Reklama**; přístup má administrátor a redaktor.

> Se zapnutým reklamním systémem se nepoužívá cache celých stránek. Reklamy se střídají a počítají při každém zobrazení, takže stránku nelze podávat z paměti. Na běžném hostingu to nevadí; u webu s vysokou návštěvností s tím počítejte.

## Pozice

| Pozice | Doporučený tvar | Jak se dostane na web |
|---|---|---|
| **Sloupec** | čtverec, např. 300×250 | blokem **Reklama** |
| **Hlavička** | široký pruh, např. 970×210 | blokem **Reklama** |
| **Patička** | široký pruh | blokem **Reklama** |
| **Pod článkem** | – | zobrazuje se automaticky pod každým článkem |

Pozice ve sloupci, hlavičce a patičce umístíte na web takto:

1. Otevřete **Vzhled → Bloky a rozvržení**.
2. V zóně, kde má reklama být, klepněte na **+ Přidat blok** a ve skupině **Vlastní** zvolte **Reklama**.
3. V nastavení bloku vyberte **Reklamní pozici** a uložte.

Název pozice je jen vodítko. Blok s pozicí Sloupec můžete dát do kterékoli zóny a tentýž blok můžete mít na webu víckrát. Blok, na jehož pozici právě žádná reklama neběží, se čtenářům nevypisuje. Blok můžete omezit na rubriku, zařízení nebo jazyk jako každý jiný – viz [Bloky a rozvržení](../vzhled/bloky-a-rozvrzeni.md).

## Nová reklama

1. V **Čtenáři → Reklama** klepněte na **Nová reklama**.
2. Vyplňte **Název** – je jen pro vás, na webu se neukazuje. U banneru slouží i jako jeho alternativní text.
3. Zvolte **Pozici**.
4. V poli **Co se má zobrazit** vyberte **Banner**, nebo **Kód reklamní sítě**, a vyplňte pole podle tabulky níže.
5. Podle potřeby rozbalte **Plánování, cílení a limity**.
6. Nechte zaškrtnuté **reklama je zapnutá** a klepněte na **Uložit**.

| Typ | Co vyplníte | Co se počítá |
|---|---|---|
| **Banner** | **Obrázek** (z Médií) a **Kam banner vede** – adresa začínající `https://` | zobrazení i prokliky |
| **Kód reklamní sítě** | **Kód**, který vám dala síť (Sklik, Google AdSense a podobně) | jen zobrazení; prokliky měří síť |

Banner se otevírá v novém okně a odkaz nese označení `sponsored`. Proklik vede přes adresu webu `/r/číslo`, která ho započítá a přesměruje na cíl. Návštěvy robotů se do prokliků nepočítají.

Kód reklamní sítě se při zapnuté cookie liště spustí až po souhlasu návštěvníka s marketingem. S lištou vypnutou se spouští hned. Viz [Měření a soukromí](../seo-a-ai/mereni-a-soukromi.md).

## Plánování, cílení a limity

| Pole | Význam |
|---|---|
| **Zobrazovat od**, **Zobrazovat do** | Časově omezená kampaň. Prázdné pole znamená bez omezení. Reklama se sama spustí i zastaví. |
| **Nejvýše zobrazení** | Po dosažení počtu se reklama vypne sama. Prázdné = bez limitu. |
| **Jen v rubrice** | Reklama se ukáže jen ve výpisu této rubriky a u jejích článků. Výchozí **ve všech**. |
| **Zařízení** | **všechna**, **jen telefony**, nebo **jen počítače a tablety**. Hranice je šířka okna 760 px. |
| **Váha** | 1–10. Když je na pozici víc reklam: váha 2 = zobrazí se dvakrát častěji než váha 1. |

Když na jedné pozici běží víc reklam, při každém zobrazení stránky se jedna vylosuje podle vah. Cílení na rubriku platí přesně pro zvolenou rubriku; její podrubriky se nezahrnují.

Cílení na zařízení řeší styl stránky: reklama se do stránky vloží a na nevhodném zařízení se skryje. Zobrazení se při tom započítá i tam, kde reklama vidět není. U reklamy cílené na zařízení je proto počet zobrazení vyšší než počet lidí, kteří ji skutečně viděli; prokliky jsou přesné.

## Přehled a výkaz

Přehled ukazuje u každé reklamy pozici, platnost, **Zobrazení** (s limitem, je-li nastaven), **Kliky**, **CTR** a stav. Stav **běží** má reklama, která je zapnutá, je v termínu a nevyčerpala limit; jinak **neběží**. Tlačítky **Vypnout** a **Zapnout** reklamu pozastavíte bez ztráty počtů. **Smazat** odstraní reklamu i s jejími počty.

**Výkaz pro inzerenta (CSV)** stáhne soubor se všemi reklamami: název, pozice, od, do, zobrazení, prokliky, míra prokliku v procentech a stav. Otevřete ho v tabulkovém programu.

Čísla jsou souhrnná. Systém neeviduje, kdo reklamu viděl, a neukládá kvůli ní žádné cookies. V náhledu článku z administrace se reklamy nezobrazují a nepočítají.

## Soubor ads.txt

Reklamní sítě vyžadují soubor `ads.txt` se seznamem autorizovaných prodejců. Pod přehledem rozbalte **Soubor ads.txt (vyžadují ho reklamní sítě)**, vložte řádky, které vám síť dodala (například `google.com, pub-…, DIRECT, …`), a klepněte na **Uložit ads.txt**. Soubor bude dostupný na adrese `/ads.txt`.

## Související

- [Bloky a rozvržení](../vzhled/bloky-a-rozvrzeni.md)
- [Podpora a příjmy](podpora-a-prijmy.md)
- [Měření a soukromí](../seo-a-ai/mereni-a-soukromi.md)
