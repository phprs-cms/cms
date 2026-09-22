# Verbindung mit Claude

Die Erweiterung **Verbindung mit Claude** macht die Website für den Assistenten Claude über das Protokoll MCP (Model Context Protocol) zugänglich. Claude kann dann auf Ihre Anweisung Artikel lesen und schreiben, Ressorts anlegen, Blöcke verwalten und eigene Website-Vorlagen erstellen – mit den Rechten Ihres Kontos und nur in den unten beschriebenen Grenzen.

Nicht zu verwechseln mit dem [KI-Assistenten](../psani/ai-asistent.md). Der ist Teil des Artikeleditors und schlägt nur Texte vor. Die Verbindung mit Claude funktioniert umgekehrt: Claude läuft bei Ihnen (App Claude, Claude Code) und die Website ist für ihn ein Werkzeug.

## Was die Verbindung kann und was nicht

| Bereich | Werkzeuge | Wer |
|---|---|---|
| Übersicht | Informationen über die Website, Rolle und Berechtigungen des Angemeldeten | alle |
| Artikel | Liste, Laden, Anlegen, Bearbeiten | je nach Rolle, siehe unten |
| Ressorts | Baum der Ressorts; Anlegen eines Ressorts | Lesen alle; Anlegen Redakteur und Administrator |
| Medien | Liste der zuletzt hochgeladenen Bilder mit Adressen | alle |
| Blöcke | Liste der Blöcke nach Zonen; Anlegen und Bearbeiten eines Blocks | Administrator |
| Vorlagen | Liste, Kopieren einer eingebauten Vorlage, Lesen und Speichern einer Datei einer eigenen Vorlage, Umschalten der Website auf eine Vorlage | Administrator |

**Die Grenze ist fest: Über die Verbindung ändern sich nur Inhalte und eigene Vorlagen.** Kein Werkzeug kann:

- den Code des Systems, Dateien in `system/`, `admin.php`, `index.php` oder eingebaute Vorlagen ändern,
- eine Datei woandershin schreiben als in den Ordner einer eigenen Vorlage `layout/<Name>/` – und dorthin nur Dateien `.php` und `.css` bis 300 kB,
- Code, einen Befehl oder eine Datenbankabfrage ausführen,
- Benutzer, Passwörter, Tokens oder Berechtigungen verwalten,
- Einstellungen, Erweiterungen, E-Mail, Sicherungen oder Aktualisierungen ändern,
- Dateien in die Medien hochladen, Artikel löschen, Kommentare sowie Daten von Lesern und Newsletter-Empfängern lesen.

Jede PHP-Datei, die in einer Vorlage gespeichert wird, durchläuft eine Prüfung, die nur die Ausgabe der übergebenen Daten erlaubt. Arbeit mit Dateien, Netzwerk, Prozessen und Datenbank lehnt sie ab und die Datei wird nicht gespeichert. Einzelheiten stehen auf der Seite [Eigene Vorlage](../vzhled/vlastni-sablona.md).

Wenn Ihnen im System eine Funktion fehlt, ergänzt Claude sie über die Verbindung nicht. Das ist Absicht: Das System soll für alle gleich und aktualisierbar sein. Anregungen gehören zu den Autoren von phpRS – siehe [Mitwirken](../pro-vyvojare/jak-prispet.md).

## Die Berechtigungen richten sich nach der Rolle

Claude handelt in Ihrem Namen und mit Ihren Rechten. Für ihn gilt dasselbe wie für Sie in der Administration:

- Ein **Autor** sieht und bearbeitet nur seine Artikel. Ohne das Recht zum Veröffentlichen speichert er nur Entwürfe und ändert keinen veröffentlichten Artikel.
- Ein auf bestimmte Ressorts beschränkter Benutzer arbeitet nur mit Artikeln aus diesen Ressorts und speichert nichts in ein anderes Ressort.
- Ein **Redakteur** arbeitet mit allen Artikeln und darf Ressorts anlegen.
- Ein **Administrator** hat zusätzlich Blöcke und Vorlagen.

Ein neuer Artikel entsteht immer als Entwurf. Veröffentlichen darf ihn Claude nur mit einem Konto, das das Recht zum Veröffentlichen hat, und nur auf Ihre ausdrückliche Anweisung. Beim Bearbeiten eines Artikels wird die vorherige Version im Versionsverlauf gespeichert. Nach dem Speichern gibt Claude die Adresse der Vorschau und einen Link zur Bearbeitung in der Administration zurück.

## Einschalten und Token

