# Newsletter

Der Newsletter ist eine E-Mail mit einer Auswahl von Artikeln, die Sie an angemeldete Empfänger senden. Sie stellen ihn von Hand zusammen oder lassen ihn automatisch versenden. Alles läuft auf Ihrer Website, ohne fremden Versanddienst.

## Einschalten

1. Öffnen Sie im Hauptmenü **Erweiterungen**, haken Sie **Newsletter** an und speichern Sie.
2. Füllen Sie unter **Einstellungen → Allgemein** die **E-Mail der Redaktion** aus – von ihr wird der Newsletter versendet. Ohne sie meldet der Bildschirm Newsletter **Füllen Sie zuerst die E-Mail der Redaktion in den Einstellungen aus – von dieser Adresse wird der Newsletter versendet.**
3. Fügen Sie der Website den Block **Newsletter** hinzu (**Design → Blöcke und Layout**). Das ist das Anmeldeformular für die Leser.
4. Richten Sie den Versand über SMTP ein. Massennachrichten, die mit der Funktion des Servers gesendet werden, landen oft im Spam. Siehe [E-Mail](../provoz/posta.md).

Der Bereich **Leser → Newsletter** ist für Administrator und Redakteur zugänglich.

## Anmeldung zum Newsletter

Die Anmeldung hat zwei Schritte (Double Opt-in):

1. Der Leser gibt seine E-Mail in den Block Newsletter ein. Die Website antwortet: **Wir haben Ihnen eine E-Mail geschickt – bestätigen Sie das Abonnement mit einem Klick auf den Link darin.**
2. Der Leser klickt auf den Link in der E-Mail. Erst damit wird er Empfänger.

Ohne Bestätigung bekommt er nichts. Sie haben so einen Nachweis, dass die Adresse von ihrem Inhaber angemeldet wurde. Die Website antwortet genauso auch dem, der schon Empfänger ist, und verrät also nicht, welche Adressen in der Liste stehen. Von einer IP-Adresse gehen höchstens fünf Anmeldungen pro Stunde durch; das Formular schützt außerdem der Spamschutz.

Den Newsletter kann man auch bei der Registrierung als Leser wählen, mit dem Häkchen bei **Ich möchte den Newsletter erhalten**. Er wird mit demselben Link bestätigt wie die Registrierung.

Der Bildschirm Newsletter zeigt oben die Zahl der bestätigten Empfänger und die Zahl derer, für die gilt: **Wartet auf Bestätigung per E-Mail**. Der Link **anzeigen** öffnet die Liste **Newsletter-Empfänger** (die letzten 500) mit den Schaltflächen **Löschen** und **CSV herunterladen**.

## Ausgabe von Hand

1. Öffnen Sie **Leser → Newsletter**, Abschnitt **Neue Ausgabe**.
2. Füllen Sie den **Betreff der E-Mail** und bei Bedarf die **Einleitung** aus – ein paar Sätze vor der Aufzählung der Artikel.
3. Haken Sie im Feld **Artikel** an, was Sie senden möchten, höchstens 20 Artikel. Angeboten werden die letzten 15 veröffentlichten Artikel; vorausgewählt sind die, die seit dem letzten Newsletter erschienen sind.
4. Klicken Sie auf **Testweise an die Redaktion senden**. Die Nachricht kommt an die E-Mail der Redaktion. Prüfen Sie sie im E-Mail-Programm und auf dem Telefon. Das Formular wird dabei geleert, füllen Sie es danach also erneut aus.
5. Klicken Sie auf **An Newsletter-Empfänger versenden** und bestätigen Sie.

Die E-Mail enthält die Einleitung und zu jedem Artikel Titel, Vorspann und den Link **Artikel lesen →**; die ersten drei Artikel haben auch ein Bild. Sie hat auch eine reine Textfassung. Der vollständige Text der Artikel wird nicht gesendet, gesperrte Artikel bleiben also gesperrt.

### Ablauf des Versands

Versendet wird in Stapeln zu 40 Empfängern. Nach der Bestätigung öffnet sich die Seite **Newsletter-Versand** mit dem laufenden Stand – gesendet und verbleibend. **Lassen Sie sie geöffnet**, sie aktualisiert sich von selbst, bis alle bedient sind. Am Ende meldet sie **Fertig.**

Wenn Sie die Seite früher schließen, hält der Versand an. Nichts geht verloren und nichts wird doppelt gesendet: Klicken Sie in der Tabelle **Versendete Ausgaben** bei der Ausgabe auf **Versand fortsetzen**.

### Planen

