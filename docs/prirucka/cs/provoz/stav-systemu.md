# Stav systému

**Nastavení → Stav systému** je první místo, kam se podívat po instalaci, po přesunu webu a vždy, když se něco chová divně. Každý řádek má stav *v pořádku*, *varování* nebo *chyba* a vysvětlení, co s tím.

## Co se kontroluje

| Skupina | Řádky |
| --- | --- |
| **Server** | verze PHP, potřebná a doporučená rozšíření, limit nahrávaných souborů, volné místo na disku |
| **Databáze** | verze serveru, struktura databáze (čekající úpravy), velikost a počet článků |
| **Soubory** | právo zápisu do složek, které ho potřebují (`media/`, `storage/`…) |
| **Bezpečnost** | smazaný instalátor, HTTPS, vypnutý ladicí režim, bezpečnostní hlavičky, dvoufázové přihlášení administrátorů, zablokované účty, neporušenost souborů jádra |
| **Provoz** | chyby za posledních 24 hodin, stáří poslední zálohy, zálohy mimo server, indexování vyhledávači, velikost médií, úlohy na pozadí, aktualizace, odesílání pošty |

**Chyba** znamená, že část systému nefunguje. **Varování** je doporučení – web běží, ale něco je vhodné dořešit (typicky chybějící 2FA, vypnuté zálohy mimo server, nenastavený cron).

## Další části stránky

- **Pošta** – odeslání zkušebního e-mailu na adresu redakce.
- **Záznam chyb** – posledních několik chyb, které systém zachytil. Návštěvník při chybě vidí jen obecnou omluvu; podrobnosti jsou tady a v souboru `storage/log/chyby.log`. Záznam jde vyprázdnit.
- **Úlohy na pozadí (cron)** – adresa pro cron, viz [Úlohy na pozadí](ulohy-na-pozadi.md).
- **Monitoring** – adresa se stavem ve formátu JSON.

## Monitoring

Po vytvoření přístupového tokenu je stav dostupný na adrese

```
https://www.vasweb.cz/stav.json?token=…
```

Odpověď obsahuje souhrnný `stav`, `verze`, `cas` a pole `kontroly` se všemi řádky. Dohledový nástroj (UptimeRobot, Zabbix, Uptime Kuma…) stačí nastavit tak, aby hlídal souhrnný stav. Bez platného tokenu adresa odpoví chybou 403.
