# Tests und Releases

Das Projekt hat zwei Sätze von Tests, eine laufende Prüfung auf GitHub und signierte Releases. Diese Seite sagt, was Sie vor dem Einreichen einer Änderung ausführen und wie eine fertige Änderung zu den Benutzern gelangt.

## Unit-Tests

```
php tools/testy.php
```

Die Tests laufen ohne Framework und ohne Datenbank, in einem Augenblick. Sie überwachen Logik, die ein Durchgang durch die Website nicht erkennt: Kryptografie, Parsen und Textumwandlungen. Dazu gehören zum Beispiel:

- Textumwandlungen, Suche, Zerlegen von SQL-Migrationen in Befehle,
- TOTP, Passkeys (WebAuthn) gegen einen Software-Authenticator, Signatur für Web Push, Signatur für Sicherungen nach S3,
- die erlaubte PHP-Schreibweise in Vorlagen (`Core\SablonaKontrola`) einschließlich dessen, dass alle eingebauten Vorlagen sie bestehen,
- Übersetzung eines Artikels durch den Assistenten: das HTML-Gerüst bleibt aus dem Original,
- Prüfung der Signaturen von Releases: mehrere Schlüssel, Wechsel und Widerruf eines Schlüssels,
- Vollständigkeit der Wörterbücher des Installationsprogramms und der Beispielinhalte in allen Sprachen,
- Übereinstimmung der Adresstabelle in `Core\Napoveda` mit `docs/prirucka/osnova.json`,
- statische Prüfungen: Die Administration enthält weder Inline-Skripte noch Event-Handler; kein Skript sucht ein Element `[data-…]`, das nirgends entsteht.

Ein neuer Test ist ein weiterer Aufruf von `over('popis', $skutecne, $ocekavane)`. Fügen Sie zu jeder Änderung an Kryptografie und Parsen einen Test einschließlich eines negativen Falls hinzu – einer Eingabe, die nicht durchgehen darf.

## Smoke-Test

```
tools/test.sh
```

Er braucht ein laufendes MySQL oder MariaDB und die Befehle `mysql` und `curl`. Ablauf des Tests:

1. er prüft die Syntax aller PHP-Dateien,
2. er stellt sicher, dass `PHPRS_VERZE_DB` der Nummer der letzten Migration entspricht,
3. er führt die Unit-Tests aus,
4. er kopiert das Projekt in einen temporären Ordner, legt eine saubere Datenbank an und führt die Installation durch; er prüft, dass sich das Installationsprogramm danach selbst gelöscht hat,
5. er geht die Website durch: Startseite, Artikel, Ressort, Suche, Feeds, Sitemaps, `robots.txt`, `llms.txt`, die bereinigte Version eines Artikels, die Seite 404 und dass `system/` und `config.php` von der Website aus nicht zugänglich sind,
6. er probiert alle drei eingebauten Vorlagen und die Ersatzvorlage bei fehlendem Ordner aus,
7. er meldet sich in der Administration an und geht ihre Bildschirme durch, einschließlich der Berechtigungen nach Ressort und der Bearbeitung eines Artikels direkt auf der Website.

Eine Antwort gilt auch dann als fehlerhaft, wenn sie den Text `Warning:`, `Notice:`, `Deprecated:` oder `Fatal error` enthält.

Die Verbindung zur Datenbank wird aus Umgebungsvariablen genommen:

