# Překlad obsahu

Na vícejazyčném webu má každá jazyková verze vlastní rubriky, články a stránky. Nejde o jeden článek se dvěma texty, ale o dva samostatné články, které můžete propojit jako překlady. Každý tak má vlastní adresu, datum vydání, komentáře i nastavení pro vyhledávače – a jedna verze může mít obsah, který druhá nemá.

Předpokladem je zapnuté rozšíření **Jazykové verze webu** a aspoň jedna další verze – viz [Jazyky webu](jazyky-webu.md).

## Rubriky určují jazyk

Jazyk se nevolí u článku, ale u rubriky. Článek ho převezme z rubriky, do které ho uložíte.

1. Otevřete **Obsah → Rubriky** a založte novou rubriku.
2. V poli **Jazyková verze** zvolte jazyk, například **English – /en/**. Články v rubrice patří do této jazykové verze webu.
3. V poli **Je překladem** vyberte odpovídající rubriku ve výchozím jazyce, pokud existuje. Přepínač jazyků pak z rubriky Sport povede přímo na Sports a vyhledávače dostanou značky hreflang. Jinak ponechte **– není překlad –**.
4. Uložte.

Začněte tím, že pro každou další verzi založíte rubriky odpovídající těm hlavním. Navigace, blok Rubriky i výběr rubriky na webu ukazují v každé verzi jen její rubriky.

Když u existující rubriky jazyk změníte, přejdou do nové verze i všechny její články.

## Ruční překlad článku

1. Založte nový článek a zařaďte ho do rubriky cílového jazyka.
2. Napište nebo vložte přeložený titulek, perex a text. Nezapomeňte na **Ve zkratce**, otázky a odpovědi, **Titulek pro vyhledávače** a **Popis pro vyhledávače**.
3. Rozbalte oddíl **Překlad článku** a do pole **Originál ve výchozím jazyce** zadejte adresu nebo číslo původního článku. Stačí vložit celou adresu článku z prohlížeče.
4. Uložte a vydejte jako každý jiný článek.

Pole **Originál ve výchozím jazyce** se vyplňuje jen u článku v další jazykové verzi. Originálem musí být článek ve výchozím jazyce; jinou hodnotu systém při uložení zahodí. Máte-li verze tři, propojte oba překlady se stejným originálem – přepínač pak vede mezi všemi třemi.

U článku ve výchozím jazyce ukazuje oddíl **Překlad článku** řádek **Jazykové verze článku**: u každého jazyka buď odkaz **otevřít překlad**, nebo poznámku **zatím bez překladu**.

### Co propojení dělá

- Přepínač jazyků u článku vede přímo na jeho překlad, ne na hlavní stránku druhé verze.
- Do hlavičky stránky se vloží značky hreflang na všechny vydané verze článku.
- Na obsah, komentáře, hodnocení ani počty přečtení nemá propojení vliv – každý článek má vlastní.

Nevydaný překlad se v přepínači ani ve značkách neobjeví.

## Překlad AI asistentem

Se zapnutým rozšířením **AI asistent v editoru** a vloženým klíčem nabídne oddíl **Překlad článku** u každého jazyka bez překladu tlačítko **Přeložit asistentem**.

1. Článek ve výchozím jazyce uložte – překládá se naposledy uložená verze.
2. Klepněte na **Přeložit asistentem** u zvoleného jazyka a potvrďte. Překlad může trvat i minutu.
3. Otevře se nový článek v cílovém jazyce: koncept, propojený s originálem.
4. Překlad přečtěte a opravte. Asistent může chybovat ve jménech, číslech a odborných výrazech. Pak článek vydejte.

Pravidla:

- Přeložit jde jen článek ve výchozím jazyce a do každého jazyka jednou. Existuje-li už překlad, tlačítko vás na něj zavede.
- Cílovou rubrikou je protějšek rubriky originálu (pole **Je překladem**). Když neexistuje, použije se první rubrika cílového jazyka, do které smíte psát. Bez takové rubriky se překlad nezaloží.
- Překládá se titulek, perex, text, Ve zkratce, otázky a odpovědi, klíčová slova, titulek a popis pro vyhledávače. Formátování, obrázky a odkazy zůstávají z originálu.
- Obrázek, šablona článku, autor, spoluautoři, štítky, zámek a další nastavení se převezmou.
- Adresa článku vznikne z přeloženého titulku.

Co se kam posílá a jaké má asistent limity, popisuje stránka [AI asistent](../psani/ai-asistent.md).

## Stránky

Stránky (O nás, Kontakt, Zásady ochrany soukromí) mají ve formuláři stejná dvě pole jako rubriky: **Jazyková verze** a **Je překladem**. Stránka se zobrazuje jen ve své jazykové verzi – v navigaci, v bloku Stránky i na své adrese (`/en/about`). S vyplněným **Je překladem** vede přepínač jazyků z jedné na druhou.

Stránky asistent nepřekládá. Přeložte je ručně.

## Novinky a ankety

Novinka i anketa má pole **Jazyková verze** a ukáže se jen v ní. V jazyce, který nemá aktivní anketu, se zobrazí nejnovější otevřená anketa tohoto jazyka.

## Štítky

Štítky jsou společné pro všechny verze a nepřekládají se. Stránka štítku vypisuje v každé verzi jen její články. Blok Štítky ukazuje jen štítky, které mají v dané verzi aspoň jeden článek. Pro jinojazyčné články můžete založit vlastní štítky v daném jazyce.

## Bloky

Systémové bloky (Rubriky, Články z rubriky, Nejčtenější, Archiv, Autoři, Stránky, Novinky, Anketa) vypisují obsah právě zobrazené verze samy. Nadpis bloku a obsah bloků **Text** a **Menu** se nepřekládá. Postup:

1. Ve vizuálním editoru bloků otevřete nastavení bloku a rozbalte **Kdy a kde blok zobrazit**.
2. V poli **Jazyková verze** zvolte výchozí jazyk.
3. Přidejte druhý blok stejného typu, dejte mu nadpis a obsah v druhém jazyce a v poli **Jazyková verze** zvolte druhý jazyk.

Volba **ve všech jazycích** se hodí pro bloky bez textu, například Reklama, nebo s vypnutým nadpisem.

## Newsletter a oznámení

- **Newsletter:** odběratel patří k verzi, na které se přihlásil. Vydání je vždy v jednom jazyce a dostanou ho jen odběratelé téhož jazyka; automatický newsletter zakládá pro každý jazyk vlastní vydání. Texty e-mailu jsou v jazyce vydání. Podrobnosti: [Newsletter](../ctenari-a-prijmy/newsletter.md).
- **E-maily čtenářům** (potvrzení odběru, registrace, přihlašovací odkaz) přicházejí v jazyce verze, na které čtenář akci provedl.
- **Oznámení v prohlížeči** se podle jazyka nedělí. Odběratel dostane oznámení o článcích ze všech verzí.
- **Redakční upozornění** (předávka ke korektuře, vydání, vrácení) chodí členům redakce v jazyce jejich administrace.

## Související

- [Jazyky webu](jazyky-webu.md)
- [AI asistent](../psani/ai-asistent.md)
- [Rubriky, štítky a seriály](../psani/rubriky-stitky-serialy.md)
- [Bloky a rozvržení](../vzhled/bloky-a-rozvrzeni.md)
