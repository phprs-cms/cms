# Produktový průchod (2026-09-20)

Čistá instalace (šablona Minimal, prostředí 2026) prošlá očima pěti rolí: vydavatel, šéfredaktor, autor, čtenář, správce serveru.
Cílem nebylo hledat chyby v kódu, ale místa, kde systém nedává smysl jako celek: slepé uličky, zbytečné volby, chybějící návaznosti.
Nálezy jsou seřazené podle dopadu. Rozsah: S = hodiny, M = den, L = víc dní.

## Co funguje dobře (neměnit)

- **Instalace**: jedna obrazovka, pět kroků, kontrola serveru předem, výběr vzhledu s náhledy.
- **První kroky** na přehledu vedou správným směrem a jdou skrýt.
- **Pokročilé volby jsou opravdu schované**: Nastavení → Základní ukazuje 8 polí a 20 drží v pokročilých, článek 13 + 18, uživatel 9 + 16.
- **Výchozí web je štíhlý**: tři bloky, komentáře, hodnocení, sdílení; čtenářské účty, newsletter, push a API jsou vypnuté, dokud je někdo nezapne.
- **Autor má jasnou cestu**: menu jen Články a Média, stav „Koncept / Ke korektuře“ s větou, co se stane dál.
- **Stav systému** je silný: říká, co je špatně, i kde to opravit (limit nahrávání, install.php, HTTPS, 2FA, zálohy mimo server, pošta).

## Nálezy podle dopadu

### 1. Předplatné je slepá ulička pro čtenáře — vysoký dopad, S (mezikrok) / L (platební brána)
Článek „jen pro předplatitele“ nabídne nepřihlášenému *Přihlásit se / Zaregistrovat se*. Po registraci ale čtenář článek stejně
neotevře a **nikde se nedozví, jak, za kolik a komu zaplatit**. Přihlášený čtenář bez předplatného nedostane žádné tlačítko vůbec.
Vydavatel navíc předplatné zapisuje ručně v modulu Čtenáři.
**Mezikrok (S):** nastavení „Jak získat předplatné“ – odkaz na stránku nebo platební odkaz a text tlačítka; výzva u zamčeného článku
i účet čtenáře pak nabídnou *Získat předplatné*. Bez toho je funkce zamčeného obsahu pro předplatitele prakticky nepoužitelná.
**Cíl (L):** platební brána, která předplatné zapisuje sama (návrh 15).

### 2. Redakční předávka nemá žádné upozornění — vysoký dopad u vícečlenné redakce, S
Autor přepne článek na „Ke korektuře“ a **nikdo se to nedozví**, dokud se redaktor nepodívá na přehled. Stejně tak autor nezjistí,
že mu článek vyšel nebo se vrátil. Systém přitom e-maily umí (komentáře, čtenáři).
**Návrh:** e-mail redaktorům (kdo smí vydávat a má článek ve své působnosti) při přechodu na „Ke korektuře“; e-mail autorovi při vydání
a při vrácení do konceptu, s poznámkou pro redakci. Vypínatelné v Můj účet. Na přehledu už fronta práce je – stačí na ni navázat.

### 3. Instalátor a první dojem jen česky — vysoký dopad pro DE/EN, M
Administrace umí čtyři jazyky, web tři, ale **instalátor je jen česky** a neptá se na jazyk webu. Německý nebo anglický uživatel
narazí hned na první obrazovce. Uvítací článek, novinka a rubrika „Aktuality“ vzniknou také česky.
**Návrh:** přepínač jazyka v instalátoru (cs/sk/en/de) → nastaví jazyk webu, jazyk administrace prvního účtu a jazyk ukázkového obsahu.

### 4. Autor rozhoduje o titulní straně — střední dopad, S
Autor bez práva vydávat vidí a smí zaškrtnout *Zobrazit na hlavní stránce* a *Připnout nahoru (otvírák)*. To je rozhodnutí editora.
**Návrh:** obě volby ukázat jen tomu, kdo smí vydávat; u autora zachovat výchozí hodnoty.

### 5. Pole, která jednočlenná redakce nepotřebuje — střední dopad, S
V článku jsou hned vidět *Autor* (výběr z jediné položky), *Poznámka pro redakci* a *Seriál*. Přehled autorovi ukazuje
„Komentáře ke schválení“, ke kterým nemá přístup.
**Návrh:** *Autor* skrýt, když není z čeho vybírat; *Poznámku pro redakci* ukázat, až má web víc než jednoho uživatele;
*Seriál* přesunout do „Další nastavení“; dlaždice přehledu filtrovat podle modulů, na které má uživatel právo.

### 6. Peníze jsou na čtyřech místech — střední dopad, M
Předplatné (Čtenáři), dobrovolná podpora (blok ve vizuálním editoru), reklama (modul) a newsletter (modul) spolu nesouvisejí
a vydavatel nemá jedno místo, kde by viděl „z čeho web žije“. Zapnutí každé části je jinde (Rozšíření, bloky, Nastavení → Základní → pokročilé).
**Návrh:** obrazovka „Příjmy“ ve skupině Čtenáři: čtyři karty (Předplatné, Podpora, Reklama, Newsletter) se stavem zapnuto/vypnuto,
jedním číslem (předplatitelů, odběratelů, zobrazení reklam) a odkazem do nastavení. Žádná nová logika, jen rozcestník.

