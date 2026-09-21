# Rubriky, štítky a seriály

Obsah webu se třídí třemi způsoby. **Rubrika** je pevné zařazení – každý článek má právě jednu. **Štítky** popisují, o čem článek je, a může jich mít víc. **Seriál** spojuje díly, které na sebe navazují.

## Rubriky

Rubriky spravujete v **Obsah → Rubriky**. Bez rubriky článek uložit nejde, první rubriku proto založte dřív než první článek.

### Nová rubrika

1. Klepněte na **Nová rubrika**.
2. Vyplňte **Název rubriky**. **Adresa rubriky** se vytvoří z názvu sama; rubrika je pak na webu na adrese `/rubrika/<adresa>`.
3. Podle potřeby doplňte **Popis** – zobrazí se nad výpisem článků rubriky a použije se jako popis pro vyhledávače.
4. Uložte.

| Pole | K čemu je |
|---|---|
| **Nadřazená rubrika** | udělá z rubriky podrubriku |
| **Obrázek (ikona) rubriky** | nepovinná adresa obrázku |
| **Pořadí** | vyšší číslo znamená výš v seznamu rubrik; nová rubrika má 100 |
| **Zobrazit** | bez zaškrtnutí se rubrika v seznamu rubrik na webu neukazuje |

### Podrubriky

Podrubrika vznikne volbou **Nadřazená rubrika**. Ve výpisu rubrik i v nabídce rubrik ve formuláři článku je odsazená pod svou nadřazenou rubrikou. Rubriku nelze podřídit sobě samé ani vlastní podrubrice.

Omezení uživatele na vybrané rubriky platí i pro jejich podrubriky – viz [Role a oprávnění](../redakce/role-a-opravneni.md).

### Mazání a přesuny

Rubriku, která obsahuje články, smazat nejde. Články nejdřív přesuňte jinam: v přehledu článků je označte a zvolte **přesunout do rubriky…** (viz [Titulní strana a kalendář](../redakce/titulni-strana-a-kalendar.md)). Podrubriky smazané rubriky se posunou o úroveň výš.

## Štítky a témata

Štítky píšete ve formuláři článku do pole **Štítky**, oddělené čárkou – například `doprava, územní plán`. Pole napovídá štítky, které už na webu jsou. Neznámý štítek se uložením článku založí sám. Článek může mít nejvýše 20 štítků.

Na webu jsou štítky pod textem článku. Klepnutím na štítek si čtenář zobrazí všechny články, které ho mají (`/stitek/<adresa>`). Podle společných štítků se také vybírají související články.

### Stránka tématu

Přehled štítků je v **Obsah → Štítky a témata**. U každého štítku vidíte počet článků.

1. U štítku klepněte na **Upravit**.
2. Vyplňte **Úvod tématu** a vyberte **Obrázek tématu**.
3. Uložte.

Štítek s úvodem se na webu chová jako stránka tématu: nahoře úvod ke kauze, volbám nebo festivalu, pod ním všechny články. V přehledu má takový štítek značku *má úvod* a řadí se nahoře.

### Sloučení a smazání

Překlepy a dvojí psaní (`Územní plán` a `uzemni plan`) napravíte sloučením: **Upravit → Sloučit s jiným štítkem → Sloučit do**. Články dostanou vybraný štítek, původní zanikne a jeho adresa se přesměruje na nový.

**Smazat** odstraní jen štítek. Články zůstanou, jen ho už nebudou mít.

Štítek lze přidat i více článkům najednou – hromadnou akcí **přidat štítek…** v přehledu článků.

## Seriály

Seriál je skupina článků, které k sobě patří: díly reportáže, pravidelný sloupek, cestopis na pokračování.

1. Ve formuláři článku najděte v oddílu **Zařazení** pole **Seriál**.
2. Vyberte existující seriál, nebo napište název do pole **…nebo název nového seriálu**. Nový seriál se založí uložením článku.
3. U dalších dílů už seriál jen vyberte z nabídky.

Na webu se pod článkem zobrazí oddíl **Související články** s odkazy na ostatní vydané díly seriálu, seřazené podle data. Článek smí být jen v jednom seriálu. Ze seriálu ho vyřadíte volbou **– článek není součástí seriálu –**.

U článku, který v žádném seriálu není, se související články vyberou samy podle štítků a rubriky. Administrátor to může vypnout v **Nastavení → Základní → Další možnosti → Související články automaticky**.

## Související

- [Editor článku](editor.md)
- [Typy obsahu](typy-obsahu.md)
