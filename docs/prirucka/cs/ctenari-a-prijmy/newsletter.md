# Newsletter

Newsletter je e-mail s výběrem článků, který posíláte přihlášeným odběratelům. Sestavíte ho ručně, nebo ho necháte odcházet automaticky. Všechno běží na vašem webu, bez cizí rozesílací služby.

## Zapnutí

1. V hlavní nabídce otevřete **Rozšíření**, zaškrtněte **Newsletter** a uložte.
2. V **Nastavení → Základní** vyplňte **E-mail redakce** – z něj newsletter odchází. Bez něj obrazovka Newsletter hlásí **Nejprve vyplňte E-mail redakce v Nastavení – z něj newsletter odchází.**
3. Přidejte na web blok **Newsletter** (**Vzhled → Bloky a rozvržení**). Je to přihlašovací formulář pro čtenáře.
4. Nastavte odesílání přes SMTP. Hromadné zprávy posílané funkcí serveru často končí ve spamu. Viz [Pošta](../provoz/posta.md).

Sekce **Čtenáři → Newsletter** je přístupná administrátorovi a redaktorovi.

## Přihlášení k odběru

Přihlášení má dva kroky (double opt-in):

1. Čtenář zadá e-mail do bloku Newsletter. Web odpoví: **Poslali jsme vám e-mail – odběr potvrďte kliknutím na odkaz v něm.**
2. Čtenář klepne na odkaz v e-mailu. Teprve tím se stává odběratelem.

Bez potvrzení mu nic nechodí. Máte tak doklad, že adresu přihlásil její majitel. Web odpovídá stejně i tomu, kdo už odběratelem je, takže neprozrazuje, které adresy v seznamu jsou. Z jedné IP adresy projde nejvýše pět přihlášení za hodinu; formulář chrání i ochrana proti robotům.

Odběr lze zvolit i při registraci čtenáře zaškrtnutím **Chci dostávat newsletter**. Potvrdí se stejným odkazem jako registrace.

Obrazovka Newsletter ukazuje nahoře počet potvrzených odběratelů a počet těch, u kterých se **čeká na potvrzení e-mailem**. Odkaz **zobrazit** otevře seznam **Odběratelé** (posledních 500) s tlačítky **Smazat** a **Stáhnout CSV**.

## Ruční vydání

1. Otevřete **Čtenáři → Newsletter**, oddíl **Nové vydání**.
2. Vyplňte **Předmět e-mailu** a případně **Úvodní slovo** – pár vět před výčtem článků.
3. V poli **Články** zaškrtněte, co chcete poslat, nejvýše 20 článků. Nabízí se posledních 15 vydaných článků; předvybrané jsou ty, které vyšly od posledního newsletteru.
4. Klepněte na **Poslat na zkoušku redakci**. Zpráva přijde na E-mail redakce. Zkontrolujte ji v poštovním programu i v telefonu. Formulář se při tom vyprázdní, takže ho potom vyplňte znovu.
5. Klepněte na **Rozeslat odběratelům** a potvrďte.

E-mail obsahuje úvodní slovo a u každého článku titulek, perex a odkaz **Číst článek →**; první tři články mají i obrázek. Má i čistě textovou podobu. Celý text článků se neposílá, takže zamčené články zůstávají zamčené.

### Průběh rozesílky

Rozesílá se po dávkách 40 příjemců. Po potvrzení se otevře stránka **Rozesílka newsletteru** s průběžným stavem – odesláno a zbývá. **Nechte ji otevřenou**, sama se obnovuje, dokud nejsou obslouženi všichni. Na konci ohlásí **Hotovo.**

Když stránku zavřete dřív, rozesílka se zastaví. Nic se neztratí ani nepošle dvakrát: v tabulce **Odeslaná vydání** klepněte u vydání na **pokračovat v rozesílce**.

### Naplánování

Rozbalte **Naplánovat na později**, zadejte **Rozeslat v** a klepněte na **Naplánovat**. Naplánované vydání se rozešle samo na pozadí; administraci otevřenou mít nemusíte. Dokud se nezačalo rozesílat, zrušíte ho v tabulce tlačítkem **Zrušit**.

## Automatický newsletter

Rozbalte oddíl **Automatický newsletter**:

| Pole | Možnosti |
|---|---|
| **Posílat sám** | **ne – newsletter sestavuji ručně** (výchozí) · **jednou týdně** · **každý den** |
| **Den a hodina** | den v týdnu platí jen pro týdenní newsletter; hodina 0–23; výchozí pátek 7 hodin |
| **Úvodní slovo** | nepovinný stálý text, nejvýše 1000 znaků |

Automat vybere až osm článků vydaných od posledního newsletteru – připnuté a nejčtenější napřed. Krátké zprávy a články vyřazené z vyhledávačů nezařazuje. Předmět složí z titulku prvního článku a názvu webu. Když nic nového nevyšlo nebo není komu psát, nic se neposílá. V tabulce vydání má štítek **automat**.

## Úlohy na pozadí

Automatická a naplánovaná vydání rozesílají úlohy na pozadí, při každém spuštění jednu dávku 40 příjemců. Ve výchozím stavu se úlohy spouštějí při návštěvách webu. Newsletter tedy odejde při první návštěvě po nastavené hodině a u webu s malou návštěvností se rozesílka protáhne. Pro přesný čas a plynulé odesílání nastavte cron – viz [Úlohy na pozadí](../provoz/ulohy-na-pozadi.md).

Zpráva, kterou se nepodařilo odeslat (například při výpadku SMTP), zůstane ve frontě pošty a úlohy na pozadí ji zkoušejí odeslat znovu. Protokol odeslaných zpráv a chyb je v **Nastavení → Pošta**.

## Jazykové verze

Na vícejazyčném webu má každý jazyk vlastní odběratele – čtenář se přihlašuje do té verze, na které formulář vyplnil. Vydání je vždy v jednom jazyce:

- u ručního vydání vyberte články jen jedné jazykové verze (nabízí se jich 30 a mají štítek jazyka); dostanou ho odběratelé téže verze,
- automat založí pro každý jazyk zvláštní vydání z jeho článků; úvodní slovo se přidává jen k vydání ve výchozím jazyce,
- texty e-mailu (odkaz na článek, patička, odhlášení) jsou v jazyce vydání.

## Statistika vydání

Tabulka **Odeslaná vydání** ukazuje posledních 30 vydání: **Příjemců**, **Otevřeno** (i v procentech) a **Prokliků**. Jsou to souhrnná čísla; u jednotlivých odběratelů se nic nesleduje. Otevření je orientační: část poštovních programů obrázky nenačítá, jiné je načítají samy.

## Odhlášení

Každá zpráva má v patičce odkaz **Odhlásit odběr** a nese hlavičky pro odhlášení jedním klepnutím, které zobrazují poštovní služby. Odhlášení je okamžité a bez přihlašování: adresa se ze seznamu smaže a web to potvrdí hlášením **Odběr je zrušený**.

Odběratele můžete odstranit i sami v seznamu **Odběratelé**, případně spolu s ostatními údaji čtenáře v **Nastavení → Soukromí a cookies** (viz [Účty čtenářů](ucty-ctenaru.md)).

## Související

- [Pošta](../provoz/posta.md)
- [Úlohy na pozadí](../provoz/ulohy-na-pozadi.md)
- [Bloky a rozvržení](../vzhled/bloky-a-rozvrzeni.md)
- [Web Push](web-push.md)
