# Role a oprávnění

Každý člen redakce má vlastní účet. Účty zakládá a spravuje administrátor v **Správa → Uživatelé**. Co kdo v administraci vidí a smí, určuje jeho role a několik doplňujících voleb.

## Tři role

| Role | Co dělá |
|---|---|
| **Autor** | Píše a upravuje vlastní články. Vydává je redaktor. |
| **Redaktor** | Upravuje a vydává články všech, spravuje rubriky, komentáře a další obsah. |
| **Administrátor** | Všechno včetně uživatelů, vzhledu a nastavení webu. |

## Co která role vidí

Hlavní nabídka ukazuje jen sekce, do kterých má uživatel přístup. **Přehled**, **Můj účet** a **Média** mají všichni.

| Sekce | Autor | Redaktor | Administrátor |
|---|---|---|---|
| Články | ano | ano | ano |
| Média | ano | ano | ano |
| Rubriky, Štítky a témata, Stránky | – | ano | ano |
| Komentáře, Statistika a další zapnutá obsahová rozšíření | – | ano | ano |
| Bloky a rozvržení | – | ano | ano |
| Identita webu, Uživatelé, Přesměrování, Protokol změn, Rozšíření, Nastavení, Čtenáři | – | – | ano |

Sekce, která patří k vypnutému rozšíření, se neukazuje nikomu.

V **Článcích** autor vidí jen své články; redaktor a administrátor vidí všechny. Totéž platí pro redakční kalendář, přehled i hledání článku v paletě příkazů. V **Médiích** vidí všichni všechno, ale popis změnit a soubor smazat smí jen ten, kdo ho nahrál, a administrátor.

## Právo vydávat

Redaktor a administrátor vydávají vždy. Autor vydávat nesmí, pokud mu administrátor nezaškrtne volbu **smí své články sám vydávat**.

Kdo právo vydávat nemá:

- má v poli **Stav** jen **Koncept – rozepsaný** a **Ke korektuře – hotovo, prosím o kontrolu**,
- nemůže měnit ani mazat už vydaný článek,
- nerozhoduje o hlavní stránce (volby **Zobrazit na hlavní stránce** a **Připnout nahoru (otvírák)** nevidí) a nemá obrazovku **Titulní strana**.

Jak článek od autora doputuje k vydání, popisuje [Předávka a korektura](predavka-a-korektura.md).

## Nový uživatel

1. **Správa → Uživatelé → Nový uživatel.**
2. Vyplňte **Jméno a příjmení** (zobrazuje se u článků), **Přihlašovací jméno** a **E-mail**. Přihlašovací jméno má 2–40 znaků: písmena bez diakritiky, číslice, tečka, pomlčka a podtržítko. Bez e-mailu uživateli nechodí upozornění a neobnoví si zapomenuté heslo.
3. Zadejte **Heslo** o nejméně 10 znacích. Uživatel si ho pak změní v nabídce **Můj účet**.
4. Zvolte **Roli** a klepněte na **Přidat uživatele**.

## Podrobné nastavení

Rozbalovací oddíl **Podrobné nastavení** ve formuláři uživatele upřesňuje, co role dovoluje.

### Přístup do sekcí

Ve výchozím stavu plyne přístup z role. Zaškrtnutím **nastavit ručně (jinak podle role)** vyberete sekce jednotlivě – autorovi tak můžete přidat třeba Komentáře, redaktorovi ubrat Bloky a rozvržení. Sekce vyhrazené administrátorovi v seznamu nejsou a přidat je nelze.

### Jen tyto rubriky

Nic nezaškrtnuto znamená, že uživatel smí psát do všech rubrik. Se zaškrtnutím vidí a upravuje jen články z vybraných rubrik. Omezení se dědí na podrubriky, a to i na ty, které vzniknou později. Do jiné rubriky článek neuloží ani hromadně nepřesune. Administrátora omezit nejde.

### Úprava cizích článků

Volba **Smí upravovat i články autorů** je určená pro roli Autor – například pro vedoucího rubriky. Zaškrtnutím kolegů mu zpřístupníte jejich články: uvidí je ve výpisu, může je upravovat a v poli **Autor** mezi těmito autory volit. Právo vydávat tím nezískává.

### Zablokování účtu

**Zablokovat účet → uživatel se nepřihlásí.** Zablokovaný uživatel je okamžitě odhlášen i z rozdělané práce. Jeho články zůstávají podepsané jeho jménem. Hodí se pro kolegu, který z redakce odešel. Ve výpisu uživatelů má zablokovaný účet poznámku *blokován*.

Naproti tomu **Smazat** účet odstraní; články zůstanou zachované, ale bez autora.

Vlastní účet administrátor nemůže zablokovat, smazat ani zbavit role administrátora.

### Dvoufázové přihlášení

Má-li uživatel zapnuté dvoufázové přihlášení, je u něj ve výpisu značka **2FA**. Ztratí-li telefon i záložní kódy, administrátor mu ho vypne volbou **vypnout (uživatel ztratil telefon i záložní kódy)**. Smažou se tím i jeho přihlašovací klíče. Podrobnosti jsou na stránce [Účet a přihlášení](ucet-a-prihlaseni.md).

## Výpis uživatelů

Tabulka ukazuje přihlašovací jméno, jméno, e-mail, roli, sloupec **Vydává** (Ano/Ne), počet článků a poslední přihlášení.

## Doporučení

- Každému člověku vlastní účet. Sdílený účet znemožní dohledat, kdo co změnil.
- Dávejte nejmenší potřebná práva a administrátorů mějte co nejméně.
- Další zásady shrnuje stránka [Bezpečnost](../provoz/bezpecnost.md).
