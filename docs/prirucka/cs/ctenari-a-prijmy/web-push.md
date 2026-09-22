# Oznámení v prohlížeči (Web Push)

Čtenář si jedním klepnutím zapne oznámení a jeho prohlížeč ho pak upozorní na každý nový článek – i když váš web zrovna otevřený nemá. Funguje to bez cizí služby, bez registrace a bez osobních údajů.

## Co je potřeba

| Požadavek | Proč |
|---|---|
| Web na **HTTPS** | Prohlížeče povolují oznámení jen zabezpečeným webům. |
| PHP rozšíření **openssl** a **curl** | Oznámení se podepisují klíčem webu a posílají službám prohlížečů. Bez nich se funkce sama vypne a blok se nezobrazí. |
| Zapnuté rozšíření **Oznámení v prohlížeči** | **Správa → Rozšíření**. Ve výchozím stavu je vypnuté. |
| Blok **Oznámení** na webu | Tlačítko, kterým čtenář oznámení zapne. |

Žádné klíče nezadáváte. Pár klíčů pro podepisování (VAPID) si web vytvoří sám při prvním použití.

## Zapnutí

1. Otevřete **Správa → Rozšíření**, zaškrtněte **Oznámení v prohlížeči** a uložte.
2. Otevřete **Vzhled → Bloky a rozvržení** a do vhodné zóny přidejte blok **Oznámení** ze skupiny **Čtenáři a redakce**.
3. V nastavení bloku můžete změnit nadpis. Výchozí text výzvy je **Dáme vám vědět, když vyjde nový článek.**
4. Otevřete web v běžném okně prohlížeče a oznámení si sami zapněte. Po vydání dalšího článku ověříte, že chodí.

## Jak to vidí čtenář

Blok obsahuje krátký text a tlačítko **Zapnout oznámení**.

1. Čtenář klepne na tlačítko. Prohlížeč se zeptá, zda webu oznámení povolí.
2. Po povolení blok ukáže **Oznámení jsou v tomto prohlížeči zapnutá.** a tlačítko se změní na **Vypnout oznámení**.
3. Vypnout je jde kdykoli stejným tlačítkem, nebo v nastavení prohlížeče.

Další chování:

- V prohlížeči, který oznámení nepodporuje, zůstane blok skrytý.
- Když čtenář oznámení v prohlížeči zakázal, blok mu poradí: **Oznámení máte pro tento web v prohlížeči zakázaná. Povolíte je v nastavení webu u adresního řádku.**
- Zapnutí platí pro jeden prohlížeč na jednom zařízení. Na telefonu a v počítači si je čtenář zapíná zvlášť.
- Na iPhonu a iPadu fungují oznámení webu jen tehdy, když si čtenář web přidá na plochu. Je to omezení systému, ne phpRS.

## Co se posílá a kdy

Oznámení odchází samo po vydání článku – hned při vydání i ve chvíli, kdy vyjde naplánovaný článek. Obsahuje:

- titulek článku,
- začátek perexu (nejvýše 160 znaků),
- hlavní obrázek článku, pokud ho má,
- ikonu webu,
- odkaz na článek; klepnutí na oznámení ho otevře.

Pravidla:

- Každý článek se oznamuje jednou. Pozdější úprava vydaného článku nové oznámení nepošle.
- Neoznamují se články s volbou vyřazení z vyhledávačů (noindex) a články s datem vydání starším než dva dny – po výpadku se tak nerozešle celý archiv.
- Zamčené články se oznamují také; čtenář bez přístupu uvidí po otevření ukázku a výzvu.
- Oznámení nelze napsat ručně ani poslat zprávu bez článku.
- Na vícejazyčném webu dostávají všichni odběratelé oznámení o článcích ze všech jazykových verzí.
- Nové oznámení nahradí na zařízení čtenáře to předchozí, pokud ho ještě neodklikl. Nehromadí se.

Rozesílá se po dávkách 300 odběrů v rámci [úloh na pozadí](../provoz/ulohy-na-pozadi.md). U webu s tisíci odběrateli trvá rozeslání několik spuštění; s nastaveným cronem je plynulejší.

## Soukromí

Web si o odběru ukládá jen technickou adresu, kterou mu přidělila služba prohlížeče (Google, Mozilla, Microsoft, Apple). Neukládá jméno, e-mail ani IP adresu a odběr nespojuje s účtem čtenáře. Oznámení lze poslat jen na adresy těchto čtyř služeb.

Samotná zpráva odchází bez obsahu. Prohlížeč čtenáře si po probuzení stáhne titulek a adresu posledního oznámení z vašeho webu. Služby prohlížečů tak nevidí, o čem píšete.

Odběr, který zanikl – čtenář oznámení zrušil nebo prohlížeč odinstaloval – se při další rozesílce sám smaže. Seznam odběrů v administraci není a počet odběratelů se nezobrazuje.

## Když oznámení nechodí

- Zkontrolujte, že web běží na HTTPS a rozšíření je zapnuté.
- Blok se nezobrazuje vůbec: na serveru chybí `openssl` nebo `curl`, nebo prohlížeč oznámení nepodporuje.
- Oznámení chodí se zpožděním: úlohy na pozadí se spouštějí jen při návštěvách webu. Nastavte cron.
- Server musí mít povolená odchozí spojení HTTPS na služby prohlížečů. Některé hostingy je blokují.

## Související

- [Bloky a rozvržení](../vzhled/bloky-a-rozvrzeni.md)
- [Úlohy na pozadí](../provoz/ulohy-na-pozadi.md)
- [Newsletter](newsletter.md)
- [Plánování a revize](../psani/planovani-a-revize.md)
