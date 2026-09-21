# Projektstruktur

phpRS ist in reinem PHP 8.4+ über MySQL oder MariaDB geschrieben. Es hat kein Framework, keinen Composer und keinen Build-Schritt. Die Seiten stellt der Server zusammen, im Browser läuft nur eine kleine Menge gewöhnliches JavaScript. Diese Seite ist eine Landkarte für alle, die den Code lesen, debuggen oder dazu beitragen möchten.

## Warum ohne Framework, Composer und npm

Das ist Absicht, keine Altlast:

- **Installation per FTP.** Das System soll auf einem gewöhnlichen Shared Hosting laufen. Sie laden die Dateien hoch, öffnen `install.php` und fertig. Eine Kommandozeile ist nicht nötig.
- **Lesbarkeit.** Den Code soll auch ein kundiger Laie lesen können, der seine Website anpassen möchte. Kein Dependency-Container, kein ORM, kein generierter Code – was geschieht, ist in einer Datei zu sehen.
- **Sicherheit und Wartung.** Zur Laufzeit wird keine Bibliothek Dritter verwendet. Es gibt nichts, was man wegen Schwachstellen in Abhängigkeiten überwachen müsste, und eine Aktualisierung ist ein einziges signiertes Paket.
- **Kein Build.** CSS und JavaScript werden so geschrieben, wie sie an den Browser gesendet werden.

Eine neue Abhängigkeit braucht einen starken Grund. Dinge, für die man anderswo eine Bibliothek nimmt (TOTP, WebAuthn, Web Push, Signatur für S3, SMTP), sind hier in einigen Hundert Zeilen geschrieben und durch Tests abgedeckt.

## Einstiegspunkte

