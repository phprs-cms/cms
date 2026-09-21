# Plánování a revize

O tom, zda a kdy je článek na webu, rozhodují dvě pole v oddílu **Vydání**: **Stav** a **Datum vydání**.

## Stavy článku

| Stav | Co znamená | Kdo ho může nastavit |
|---|---|---|
| **Koncept – rozepsaný** | na článku se pracuje, vidí ho jen redakce | každý |
| **Ke korektuře – hotovo, prosím o kontrolu** | autor je hotov a předává článek ke kontrole | každý |
| **Schváleno – čeká na vydání** | zkontrolováno, zbývá vydat | kdo smí vydávat |
| **Vydaný** | článek je na webu, nebo na něj v daný čas přijde | kdo smí vydávat |

Autor bez práva vydávat má v nabídce jen první dvě volby. Kdo právo vydávat má, říká stránka [Role a oprávnění](../redakce/role-a-opravneni.md); jak si redakce článek předává, popisuje [Předávka a korektura](../redakce/predavka-a-korektura.md).

Ve výpisu článků stavům odpovídají záložky **Všechny**, **Vydané**, **Naplánované**, **Koncepty**, **Ke korektuře** a **Schválené**.

## Plánované vydání

1. Nastavte **Stav** na **Vydaný**.
2. Do pole **Datum vydání** zadejte datum a čas v budoucnosti.
3. Uložte.

Článek má ve výpisu značku *naplánováno* a najdete ho v záložce **Naplánované** i v redakčním kalendáři. Na webu se objeví sám v daný čas. Do té doby ho neuvidí nikdo kromě přihlášené redakce (tlačítkem **Náhled**).

Datum vydání se řídí časovým pásmem webu. Nastavuje ho administrátor v **Nastavení → Základní → Časové pásmo**; nápověda u pole ukazuje, kolik je podle něj právě hodin.

Tlačítko **Vydat**, které ve výpisu u nevydaného článku vidí ten, kdo smí vydávat, udělá totéž co stav **Vydaný**: je-li datum vydání v budoucnosti, článek se naplánuje, jinak vyjde hned.

### Role úloh na pozadí

Naplánovaný článek se v daný čas zobrazí i bez dalšího nastavení. Na vydání ale navazují práce, které obstarávají úlohy na pozadí: obnovení uložených stránek, aby se článek objevil i na hlavní stránce, a rozeslání oznámení. Ve výchozím stavu se úlohy spouštějí při návštěvách webu. Na webu, kam v noci nikdo nechodí, se tak ranní článek může opozdit. Přesný čas zajistí cron – postup je na stránce [Úlohy na pozadí](../provoz/ulohy-na-pozadi.md).

### Stažení z hlavní stránky

Pole **Stáhnout z hlavní stránky** v oddílu **Další nastavení** je nepovinné. Po zadaném datu článek zmizí z hlavní stránky; v rubrice a ve vyhledávání zůstane.

## Aktualizováno

U vydaného článku je v oddílu **Vydání** volba **Označit jako aktualizovaný (čtenář uvidí „Aktualizováno“ s dnešním datem)**. Zaškrtněte ji, když do článku doplníte podstatnou novou informaci, a uložte. Čtenář nad textem uvidí **Aktualizováno** s datem a časem.

Volba platí pro jedno uložení. Při opravě překlepu ji nechte prázdnou – datum aktualizace se nezmění.

## Revize

Kdykoli uložíte článek se změněným titulkem, perexem nebo textem, předchozí znění se uloží jako verze. Uchovává se posledních 20 verzí. Změny ostatních polí (rubrika, štítky, datum) verzi nevytvářejí.

Verze najdete dole ve sloupci nastavení v oddílu **Historie verzí**. U každé je datum, jméno toho, kdo změnu uložil, a odkaz **co se změnilo**.

### Porovnání verzí

Odkaz **co se změnilo** otevře obrazovku **Porovnání verzí**. Ukazuje rozdíl mezi zvolenou verzí a současným zněním zvlášť pro titulek, perex a text; přidaný a smazaný text je barevně odlišený a nahoře je jejich součet. Změny formátování a obrázků se neporovnávají.

### Obnovení starší verze

1. V **Historii verzí** klepněte na datum verze, nebo v porovnání na **Načíst tuto verzi do editoru**.
2. Starší znění se načte do editoru. Nahoře se zobrazí upozornění, že platit začne až po uložení.
3. Zkontrolujte text a klepněte na **Uložit**. Dosavadní znění se přitom samo uloží jako další verze, takže se k němu lze vrátit.

Pokud si obnovení rozmyslíte, formulář prostě opusťte bez uložení.

## Úprava přímo na webu

Přihlášený uživatel, který smí článek upravit, vidí na jeho stránce na webu odkaz **Upravit zde**.

1. Klepněte na **Upravit zde**. Místo článku se v šabloně webu otevře editor s poli **Titulek**, **Perex** a **Text**.
2. Upravte text a klepněte na **Uložit**. Vrátíte se na stránku článku. **Zrušit** se vrátí beze změny.

Platí stejná pravidla jako v administraci: vydaný článek smí měnit jen ten, kdo má právo vydávat, předchozí znění se uloží jako verze a článek se po dobu úprav zamyká. Má-li ho právě otevřený kolega, zobrazí se jeho jméno a editor se neotevře.

Ostatní nastavení (rubrika, štítky, datum) takto změnit nejde – vede k nim odkaz **Všechna nastavení v administraci**.

Stejně fungují i stránky webu (**Obsah → Stránky**): odkaz **Upravit zde** u nich vidí ten, kdo má přístup do sekce Stránky, a upravuje se titulek a text. Stránky historii verzí nemají.

## Související

- [Editor článku](editor.md)
- [Titulní strana a kalendář](../redakce/titulni-strana-a-kalendar.md)
