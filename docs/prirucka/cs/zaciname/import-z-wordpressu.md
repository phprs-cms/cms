# Import z WordPressu a export webu

Máte web ve WordPressu a chcete s ním přejít na phpRS? Import převede rubriky, štítky, články, stránky a schválené komentáře a ze starých adres udělá přesměrování, takže odkazy ani pozice ve vyhledávačích nepřijdou vniveč. A protože obsah má patřit vám, umí phpRS opačný směr také: celý web vyexportuje do jednoho archivu v otevřeném formátu.

Obojí najdete v administraci pod **Správa → Import a export**. Obrazovku vidí jen správce.

## Než začnete

- Ve WordPressu otevřete **Nástroje → Export**, zvolte **Veškerý obsah** a stáhněte soubor `.xml`.
- V phpRS si v **Nastavení → Zálohy a aktualizace** vytvořte zálohu databáze. Import se dá pustit opakovaně a nic nezdvojí, ale záloha před velkou změnou se hodí vždy.
- Starý web zatím nevypínejte – budou se z něj stahovat obrázky.

## Krok 1: soubor

Soubor nahrajte formulářem. Většina hostingů ale dovolí nahrát jen pár megabajtů; obrazovka vám řekne, kolik je to u vás. Větší export zkopírujte přes FTP do složky `storage/import/` – objeví se v seznamu pod formulářem a vyberete ho tlačítkem **Zobrazit náhled**.

Přijme se jen skutečný export z WordPressu. Soubor se čte po kouscích, takže nevadí ani export o stovkách megabajtů; opravdu velký web (přes 1 GB) exportujte z WordPressu po částech, například po letech.

## Krok 2: náhled

Náhled do databáze nic nezapisuje. Ukáže, kolik je v souboru příspěvků (vydaných, naplánovaných, konceptů), stránek, rubrik, štítků, schválených komentářů, obrázků a autorů – a upozorní na to, co se **nepřevede**:

- **účty a hesla uživatelů** – u článku zůstane jméno autora, článek bude patřit vám;
- **e-maily a IP adresy komentujících** – nepřenášejí se vůbec;
- **nabídky, widgety, vzhled a nastavení doplňků** – navigaci a bloky si sestavíte v phpRS znovu;
- **vlastní typy obsahu doplňků** (produkty e-shopu, události, portfolio…) – náhled je vyjmenuje;
- **zkratky doplňků** v hranatých závorkách (formuláře, stavitelé stránek) – značka zmizí, text uvnitř zůstane;
- soukromé příspěvky, koš, revize a automatické koncepty. Příspěvek chráněný heslem se převede jako koncept.

Pod přehledem zvolíte:

- **jazykovou verzi**, do které obsah patří (nabízí se jen na webu s více jazyky),
- zda importovat **koncepty**, **stránky** a **schválené komentáře**,
- zda vytvořit **přesměrování ze starých adres**,
- rubriku pro příspěvky, které ve WordPressu žádnou neměly (jinak vznikne rubrika Nezařazené).

## Krok 3: import

Import běží po dávkách a stránka se sama obnovuje – nechte ji otevřenou a sledujte, kolik položek z kolika je hotovo. Když okno zavřete nebo vypadne spojení, nic se neděje: vraťte se na **Import a export** a u souboru klepněte na **Pokračovat**.

Co se kam převede:

- **Kategorie** se stanou rubrikami včetně podřazení. Zakládají se jen ty, které mají nějaký článek. Rubrika se stejným názvem a adresou, která už na webu je, se použije.
- **Štítky** zůstávají štítky.
- **Příspěvky** se stanou články. Perex je výtah z WordPressu; když chybí, vezme se text před značkou „Číst dál“, jinak první odstavec. Datum vydání zůstává, naplánované příspěvky vyjdou ve svůj čas, koncepty zůstanou koncepty a příspěvek čekající na schválení bude ve stavu Ke korektuře. „Přilepený“ příspěvek se připne na titulní stranu.
- **Text** se vyčistí do podoby, jakou píše editor phpRS: zmizí poznámky blokového editoru, skripty, vložené styly a rámce; obrázek s popiskem zůstane obrázkem s popiskem, galerie se stane fotogalerií a adresa videa z YouTube nebo Vimea se na webu promění v přehrávač.
- **Stránky** se převedou jako stránky; do nabídky v patičce se samy nezařadí, aby ji desítky starých stránek nezaplavily.
- **Komentáře** jen schválené, se jménem, datem a vlákny odpovědí.