| Variable | Standard |
|---|---|
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_NAME` | `phprs3_test` |
| `DB_USER` | `root` |
| `DB_PASS` | leer |
| `PORT` | `8099` – Port des temporären Webservers |

> Die Datenbank `DB_NAME` wird vom Test **gelöscht und neu angelegt**. Geben Sie nie eine Datenbank an, an der Ihnen etwas liegt.

Nach einer Änderung an Vorlagen, Dialogen des Editors oder Stilen genügen die Tests nicht. Gehen Sie die betroffenen Seiten im Browser durch – im hellen und im dunklen Modus und in der Breite eines Telefons.

## Prüfung auf GitHub

Der Workflow **Kontrola** läuft bei jeder Änderung und einmal pro Woche auch ohne Änderungen:

- Smoke-Test auf PHP 8.4 und 8.5,
- Semgrep mit Sicherheitsregeln für PHP und JavaScript,
- Gitleaks – im Repository dürfen weder Schlüssel noch Passwörter liegen,
- eine Prüfung, dass der private Schlüssel des Herausgebers nicht in git liegt.

Dependabot beobachtet nur GitHub Actions. Andere Abhängigkeiten hat das Projekt nicht.

## Tägliche Sicherheitsprüfung

Der Workflow **Denní kontrola** läuft jede Nacht. Er veröffentlicht und signiert nichts; er weist nur rechtzeitig darauf hin, dass gehandelt werden muss:

| Prüfung | Was sie aufdeckt |
|---|---|
| Aktualisierungskanal | Das Aktualisierungsmanifest auf der Website des Projekts ist nicht mit dem Schlüssel des Herausgebers signiert, das Paket entspricht nicht dem Hash oder trägt einen fremden öffentlichen Schlüssel – also eine Fälschung oder Beschädigung dessen, was die Installationen herunterladen. |
| Tests auf den unterstützten PHP-Versionen und auf der kommenden Version | Eine Änderung in PHP, die das System kaputt macht, bevor sie bei den Hostings ankommt. Die kommende Version darf fehlschlagen. |
| Statische Analyse mit täglich frischen Regeln | Neu beschriebene verwundbare Muster im Code. Die Funde sehen nur die Verwalter des Repositorys. |
| Website des Projekts und Demo von außen | Fehlende Sicherheits-Header, zugängliche `config.php`, `system/`, `storage/` oder `.git/`. |

Wenn etwas fehlschlägt, wird im Repository ein Issue mit einem Link zum Lauf der Prüfung angelegt.

## Wie ein Release entsteht

Die Versionsnummer ist die Konstante `PHPRS_VERSION` in `system/bootstrap.php`. Ein Release stellt das Skript `tools/vydani.php` zusammen. Es erzeugt ein ZIP-Paket und ein Aktualisierungsmanifest, das die Versionsnummer, den Hash des Pakets, die Beschreibung der Änderungen und das Kennzeichen trägt, ob es sich um ein gewöhnliches oder ein Sicherheits-Release handelt.

Was signiert ist:

- eine Zeichenkette aus Version, SHA-256-Hash des Pakets und Art des Release. Das Kennzeichen des Sicherheits-Release ist also von der Signatur gedeckt – wer nur die Website mit dem Manifest beherrschte, kann ein gewöhnliches Release nicht zum Sicherheits-Release erklären und seine automatische Installation erzwingen,
- die Liste der Kerndateien `system/soubory.json`. Nach ihr meldet der **Systemstatus** geänderte, fehlende und hinzugefügte Dateien, und die Aktualisierung räumt Dateien auf, die das neue Release nicht mehr enthält.

Die Signaturen sind Ed25519 und werden nur von `Core\Podpis` geprüft. Die öffentlichen Schlüssel stehen in `system/aktualizace.pub`, einer pro Zeile. Es gilt die Signatur mit einem beliebigen von ihnen; dadurch lässt sich der Betriebsschlüssel wechseln und es gibt einen Reserveschlüssel für den Fall seines Verlusts. Die Datei ist Teil des Pakets, neue Schlüssel gelangen also per Aktualisierung in die Installationen und widerrufene verschwinden aus ihnen.

Zwei Regeln, die sich nicht ändern:

- **Signiert wird lokal, nicht in der CI.** In der CI könnte jeder mit dem Schlüssel signieren, der Workflows ändern darf. Der Workflow **Vydání** prüft nach dem Markieren der Version mit einem Tag nur, dass die Version im Code dem Tag entspricht, führt die Tests aus und legt einen Release-Entwurf an.
- **Private Schlüssel gehören nie in git und nie ins Paket.** Das überwachen `.gitignore` und die Prüfung auf GitHub.

Das Kennzeichen des Sicherheits-Release ist echten Sicherheitskorrekturen vorbehalten. Solche Releases installieren die Installationen in der Standardeinstellung selbst und der Administrator bekommt eine E-Mail. Wie eine Aktualisierung aus Sicht des Administrators der Website aussieht, beschreibt die Seite [Aktualisierungen](../zaciname/aktualizace.md).

Der vollständige Ablauf für den Herausgeber – Anlegen und Wechsel der Schlüssel, Vorgehen bei Verlust oder Abfluss eines Schlüssels, Veröffentlichung eines Patches – steht in `docs/VYDAVANI.md`. Nach dem Release der Version 3.0.0 werden Korrekturen auf dem Hauptzweig gemacht und in den Zweig der gepflegten Reihe übertragen, aus dem die Versionen 3.0.x hervorgehen; neue Funktionen gehen nur in den Hauptzweig.

## Siehe auch

- [Grundsätze des Projekts](zasady.md)
- [Mitwirken](jak-prispet.md)
- [Aktualisierungen](../zaciname/aktualizace.md)
- [Systemstatus](../provoz/stav-systemu.md)