Klappen Sie **Für später planen** auf, geben Sie **Versenden um** ein und klicken Sie auf **Planen**. Eine geplante Ausgabe wird von selbst im Hintergrund versendet; die Administration müssen Sie nicht geöffnet haben. Solange der Versand nicht begonnen hat, brechen Sie ihn in der Tabelle mit der Schaltfläche **Abbrechen** ab.

## Automatischer Newsletter

Klappen Sie den Abschnitt **Automatischer Newsletter** auf:

| Feld | Möglichkeiten |
|---|---|
| **Automatisch versenden** | **nein – ich stelle den Newsletter von Hand zusammen** (Standard) · **einmal pro Woche** · **jeden Tag** |
| **Tag und Uhrzeit** | der Wochentag gilt nur für den wöchentlichen Newsletter; Stunde 0–23; Standard Freitag 7 Uhr |
| **Einleitung** | freiwilliger fester Text, höchstens 1000 Zeichen |

Die Automatik wählt bis zu acht Artikel, die seit dem letzten Newsletter veröffentlicht wurden – angeheftete und meistgelesene zuerst. Kurze Meldungen und Artikel, die von Suchmaschinen ausgeschlossen sind, nimmt sie nicht auf. Den Betreff setzt sie aus dem Titel des ersten Artikels und dem Namen der Website zusammen. Wenn nichts Neues erschienen ist oder es niemanden gibt, dem man schreiben könnte, wird nichts gesendet. In der Tabelle der Ausgaben trägt sie das Etikett **automatisch**.

## Hintergrundaufgaben

Automatische und geplante Ausgaben versenden die Hintergrundaufgaben, bei jedem Lauf einen Stapel von 40 Empfängern. In der Standardeinstellung werden die Aufgaben bei Besuchen der Website gestartet. Der Newsletter geht also beim ersten Besuch nach der eingestellten Stunde hinaus, und bei einer Website mit wenigen Besuchern zieht sich der Versand in die Länge. Für eine genaue Uhrzeit und einen flüssigen Versand richten Sie cron ein – siehe [Hintergrundaufgaben](../provoz/ulohy-na-pozadi.md).

Eine Nachricht, die sich nicht senden ließ (zum Beispiel bei einem Ausfall von SMTP), bleibt in der E-Mail-Warteschlange und die Hintergrundaufgaben versuchen erneut, sie zu senden. Das Protokoll der gesendeten Nachrichten und der Fehler finden Sie unter **Einstellungen → E-Mail**.

## Sprachversionen

Auf einer mehrsprachigen Website hat jede Sprache ihre eigenen Empfänger – der Leser meldet sich für die Version an, auf der er das Formular ausgefüllt hat. Eine Ausgabe ist immer in einer Sprache:

- wählen Sie bei einer Ausgabe von Hand nur Artikel einer Sprachversion (angeboten werden 30 und sie tragen ein Sprach-Etikett); sie geht an die Empfänger derselben Version,
- die Automatik legt für jede Sprache eine eigene Ausgabe aus deren Artikeln an; die Einleitung wird nur der Ausgabe in der Standardsprache hinzugefügt,
- die Texte der E-Mail (Link zum Artikel, Fußzeile, Abbestellen) sind in der Sprache der Ausgabe.

## Statistik der Ausgaben

Die Tabelle **Versendete Ausgaben** zeigt die letzten 30 Ausgaben: **Empfänger**, **Geöffnet** (auch in Prozent) und **Klicks**. Das sind Gesamtzahlen; bei einzelnen Empfängern wird nichts verfolgt. Die Öffnungen sind ein Richtwert: Manche E-Mail-Programme laden keine Bilder, andere laden sie von selbst.

## Abbestellen

Jede Nachricht hat in der Fußzeile den Link **Newsletter abbestellen** und trägt Header für das Abbestellen mit einem Klick, die E-Mail-Dienste anzeigen. Das Abbestellen wirkt sofort und ohne Anmeldung: Die Adresse wird aus der Liste gelöscht und die Website bestätigt es mit der Meldung **Abonnement beendet**.

Einen Empfänger können Sie auch selbst in der Liste **Newsletter-Empfänger** entfernen, gegebenenfalls zusammen mit den übrigen Daten des Lesers unter **Einstellungen → Datenschutz und Cookies** (siehe [Leserkonten](ucty-ctenaru.md)).

## Siehe auch

- [E-Mail](../provoz/posta.md)
- [Hintergrundaufgaben](../provoz/ulohy-na-pozadi.md)
- [Blöcke und Layout](../vzhled/bloky-a-rozvrzeni.md)
- [Web Push](web-push.md)
