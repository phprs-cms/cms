# Grundsätze des Projekts

Diese Seite fasst die Regeln zusammen, nach denen phpRS entwickelt wird. Wer beiträgt, erspart sich damit eine Runde Anmerkungen beim Pull Request. Den vollständigen Wortlaut samt Begründung enthält die Datei `CLAUDE.md` im Wurzelverzeichnis des Repositorys; die Regeln für Design-Vorlagen stehen in `layout/CLAUDE.md`.

## Einfachheit vor Abstraktion

Den Code soll auch ein kundiger Laie lesen können, der seine Website anpassen möchte. Deshalb:

- keine Dependency-Container, kein ORM, keine Build-Schritte und kein npm,
- eine neue Abhängigkeit nur mit starkem Grund,
- ein Modul der Administration ist eine Klasse, ein Bildschirm ist eine Vorlage,
- die Einstellung einer Funktion soll mit ein paar Feldern auskommen – was sich ableiten lässt, danach fragt das System nicht.

Die Bedienung muss für einen Menschen verständlich sein, der das System zum ersten Mal sieht. Die Administration hat ein einziges Erscheinungsbild; jede Änderung ihrer Vorlagen wird im hellen und im dunklen Modus und in der Breite eines Telefons geprüft.

## Erweiterungen sind ein geschlossener Satz

Die optionalen Funktionen sind in `Core\Rozsireni::SEZNAM` aufgezählt. Alle Erweiterungen sind Teil des Pakets und entstehen im Projekt. Das System hat keine fremden Plug-ins, keine öffentliche API für Steckmodule und kein Hochladen von Code aus der Administration – das ist eine Sicherheitsentscheidung.

Eine neue optionale Funktion bedeutet:

1. einen Eintrag in `Rozsireni::SEZNAM` (Name, Beschreibung, Standardzustand),
2. die Konstante `ROZSIRENI` beim Modul der Administration,
3. die Prüfung `Rozsireni::je()` überall, wo sich die Funktion auf der Website zeigt.

Der Kern – Artikel, Medien, Ressorts, Seiten, Blöcke, Benutzer, Einstellungen – lässt sich nicht ausschalten. Eine ausgeschaltete Erweiterung verschwindet aus dem Menü und von der Website, ihre Daten bleiben erhalten.

## Änderung der Datenbank

Jede Änderung der Struktur wird an drei Stellen eingetragen:

1. **`system/sql/schema.sql`** – das vollständige Schema für neue Installationen.
2. **`system/sql/migrace/NNNN-popis.sql`** – die Migration für bestehende Websites. Sie wird beim ersten Besuch der Website oder der Administration nach der Aktualisierung von selbst ausgeführt; die Nummer der zuletzt ausgeführten hält die Einstellung `verze_db`.
3. **`PHPRS_VERZE_DB`** in `system/bootstrap.php` – auf die Nummer der neuen Migration erhöhen. Das überwacht `tools/test.sh`.

Eine neue Spalte der Artikeltabelle, die in den Listen zu sehen sein soll, ergänzen Sie auch in `Front\Clanky::SLOUPCE_VYPISU` – die Listen laden lange Texte absichtlich nicht.

## Neue Einstellung

Eine neue Option in den Einstellungen hat drei Teile: den Schlüssel mit Standardwert in `Settings::DEFAULTS`, den Typ in `Konfigurace::POLE` (nach dem Typ wird der Wert beim Speichern bereinigt) und die Zeile `$pole(...)` in der Vorlage `system/views/admin/config/<Reiter>.php`. Geheime Werte (Schlüssel, Passwörter) haben den Typ `tajne` und werden nie zurück ins Formular ausgegeben.

## Übersetzungen

- Texte der Website und der Administration werden mit `t('Česky')` umschlossen. Schlüssel ist der tschechische Text.
- Einen Text in einer Website-Vorlage oder in `system/views/front/` ergänzen Sie in den Wörterbüchern `en.php`, `sk.php` und `de.php`; einen Text der Administration in `admin-en.php`, `admin-sk.php` und `admin-de.php`. E-Mails an die Leser gehören in die Wörterbücher der Website.
- Werte von Formularen – `value` versteckter Felder und von Schaltflächen mit dem Attribut `name` – werden nie übersetzt.
- Die Texte des visuellen Blockeditors sind in der Sprache der Administration des Angemeldeten, nicht in der Sprache der angezeigten Version der Website: in JavaScript `T()`, Wörterbuch `image/jazyky/admin-<Code>.js`.

Ergänzen Sie Übersetzungen nicht von Hand. Verwenden Sie das Werkzeug, das Apostrophe richtig behandelt:

```
tools/slovnik.py system/jazyky/admin-en.php < radky.txt
```

