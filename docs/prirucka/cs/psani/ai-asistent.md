# AI asistent

AI asistent je volitelný pomocník v editoru článku. Navrhuje titulky, perex, shrnutí, štítky a popis pro vyhledávače, dělá korekturu, popisuje obrázky a umí článek přeložit do jiné jazykové verze webu. Jen navrhuje – nic sám neukládá ani nevydává.

## Zapnutí

Asistent je po instalaci vypnutý. Zapíná ho administrátor:

1. V hlavní nabídce otevřete **Rozšíření** a zaškrtněte **AI asistent v editoru**.
2. Níže v oddílu **AI asistent – klíč a model** vložte do pole **Klíč Claude API** vlastní klíč. Vytvoříte ho na console.anthropic.com v části API Keys.
3. V poli **Model** zvolte jednu ze tří možností: rychlý a úsporný, vyvážený (doporučeno), nebo nejpečlivější.
4. Klepněte na **Uložit nastavení**.

Používání platíte službě Anthropic podle skutečné spotřeby. Klíč se ukládá jen na vašem webu a do formuláře se už nevypisuje – zobrazí se jen jeho konec. Volbou **Odebrat uložený klíč** ho smažete. Bez klíče se tlačítka asistenta v editoru neobjeví.

## Co asistent umí

U popisků polí ve formuláři článku přibudou tlačítka se znakem ✦:

| Pole | Tlačítko | Co dostanete |
|---|---|---|
| **Titulek** | **✦ Navrhnout** | několik různě pojatých titulků |
| **Perex (úvod)** | **✦ Navrhnout** | varianty perexu |
| **Text článku** | **✦ Korektura** | seznam oprav pravopisu, překlepů, interpunkce a typografie |
| **Štítky** | **✦ Navrhnout** | štítky, přednostně z těch, které už web má |
| **Ve zkratce** | **✦ Navrhnout** | tři až pět bodů s hlavními fakty |
| **Popis pro vyhledávače** | **✦ Navrhnout** | varianty krátkého popisu |

Asistent vychází z textu článku. Dokud je článek příliš krátký, požádá vás, abyste nejdřív kus napsali.

### Návrhy

Po klepnutí se otevře okno s návrhy. U vybraného klepněte na **Použít** – návrh se vloží do pole a můžete ho dál upravit. Navržené štítky se přidají k těm, které v poli už jsou. Nic se neuloží, dokud článek neuložíte sami.

### Korektura

Korektura ukáže seznam oprav: původní znění, opravené znění a důvod. Opravy, které chcete, nechte zaškrtnuté a klepněte na **Opravit označené**. Asistent nemění styl, fakta ani význam. Oprava, jejíž úsek prochází formátováním (část je třeba tučně), zaškrtnout nejde – opravte ji ručně.

### Popisy obrázků

V oddílu **Kontrola přístupnosti** je u každého obrázku bez popisu tlačítko **✦**. Asistent si obrázek prohlédne a navrhne alternativní text. Návrh se vloží do pole; upravte ho podle potřeby a potvrďte Enterem. Funguje jen u obrázků nahraných do Médií.

### Překlad článku

Překlad se nabízí na webu, který má zapnuté rozšíření **Jazykové verze webu** a aspoň jednu další jazykovou verzi.

1. Článek ve výchozím jazyce uložte.
2. V oddílu **Překlad článku** klepněte na **Přeložit asistentem** u zvoleného jazyka a potvrďte dotaz. Překlad může trvat i minutu.
3. Otevře se nový článek v cílovém jazyce. Je založený jako koncept v rubrice daného jazyka a propojený s originálem.
4. Překlad přečtěte. Asistent může chybovat ve jménech, číslech a odborných výrazech. Teprve potom článek vydejte.

Překládá se naposledy uložená verze: titulek, perex, text, Ve zkratce, otázky a odpovědi, klíčová slova, titulek a popis pro vyhledávače. Formátování, obrázky a odkazy zůstávají z originálu. Obrázek, šablona, autor, štítky a další nastavení se převezmou. Přeložit jde jen článek ve výchozím jazyce a do každého jazyka jednou; v cílovém jazyce musí existovat rubrika, do které smíte psát.

## Co se kam posílá

Bez kliknutí na tlačítko asistenta se nikam nic neposílá. Samotné psaní, ukládání ani automatické ukládání rozepsaného textu s asistentem nesouvisí.

Při kliknutí odejde službě Anthropic (Claude):

- u návrhů a korektury titulek, perex a text rozepsaného článku,
- u štítků navíc seznam štítků vašeho webu,
- u popisu obrázku daný obrázek a začátek článku jako kontext,
- u překladu uložené texty článku vyjmenované výše.

Údaje o čtenářích, komentáře ani jiné články se neposílají. Nepoužívejte asistenta u textů, které redakci nesmějí opustit – například u neověřených informací od chráněného zdroje.

## Omezení

- Každý uživatel může asistenta použít nejvýše 60× za hodinu. Je to pojistka proti nechtěné útratě.
- Použití asistenta se zapisuje do **Protokolu změn** (vidí ho administrátor).
- Velmi dlouhý článek asistent přeložit odmítne.

## Související

- [Editor článku](editor.md)
- [Typy obsahu](typy-obsahu.md)
