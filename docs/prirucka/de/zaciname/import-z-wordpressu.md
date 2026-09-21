# Import aus WordPress und Export der Website

Sie haben eine Website in WordPress und möchten damit zu phpRS wechseln? Der Import übernimmt Ressorts, Schlagwörter, Artikel, Seiten und freigegebene Kommentare und macht aus den alten Adressen Weiterleitungen, sodass weder Links noch Positionen in den Suchmaschinen verloren gehen. Und weil der Inhalt Ihnen gehören soll, kann phpRS auch die umgekehrte Richtung: Es exportiert die ganze Website in ein einziges Archiv in einem offenen Format.

Beides finden Sie in der Administration unter **Verwaltung → Import und Export**. Den Bildschirm sieht nur der Administrator.

## Bevor Sie beginnen

- Öffnen Sie in WordPress **Werkzeuge → Daten exportieren**, wählen Sie **Alle Inhalte** und laden Sie die `.xml`-Datei herunter.
- Erstellen Sie in phpRS unter **Einstellungen → Sicherungen und Aktualisierungen** eine Sicherung der Datenbank. Der Import lässt sich wiederholt ausführen und verdoppelt nichts, aber eine Sicherung vor einer großen Änderung ist immer nützlich.
- Schalten Sie die alte Website vorerst nicht ab – von ihr werden die Bilder heruntergeladen.

## Schritt 1: Datei

Die Datei laden Sie über das Formular hoch. Die meisten Hostings erlauben aber nur das Hochladen von ein paar Megabyte; der Bildschirm sagt Ihnen, wie viel es bei Ihnen ist. Einen größeren Export kopieren Sie per FTP in den Ordner `storage/import/` – er erscheint in der Liste unter dem Formular und Sie wählen ihn mit der Schaltfläche **Vorschau anzeigen** aus.

Angenommen wird nur ein echter Export aus WordPress. Die Datei wird stückweise gelesen, auch ein Export von Hunderten Megabyte stört also nicht; eine wirklich große Website (über 1 GB) exportieren Sie aus WordPress in Teilen, zum Beispiel nach Jahren.

## Schritt 2: Vorschau

Die Vorschau schreibt nichts in die Datenbank. Sie zeigt, wie viele Beiträge (veröffentlichte, geplante, Entwürfe), Seiten, Ressorts, Schlagwörter, freigegebene Kommentare, Bilder und Autoren in der Datei sind – und weist darauf hin, was **nicht übernommen wird**:

- **Benutzerkonten und Passwörter** – beim Artikel bleibt der Name des Autors erhalten, der Artikel gehört Ihnen;
- **E-Mail- und IP-Adressen der Kommentierenden** – sie werden überhaupt nicht übertragen;
- **Menüs, Widgets, das Design und Plug-in-Einstellungen** – Navigation und Blöcke stellen Sie in phpRS neu zusammen;
- **eigene Inhaltstypen von Plug-ins** (Produkte eines Onlineshops, Veranstaltungen, Portfolio…) – die Vorschau zählt sie auf;
- **Shortcodes von Plug-ins** in eckigen Klammern (Formulare, Page Builder) – das Kürzel verschwindet, der Text darin bleibt;
- private Beiträge, Papierkorb, Revisionen und automatische Entwürfe. Ein passwortgeschützter Beitrag wird als Entwurf übernommen.

Unter der Übersicht wählen Sie:

- die **Sprachversion**, zu der der Inhalt gehört (wird nur auf einer Website mit mehreren Sprachen angeboten),
- ob **Entwürfe**, **Seiten** und **freigegebene Kommentare** importiert werden,
- ob **Weiterleitungen von alten Adressen** angelegt werden,
- das Ressort für Beiträge, die in WordPress keines hatten (sonst entsteht das Ressort Nicht zugeordnet).

## Schritt 3: Import

Der Import läuft in Stapeln und die Seite aktualisiert sich von selbst – lassen Sie sie geöffnet und verfolgen Sie, wie viele Einträge von wie vielen fertig sind. Wenn Sie das Fenster schließen oder die Verbindung abbricht, passiert nichts: Kehren Sie zu **Import und Export** zurück und klicken Sie bei der Datei auf **Weiter**.

Was wohin übernommen wird:

- **Kategorien** werden zu Ressorts einschließlich der Unterordnung. Angelegt werden nur die, die einen Artikel haben. Ein Ressort mit demselben Namen und derselben Adresse, das es auf der Website schon gibt, wird verwendet.
- **Schlagwörter** bleiben Schlagwörter.
- **Beiträge** werden zu Artikeln. Der Vorspann ist der Textauszug aus WordPress; wenn er fehlt, wird der Text vor der Markierung „Weiterlesen“ genommen, sonst der erste Absatz. Das Veröffentlichungsdatum bleibt, geplante Beiträge erscheinen zu ihrer Zeit, Entwürfe bleiben Entwürfe und ein Beitrag, der auf Freigabe wartet, bekommt den Status Zur Korrektur. Ein „oben gehaltener“ Beitrag wird auf der Titelseite angeheftet.
- **Text** wird in die Form bereinigt, die der Editor von phpRS schreibt: Es verschwinden die Kommentare des Block-Editors, Skripte, eingebettete Stile und Frames; ein Bild mit Bildunterschrift bleibt ein Bild mit Bildunterschrift, eine Galerie wird zur Fotogalerie und die Adresse eines Videos von YouTube oder Vimeo verwandelt sich auf der Website in einen Player.
- **Seiten** werden als Seiten übernommen; in das Menü in der Fußzeile werden sie nicht von selbst aufgenommen, damit Dutzende alter Seiten es nicht überschwemmen.
- **Kommentare** nur freigegebene, mit Name, Datum und den Antwortsträngen.

