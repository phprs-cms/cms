# Úlohy na pozadí (cron)

Některé práce se nedějí na pokyn člověka, ale v čase: vydání naplánovaného článku, rozeslání oznámení a newsletteru, opakované odeslání nedoručené pošty, týdenní záloha.

## Bez nastavení to funguje taky

Ve výchozím stavu se tyto úlohy spouštějí **při návštěvách webu**. Web s běžnou návštěvností tak nic nastavovat nemusí. U webu, kam v noci nikdo nechodí, se ale článek naplánovaný na 6:00 vydá až s prvním ranním čtenářem – a newsletter odejde později, než jste chtěli.

## Přesný čas zajistí cron

1. Otevřete **Nastavení → Stav systému**, oddíl **Úlohy na pozadí (cron)**.
2. Klepněte na **Vytvořit adresu pro cron**. Zobrazí se hotový řádek:

   ```
   */5 * * * * curl -s "https://www.vasweb.cz/ulohy?token=…" > /dev/null
   ```

3. Řádek vložte do plánovače úloh (cronu) v administraci hostingu. Pokud hosting nechce celý příkaz, ale jen adresu, zadejte samotnou adresu a interval **5 minut**.

Hosting bez cronu nahradí kterákoli služba, která umí pravidelně volat adresu (například dohledové nástroje typu UptimeRobot).

Adresa obsahuje tajný token. Kdyby unikla, tlačítkem **Vytvořit novou adresu (stará přestane platit)** ji vyměníte; potom nezapomeňte upravit cron.

## Jak poznám, že to běží

**Stav systému**, řádek **Úlohy na pozadí**: v pořádku je, když úlohy běžely v posledních 30 minutách. Varování znamená, že cron nevolá, nebo že na web nikdo nepřišel.

Adresu si můžete otevřít i v prohlížeči – odpoví řádkem `OK`, časem a seznamem toho, co se vykonalo.

## Co přesně úlohy dělají

- vydají naplánované články a rozešlou k nim oznámení (webhook, IndexNow pro vyhledávače, Web Push),
- založí automatické vydání newsletteru a odešlou další dávku rozesílaného newsletteru i oznámení Web Push,
- zkusí znovu odeslat poštu, která se nepodařila doručit,
- hledají nefunkční odkazy ve vydaných článcích,
- dvakrát denně zkontrolují aktualizace a bezpečnostní vydání nainstalují sama (pokud je to zapnuté),
- vytvoří týdenní zálohu databáze a nahrají ji mimo server, pokud je to zapnuté. Záloha vzniká při volání cronu nebo při vstupu administrátora do administrace, ne při návštěvě čtenáře.
