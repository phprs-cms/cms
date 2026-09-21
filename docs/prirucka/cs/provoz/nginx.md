# Provoz na nginx

Na Apache a LiteSpeed se o ochranu citlivých složek, hezké adresy a cache statických souborů starají dodané soubory `.htaccess`. **Nginx je nečte.** Bez vlastních pravidel by šly z webu stáhnout soubory, které veřejné být nemají – `config.php` s heslem k databázi, složka `storage/` se zálohami.

> Ukázková konfigurace je v balíčku v souboru `system/nginx.priklad.conf`. Zatím **nebyla ověřena na běžícím serveru** – před nasazením spusťte `nginx -t` a projděte kontrolu na konci této stránky.

## Co musí konfigurace zajistit

1. **Zákaz přístupu** k `config.php`, složkám `system/`, `storage/`, `tools/`, `docs/`, ke skrytým souborům (`.git`, `.htaccess`) a k souborům `.php` uvnitř `layout/`.
2. **Ve složce `media/` se nic nespouští.** Soubory `.php`, `.html`, `.svg` a `.js` se odtud nevydávají vůbec, dokumenty se nabízejí ke stažení.
3. **Hezké adresy:** co není soubor, obslouží `index.php` (`try_files $uri $uri/ /index.php?$query_string;`).
4. **PHP se spouští jen ze tří souborů:** `index.php`, `admin.php`, `install.php`. Ostatní `.php` vracejí 404.
5. **Hlavička `Authorization`** se předává do PHP – potřebuje ji napojení na Claude (MCP) a API.
6. **WebP:** prohlížeči, který ho umí, se místo `foto.jpg` vydá `foto.jpg.webp`, pokud existuje. Vyžaduje mapu `$webp_pripona` v bloku `http`.
7. **Limit nahrávání** `client_max_body_size` sladěný s `upload_max_filesize` v PHP.

## Kontrola po nasazení

Tyto adresy musí vracet **403 nebo 404**, nikdy obsah souboru:

```
https://www.vasweb.cz/config.php
https://www.vasweb.cz/system/sql/schema.sql
https://www.vasweb.cz/storage/log/chyby.log
https://www.vasweb.cz/.htaccess
```

Pak projděte **Nastavení → Stav systému** a zkuste nahrát obrázek, otevřít článek s hezkou adresou a přihlásit se do administrace.