Importované články se nikam neoznamují – žádný webhook, IndexNow, Web Push ani newsletter.

### Opakovaný import

Systém si pamatuje, co už ze starého webu převedl. Stejný (nebo novější) export proto můžete pustit znovu: přibude jen to, co na webu ještě není, a články, které jste mezitím v phpRS upravili, zůstanou beze změny. Článek, který jste po importu smazali, se při dalším importu vrátí.

## Přesměrování starých adres

Ke každému článku a stránce vznikne přesměrování ze staré adresy (například `/2024/05/nazev-clanku/`) i z číselné adresy `/?p=123` na novou adresu. Fungují, když je zapnuté rozšíření **Přesměrování**, a najdete je ve **Správa → Přesměrování**. Předpokladem je, že nový web poběží na stejné doméně jako starý.

## Obrázky ze starého webu

Po importu ukazují články pořád na obrázky starého webu. Na stránce s výsledkem proto klepněte na **Stáhnout obrázky ze starého webu**. Hlavní obrázky článků a obrázky v textech se stáhnou, zmenší, dostanou náhledy a uloží do **Médií** – stejně, jako byste je nahráli ručně – a odkazy v textech se přepíší. I tady se pracuje po dávkách a stránka pokračuje sama.

Z bezpečnostních důvodů se stahuje **jen z domény starého webu** uvedené v exportu, jen obrázky JPG, PNG, GIF a WebP do 15 MB. Obrázky z jiných domén (například ze sítě CDN nebo z cizích webů) zůstanou v textu, jak byly. Co se nepodařilo stáhnout, vypíše výsledek; tlačítkem **Zkusit stáhnout znovu** to zopakujete.

Když server stahovat neumí (nemá `curl` ani povolené `allow_url_fopen`), obrazovka to řekne. Obrázky pak nahrajte do Médií ručně a v článcích je vyměňte.

## Po importu

- Projděte pár článků na webu a v **Rubrikách** si srovnejte pořadí a případně rubriky slučte.
- Sestavte navigaci a bloky, vyberte šablonu a nastavte **Identitu webu**.
- Soubor s exportem **smažte** (tlačítko u souboru). Obsahuje e-maily autorů a komentujících ze starého webu a na serveru už není k ničemu.

## Export celého webu

Tlačítko **Vytvořit export** na téže obrazovce uloží do `storage/zalohy/` archiv `export-RRRRMMDD-HHMMSS.zip` a nabídne ho ke stažení. Je v něm:

- `obsah.json` – rubriky, štítky, seriály, články se všemi údaji (včetně jazyka a vazeb mezi překlady), stránky, schválené komentáře, bloky, přesměrování, knihovna médií a základní nastavení webu (název, popis, identita, jazyky),
- `README.txt` – popis formátu,
- složka `media/` se všemi nahranými soubory.

V exportu **nejsou** hesla, účty redakce, klíče a tokeny, údaje k poště a zálohám, čtenáři, odběratelé newsletteru ani e-maily komentujících.

Když mají média přes 1 GB nebo na disku není dost místa, vznikne export jen s daty a obrazovka to oznámí – složku `media/` si pak stáhněte přes FTP. Bez rozšíření PHP `zip` vznikne místo archivu samotný soubor JSON. Uchovávají se poslední tři exporty.

Export není záloha pro obnovu téhož webu – k tomu slouží záloha databáze. Je to pojistka svobody: obsah v čitelném formátu, který jde převést kamkoli.
