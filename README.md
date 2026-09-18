# phpRS 3

Redakční systém pro internetové časopisy a magazíny. Duchovní nástupce českého
[phpRS](https://phprs.net/) (Jiří Lukáš, 2001–2007; komunitní verze do 2.8.3a) napsaný
od nuly pro PHP 8.4+ a MySQL 8 / MariaDB 10.6+.

Co zůstává z originálu: jednoduchost, zaměření na články a rubriky, administrace
`admin.php?modul=clanky&akce=edit`, tabulky `rs_*` s českými názvy sloupců, web složený ze
sloupců a bloků, šablony článků se třemi režimy (náhled / krátký / celý), autoři s právem
vydávat a vazbami nadřízený–podřízený. A vzhled administrace – modré menu, šedá tlačítka, Verdana.

Co je nové: PDO a připravené dotazy všude, `password_hash`, CSRF ochrana, InnoDB s cizími
klíči, utf8mb4, hezké adresy (`/clanek/titulek`), responzivní administrace i web, žádné globální proměnné.

## Instalace

1. Nahrajte obsah složky na hosting (FTP stačí, Composer ani příkazová řádka nejsou potřeba).
2. Založte prázdnou databázi.
3. Otevřete `https://vas-web.cz/install.php` a vyplňte formulář.
4. Smažte `install.php`.

Apache používá přiložený `.htaccess`. Pro nginx: vše, co není soubor, směrujte na `index.php`
a zakažte přístup do `system/`, `storage/` a ke `config.php`.

## Vývoj

```bash
php -S localhost:8080 system/dev-router.php
```

## Struktura

```
index.php, admin.php, install.php   vstupní body (view.php a search.php jen přesměrují staré adresy phpRS 2)
config.php                          vytvoří instalátor
image/                              CSS, JS a logo administrace
layout/<název>/                     vzhled webu: base.php, blok.php, cla_*.php, style.css
plugins/                            plug-iny (připravuje se)
storage/                            logy a cache, z webu nepřístupné
system/src/Core/                    jádro: App, Db, Request, Response, Session, View, Auth, Settings
system/src/Admin/Moduly/            moduly administrace - jeden modul = jedna třída
system/src/Front/                   veřejná část webu
system/views/                       šablony administrace, instalátoru a výchozí šablony webu
system/sql/schema.sql               struktura databáze
```

Nový modul administrace: třída v `system/src/Admin/Moduly/` dědící z `Modul` (konstanty `IDENT`,
`NAZEV`, metody `akceVypis()`, `akceEdit()`…), šablony ve `system/views/admin/<ident>/` a zápis do
`Kernel::MODULY`.

Vlastní layout: zkopírujte `layout/default/` pod jiným názvem a vyberte ho v Konfiguraci. Layout může
přepsat i kteroukoli šablonu ze `system/views/front/` (výpis, systémové bloky, RSS).

## Stav

Hotovo (milník 1): instalátor, přihlášení, Editace autorů (práva, vazby), Editace článků, Editace
novinek, Úprava bloků, Úprava rubrik, Konfigurace; web: hlavní stránka, článek, rubrika, vyhledávání,
RSS, přesměrování starých adres phpRS 2.

Plán:

- **M2 – obsah:** nahrávání obrázků a Galerie obrázků (značka `<obrazek id="…">`), WYSIWYG editor,
  skupiny souvisejících článků, Redaktor (vydavatelská nástěnka), Stránkové aliasy.
- **M3 – čtenáři:** Komentáře s moderací a antispamem, Ankety, hodnocení článků, Statistika.
- **M4 – import z phpRS 2.x:** převod `rs_*` tabulek včetně kódování win-1250 / ISO-8859-2 → UTF-8,
  zachování `link` (staré adresy fungují dál) a hesel (přehashování při prvním přihlášení).
- **M5 – rozšíření:** plug-iny (položka menu + systémový blok), Reklamní systém, Download, Weblinky,
  levely a registrace čtenářů, Záloha DB, Správa modulů, slovenština.

## Licence

GNU GPL verze 2 nebo novější – stejně jako původní phpRS. Text licence je v souboru `LICENSE`.
