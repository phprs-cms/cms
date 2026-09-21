# Mitwirken

phpRS ist freie Software und wird öffentlich auf GitHub im Repository `phprs-cms/cms` entwickelt. Beitragen können Sie mit einer Fehlermeldung, einer Übersetzung, einer Korrektur oder einer Anregung. Diese Seite sagt, wie Sie vorgehen, damit sich Ihr Beitrag leicht annehmen lässt.

## Fehler melden

Einen gewöhnlichen Fehler melden Sie in den Issues des Repositorys. Eine gute Meldung enthält:

- die Version von phpRS – sie steht in der Administration unten in der Fußzeile und unter **Einstellungen → Sicherungen und Aktualisierungen**,
- die Version von PHP und der Datenbank – sie zeigt **Einstellungen → Systemstatus**,
- die genauen Schritte, mit denen sich der Fehler auslösen lässt, was Sie erwartet haben und was geschehen ist,
- den Wortlaut der Fehlermeldung; Einzelheiten stehen meist in der Datei `storage/log/chyby.log`,
- bei Fehlern im Aussehen die verwendete Vorlage, den Browser und die Fensterbreite, gegebenenfalls einen Screenshot.

Bevor Sie einen Fehler melden, prüfen Sie, dass Sie die neueste Version haben, und gehen Sie die Seite [Problemlösung](../provoz/reseni-potizi.md) durch. Entfernen Sie aus Logs und Screenshots Passwörter, Tokens und personenbezogene Daten der Leser.

## Sicherheitslücken

Eine Sicherheitslücke **melden Sie nicht öffentlich** in den Issues. Verwenden Sie die private Meldung auf GitHub – im Repository der Reiter **Security → Report a vulnerability** – oder die auf der Website des Projekts angegebene E-Mail-Adresse. Beschreiben Sie Version, Vorgehen und Auswirkung.

Was danach geschieht:

1. Wir melden uns innerhalb von 3 Werktagen.
2. Die Korrektur entsteht nicht öffentlich. Üblicherweise erscheint sie innerhalb von 14 Tagen, bei kritischen Fehlern so bald wie möglich.
3. Sie erscheint als Version, die als Sicherheits-Release gekennzeichnet ist. Die Installationen sehen zweimal täglich nach Neuigkeiten und installieren eine solche Version – sofern der Administrator das nicht ausgeschaltet hat – selbst; der Administrator bekommt eine E-Mail.
4. Nach der Veröffentlichung der Korrektur veröffentlichen wir einen Sicherheitshinweis mit Beschreibung, betroffenen Versionen und einem Dank an den Finder.

Unterstützt wird immer die zuletzt veröffentlichte Version. Die Regeln stehen auch in der Datei `SECURITY.md`.

## Übersetzungen

Das System ist ins Tschechische, Slowakische, Englische und Deutsche übersetzt. Quelle ist Tschechisch; Schlüssel jeder Übersetzung ist der tschechische Text.

| Was | Wo |
|---|---|
| Texte der Website und E-Mails an die Leser | `system/jazyky/en.php`, `sk.php`, `de.php` |
| Administration | `system/jazyky/admin-en.php`, `admin-sk.php`, `admin-de.php` |
| Visueller Blockeditor und Artikeleditor | `image/jazyky/admin-<Code>.js` |
| Installationsprogramm | `system/jazyky/install-<Code>.php` |
| Beispielinhalte | `system/demo/` |
| Handbuch | `docs/prirucka/<Sprache>/` – die Dateien heißen in allen Sprachen gleich, tschechisch |

Die Korrektur einer Übersetzung schicken Sie als Pull Request oder beschreiben sie in den Issues: ursprünglicher Wortlaut, vorgeschlagener Wortlaut und die Stelle, an der Sie den Text gesehen haben. Fehlende Übersetzungen ergänzen Sie mit dem Werkzeug `tools/slovnik.py`, nicht durch Bearbeiten des Wörterbuchs von Hand – siehe [Grundsätze des Projekts](zasady.md). Das Datumsformat für eine Sprache bestimmen im Wörterbuch der Website die Schlüssel `datum_format` und `datum_slovy`.