Jede Zeile der Eingabe hat die Form `tschechisch|Übersetzung`. Vorhandene Schlüssel überspringt das Werkzeug. Führen Sie nach einer Änderung der Wörterbücher die Tests aus – die Vollständigkeit der Wörterbücher des Installationsprogramms und der Beispielinhalte überwacht `tools/testy.php`.

## Sicherheit

- **Datenbank:** nur vorbereitete Abfragen; `{tabulka}` ergänzt das Präfix.
- **Ausgabe:** alles über `e()`. Das HTML von Artikeln und Blöcken ist vertrauenswürdig, weil es die Autoren schreiben. Kommentare und andere Eingaben der Leser nie.
- **Administration:** jeder POST hat ein CSRF-Token; es prüft `Admin\Kernel`.
- **Formulare der Leser** haben weder Sitzung noch CSRF-Token. Sie schützt `Core\Antispam`: signierte Zeit, verstecktes Feld und ein Limit pro Hash der IP-Adresse. Die IP-Adresse wird nicht gespeichert, nur ihr Hash.
- **Hochladen:** Bilder immer über `Core\Obrazky` (Neukodierung), Anhänge über `Core\Soubory` mit einer Liste erlaubter Endungen. HTML, SVG und Skripte nie.
- **E-Mail** geht immer über `Core\Posta::odesli()`, nie direkt über die Funktion `mail()`.
- **Die Adresse der Website** wird über `$app->request->origin()` aus den Einstellungen genommen, nie aus dem Header `Host`.
- **Die Verbindung mit Claude** darf nur Inhalte und eigene Vorlagen ändern. Kein Werkzeug darf außerhalb von `layout/<eigene>/` schreiben, Code oder eine Abfrage ausführen. Eine neue Funktion nehmen Sie in die Erlaubnisliste `Core\SablonaKontrola` nur dann auf, wenn sie nicht mit Dateien, Netzwerk, Prozessen, Callbacks oder Reflection arbeitet.

### Content-Security-Policy in der Administration

Die Administration sendet den Header `script-src 'self'`. Die Folge für die Vorlagen der Administration:

- kein Inline-`<script>`,
- keine Attribute `onclick=`, `onchange=` und ähnliche,
- Verhalten gehört in `image/admin.js` und wird über Attribute `data-…` angebunden (zum Beispiel `data-odeslat-pri-zmene`, `data-ukaz-heslo`).

Das überwacht eine statische Prüfung in `tools/testy.php`. Dieselbe Prüfung stellt sicher, dass kein Skript ein Element `[data-…]` sucht, das nirgends entsteht.

### Nie window.confirm

In Apps eingebaute Browser unterdrücken den Dialog `window.confirm()`, eine Bestätigung würde also stillschweigend nicht stattfinden. Geben Sie in der Administration dem Formular oder der Schaltfläche das Attribut `data-potvrdit="Text der Frage"` – es wird von `admin.js` behandelt. Der visuelle Blockeditor hat einen eigenen Dialog.

## Website und Vorlagen

- Eine neue Variable für Vorlagen oder ein neuer Systemblock muss sich in allen drei eingebauten Vorlagen niederschlagen.
- Eine Vorlage muss `$hlava` vor `</head>` und `$pata` vor `</body>` ausgeben.
- Was nicht ausgegeben wird, hat weder Stil noch Skript. In `image/web.css` und in das `style.css` der Vorlagen gehört kein Selektor, der nirgends entsteht.
- Das gemeinsame Aussehen der Elemente steht am Ende von `image/web.css` in `:where()` mit der Spezifität null. Eine Vorlage trägt nur das, was abweicht.
- Keine externen Schriften und kein CDN.
- Jede Abfrage auf der Website, die Inhalt ausgibt, filtert nach der Sprachversion.
- Alles, was Artikel auf anderem Weg als über `Front\Clanky` liest, muss die Sperre des Inhalts selbst beachten.
- Gecacht wird nur die Ausgabe für nicht Angemeldete ohne persönliche Cookies. Was sich je nach Leser unterscheiden soll, läuft in JavaScript oder hat ein eigenes Cookie `phprs_*`, das den Cache ausschaltet.

## Handbuch

Passen Sie bei einer Änderung des Verhaltens oder einer Beschriftung in der Administration auch das Handbuch in `docs/prirucka/cs/` an. Tschechisch ist die Quelle, Englisch und Deutsch sind Übersetzungen mit denselben Dateinamen. Die Reihenfolge der Seiten und die Übersetzung der Adressen hält `docs/prirucka/osnova.json`; die Tabelle in `Core\Napoveda` muss ihr entsprechen.

## Siehe auch

- [Projektstruktur](struktura-projektu.md)
- [Tests und Releases](testy-a-vydavani.md)
- [Mitwirken](jak-prispet.md)