1. Der Administrator öffnet **Verwaltung → Erweiterungen**, hakt **Verbindung mit Claude** an und speichert. In der Standardeinstellung ist die Erweiterung ausgeschaltet.
2. Jeder Benutzer, der die Verbindung verwenden möchte, öffnet **Mein Konto**, Abschnitt **Verbindung mit Claude**.
3. Er füllt **Name des neuen Tokens** aus – zum Beispiel „Claude auf dem Notebook“ – und klickt auf **Token erstellen**.
4. Das Token wird **nur einmal** angezeigt. Kopieren Sie es sofort. In der Datenbank ist nur sein Hash gespeichert, es lässt sich später also nicht anzeigen – nur widerrufen und ein neues erstellen.

Unter dem Token steht der fertige Befehl für Claude Code:

```
claude mcp add --transport http phprs https://www.example.cz/mcp --header "Authorization: Bearer phprs_…"
```

In der App Claude fügen Sie einen eigenen Konnektor mit der Adresse `https://www.example.cz/mcp` und demselben Header `Authorization` hinzu.

Die Adresse der Verbindung ist immer die Adresse der Website mit `/mcp` am Ende. Sie funktioniert nur über HTTPS (Ausnahme ist die Entwicklung auf `localhost`) und nur mit eingeschalteter Erweiterung. Zugänglich bleibt sie auch im Wartungsmodus.

### Tokens verwalten

Die Liste der Tokens unter **Mein Konto** zeigt bei jedem den Namen, das Datum der Erstellung und wann es zuletzt verwendet wurde. **Token widerrufen** macht es sofort ungültig.

Bei einer Änderung des Passworts ist die Option **auch die Verbindungs-Token widerrufen (Claude, API)** vorab angehakt. Wenn Sie das Passwort wegen des Verdachts auf Missbrauch ändern, lassen Sie sie angehakt und richten Sie die Verbindung danach neu ein. Das Token eines gesperrten Benutzers funktioniert nicht.

## Eintrag im Änderungsprotokoll

Jeder Eingriff, der etwas ändert – Anlegen und Bearbeiten eines Artikels, Ressorts oder Blocks, Erstellen einer Vorlage, Speichern einer Vorlagendatei, Umschalten der Vorlage –, wird unter **Verwaltung → Änderungsprotokoll** als Modul `claude` mit dem Namen des Werkzeugs und dem Titel, Namen oder der Nummer des betroffenen Eintrags vermerkt. Er wird unter dem Benutzer vermerkt, dem das Token gehört. Auch das Erstellen eines Tokens wird vermerkt.

Lesen (Listen, Laden eines Artikels oder einer Vorlagendatei) wird nicht vermerkt.

## Sicherheitsempfehlungen

- **Schützen Sie das Token wie ein Passwort.** Es funktioniert ohne Passwort und ohne Zwei-Faktor-Anmeldung. Fügen Sie es nicht in geteilte Dokumente, Repositorys oder einen Chat ein.
- **Für jedes Gerät und jeden Zweck ein eigenes Token.** Beim Verlust des Notebooks widerrufen Sie eines und die anderen funktionieren weiter. Löschen Sie Tokens, die Sie nicht verwenden.
- **Verwenden Sie ein Konto mit der niedrigsten nötigen Rolle.** Zum Schreiben von Artikeln genügt das Konto eines Autors oder Redakteurs. Ein Administrator-Token erstellen Sie nur für die Arbeit an Blöcken und Vorlagen und widerrufen es danach.
- **Lesen Sie, was Claude getan hat.** Neue Artikel sind Entwürfe – lesen Sie sie vor der Veröffentlichung. Eine Vorlage sehen Sie sich vor dem Umschalten in der Vorschau `/?sablona=Name` an.
- **Prüfen Sie laufend das Änderungsprotokoll**, vor allem nach der Arbeit mit Vorlagen.
- Nach 20 ungültigen Anmeldeversuchen mit einem Token von einer Adresse innerhalb von 15 Minuten lehnt der Server weitere Versuche von dieser Adresse vorübergehend ab.

Wenn Sie die Verbindung nicht verwenden, schalten Sie die Erweiterung aus. Die Adresse `/mcp` antwortet dann nicht mehr und die Tokens bleiben für das nächste Einschalten gespeichert.

## Siehe auch

- [Eigene Vorlage](../vzhled/vlastni-sablona.md)
- [KI-Assistent](../psani/ai-asistent.md)
- [Sicherheit](../provoz/bezpecnost.md)
- [Rollen und Berechtigungen](../redakce/role-a-opravneni.md)