### 7. Oprávnění jsou na čtyřech místech formuláře — střední dopad, S–M
Role, právo vydávat, ruční moduly, „smí upravovat i články autorů“ a „jen tyto rubriky“ jsou samostatné volby bez shrnutí.
Správce po uložení neví, co uživatel ve výsledku smí.
**Návrh:** nad formulářem jednověté shrnutí složené z voleb („Píše a upravuje vlastní články v rubrice Kultura, nevydává.“)
a tentýž text ve výpisu uživatelů.

### 8. (zrušeno) E-mail z instalace
Původní nález byl chybný: instalátor e-mail redakce nastavuje. Krok 5 Prvních kroků zůstává otevřený záměrně, dokud není
nastavené odesílání pošty (SMTP nebo adresa odesílatele). Beze změny.

### 9. Výchozí menu má 15 položek — nízký dopad, S
Blogger po instalaci vidí i Novinky, Ankety, Přesměrování a Protokol změn. **Návrh:** Ankety a Novinky ve výchozí sadě vypnout
(zapnou se v Rozšíření); Přesměrování a Protokol změn přesunout pod Nastavení jako záložky, nebo je v menu nechat až na konci skupiny Správa.
*Pozn.: mění výchozí chování – jen pro nové instalace.*

### 10. V administraci chybí cesta k nápovědě — nízký dopad teď, vyšší po vydání, S
Nikde není odkaz na dokumentaci ani na hlášení chyb. **Návrh:** položka „Nápověda“ v menu u avataru → `phprs.dev/docs` (v jazyce administrace)
a odkaz „Nahlásit chybu“; u složitějších obrazovek (Pošta, Jazykové verze, Zálohy mimo server) odkaz na konkrétní stránku dokumentace.
Dává smysl až s webem projektu.

### 11. Stav systému neví o úlohách na pozadí a o aktualizacích — nízký dopad, S
Chybí řádky „Úlohy na pozadí: naposledy před X minutami“ (naplánované články, fronta pošty a push na nich závisí) a „Aktualizace: adresa nenastavena / poslední kontrola“.

### 12. Jazykové verze nejsou dotažené všude — nízký dopad, M
Štítky jsou společné pro všechny jazyky (český štítek u anglického článku), texty vlastních bloků a menu se nepřekládají
(řeší se dnes volbou „jen v jazyce“ a druhým blokem). Pro dvojjazyčné weby použitelné, pro tři jazyky pracné.

## Co průchod nepokrýval

Skutečné odesílání pošty, Web Push, Claude API, vzdálené zálohy a aktualizaci z administrace – ty ověří až beta na ostrém hostingu.
Vizuální editor bloků a editor článku byly prověřeny dříve (včetně opravy dialogu Médií).

## Stav úprav

- **1 mezikrok – HOTOVO 2026-09-20:** Nastavení → Základní → Čtenáři a zamčený obsah → „Kde získat předplatné“ (stránka webu nebo https odkaz);
  tlačítko *Získat předplatné* u zamčeného článku i v účtu čtenáře. Platební brána zůstává.
- **2 – HOTOVO:** e-mail těm, kdo smějí vydávat (a nejsou omezeni na jinou rubriku), když článek přejde na „Ke korektuře“; e-mail autorovi
  při vydání a při vrácení do konceptu, v jazyce jeho administrace. Vypíná se v Můj účet (sloupec `rs_user.upozorneni`, migrace 0037).
- **4 – HOTOVO:** volby titulní strany vidí a ukládá jen ten, kdo smí vydávat (vynuceno i na serveru).
- **9 – HOTOVO 2026-09-20 (rozhodnutí uživatele):** Novinky a Ankety jsou u nových instalací vypnuté a zapínají se v Rozšíření; instalátor už nezakládá
  ukázkovou novinku ani blok Novinky. Stávající weby na výchozí sadě si ji migrací 0039 zapíší výslovně, nic jim nezmizí.
- **Retro prostředí administrace odstraněno** (rozhodnutí uživatele 2026-09-20): jeden vzhled, instalace má čtyři kroky místo pěti.
- **5 – HOTOVO zčásti:** výběr autora se skryje, když není z čeho vybírat; poznámka pro redakci jen ve vícečlenné redakci;
  dlaždice „Komentáře ke schválení“ jen s přístupem ke komentářům. Seriál zůstal na místě.

## Doporučené pořadí úprav

1. Rychlé a s velkým účinkem (vše S): **1 mezikrok**, **2**, **4**, **5**.
2. Před betou: **3** (instalátor v jazycích), **11**.
3. Po betě podle ohlasů: **6**, **7**, **9**, **12**; **10** spolu s webem projektu.
