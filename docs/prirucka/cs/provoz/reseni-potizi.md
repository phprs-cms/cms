# Řešení potíží

Začněte vždy v **Nastavení → Stav systému**: většinu příčin ukáže přímo, včetně **Záznamu chyb**. Stejný záznam je v souboru `storage/log/chyby.log`.

## Web ukazuje chybu nebo bílou stránku

- Podívejte se do `storage/log/chyby.log` (přes FTP, když nejde administrace).
- Po přesunu na jiný hosting jde nejčastěji o chybějící rozšíření PHP, starší verzi PHP než 8.4 nebo špatné údaje k databázi v `config.php`.
- Podrobný výpis chyby přímo na stránce zapne `'debug' => true` v `config.php`. **Na ostrém webu ho hned zase vypněte** – výpis prozrazuje cesty a části kódu.

## Úvodní stránka funguje, články vracejí 404

Nefungují hezké adresy.
Na Apache chybí v kořeni webu soubor `.htaccess` (FTP klienti skryté soubory často nenahrají) nebo hosting nemá zapnutý `mod_rewrite`. Na nginx chybí pravidlo `try_files` – viz [Provoz na nginx](nginx.md).

## „Web se právě aktualizuje“ nezmizí

Hláška se zobrazuje během přepisu souborů a sama zmizí nejpozději za 10 minut. Pokud aktualizace spadla, smažte soubor `storage/udrzba.lock` a aktualizaci zopakujte, případně ji proveďte [ručně](../zaciname/aktualizace.md).

## Nejde se přihlásit

- **„Příliš mnoho pokusů“** – počkejte 15 minut; zámek je dočasný.
- **Ztracený telefon s ověřovací aplikací** – místo kódu zadejte jeden ze záložních kódů. Když je nemáte, dvoufázové přihlášení vám v Uživatelích vypne jiný administrátor.
- **Zapomenuté heslo** – na přihlašovací stránce klepněte na **Zapomenuté heslo?** a zadejte přihlašovací jméno nebo e-mail. Na e-mail účtu přijde odkaz pro nastavení nového hesla; platí hodinu a jde použít jednou. Dvoufázové přihlášení zůstává zapnuté. Podmínkou je vyplněný e-mail u účtu a funkční [pošta](posta.md); jinak nové heslo nastaví jiný administrátor v Uživatelích.
- **Jediný administrátor bez přístupu a bez funkčního e-mailu** – pomůže jen zásah do databáze: v tabulce `rs_user` (předpona podle vaší instalace) vložte do sloupce `password` nový otisk hesla a vyprázdněte `totp_tajemstvi`. Otisk vytvoříte na příkazové řádce:

  ```
  php -r 'echo password_hash("nove-dlouhe-heslo", PASSWORD_DEFAULT), "\n";'
  ```

## Naplánovaný článek nevyšel včas

Úlohy na pozadí se bez cronu spouštějí až s návštěvou webu. Nastavte [cron](ulohy-na-pozadi.md). Pokud čas nesedí o celé hodiny, zkontrolujte **časové pásmo** v Nastavení → Základní.

## E-maily nechodí

Viz [Pošta → Nejčastější potíže](posta.md). Stručně: přehled **Poslední zprávy** řekne, jestli zpráva odešla; když ano, hledejte ve spamu a nastavte SPF a DKIM.

## Nejde nahrát obrázek

- **Soubor je moc velký** – limit určuje PHP (`upload_max_filesize`, `post_max_size`); Stav systému ho ukazuje v řádku *Limit nahrávaných souborů*. Zvýšíte ho v administraci hostingu.
- **Chyba zápisu** – složka `media/` nemá právo zápisu.
- **Nevznikají náhledy** – chybí rozšíření PHP `gd`.

## Změna vzhledu se neprojevila

Prohlížeč drží starou verzi stylů. Obnovte stránku s vymazáním cache (Ctrl/⌘+Shift+R). Pokud web stojí za CDN nebo cache hostingu, vyprázdněte ji i tam. Cache samotného systému smažete vyprázdněním složky `storage/cache/`.

## Aktualizace se nenabízí

Tlačítko **Zkontrolovat teď** v Nastavení → Zálohy a aktualizace zeptá server hned. Řádek **Aktualizace** ve Stavu systému ukáže případnou chybu spojení – některé hostingy blokují odchozí požadavky; pak aktualizujte ručně.

## Když nic z toho nepomůže

Založte hlášení na GitHubu projektu. Připojte verzi phpRS a PHP, text chyby ze záznamu a postup, jak chybu vyvolat. **Nikdy nepřikládejte `config.php`, zálohu databáze ani tokeny.**
