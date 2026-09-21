# Přesun webu na jiný hosting nebo doménu

Web tvoří tři věci: **soubory**, **databáze** a **nastavení adresy**. Přesun trvá podle množství fotek od minut po desítky minut.

## Postup

1. **Záloha databáze.** **Nastavení → Zálohy a aktualizace → Vytvořit zálohu teď** a soubor si stáhněte. Tamtéž stáhnete i **zálohu médií (ZIP)**.
2. **Soubory.** Celou složku webu zkopírujte na nový hosting včetně `config.php`, složek `media/` a `storage/` a všech skrytých souborů `.htaccess`.
3. **Databáze.** Na novém hostingu založte prázdnou databázi a zálohu do ní nahrajte – přes phpMyAdmin (soubor `.sql.gz` umí načíst přímo) nebo nástrojem hostingu.
4. **`config.php`.** Přepište v něm údaje k databázi podle nového hostingu: `host`, `port`, `name`, `user`, `password`. Předponu tabulek neměňte.
5. **Adresa webu.** Pokud se mění doména, přihlaste se do administrace a v **Nastavení → Základní → Adresa webu** zadejte novou adresu včetně `https://`. Z tohoto nastavení – ne z adresy v prohlížeči – se skládají odkazy v e-mailech, RSS, mapě webu a oznámeních.
6. **Cache.** Smažte obsah složky `storage/cache/`; vytvoří se znovu.
7. **Kontrola.** Projděte **Nastavení → Stav systému** – ukáže chybějící rozšíření PHP, práva k zápisu i stav HTTPS na novém místě.

## Na co nezapomenout

- **Přesměrování ze staré domény.** Na starém hostingu nechte přesměrování 301 na novou doménu, ať nepřijdete o odkazy a pozice ve vyhledávačích.
- **Pošta.** Pokud se mění i e-mailová schránka, upravte **Nastavení → Pošta** a pošlete si zkušební zprávu.
- **Cron.** Máte-li nastavené volání adresy úloh, přeneste ho na nový hosting s novou adresou (viz [Úlohy na pozadí](../provoz/ulohy-na-pozadi.md)).
- **Web Push.** Odběry oznámení jsou vázané na doménu; po změně domény si je čtenáři musí zapnout znovu.
- **Časové pásmo.** Je uložené v nastavení webu a přenáší se s databází; na pásmu nového serveru nezáleží.

## Obnova ze zálohy

Stejný postup poslouží i po havárii. Když administrace běží, jde zálohu obnovit i přímo v ní: **Nastavení → Zálohy a aktualizace**, u vybrané zálohy **Obnovit**. Obnova přepíše současný obsah databáze; stav před obnovou si systém sám uloží do nové zálohy.