Das Hinzufügen einer weiteren Sprache ist eine größere Arbeit: drei Wörterbücher, das Wörterbuch für JavaScript, das Installationsprogramm, die Beispielinhalte und ein Eintrag in `Core\Jazyk`. Sprechen Sie sich vorher in den Issues ab.

## Pull Requests

1. **Sprechen Sie sich bei einer größeren Änderung zuerst ab.** Legen Sie ein Issue an und beschreiben Sie das Vorhaben. Das Projekt hält einen engen Fokus – ein Redaktionssystem für Magazine – und Einfachheit hat Vorrang vor der Zahl der Funktionen. Sie ersparen sich Arbeit an etwas, das nicht angenommen würde.
2. Lesen Sie die [Grundsätze des Projekts](zasady.md) und die [Projektstruktur](struktura-projektu.md).
3. Arbeiten Sie in einem eigenen Zweig. Ein Pull Request löst eine Sache.
4. Halten Sie sich an den Stil des umgebenden Codes: `declare(strict_types=1)`, Bezeichner der Domäne tschechisch ohne diakritische Zeichen, Kommentare tschechisch. Ein Kommentar erklärt, warum der Code etwas tut – nicht, was er tut.
5. Führen Sie vor dem Einreichen `tools/test.sh` aus oder wenigstens `php tools/testy.php`. Fügen Sie zur Korrektur eines Fehlers einen Test hinzu, der ihn beim nächsten Mal abfangen würde, sofern es um Logik ohne Datenbank geht.
6. Eine Änderung der Datenbank braucht eine Migration, eine Anpassung von `schema.sql` und die Erhöhung von `PHPRS_VERZE_DB`.
7. Ein neuer Text für Benutzer braucht eine Übersetzung in allen Wörterbüchern.
8. Wenn sich das Verhalten oder eine Beschriftung in der Administration ändert, passen Sie auch das tschechische Handbuch in `docs/prirucka/cs/` an.
9. Geben Sie in der Beschreibung des Pull Requests an, was Sie warum ändern und wie Sie es ausprobiert haben. Fügen Sie bei Änderungen am Aussehen Screenshots im hellen und im dunklen Modus und in der Breite eines Telefons bei.

Jeder Pull Request durchläuft die Prüfung auf GitHub: Smoke-Test auf den unterstützten PHP-Versionen, Semgrep und Gitleaks. Einzelheiten stehen auf der Seite [Tests und Releases](testy-a-vydavani.md).

### Was nicht angenommen wird

- neue Abhängigkeiten, Frameworks, Build-Schritte, externe Schriften und CDN,
- Steckmodule Dritter und das Hochladen von Code aus der Administration – die Erweiterungen sind ein geschlossener Satz,
- Werkzeuge der Verbindung mit Claude, die über Inhalte und eigene Vorlagen hinausgreifen würden,
- Funktionen für eine einzige Website. Ein eigenes Aussehen gehört in eine [eigene Vorlage](../vzhled/vlastni-sablona.md), nicht in den Kern.

## Lizenz

phpRS ist unter der Lizenz **GNU General Public License Version 2** oder neuer veröffentlicht. Der Text steht in der Datei `LICENSE`. Mit dem Einreichen eines Beitrags stimmen Sie seiner Veröffentlichung unter derselben Lizenz zu. Fügen Sie keinen Code und keine Bilder hinzu, deren Lizenz damit nicht vereinbar ist. Die Schrift der Administration, Noto Sans, hat eine eigene Lizenz OFL, die in `image/pisma/OFL.txt` beiliegt.

## Unterstützung des Projekts

Die Entwicklung können Sie auch finanziell über GitHub Sponsors unterstützen. Der Link **phpRS unterstützen** steht in der Fußzeile der Administration; der Administrator kann ihn unter **Einstellungen → Sicherungen und Aktualisierungen** ausschalten.

## Siehe auch

- [Grundsätze des Projekts](zasady.md)
- [Tests und Releases](testy-a-vydavani.md)
- [Sicherheit](../provoz/bezpecnost.md)
- [Problemlösung](../provoz/reseni-potizi.md)
