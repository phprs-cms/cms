# Bezpečnost

phpRS je ve výchozím stavu nastavený bezpečně. Tahle stránka shrnuje, co udělat po instalaci, co systém hlídá sám a jak postupovat při podezření na napadení.

## Pět věcí po instalaci

1. **Ověřte, že je `install.php` pryč.** Instalátor se po dokončení maže sám; když to server nedovolil, smažte soubor ručně – Stav systému na něj upozorňuje v řádku **Instalátor**.
2. **Zapněte HTTPS** a přesměrování z `http://`. Certifikát (Let's Encrypt) dnes nabízí každý hosting zdarma. Přes HTTPS systém posílá i hlavičku HSTS.
3. **Zapněte dvoufázové přihlášení** všem administrátorům: klepnutím na avatar vpravo nahoře se otevře **Můj účet**, oddíl **Dvoufázové přihlášení**. **Přihlašovací klíče** (otisk prstu, Face ID) se nabídnou až po jeho zapnutí. Stačí libovolná ověřovací aplikace (Google Authenticator, 1Password, Aegis…). Osm **záložních kódů** se zobrazí jen jednou – uložte si je mimo telefon.
   Kdo nechce při každém přihlášení opisovat kód, přidá si tamtéž **přihlašovací klíč**: otisk prstu, Face ID, Windows Hello nebo bezpečnostní klíč. Klíč je vázaný na doménu webu, takže ho podvržená stránka nezíská; kód z aplikace a záložní kódy zůstávají jako záloha pro případ, že zařízení nemáte u sebe. Po přestěhování webu na jinou doménu je potřeba klíče přidat znovu.
4. **Nastavte zálohy mimo server** – viz [Zálohy a obnova](zalohy.md).
5. **Nechte zapnuté automatické bezpečnostní aktualizace** – viz [Aktualizace](../zaciname/aktualizace.md).

## Účty a oprávnění

- Každý člověk má **vlastní účet**. Sdílené účty znemožní dohledat, kdo co změnil.
- Dávejte **nejmenší potřebná práva**: redaktorovi jen moduly, které používá, případně jen jeho rubriky. Administrátorů mějte co nejméně.
- Odcházejícímu kolegovi účet **zablokujte** (**Správa → Uživatelé** → účet → **Podrobné nastavení** → **Zablokovat účet**) – jeho články zůstanou podepsané.
- Heslo má nejméně 10 znaků. Po změně hesla se účet odhlásí na všech ostatních zařízeních.

## Co systém hlídá sám

- **Hádání hesel:** po 10 chybných pokusech se účet na 15 minut zamkne; stejný limit platí na jednu IP adresu i na chybné kódy dvoufázového přihlášení. Zámek je dočasný záměrně – jinak by kdokoli mohl redakci vyřadit z provozu.
- **Přihlášení čtenářů** má stejnou ochranu.
- **Administrace** posílá přísnou Content Security Policy (žádné cizí ani vložené skripty) a zákaz ukládání do cache.
- **Nahrané soubory** se ve složce `media/` nikdy nespouštějí; povolené jsou jen bezpečné typy.
- **Aktualizace** se instalují jen s platným podpisem vydavatele.
- **Neporušenost jádra:** Stav systému porovnává soubory s podepsaným seznamem vydání a hlásí změněné, chybějící i přidané soubory.
- **Správa → Protokol změn** zaznamenává přihlášení a důležité změny.
- **Komentáře a formuláře** chrání antispam bez cizích služeb a bez sledování čtenářů.

## Napojení na Claude a API

Tokeny pro napojení (MCP) a API vytvářejte **pro každý účel zvlášť** a nepoužívané mažte. Napojení smí pracovat jen s obsahem a vlastními šablonami – nedostane se k nastavení serveru, uživatelům ani souborům systému. Vlastní šablony procházejí kontrolou, která nepovolí práci se soubory, sítí ani spouštění procesů.

## Podezření na napadení

1. V **Stavu systému** zkontrolujte řádek **Soubory jádra** a projděte **Správa → Protokol změn**.
2. Změňte hesla všech administrátorů, heslo k databázi a k FTP. Vytvořte nové tokeny pro cron, monitoring, API a napojení.
3. Přepište soubory systému čistým balíčkem stejné verze (postup ruční aktualizace). Neznámé soubory smažte – hlavně v `media/` a `layout/`.
4. Pokud si nejste jistí rozsahem, obnovte databázi ze zálohy vytvořené před napadením.

## Nahlášení chyby v zabezpečení

Bezpečnostní chyby prosíme **nehlaste veřejně**. Použijte soukromé hlášení na GitHubu (*Security → Report a vulnerability*) nebo e-mail uvedený na webu projektu. Uveďte verzi a postup, jak chybu vyvolat. Oprava vyjde jako bezpečnostní vydání, které se webům se zapnutou automatikou nainstaluje samo.
