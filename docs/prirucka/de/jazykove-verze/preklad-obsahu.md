# Inhalte übersetzen

Auf einer mehrsprachigen Website hat jede Sprachversion eigene Ressorts, Artikel und Seiten. Es handelt sich nicht um einen Artikel mit zwei Texten, sondern um zwei eigenständige Artikel, die Sie als Übersetzungen verknüpfen können. Jeder hat so eine eigene Adresse, ein eigenes Veröffentlichungsdatum, eigene Kommentare und eigene Einstellungen für Suchmaschinen – und eine Version kann Inhalt haben, den die andere nicht hat.

Voraussetzung ist die eingeschaltete Erweiterung **Sprachversionen der Website** und mindestens eine weitere Version – siehe [Sprachen der Website](jazyky-webu.md).

## Ressorts bestimmen die Sprache

Die Sprache wird nicht beim Artikel gewählt, sondern beim Ressort. Der Artikel übernimmt sie von dem Ressort, in dem Sie ihn speichern.

1. Öffnen Sie **Inhalt → Ressorts** und legen Sie ein neues Ressort an.
2. Wählen Sie im Feld **Sprachversion** die Sprache, zum Beispiel **English – /en/**. Die Artikel im Ressort gehören zu dieser Sprachversion der Website.
3. Wählen Sie im Feld **Ist Übersetzung von** das entsprechende Ressort in der Standardsprache, sofern es existiert. Der Sprachumschalter führt dann vom Ressort Sport direkt zu Sports und die Suchmaschinen bekommen hreflang-Tags. Sonst belassen Sie **– keine Übersetzung –**.
4. Speichern Sie.

Beginnen Sie damit, für jede weitere Version Ressorts anzulegen, die den wichtigsten entsprechen. Navigation, Block Ressorts und die Ressortauswahl auf der Website zeigen in jeder Version nur deren Ressorts.

Wenn Sie bei einem bestehenden Ressort die Sprache ändern, gehen auch alle seine Artikel in die neue Version über.

## Artikel von Hand übersetzen

1. Legen Sie einen neuen Artikel an und ordnen Sie ihn in ein Ressort der Zielsprache ein.
2. Schreiben Sie den übersetzten Titel, Vorspann und Text oder fügen Sie sie ein. Vergessen Sie nicht **Kurz gefasst**, Fragen und Antworten, **Titel für Suchmaschinen** und **Beschreibung für Suchmaschinen**.
3. Klappen Sie den Abschnitt **Übersetzung des Artikels** auf und geben Sie in das Feld **Original in der Standardsprache** die Adresse oder Nummer des ursprünglichen Artikels ein. Es genügt, die vollständige Adresse des Artikels aus dem Browser einzufügen.
4. Speichern und veröffentlichen Sie ihn wie jeden anderen Artikel.

Das Feld **Original in der Standardsprache** wird nur bei einem Artikel in einer weiteren Sprachversion ausgefüllt. Das Original muss ein Artikel in der Standardsprache sein; einen anderen Wert verwirft das System beim Speichern. Wenn Sie drei Versionen haben, verknüpfen Sie beide Übersetzungen mit demselben Original – der Umschalter führt dann zwischen allen dreien.

Bei einem Artikel in der Standardsprache zeigt der Abschnitt **Übersetzung des Artikels** die Zeile **Sprachversionen des Artikels**: bei jeder Sprache entweder den Link **Übersetzung öffnen** oder den Hinweis **noch ohne Übersetzung**.

### Was die Verknüpfung bewirkt

- Der Sprachumschalter führt beim Artikel direkt zu seiner Übersetzung, nicht zur Startseite der anderen Version.
- In den Kopf der Seite werden hreflang-Tags zu allen veröffentlichten Versionen des Artikels eingefügt.
- Auf Inhalt, Kommentare, Bewertungen und Aufrufzahlen hat die Verknüpfung keinen Einfluss – jeder Artikel hat seine eigenen.

Eine nicht veröffentlichte Übersetzung erscheint weder im Umschalter noch in den Tags.

## Übersetzung mit dem KI-Assistenten

Mit eingeschalteter Erweiterung **KI-Assistent im Editor** und eingegebenem Schlüssel bietet der Abschnitt **Übersetzung des Artikels** bei jeder Sprache ohne Übersetzung die Schaltfläche **Mit dem Assistenten übersetzen** an.

1. Speichern Sie den Artikel in der Standardsprache – übersetzt wird die zuletzt gespeicherte Version.
2. Klicken Sie bei der gewählten Sprache auf **Mit dem Assistenten übersetzen** und bestätigen Sie. Die Übersetzung kann bis zu einer Minute dauern.
3. Es öffnet sich ein neuer Artikel in der Zielsprache: ein Entwurf, der mit dem Original verknüpft ist.
4. Lesen und korrigieren Sie die Übersetzung. Der Assistent kann sich bei Namen, Zahlen und Fachausdrücken irren. Veröffentlichen Sie den Artikel danach.

Regeln:

- Übersetzen lässt sich nur ein Artikel in der Standardsprache und in jede Sprache nur einmal. Wenn es die Übersetzung schon gibt, führt Sie die Schaltfläche zu ihr.
- Das Zielressort ist das Gegenstück zum Ressort des Originals (Feld **Ist Übersetzung von**). Wenn es keines gibt, wird das erste Ressort der Zielsprache verwendet, in dem Sie schreiben dürfen. Ohne ein solches Ressort wird die Übersetzung nicht angelegt.
- Übersetzt werden Titel, Vorspann, Text, Kurz gefasst, Fragen und Antworten, Schlüsselwörter sowie Titel und Beschreibung für Suchmaschinen. Formatierung, Bilder und Links bleiben aus dem Original erhalten.
- Bild, Artikelvorlage, Autor, Mitautoren, Schlagwörter, Sperre und weitere Einstellungen werden übernommen.
- Die Adresse des Artikels entsteht aus dem übersetzten Titel.

Was wohin gesendet wird und welche Limits der Assistent hat, beschreibt die Seite [KI-Assistent](../psani/ai-asistent.md).

## Seiten

Seiten (Über uns, Kontakt, Datenschutzerklärung) haben im Formular dieselben zwei Felder wie Ressorts: **Sprachversion** und **Ist Übersetzung von**. Eine Seite wird nur in ihrer Sprachversion angezeigt – in der Navigation, im Block Seiten und unter ihrer Adresse (`/en/about`). Mit ausgefülltem **Ist Übersetzung von** führt der Sprachumschalter von der einen zur anderen.

Seiten übersetzt der Assistent nicht. Übersetzen Sie sie von Hand.

## Kurzmeldungen und Umfragen

Kurzmeldung und Umfrage haben das Feld **Sprachversion** und erscheinen nur in ihr. In einer Sprache, die keine aktive Umfrage hat, wird die neueste offene Umfrage dieser Sprache angezeigt.

## Schlagwörter

Schlagwörter sind allen Versionen gemeinsam und werden nicht übersetzt. Die Seite eines Schlagworts gibt in jeder Version nur deren Artikel aus. Der Block Schlagwörter zeigt nur Schlagwörter, die in der jeweiligen Version mindestens einen Artikel haben. Für anderssprachige Artikel können Sie eigene Schlagwörter in der jeweiligen Sprache anlegen.

## Blöcke

Die Systemblöcke (Ressorts, Artikel aus einem Ressort, Meistgelesen, Archiv, Autoren, Seiten, Kurzmeldungen, Umfrage) geben von selbst den Inhalt der gerade angezeigten Version aus. Die Überschrift des Blocks und der Inhalt der Blöcke **Text** und **Menü** werden nicht übersetzt. Vorgehen:

1. Öffnen Sie im visuellen Blockeditor die Einstellungen des Blocks und klappen Sie **Wann und wo der Block angezeigt wird** auf.
2. Wählen Sie im Feld **Sprachversion** die Standardsprache.
3. Fügen Sie einen zweiten Block desselben Typs hinzu, geben Sie ihm Überschrift und Inhalt in der zweiten Sprache und wählen Sie im Feld **Sprachversion** die zweite Sprache.

Die Option **in allen Sprachen** eignet sich für Blöcke ohne Text, zum Beispiel Werbung, oder mit ausgeschalteter Überschrift.

## Newsletter und Benachrichtigungen

- **Newsletter:** Ein Empfänger gehört zu der Version, auf der er sich angemeldet hat. Eine Ausgabe ist immer in einer Sprache und geht nur an Empfänger derselben Sprache; der automatische Newsletter legt für jede Sprache eine eigene Ausgabe an. Die Texte der E-Mail sind in der Sprache der Ausgabe. Einzelheiten: [Newsletter](../ctenari-a-prijmy/newsletter.md).
- **E-Mails an die Leser** (Bestätigung des Newsletter-Abonnements, Registrierung, Anmeldelink) kommen in der Sprache der Version, auf der der Leser die Aktion ausgeführt hat.
- **Benachrichtigungen im Browser** werden nicht nach Sprache getrennt. Ein Empfänger erhält Benachrichtigungen über Artikel aus allen Versionen.
- **Redaktionelle Benachrichtigungen** (Übergabe zur Korrektur, Veröffentlichung, Rückgabe) gehen an die Mitglieder der Redaktion in der Sprache ihrer Administration.

## Siehe auch

- [Sprachen der Website](jazyky-webu.md)
- [KI-Assistent](../psani/ai-asistent.md)
- [Ressorts, Schlagwörter und Serien](../psani/rubriky-stitky-serialy.md)
- [Blöcke und Layout](../vzhled/bloky-a-rozvrzeni.md)
