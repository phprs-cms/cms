# Instalace

Instalace trvá několik minut a má čtyři kroky na jedné obrazovce.

## 1. Nahrajte soubory

1. Stáhněte si poslední vydání – soubor `phprs-X.Y.Z.zip`.
2. Rozbalte ho a **obsah** nahrajte do složky webu na hostingu. V cílové složce musí být přímo `index.php`, `admin.php`, `install.php` a složky `system/`, `layout/`, `image/`, `media/`, `storage/` – ne další vnořená složka.
3. Zkontrolujte, že se nahrály i **skryté soubory `.htaccess`**: jeden je v kořeni, další ve složkách `system/`, `storage/` a `media/`. Některé FTP programy skryté soubory ve výchozím nastavení nezobrazují ani nepřenášejí. Bez nich by byly z internetu čitelné soubory, které veřejné být nemají.

Na serveru s nginx soubory `.htaccess` neplatí – nastavte pravidla podle kapitoly [Provoz → nginx](../provoz/nginx.md).

## 2. Otevřete instalátor

V prohlížeči otevřete adresu `https://vas-web.cz/install.php`. Instalátor se zobrazí v jazyce vašeho prohlížeče; vpravo nahoře ho přepnete na češtinu, slovenštinu, angličtinu nebo němčinu.
Zvolený jazyk se zároveň nastaví jako jazyk webu, jazyk administrace vašeho účtu a jazyk ukázkového obsahu.

### Krok 1 – Kontrola serveru

Instalátor ověří verzi PHP, povinná rozšíření a právo zápisu do kořenové složky a do `storage/`. Položky označené křížkem je potřeba opravit na hostingu; potom stránku obnovte.

### Krok 2 – Databáze

Vyplňte údaje k prázdné databázi. **Server** bývá `localhost`, ale řada hostingů používá vlastní adresu – najdete ji v administraci hostingu u databáze.
**Předponu tabulek** (`rs_`) měňte jen tehdy, když ve stejné databázi poběží víc instalací.

### Krok 3 – Web a administrátor

- **Název webu** – zobrazuje se v záhlaví a ve vyhledávačích; později ho změníte v Nastavení.
- **Přihlašovací jméno** a **heslo** prvního účtu. Heslo musí mít aspoň 10 znaků; zvolte dlouhé a jedinečné, tento účet smí na webu všechno.
- **Jméno a příjmení** – zobrazuje se u článků.
- **E-mail** – stane se e-mailem redakce: chodí na něj upozornění systému.
- **Časové pásmo** – podle něj se vydávají naplánované články a zobrazují data.
- **Nahrát ukázkový obsah** – nepovinné. Místo jediného uvítacího článku dostane web pět rubrik, deset článků a obrázky smyšleného deníku v jazyce instalace (slovenská instalace česky), takže hned vidíte, jak šablona vypadá s obsahem. Všechno je vymyšlené a volně použitelné. Ukázku později smažete jedním kliknutím v **Nastavení → Základní → Ukázkový obsah**; tamtéž ji jde nahrát i dodatečně.

### Krok 4 – Šablona webu

Vyberte jednu ze tří vestavěných šablon: Classic Newspaper (deník), Modern Magazine (výrazný magazín) nebo Minimal (blog, osobní magazín). Kdykoli později ji změníte ve **Vzhled → Identita webu**, o obsah nepřijdete.

Tlačítko **Nainstalovat phpRS 3** vytvoří tabulky v databázi a soubor `config.php` s přístupem k databázi.

## 3. Po instalaci

1. Instalátor se po dokončení **smaže sám**. Když to práva na serveru nedovolí, řekne to a soubor `install.php` smažte ručně – dokud tam je, připomíná to Stav systému.
2. Přihlaste se na adrese `https://vas-web.cz/admin.php`.
3. Zapněte si **dvoufázové přihlášení**: klepněte na svůj avatar vpravo nahoře → **Můj účet**.
4. Pokračujte kapitolou [První kroky](prvni-kroky.md).

## Když se instalace nepovede

- **„K databázi se nepodařilo připojit“** – nejčastěji špatná adresa serveru nebo heslo. Údaje zkopírujte z administrace hostingu, nepřepisujte je ručně.
- **„V databázi už tabulky s touto předponou existují“** – databáze není prázdná. Zvolte jinou předponu, nebo staré tabulky odstraňte.
- **„Tabulky jsou vytvořeny, ale nepodařilo se zapsat config.php“** – kořenová složka webu není zapisovatelná. Upravte práva a instalaci spusťte znovu s jinou předponou tabulek, případně předtím tabulky smažte.
- **Bílá stránka nebo chyba 500** – hosting nejspíš běží na starší verzi PHP. Přepněte ji na 8.4 nebo novější.