Importierte Artikel werden nirgendwohin gemeldet – kein Webhook, IndexNow, Web Push und kein Newsletter.

### Wiederholter Import

Das System merkt sich, was es von der alten Website schon übernommen hat. Denselben (oder einen neueren) Export können Sie deshalb erneut ausführen: Hinzu kommt nur, was auf der Website noch nicht vorhanden ist, und Artikel, die Sie inzwischen in phpRS bearbeitet haben, bleiben unverändert. Ein Artikel, den Sie nach dem Import gelöscht haben, kehrt beim nächsten Import zurück.

## Weiterleitungen der alten Adressen

Zu jedem Artikel und jeder Seite entsteht eine Weiterleitung von der alten Adresse (zum Beispiel `/2024/05/nazev-clanku/`) und von der numerischen Adresse `/?p=123` auf die neue Adresse. Sie funktionieren, wenn die Erweiterung **Weiterleitungen** eingeschaltet ist, und Sie finden sie unter **Verwaltung → Weiterleitungen**. Voraussetzung ist, dass die neue Website auf derselben Domain läuft wie die alte.

## Bilder von der alten Website

Nach dem Import verweisen die Artikel weiterhin auf die Bilder der alten Website. Klicken Sie deshalb auf der Seite mit dem Ergebnis auf **Bilder von der alten Website herunterladen**. Die Hauptbilder der Artikel und die Bilder in den Texten werden heruntergeladen, verkleinert, bekommen Vorschaubilder und werden in den **Medien** gespeichert – genauso, als hätten Sie sie von Hand hochgeladen – und die Links in den Texten werden umgeschrieben. Auch hier wird in Stapeln gearbeitet und die Seite macht von selbst weiter.

Aus Sicherheitsgründen wird **nur von der Domain der alten Website** heruntergeladen, die im Export angegeben ist, nur Bilder JPG, PNG, GIF und WebP bis 15 MB. Bilder von anderen Domains (zum Beispiel aus einem CDN oder von fremden Websites) bleiben im Text, wie sie waren. Was sich nicht herunterladen ließ, führt das Ergebnis auf; mit der Schaltfläche **Erneut herunterladen** wiederholen Sie es.

Wenn der Server nicht herunterladen kann (er hat weder `curl` noch ein erlaubtes `allow_url_fopen`), sagt es der Bildschirm. Laden Sie die Bilder dann von Hand in die Medien hoch und tauschen Sie sie in den Artikeln aus.

## Nach dem Import

- Gehen Sie ein paar Artikel auf der Website durch, bringen Sie unter **Ressorts** die Reihenfolge in Ordnung und legen Sie Ressorts gegebenenfalls zusammen.
- Stellen Sie Navigation und Blöcke zusammen, wählen Sie eine Vorlage und stellen Sie die **Website-Identität** ein.
- **Löschen** Sie die Datei mit dem Export (Schaltfläche bei der Datei). Sie enthält E-Mail-Adressen von Autoren und Kommentierenden der alten Website und wird auf dem Server nicht mehr gebraucht.

## Export der ganzen Website

Die Schaltfläche **Export erstellen** auf demselben Bildschirm speichert in `storage/zalohy/` das Archiv `export-RRRRMMDD-HHMMSS.zip` und bietet es zum Herunterladen an. Es enthält:

- `obsah.json` – Ressorts, Schlagwörter, Serien, Artikel mit allen Angaben (einschließlich Sprache und Verknüpfungen zwischen Übersetzungen), Seiten, freigegebene Kommentare, Blöcke, Weiterleitungen, die Medienbibliothek und die Grundeinstellungen der Website (Name, Beschreibung, Identität, Sprachen),
- `README.txt` – Beschreibung des Formats,
- den Ordner `media/` mit allen hochgeladenen Dateien.

Im Export sind **nicht enthalten**: Passwörter, Konten der Redaktion, Schlüssel und Tokens, Angaben zu E-Mail und Sicherungen, Leser, Newsletter-Empfänger und E-Mail-Adressen der Kommentierenden.

Wenn die Medien mehr als 1 GB haben oder auf der Festplatte nicht genug Platz ist, entsteht ein Export nur mit den Daten und der Bildschirm meldet es – den Ordner `media/` laden Sie dann per FTP herunter. Ohne die PHP-Erweiterung `zip` entsteht statt des Archivs die bloße JSON-Datei. Aufbewahrt werden die letzten drei Exporte.

Der Export ist keine Sicherung zur Wiederherstellung derselben Website – dazu dient die Sicherung der Datenbank. Er ist eine Versicherung Ihrer Freiheit: Inhalt in einem lesbaren Format, der sich überallhin übertragen lässt.
