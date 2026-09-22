# Požadavky na hosting

phpRS běží na běžném sdíleném hostingu. Nepotřebuje přístup přes SSH, Composer, Node.js ani žádný krok sestavení – nahrajete soubory a otevřete instalátor.

## Co hosting musí umět

| Co | Požadavek |
| --- | --- |
| PHP | **8.4 nebo novější** |
| Databáze | **MySQL 8** nebo **MariaDB 10.6** a novější |
| Rozšíření PHP – povinná | `pdo_mysql`, `mbstring`; pro práci s obrázky `gd` (instalace proběhne i bez něj, ale nevzniknou náhledy a Stav systému to hlásí jako chybu) |
| Rozšíření PHP – doporučená | `exif` (správné otočení fotek z mobilu), `intl` (řazení podle češtiny), `curl` (oznamování novinek vyhledávačům), `zip` a `sodium` (aktualizace jedním kliknutím), `zlib` (komprimované zálohy) |
| Webový server | Apache s povoleným `.htaccess`, nebo nginx (viz [Provoz → nginx](../provoz/nginx.md)) |
| HTTPS | silně doporučeno; bez něj nefunguje Web Push ani napojení na Claude a přihlašovací údaje putují nešifrovaně |
| Místo na disku | samotný systém zabere kolem 3 MB; počítejte hlavně s fotkami |

Bez doporučených rozšíření se systém nainstaluje, jen některé funkce nebudou dostupné. Chybějící `exif`, `intl` a `curl` ukáže po instalaci **Nastavení → Stav systému**; chybějící `zip` nebo `sodium` ohlásí až pokus o aktualizaci a bez `zlib` vznikají zálohy jako nekomprimované soubory `.sql`.

## Co si připravit před instalací

1. **Prázdnou databázi** a k ní jméno serveru, název databáze, uživatele a heslo. Zakládá se v administraci hostingu.
2. **Přístup pro nahrání souborů** – FTP, SFTP nebo správce souborů hostingu.
3. **Doménu nebo subdoménu**, na které web poběží, nejlépe už s certifikátem pro HTTPS.
4. **E-mailovou schránku redakce**, ideálně na stejné doméně. Z ní bude web posílat e-maily a do ní budou chodit upozornění.

## Limity PHP, na které se vyplatí podívat

- `upload_max_filesize` a `post_max_size` – výchozí 2 MB a 8 MB jsou na dnešní fotky málo. Doporučujeme aspoň **16 MB** a **32 MB**.
- `memory_limit` – pro zmenšování velkých fotek aspoň **256 MB**.
- `max_execution_time` – stačí výchozích 30 vteřin; delší čas pomůže jen u překladu dlouhých článků asistentem.

Na většině hostingů se limity mění v administraci hostingu v části nazvané podobně jako „Nastavení PHP“.
