# Zálohy a obnova

Zálohy se spravují v **Nastavení → Zálohy a aktualizace**. Systém zálohuje **databázi** – články, stránky, nastavení, uživatele, čtenáře, komentáře. **Nahrané obrázky a přílohy** jsou ve složce `media/` a zálohují se zvlášť.

## Zálohy databáze

- **Automaticky jednou týdně**, pokud je zapnutá volba **Automatická záloha jednou týdně**. Záloha vznikne při přihlášení administrátora, nebo – máte-li nastavený cron – při běhu [úloh na pozadí](ulohy-na-pozadi.md), jakmile je poslední záloha starší než týden.
- **Před každou aktualizací** systému.
- **Ručně** tlačítkem **Vytvořit zálohu teď**.

Soubory `phprs-RRRRMMDD-HHMMSS-….sql.gz` leží ve složce `storage/zalohy/`, která je z internetu nedostupná. Systém drží **posledních 10 záloh**, starší maže. K vytvoření nepotřebuje `mysqldump`, funguje tedy i na sdíleném hostingu.

Každou zálohu můžete **stáhnout**, **obnovit** nebo smazat.

## Kopie záloh mimo server

Záloha na stejném serveru jako web nepomůže, když o hosting přijdete, když ho někdo napadne nebo když selže disk. V oddílu **Zálohy databáze** proto rozbalte **Kopie záloh mimo server** a v poli **Kam kopírovat** zvolte, kam se má každá nová záloha sama nahrát:

- **na FTP server** – jiný hosting nebo domácí NAS; vyplňte **Server**, **Jméno / přístupový klíč**, **Heslo / tajný klíč** a **Složka / bucket**,
- **do úložiště S3** – Amazon S3, Backblaze B2, Wasabi, Cloudflare R2; do pole **Server** patří koncový bod, dále **Jméno / přístupový klíč**, **Heslo / tajný klíč**, **Složka / bucket** (kbelík se složkou) a **Region (jen S3)**.

Nastavení uložte a klepněte na **Vytvořit zálohu teď** – kopie se nahraje hned a uvidíte, jestli spojení funguje. Výsledek posledního pokusu ukazuje i **Stav systému**.

Pro zálohy doporučujeme v úložišti samostatný přístup, který smí **jen zapisovat** do jedné složky.

## Záloha médií

Tlačítko **Stáhnout zálohu médií (ZIP)** zabalí složku `media/`. U webu s mnoha fotkami může být soubor velký a jeho příprava trvat; pak je spolehlivější složku `media/` pravidelně stahovat přes FTP nebo zálohovat nástrojem hostingu.

## Obnova

U zálohy klepněte na **Obnovit**. Obnova **přepíše současný obsah databáze** stavem ze zálohy – všechno, co na webu přibylo potom, zmizí. Současný stav si systém předtím sám uloží do nové zálohy, takže se dá vrátit zpět.

Když administrace neběží, postupujte podle kapitoly [Přesun webu](../zaciname/presun-webu.md): zálohu nahrajte do databáze nástrojem hostingu.

## Doporučený režim

1. Automatické zálohy nechte zapnuté.
2. Nastavte kopie mimo server.
3. Jednou za čas zkuste zálohu obnovit na zkušební instalaci – záloha, kterou nikdo nezkusil obnovit, je jen naděje.
