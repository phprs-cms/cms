# Účet a přihlášení

Vlastní účet si každý spravuje sám na obrazovce **Můj účet**. Otevřete ji klepnutím na avatar s iniciálou vpravo nahoře. Vedle avataru je i přepínač světlého a tmavého režimu administrace.

## Moje údaje

| Pole | K čemu je |
|---|---|
| **Jméno** | zobrazuje se u vašich článků na webu |
| **E-mail** | chodí na něj upozornění redakce a odkaz pro zapomenuté heslo |
| **Můj web** | nepovinná adresa vašeho webu |
| **Pozice v redakci** | například *redaktorka kultury* |
| **Moje fotka** | čtvercová fotka, stačí 300 × 300 px; vyberete ji z Médií |
| **Pár vět o mně** | nejvýše 1 200 znaků |
| **Jazyk administrace** | Čeština, Slovenčina, English nebo Deutsch |
| **Upozornění e-mailem** | zprávy o korektuře, vydání a vrácení článku |

Máte-li vyplněno **Pár vět o mně**, zobrazí se pod vašimi články medailonek se jménem, pozicí, fotkou a tímto textem. Stejné údaje jsou na stránce autora.

**Jazyk administrace** platí jen pro vás – každý člen redakce může pracovat v jiném jazyce. V tomto jazyce vám chodí i redakční upozornění. Jazyk webu se tím nemění.

**Přihlašovací jméno** změnit nemůžete; mění ho administrátor v sekci Uživatelé. Změny potvrďte tlačítkem **Uložit údaje**.

## Změna hesla

1. Vyplňte **Současné heslo**.
2. Zadejte **Nové heslo** (nejméně 10 znaků) a ještě jednou do pole **Nové heslo znovu**.
3. Klepněte na **Změnit heslo**.

Změnou hesla končí všechna ostatní přihlášení vašeho účtu – na jiném počítači, v telefonu, v zapomenutém prohlížeči. Přihlášení, ve kterém heslo měníte, zůstává.

## Zapomenuté heslo

1. Na přihlašovací obrazovce klepněte na **Zapomenuté heslo?**
2. Zadejte přihlašovací jméno nebo e-mail svého účtu a klepněte na **Poslat odkaz**.
3. Otevřete odkaz z e-mailu, zadejte dvakrát nové heslo a potvrďte tlačítkem **Nastavit heslo**.
4. Přihlaste se novým heslem.

Odkaz platí hodinu a jde použít jednou. Obrazovka odpoví vždy stejně, ať účet existuje, nebo ne – cizí člověk tak nezjistí, kdo v redakci pracuje. E-mail přijde jen účtu, který má vyplněnou adresu a není zablokovaný. Dvoufázové přihlášení obnovou hesla zůstává zapnuté. Když e-mail nedorazí, heslo vám nastaví administrátor v **Správa → Uživatelé**.

## Dvoufázové přihlášení

S dvoufázovým přihlášením zadáváte po heslu ještě šestimístný kód z ověřovací aplikace v telefonu. Kdo heslo uhodne nebo ukradne, bez vašeho telefonu se nepřihlásí.

1. V oddílu **Dvoufázové přihlášení** klepněte na **Zapnout dvoufázové přihlášení**.
2. V ověřovací aplikaci (Google Authenticator, Microsoft Authenticator, 1Password, Aegis…) přidejte nový účet ručním zadáním zobrazeného klíče. Na mobilu stačí klepnout na odkaz pod klíčem – otevře ověřovací aplikaci.
3. Opište kód z aplikace do pole **Kód z aplikace** a klepněte na **Potvrdit a zapnout**.
4. Zobrazí se osm **záložních kódů**. Uložte si je mimo telefon – znovu se už nezobrazí.

Každý záložní kód jde použít jednou, místo kódu z aplikace. Kolik jich zbývá, vidíte v oddílu Dvoufázové přihlášení. Nesouhlasí-li kód při zapínání, zkontrolujte čas v telefonu.

Vypnutí: zadejte **Heslo pro potvrzení** a klepněte na **Vypnout dvoufázové přihlášení**. Ztratíte-li telefon i záložní kódy, vypne vám ho administrátor ve vašem účtu v sekci Uživatelé.

## Přihlašovací klíče (passkeys)

Přihlašovací klíč nahrazuje opisování kódu: druhý krok přihlášení potvrdíte otiskem prstu, Face ID, Windows Hello nebo bezpečnostním klíčem.

Podmínky:

- klíč jde přidat jen k účtu se zapnutým dvoufázovým přihlášením – oddíl **Přihlašovací klíče** se do té doby nezobrazuje,
- web musí běžet na HTTPS a prohlížeč musí klíče podporovat,
- klíč je svázaný s doménou webu. Na jiné adrese nefunguje, takže ho nezíská ani podvržená přihlašovací stránka. Po přestěhování webu na jinou doménu je potřeba klíče přidat znovu.

Přidání klíče:

1. Do pole **Název zařízení** napište, o jaké zařízení jde (například *MacBook* nebo *telefon*).
2. Klepněte na **Přidat klíč z tohoto zařízení** a potvrďte výzvu zařízení.

Přihlášení: po zadání jména a hesla klepněte na **Přihlásit se otiskem prstu nebo klíčem**. Kód z aplikace i záložní kódy fungují dál – pro případ, že zařízení nemáte u sebe.

Tabulka klíčů ukazuje zařízení, datum přidání a poslední použití. Tlačítkem **Smazat** klíč odeberete, třeba po ztrátě zařízení. Vypnutím dvoufázového přihlášení se smažou všechny klíče.

## Ochrana přihlášení

Po 10 chybných pokusech v řadě se účet na 15 minut zamkne. Stejně se počítají chybné kódy druhého kroku a platí i limit 10 pokusů za 15 minut z jedné adresy. Zámek po čtvrthodině pomine sám.

## Tokeny pro napojení na Claude

Oddíl **Napojení na Claude** je vidět jen se zapnutým rozšířením **Napojení na Claude** (hlavní nabídka **Rozšíření**). Token dovolí Claudovi pracovat s webem vaším jménem a s vašimi právy. Nové články zakládá jako koncepty a všechny jeho zásahy jsou v Protokolu změn.

1. Vyplňte **Název nového tokenu** (například *Claude na notebooku*) a klepněte na **Vytvořit token**.
2. Token se zobrazí jen jednou, spolu s návodem k připojení. Zkopírujte si ho hned.

Token funguje bez hesla i bez dvoufázového přihlášení – chraňte ho jako heslo. Nepotřebný token zrušíte tlačítkem **Zrušit token**. Máte-li nějaké tokeny, nabídne formulář změny hesla volbu **zrušit i tokeny napojení (Claude, API)**; při podezření na zneužití ji nechte zaškrtnutou.

## Související

- [Role a oprávnění](role-a-opravneni.md)
- [Předávka a korektura](predavka-a-korektura.md)
- [Bezpečnost](../provoz/bezpecnost.md)
