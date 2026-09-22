# Platby přes Stripe

Se službou Stripe si čtenář zaplatí předplatné kartou sám a web mu ho sám zapne, prodlužuje a po zrušení nechá doběhnout. Nemusíte hlídat výpis z účtu ani nic zapisovat ručně.

Platba probíhá na stránkách Stripe, ne na vašem webu. phpRS nikdy nevidí číslo karty a neukládá ho. Ruční zápis předplatného z [Zamčeného obsahu](zamceny-obsah.md) funguje vedle plateb dál – hodí se pro platby převodem nebo dárkové předplatné.

Potřebujete rozšíření **Čtenáři a zamčený obsah**, účet na [stripe.com](https://stripe.com) a web na adrese `https://`. V **Nastavení → Základní** musí být správně vyplněná **Adresa webu** – skládají se z ní adresy, na které Stripe čtenáře po platbě vrací.

## Jak to funguje

1. Přihlášený čtenář klepne ve svém účtu na **Předplatit měsíčně** nebo **Předplatit ročně**.
2. Web ho přesměruje na platební stránku Stripe (Checkout). Tam zadá kartu a zaplatí.
3. Stripe pošle vašemu webu podepsanou zprávu (webhook), že platba proběhla. Web zprávu ověří a čtenáři zapíše předplatné.
4. Před koncem období Stripe strhne další platbu sám a web předplatné prodlouží.
5. Tlačítkem **Spravovat předplatné** se čtenář dostane do zákaznického portálu Stripe. Tam změní kartu, stáhne si doklady nebo předplatné zruší.

Předplatné se zapisuje jen podle ověřené zprávy ze Stripe, nikdy podle toho, co pošle prohlížeč čtenáře. Čtenář se proto po zaplacení vrátí na web se zprávou, že se předplatné zapne během chvilky – zpráva ze Stripe obvykle dorazí do několika vteřin.

Tlačítko **Získat předplatné** u zamčených článků vede se zapnutými platbami do účtu čtenáře. Nepřihlášený se nejdřív přihlásí nebo zaregistruje (registrace je dál bez hesla, odkazem z e-mailu), tlačítka pro platbu najde hned po přihlášení. Adresa z pole **Kde získat předplatné** se v tu chvíli nepoužívá.

## Nastavení krok za krokem

Všechno nejdřív vyzkoušejte v testovacím režimu Stripe (viz oddíl Zkouška nanečisto níže). Postup je v obou režimech stejný, liší se jen klíče.

### 1. Produkt a ceny

1. Ve Stripe otevřete **Product catalog** a založte produkt, například „Předplatné Magazínu“.
2. Přidejte mu opakovanou cenu (**Recurring**) s obdobím **Monthly**, případně druhou s obdobím **Yearly**. Stačí jedna z nich.
3. U každé ceny zkopírujte její číslo (**Copy price ID**). Začíná `price_`.

Ceny, měnu, DPH i zkušební období spravujete jen ve Stripe. phpRS ceny nezakládá ani nemění – částku účtuje Stripe podle ceny, jejíž číslo zadáte.

### 2. Omezený klíč

V **Developers → API keys** založte omezený klíč (**Create restricted key**). Začíná `rk_`. Dejte mu jen tato práva, všechna ostatní nechte na **None**:

| Oprávnění ve Stripe | Úroveň | K čemu je |
|---|---|---|
| **Checkout Sessions** | Write | založení platby předplatného |
| **Customer portal** | Write | odkaz do správy předplatného |
| **Subscriptions** | Read | načtení stavu po návratu ze správy předplatného |

Web dělá jen tato tři volání. Kdyby Stripe některé odmítl kvůli chybějícímu právu, čtenář uvidí obecnou omluvu a přesný název chybějícího práva najdete v **Nastavení → Stav systému** v záznamu chyb. Použít jde i úplný tajný klíč (`sk_…`), ale omezený je bezpečnější: kdyby unikl, nedá se s ním vracet peníze ani číst údaje zákazníků.

### 3. Webhook

1. V administraci phpRS otevřete **Nastavení → Čtenáři a platby**, oddíl **Platby přes Stripe**, a zkopírujte **Adresu webhooku**. Má tvar `https://vas-web.cz/platba/stripe`.
2. Ve Stripe otevřete **Developers → Webhooks**, zvolte **Add endpoint** a adresu vložte.
3. Zapněte právě tyto události:
   - `checkout.session.completed`
   - `invoice.paid`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
4. Po uložení zkopírujte **Signing secret**. Začíná `whsec_`.

### 4. Zákaznický portál

V **Settings → Billing → Customer portal** portál aktivujte. Povolte v něm aspoň zrušení předplatného a změnu platební metody. Doporučujeme rušit **ke konci období**: čtenář pak dočte, co si zaplatil. Bez aktivovaného portálu tlačítko **Spravovat předplatné** skončí omluvou.

### 5. Vyplnění v phpRS

V **Nastavení → Čtenáři a platby** v oddílu **Platby přes Stripe** vyplňte:

| Pole | Co zadat |
|---|---|
| **Tajný klíč** | omezený klíč `rk_…` (nebo `sk_…`) |
| **Tajemství webhooku** | `whsec_…` z kroku 3 |
| **Měsíční cena**, **Roční cena** | čísla cen `price_…`; prázdné pole = období se nenabízí |
| **Popis měsíční ceny**, **Popis roční ceny** | text u tlačítka, například „99 Kč měsíčně“; nepovinné |

Klíč a tajemství se po uložení už nevypisují – v poli vidíte jen poslední čtyři znaky. Hodnotu, která nemá tvar klíče, tajemství nebo čísla ceny, web neuloží a řekne to. Platby jsou zapnuté, jakmile je vyplněný klíč, tajemství a aspoň jedna cena; štítek u nadpisu oddílu se změní na **zapnuté**. Platby vypnete zaškrtnutím **Odebrat uložený klíč** u pole **Tajný klíč** a uložením.

Popis ceny je jen text. Skutečnou částku určuje cena ve Stripe – když ji tam změníte, upravte i popis.

## Zkouška nanečisto

Stripe má testovací režim s vlastními klíči (`rk_test_…`, `sk_test_…`), vlastními cenami a vlastním webhookem. Nejdřív nastavte všechno v něm:

1. Přepněte Stripe do testovacího režimu a projděte kroky 1–5 s testovacími hodnotami.
2. Zaregistrujte se na webu jako čtenář a klepněte na **Předplatit měsíčně**.
3. Na platební stránce zadejte testovací kartu `4242 4242 4242 4242`, libovolné budoucí datum platnosti a libovolný kód CVC.
4. Po návratu by se v účtu čtenáře mělo do chvíle objevit **předplatné do** s datem a v **Čtenáři → Čtenáři** štítek **Stripe: platí**.
5. Vyzkoušejte **Spravovat předplatné** a zrušení – štítek se změní na **Stripe: neobnoví se**.

Když se předplatné nezapne, podívejte se ve Stripe do **Developers → Webhooks** na doručení zpráv. Odpověď 400 znamená špatné tajemství webhooku (nebo tajemství z jiného režimu), odpověď 404 špatnou adresu. Pro ostrý provoz potom vyměňte klíč, tajemství i čísla cen za ostré – testovací a ostré hodnoty nejde míchat.

## Co se děje dál

| Situace | Co udělá Stripe | Co udělá web |
|---|---|---|
| Řádná platba dalšího období | strhne částku a pošle `invoice.paid` | posune **předplatné do** na konec zaplaceného období a jeden den navíc; platbu zapíše |
| Platba neprojde | zkouší to několik dní znovu a píše čtenáři (podle nastavení ve Stripe) | štítek **Stripe: platba neprošla**; čtenář v účtu vidí výzvu ke kontrole karty; přístup končí dnem **předplatné do** |
| Čtenář předplatné zruší | nechá ho doběhnout do konce období | štítek **Stripe: neobnoví se**; přístup zůstává do konce zaplaceného období |
| Předplatné skončí | pošle `customer.subscription.deleted` | štítek **Stripe: zrušeno**; čtenář si může předplatit znovu |

Datum **předplatné do** web nikdy nezkracuje: platí pozdější z data, které zapsala platba, a data, které jste zapsali ručně. Den navíc pokrývá chvíli mezi koncem období a stržením další platby. Peníze vrácené ve Stripe (refund) přístup samy neodeberou – předplatné čtenáři případně zrušte ručně v **Čtenáři → Čtenáři**.

Web zapisuje jen předplatné, které čtenář založil tlačítkem na webu. Předplatné vytvořené ručně ve Stripe ani jiný prodej přes stejný účet Stripe na přístup čtenářů vliv nemají.

Kdo už přes Stripe platí, tlačítka pro nové předplatné nevidí, takže si nemůže založit druhé vedle prvního. Účet s běžícím předplatným nejde smazat, dokud ho čtenář nezruší.

### Mazání čtenáře

phpRS do Stripe při mazání nevolá. Když v administraci smažete čtenáře s běžícím předplatným (nebo jeho údaje přes **Soukromí a cookies → Žádost čtenáře o osobní údaje**), web vás upozorní, že předplatné ve Stripe běží dál. Zrušte ho ve Stripe u daného zákazníka, jinak se mu budou strhávat další platby.

## Přehled v administraci

- **Čtenáři → Čtenáři** ukazuje u každého čtenáře datum předplatného a pod ním stav ze Stripe (**platí**, **neobnoví se**, **platba neprošla**, **zrušeno**), nebo poznámku **zapsáno ručně**. Dlaždice **Platí přes Stripe** je počet běžících předplatných.
- **Čtenáři → Příjmy** na kartě **Předplatné** doplní počet platících přes Stripe a součet plateb za posledních 30 dní, zvlášť pro každou měnu.

## Co web ukládá

| Kde | Co |
|---|---|
| u čtenáře | číslo zákazníka a předplatného ve Stripe, stav předplatného, datum **předplatné do** |
| tabulka plateb | číslo zprávy ze Stripe, čtenář, částka, měna, datum |

Neukládá se číslo karty, fakturační adresa ani žádný jiný platební údaj – ty zůstávají ve Stripe. Výpis osobních údajů čtenáře obsahuje i jeho platby (datum, částka, měna). Po výmazu čtenáře záznamy o platbách zůstávají kvůli účetnictví, ale bez vazby na osobu. Údaje zákazníka ve Stripe se odsud nemažou; smažte je tam.

Klíč, tajemství webhooku ani platby nejsou součástí [exportu webu](../zaciname/import-z-wordpressu.md). V záloze databáze jsou, stejně jako ostatní nastavení – zálohy proto držte v bezpečí.

## Daně a doklady

phpRS nevystavuje faktury, nepočítá DPH a neřeší evidenci tržeb. Doklady o platbách posílá a zpřístupňuje čtenářům Stripe (nastavíte v **Settings → Billing**), DPH umí počítat Stripe Tax. Za správné zdanění, obchodní podmínky, poučení o automatickém obnovování a vyřizování reklamací odpovídáte jako vydavatel vy. Poplatky si Stripe strhává z každé platby; jejich výši najdete v jeho ceníku.

## Související

- [Zamčený obsah a předplatné](zamceny-obsah.md)
- [Účty čtenářů](ucty-ctenaru.md)
- [Podpora a příjmy](podpora-a-prijmy.md)
- [Měření a soukromí](../seo-a-ai/mereni-a-soukromi.md)
