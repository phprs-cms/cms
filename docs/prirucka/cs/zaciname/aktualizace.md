# Aktualizace

phpRS se umí aktualizovat sám. Každý balíček je podepsaný vydavatelem a systém nainstaluje jen ten, jehož podpis sedí – podvržený nebo poškozený balíček odmítne.

## Jak to funguje

- Systém se dvakrát denně zeptá na adrese `https://phprs.eu/aktualizace.json`, jestli vyšla nová verze. Neposílá přitom nic než číslo své verze v hlavičce požadavku.
- **Běžnou verzi** instalujete sami jedním tlačítkem.
- **Bezpečnostní vydání** se ve výchozím nastavení nainstaluje **samo**: systém nejdřív zazálohuje databázi, ověří otisk a podpis balíčku, přepíše soubory a pošle e-mail redakci. Kdo to nechce, vypne volbu **Bezpečnostní aktualizace instalovat automaticky** – pak dostane jen e-mail s upozorněním.

## Aktualizace jedním tlačítkem

**Nastavení → Zálohy a aktualizace.** Když je k dispozici nová verze, uvidíte její číslo, seznam změn a tlačítko k instalaci. Po potvrzení:

1. vytvoří se záloha databáze,
2. balíček se stáhne a ověří se jeho otisk a podpis,
3. web na několik vteřin odpovídá hláškou o údržbě,
4. soubory systému se přepíšou, soubory, které nové vydání už neobsahuje, se smažou, a databáze se při prvním požadavku upraví na novou strukturu.

**Nepřepisuje se** `config.php`, složky `media/` a `storage/`, ani vaše vlastní šablony ve složce `layout/`. Vestavěné šablony (`default`, `classic-newspaper`, `modern-magazine`, `minimal`) se přepisují – proto je neupravujte; vlastní vzhled dělejte jako kopii pod jiným názvem.

Tlačítko **Zkontrolovat teď** se na novou verzi zeptá okamžitě.

### Co aktualizace potřebuje

Rozšíření PHP `zip` a `sodium` a právo zápisu do složky webu. Když něco z toho chybí, systém to řekne a nabídne ruční postup.

## Ruční aktualizace

Funguje vždy, i když automatická nejde:

1. V **Nastavení → Zálohy a aktualizace** klepněte na **Vytvořit zálohu teď** a zálohu si stáhněte.
2. Stáhněte nové vydání a rozbalte ho.
3. Přes FTP **přepište všechny soubory kromě** `config.php` a složek `media/` a `storage/`. Soubor `install.php` z balíčku na server nenahrávejte.
4. Otevřete web nebo administraci – databáze se upraví sama.

## Kontrola neporušenosti

Každé vydání nese podepsaný seznam souborů jádra s jejich otisky. **Nastavení → Stav systému** podle něj v řádku **Soubory jádra** hlásí soubory změněné, chybějící i přidané.
Hlášení po vlastní úpravě jádra je v pořádku jen tehdy, když o ní víte; jinak jde o známku napadení webu – viz [Provoz → Bezpečnost](../provoz/bezpecnost.md).