| Datei | Wozu sie dient |
|---|---|
| `index.php` | öffentliche Website; übergibt die Steuerung an `Front\Kernel` |
| `admin.php` | Administration; sendet Sicherheits-Header einschließlich Content-Security-Policy und übergibt die Steuerung an `Admin\Kernel` |
| `install.php` | Installationsprogramm; löscht sich nach Abschluss selbst (in einer Entwicklungskopie mit dem Ordner `.git` nicht) |
| `sw.js` | Service Worker für Web-Push-Benachrichtigungen |
| `system/bootstrap.php` | Konstanten `PHPRS_VERSION` und `PHPRS_VERZE_DB`, PSR-4-Autoloader für den Namensraum `PhpRS\`, Hilfsfunktionen |
| `system/dev-router.php` | Router für den eingebauten PHP-Server bei der Entwicklung |

Sprechende Adressen auf Apache stellt `.htaccess` sicher. Für nginx gibt es in `system/nginx.priklad.conf` ein fertiges Beispiel – siehe [nginx](../provoz/nginx.md).

## Ordner

| Ordner | Inhalt |
|---|---|
| `system/src/Core/` | Kern: `App`, `Db`, `Request`, `Response`, `Session`, `View`, `Auth`, `Settings` und Dienste (E-Mail, Bilder, Sicherungen, Aktualisierungen, Signaturen, Web Push, KI-Assistent, Sprachen, Erweiterungen…) |
| `system/src/Admin/` | `Kernel` der Administration, Basisklasse `Modul`, Benutzerkonto, Zurücksetzen des Passworts, Änderungsprotokoll |
| `system/src/Admin/Moduly/` | Module der Administration – ein Modul ist eine Klasse |
| `system/src/Front/` | öffentliche Website: Routing (`Kernel`), Artikel, Blöcke, SEO, Leser, Newsletter, Werbung, Cache, Statistik, API |
| `system/src/Mcp/` | MCP-Server für die Verbindung mit Claude: `Server` und `Nastroje` |
| `system/src/Install/` | Installationsprogramm |
| `system/views/admin/` | Vorlagen der Administration; Ordner nach dem Bezeichner des Moduls |
| `system/views/front/` | Standardvorlagen der Website, die eine Design-Vorlage überschreiben darf |
| `system/views/install/` | Vorlagen des Installationsprogramms |
| `system/jazyky/` | Wörterbücher: Website (`en.php`, `sk.php`, `de.php`), Administration (`admin-*.php`), Installationsprogramm (`install-*.php`) |
| `system/sql/` | `schema.sql` für neue Installationen und `migrace/NNNN-popis.sql` für bestehende |
| `system/demo/` | Beispielinhalte auf Tschechisch, Englisch und Deutsch |
| `layout/` | Design-Vorlagen der Website; eingebaut sind `classic-newspaper`, `modern-magazine`, `minimal` |
| `image/` | CSS und JavaScript der Administration, des Editors, des visuellen Blockeditors sowie die gemeinsamen `web.css` und `web.js` für die Website; die Schrift der Administration in `image/pisma/` |
| `media/` | hochgeladene Dateien, in Ordner `RRRR/MM/` einsortiert |
| `storage/` | Cache, Logs, Sicherungen; von der Website aus nicht zugänglich |
| `tools/` | Tests, Werkzeug für die Wörterbücher, Skript für das Release |
| `docs/` | dieses Handbuch (`docs/prirucka/`) und der Ablauf eines Release |

`config.php` mit dem Zugang zur Datenbank erstellt das Installationsprogramm. Ins Repository gehört sie nicht; das Muster ist `config.sample.php`.

## Wie eine Anfrage durchläuft

**Website.** `Front\Kernel` setzt die Adresse der Website und die Zeitzone, führt gegebenenfalls ausstehende Migrationen aus, erkennt die Sprachversion am Präfix der Adresse (`/en/…`) und wählt die Design-Vorlage. Dann versucht er den Seiten-Cache, und wenn das nicht gelingt, ruft er je nach Pfad die zuständige Behandlung auf (Artikel, Ressort, Seite, Feeds, Leserkonto…). Das Ergebnis umschließt `base.php` der Vorlage. Eine Vorlagendatei wird zuerst im Ordner der Design-Vorlage gesucht, danach in `system/views/front/`.

**Administration.** `Admin\Kernel` prüft die Anmeldung und bei jeder POST-Anfrage das CSRF-Token. Nach dem Parameter `modul` findet er die Klasse in der Liste `Kernel::MODULY`, prüft Erweiterung und Berechtigung und ruft die Methode `akce<Name>()` auf – `?modul=clanky&akce=edit` führt zu `Moduly\Clanky::akceEdit()`.

### Neues Modul der Administration

1. Eine Klasse in `system/src/Admin/Moduly/`, die von `Modul` erbt. Konstanten `IDENT`, `NAZEV`, `SKUPINA` (Gruppe im Menü), `IKONA`; bei Bedarf `ROZSIRENI` (Schlüssel der Erweiterung) und `JEN_ADMIN`.
2. Die Methoden `akceVypis()`, `akceEdit()`, `akceUloz()`… geben eine `Response` zurück.
3. Vorlagen in `system/views/admin/<ident>/`.
4. Eintrag der Klasse in `Admin\Kernel::MODULY`.

## Benennung

Die Domäne des Systems ist tschechisch und der Code spiegelt das wider:

- **Tschechisch ohne diakritische Zeichen:** Tabellen und Spalten (`rs_clanky.titulek`, `rs_topic`), Methoden der Module (`akceUloz`), Variablen der Domäne (`$clanek`, `$rubrika`), Vorlagen (`vypis.php`, `formular.php`), Schlüssel der Einstellungen (`nazev_webu`).
- **Tschechisch mit diakritischen Zeichen:** Kommentare, Texte für Benutzer, Commit-Nachrichten.
- **Englisch:** die API des Kerns in `Core/` (`Request::post()`, `Db::all()`, `Settings::get()`).

Tabellen haben ein Präfix (Standard `rs_`). In Abfragen schreibt man `{clanky}` und das Präfix ergänzt `Db`.

Texte für Benutzer werden mit der Funktion `t('Česky')` umschlossen. Schlüssel des Wörterbuchs ist der tschechische Text; was im Wörterbuch fehlt, wird tschechisch angezeigt.

## Start bei der Entwicklung

```
php -S localhost:8080 system/dev-router.php
```

Sie brauchen PHP 8.4+ mit den Erweiterungen `pdo_mysql`, `mbstring` und `gd` sowie ein laufendes MySQL oder MariaDB. Eine saubere Installation lösen Sie aus, indem Sie `config.php` löschen, die Tabellen `rs_*` entfernen und `/install.php` öffnen.

## Siehe auch

- [Grundsätze des Projekts](zasady.md)
- [Tests und Releases](testy-a-vydavani.md)
- [Eigene Vorlage](../vzhled/vlastni-sablona.md)
- [Anforderungen an das Hosting](../zaciname/pozadavky.md)
