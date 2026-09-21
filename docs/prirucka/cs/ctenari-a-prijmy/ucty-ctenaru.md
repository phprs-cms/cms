# Účty čtenářů

Čtenáři se mohou na webu zaregistrovat. Účet jim dovolí ukládat si články na později, číst zamčené články a – pokud to nastavíte – komentovat. Účty čtenářů jsou oddělené od účtů redakce: čtenář se nikdy nedostane do administrace.

## Zapnutí

1. V hlavní nabídce otevřete **Rozšíření**, zaškrtněte **Čtenáři a zamčený obsah** a uložte.
2. Přidejte na web blok **Účet čtenáře** (**Vzhled → Bloky a rozvržení**). Vypíše tlačítko **Přihlášení / Můj účet**. Bez bloku se čtenář k přihlášení dostane jen z výzvy u zamčeného článku, z odkazu **Uložit na později** pod článkem a přímo na adrese `/ctenar`.
3. Ověřte, že web odesílá e-maily – registrace i přihlášení odkazem na nich stojí. Viz [Pošta](../provoz/posta.md).

V administraci přibude sekce **Čtenáři → Čtenáři** a v Nastavení záložka **Čtenáři a platby**. Obojí vidí jen administrátor.

## Registrace

Čtenář na stránce `/ctenar` v části **Jsem tu poprvé** vyplní **E-mail**, případně **Jméno** (nepovinné), a klepne na **Zaregistrovat se zdarma**. Je-li zapnuté rozšíření Newsletter, může zaškrtnout **Chci dostávat newsletter**.

Heslo se při registraci nezadává. Čtenáři přijde e-mail s odkazem, na kterém si heslo nastaví (aspoň 8 znaků) – tím registraci dokončí a je rovnou přihlášen. Odkaz platí 3 dny. Kdo zaregistruje cizí adresu, k účtu se tak nedostane. Odběr newsletteru zvolený při registraci se potvrdí stejným odkazem.

Pokusí-li se někdo zaregistrovat e-mail, který už účet má, web odpoví stejně jako u nové registrace. Majitel adresy dostane e-mail, že účet už má. Web tak neprozrazuje, které adresy jsou registrované.

Nové registrace zastavíte volbou **Povolit nové registrace** v **Nastavení → Čtenáři a platby**. Stávající čtenáři se přihlašují dál.

## Přihlášení

Čtenář má tři možnosti:

| Způsob | Jak funguje |
|---|---|
| **E-mail a heslo** | běžné přihlášení |
| **Přihlásit se odkazem z e-mailu** | čtenář vyplní jen e-mail a dostane jednorázový odkaz; platí 20 minut |
| **Zapomněli jste heslo?** | pošle odkaz pro nastavení nového hesla; platí 2 hodiny |

Odkaz z e-mailu nepřihlásí hned – ukáže tlačítko **Přihlásit se**. Je to záměr: některé poštovní programy si odkazy otevírají předem a jednorázový odkaz by spotřebovaly.

Po deseti chybných heslech během 15 minut se přihlášení heslem k danému e-mailu dočasně zablokuje. Přihlášení odkazem z e-mailu funguje dál.

Přihlášení drží cookie `phprs_ctenar` po dobu 60 dnů. Je to technická cookie nezbytná pro přihlášení. Přihlášenému čtenáři se stránky neberou z cache.

## Co má čtenář v účtu

Stránka `/ctenar` po přihlášení ukazuje:

- e-mail a případně datum, do kdy platí předplatné (nebo tlačítko **Získat předplatné**),
- se zapnutými [platbami přes Stripe](platby-stripe.md) oddíl **Předplatné**: tlačítka **Předplatit měsíčně** a **Předplatit ročně**, u platícího čtenáře **Spravovat předplatné**,
- **Uložené články** – seznam s možností **Odebrat z uložených**,
- změnu jména a hesla (**Změnit heslo** vyžaduje stávající heslo),
- **Odhlásit se**,
- **Smazat účet** – po zadání hesla smaže účet i všechny údaje o něm; nejde to vrátit. Čtenář s běžícím předplatným přes Stripe ho musí nejdřív zrušit (**Spravovat předplatné**), jinak by se mu platby strhávaly dál.

### Uložené články

Pod každým článkem je odkaz **☆ Uložit na později**. Nepřihlášeného zavede na přihlášení a vrátí zpět na článek. U uloženého článku se tlačítko změní na **★ Uloženo – odebrat** a přibude odkaz **Moje uložené články**. Jeden čtenář si může uložit nejvýše 500 článků.

## Čtenáři v administraci

Sekce **Čtenáři → Čtenáři** ukazuje nahoře tři počty: **Registrovaní**, **Předplatitelé** a **Zamčené články**. Pod nimi je hledání podle e-mailu nebo jména a tabulka posledních 300 účtů:

| Sloupec | Obsah |
|---|---|
| **E-mail** | u nedokončené registrace se štítkem „nepotvrdil e-mail“ |
| **Jméno** | pokud ho čtenář vyplnil |
| **Registrace** | datum registrace |
| **Naposledy** | datum posledního přihlášení |
| **Předplatné** | stav a nabídka **změnit…** – viz [Zamčený obsah](zamceny-obsah.md) |
| **Akce** | **Smazat** – po potvrzení smaže účet |

**Stáhnout CSV** uloží soubor `ctenari.csv` se všemi potvrzenými účty: e-mail, jméno, datum registrace a konec předplatného. Hesla ani uložené články v něm nejsou.

Administrátor heslo čtenáře nevidí a nemůže ho změnit. Čtenář si nové nastaví sám odkazem z e-mailu.

## Komentáře jen pro přihlášené

Volba **Komentovat smí jen přihlášení čtenáři** (**Nastavení → Čtenáři a platby**) omezí diskusi na registrované. Čtenář pak komentuje pod svým účtem. Více na stránce [Komentáře](../redakce/komentare.md).

## Žádost o výpis nebo výmaz údajů

Čtenář si účet smaže sám. Když požádá vás (GDPR), použijte **Nastavení → Soukromí a cookies**, oddíl **Žádost čtenáře o osobní údaje**:

1. Do pole **E-mail čtenáře** zadejte jeho adresu.
2. **Stáhnout jeho údaje** uloží soubor `osobni-udaje.json` s jeho komentáři, odběrem newsletteru a účtem čtenáře.
3. **Smazat jeho údaje** po potvrzení nevratně smaže jeho komentáře, odběr newsletteru i účet čtenáře. Hlášení po smazání vypíše, co bylo odstraněno.

## Související

- [Zamčený obsah](zamceny-obsah.md)
- [Newsletter](newsletter.md)
- [Komentáře](../redakce/komentare.md)
- [Měření a soukromí](../seo-a-ai/mereni-a-soukromi.md)
